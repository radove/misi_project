<html>
<head>
    <script src="resources/wordcloud/lib/d3/d3.js"></script>
    <script src="resources/wordcloud/lib/d3/d3.layout.cloud.js"></script>
    <script src="resources/wordcloud/d3.wordcloud.js"></script>
</head>
<div class="aggs">
  <script>
  $( function() {
    $( ".widget input[type=submit], .widget a, .widget button" ).button().css("font-size", "12pt";
    $( "button, input, a" ).click( function( event ) {
      event.preventDefault();
    } );
  } );
  </script>

<?php
use Elasticsearch\ClientBuilder;

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
                                'size' => '20' # Can be increased if too small, but is a limit for protecting a crazy amount of adIds
                        ]
                ]
        ],
        'query' => [ 'bool' => [ 'filter' => $queryBuilder, 'should' => $shouldBuilder, 'must' => [ 'bool' => [ 'should' => $shouldMustBuilder ] ] ] ],
    ]
];

echo "<div class=\"leftDiv\"><b>Top Topics</b><br><br>";
$response = $client->search($params);
$responsePeople = $client->search($paramsTopPeople);
$i = 0;
$people = array();
foreach ($response['aggregations']['top_slices']['buckets'] as $ngram) {
	$i++;
	$keyword = $ngram['key'];
	$count = $ngram['doc_count'];
		if ($keyword == $search) {
	        	echo "<a href=\"index.php?search=".$keyword."\" class=\"ui-button ui-widget ui-corner-all\"  target=\"_parent\"><font color=\"green\" size=\"2em\">".$keyword." (".$count.")</font></a>";
		}
	        else {
	        	echo "<a href=\"index.php?search=".$keyword."\" class=\"ui-button ui-widget ui-corner-all\"  target=\"_parent\"><font size=\"2em\">".$keyword." (".$count.")</font></a>";
	        }
        $childrenEntry['text'] = $keyword;
        $childrenEntry['size'] = $count;
        $children[] = $childrenEntry;

}

foreach ($responsePeople['aggregations']['top_slices']['buckets'] as $ngram) {
        $keyword = $ngram['key'];
        $count = $ngram['doc_count'];
        if (substr_count($keyword, ' ') == 1) {
        	$entry = array();
                $entry['key'] = $keyword;
                $entry['count'] =  $count;
                $people[] = $entry;
        }
}

?>
<br><br><br></div>
<div class="\"rightDiv\">
<b>People Tags:</b><br><br>
<?php
foreach ($people as $person) {

        $searchTerm = $_SESSION["search"];
	$key = $person['key'];
	$count = $person['count'];
        $childrenEntry['text'] = $key;
        $childrenEntry['size'] = $count;
        $children[] = $childrenEntry;

        if ($key == $search) {
		echo "<a href=\"index.php?search=".$key."\" class=\"ui-button ui-widget ui-corner-all\"  target=\"_parent\"><font color=\"green\" size=\"2em\">".$key." (".$count.")</font></a>";
	}
	else {
		echo "<a href=\"index.php?search=".$key."\" class=\"ui-button ui-widget ui-corner-all\"  target=\"_parent\"><font color=\"blue\" size=\"2em\">".$key." (".$count.")</font></a>";
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
