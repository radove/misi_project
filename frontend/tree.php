<html>
<head>
<script src="https://cdn.jsdelivr.net/npm/vue"></script>
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
                'ngram_slices' => [
                        'terms' => [
                                'field' => 'ngram.keyword',
                                'size' => '10' # Can be increased if too small, but is a limit for protecting a crazy amount of adIds
                        ],
                        'aggs' => [
                                'persons_slices' => [
                                        'terms' => [
                                                'field' => 'realName.keyword',
                                                'size' => 10
                                        ]
                                ]
                        ]
                ]
        ],
        'query' => [ 'bool' => [ 'filter' => $queryBuilder, 'should' => $shouldBuilder, 'must' => [ 'bool' => [ 'should' => $shouldMustBuilder ] ] ] ],
    ]
];
echo "<div class=\"leftDiv\">";
$response = $client->search($params);
$i = 0;
$people = array();
foreach ($response['aggregations']['ngram_slices']['buckets'] as $ngram) {
	$i++;
	$keyword = $ngram['key'];
	$count = $ngram['doc_count'];
		if ($keyword == $search) {
	        	echo "<a href=\"index.php?search=".$keyword."\" class=\"ui-button ui-widget ui-corner-all\"  target=\"_parent\"><font color=\"green\" size=\"2em\">".$keyword." (".$count.")</font></a>";
 		       foreach($ngram['persons_slices']['buckets'] as $persons) {
		                $person_count = $persons['doc_count'];
		                $person_key = $persons['key'];
				if (substr_count($person_key, ' ') == 1) {
					$entry = array();
					$entry['key'] = $person_key;
					$entry['count'] =  $person_count;
					$people[] = $entry;
				}
		        }
		}
	        else {
	        	echo "<a href=\"index.php?search=".$keyword."\" class=\"ui-button ui-widget ui-corner-all\"  target=\"_parent\"><font size=\"2em\">".$keyword." (".$count.")</font></a>";
	        }
        $childrenEntry['text'] = $keyword;
        $childrenEntry['size'] = $count;
        $children[] = $childrenEntry;

}
?>
<br><br>
<?php
foreach ($people as $person) {
	$key = $person['key'];
	$count = $person['count'];
        $childrenEntry['text'] = $key;
        $childrenEntry['size'] = $count;
        $children[] = $childrenEntry;

	echo "<a href=\"index.php?search=".$key."\" class=\"ui-button ui-widget ui-corner-all\"  target=\"_parent\"><font color=\"blue\" size=\"2em\">".$key." (".$count.")</font></a>";
}
?>
</div>
    <div id='wordcloud' class="rightDiv"></div>
    <script>
      d3.wordcloud()
        .size([700, 400])
        .selector('#wordcloud')
        .words(<?php echo json_encode($children); ?>)
.onwordclick(function(d, i) {
  window.location = "index.php?search=" + d.text;
})

        .start();
    </script>
</div>
