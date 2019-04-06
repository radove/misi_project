<html>
<head>
    <script src="resources/wordcloud/lib/d3/d3.js"></script>
    <script src="resources/wordcloud/lib/d3/d3.layout.cloud.js"></script>
    <script src="resources/wordcloud/d3.wordcloud.js"></script>
</head>
<div class="aggs">
<?php

include('settings.php');
$searchType = 'main';
include('searchform.php');

$client = include 'elastic.php';
$params = [
    'index' => 'cyberdata',
    'type' => 'data',
    'size' => 0,
    'body' => [
        'aggs' => [
                'top_slices' => [
                        'terms' => [
                                'field' => 'ngram.keyword',
                                'size' => '10' # Can be increased if too small, but is a limit for protecting a crazy amount of adIds
                        ]
                ]
        ],
        'query' => [ 'bool' => [ 'filter' => $queryBuilder, 'should' => $shouldBuilder, 'must' => [ 'bool' => [ 'should' => $shouldMustBuilder ] ] ] ],
    ]
];

$paramsTopPeople = [
    'index' => 'cyberdata',
    'type' => 'data',
    'size' => 0,
    'body' => [
        'aggs' => [
                'top_slices' => [
                        'terms' => [
 #                               'field' => 'realName.keyword',
                                'field' => 'entities.persons.keyword',
                                'size' => '10' # Can be increased if too small, but is a limit for protecting a crazy amount of adIds
                        ]
                ]
        ],
        'query' => [ 'bool' => [ 'filter' => $queryBuilder, 'should' => $shouldBuilder, 'must' => [ 'bool' => [ 'should' => $shouldMustBuilder ] ] ] ],
    ]
];
echo "<h3>Trending in Cyber Security</h3>";
echo "<div id=\"barDiv\"></div>";
# echo "<div class=\"leftDiv\"><b>Topic Tags</b><br><br>";
$response = $client->search($params);
$responsePeople = $client->search($paramsTopPeople);
$i = 0;
$people = array();
$barChartData = array();
foreach ($response['aggregations']['top_slices']['buckets'] as $ngram) {
	$i++;
	$keyword = $ngram['key'];
	$count = $ngram['doc_count'];
		if ($keyword == $search) {
#	        	echo "<a href=\"index.php?search=".$keyword."\" class=\"ui-button ui-widget ui-corner-all\"  target=\"_parent\"><font color=\"green\" size=\"2em\">".$keyword." (".$count.")</font></a>";
		}
	        else {
#	        	echo "<a href=\"index.php?search=".$keyword."\" class=\"ui-button ui-widget ui-corner-all\"  target=\"_parent\"><font size=\"2em\">".$keyword." (".$count.")</font></a>";
	        }
	$keywordsBar[] = $keyword;
	$countsBar[] = $count;
        $childrenEntry['text'] = $keyword;
        $childrenEntry['size'] = $count;
        $children[] = $childrenEntry;

}

$barChartData['x'] = $keywordsBar;
$barChartData['y'] = $countsBar;
$barChartData['type'] = 'bar';
$barChartData['name'] = 'Topics';

$barChartData2 = array();

foreach ($responsePeople['aggregations']['top_slices']['buckets'] as $ngram) {
        $keyword = $ngram['key'];
        $count = $ngram['doc_count'];

        if (substr_count($keyword, ' ') == 1) {
	        $keywordsBar2[] = $keyword;
	        $countsBar2[] = $count;
        	$entry = array();
                $entry['key'] = $keyword;
                $entry['count'] =  $count;
                $people[] = $entry;
        }
}

$barChartData2['x'] = $keywordsBar2;
$barChartData2['y'] = $countsBar2;
$barChartData2['type'] = 'bar';
$barChartData2['name'] = 'People';


?>
<script>
var layout = {barmode: 'stack'};
var data1 = <?php echo json_encode($barChartData); ?>;
var data2 = <?php echo json_encode($barChartData2); ?>;
var data = [ data1, data2 ];
Plotly.newPlot('barDiv', data, layout);

document.getElementById("barDiv").on('plotly_click', function(data){

	window.location.href = "http://research.dovestech.com/index.php?search=" + data.points[0].x;
});

</script>
<br><br><br></div>
<div class="\"rightDiv\">
<?php
foreach ($people as $person) {

        $searchTerm = $_SESSION["search"];
	$key = $person['key'];
	$count = $person['count'];
        $childrenEntry['text'] = $key;
        $childrenEntry['size'] = $count;
        $children[] = $childrenEntry;

        if ($key == $search) {
	#	echo "<a href=\"index.php?search=".$key."\" class=\"ui-button ui-widget ui-corner-all\"  target=\"_parent\"><font color=\"green\" size=\"2em\">".$key." (".$count.")</font></a>";
	}
	else {
	#	echo "<a href=\"index.php?search=".$key."\" class=\"ui-button ui-widget ui-corner-all\"  target=\"_parent\"><font color=\"blue\" size=\"2em\">".$key." (".$count.")</font></a>";
	}
}
?>
</div>
    <div id='wordcloud' class="centerDiv"></div>
    <script>
      d3.wordcloud()
        .size([960, 400])
        .selector('#wordcloud')
        .words(<?php echo json_encode($children); ?>)
.onwordclick(function(d, i) {
  window.location = "index.php?search=" + d.text;
})

        .start();
    </script>
</div>
