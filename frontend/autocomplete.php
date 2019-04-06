<?php
use Elasticsearch\ClientBuilder;
$client = include 'elastic.php';

if (isset($_GET['term']))
{
        $search = $_GET['term'];
}
else {
        $search = "*";
}

        $queryKeyword = [ 'query_string' => [ 'fields' => ['ngram.keyword'], 'query' => "$search*", 'default_operator' => 'AND', 'boost' => 5.0 ] ];
        $filterBuilder[] = $queryKeyword;

$autocompleteParam = [
    'index' => 'cyberdata',
    'type' => 'data',
    'size' => 0,
    'body' => [
        'aggs' => [
                'sub_cat' => [
                        'terms' => [
                                'field' => 'user_category.keyword',
                                'size' => "10"
                        ],
                'aggs' => [
                        'top_slices' => [
                                'terms' => [
                                        'field' => 'ngram.keyword',
                                        'size' => "100" # Can be increased if too small, but is a limit for protecting a crazy amount of adIds
                                ]
                        ]
                ] ]
        ],
        'query' => [ 'bool' => [ 'filter' => $filterBuilder ] ]
    ]
];

$autocompleteResponse = $client->search($autocompleteParam);

foreach ($autocompleteResponse['aggregations']['sub_cat']['buckets'] as $cats) {
        $sub_cat = $cats['key'];
        if (!empty($sub_cat)) {
                foreach($cats['top_slices']['buckets'] as $desc) {
                        $desc_key = $desc['key'];
			if (substr($desc_key, 0, strlen($search)) === $search) {
                        	$autocomplete[] = trim($desc_key) . " | " . $sub_cat;
	                        $autocompleteTop[] = trim($desc_key);
			}
                }
        }
}
        print_r(json_encode($autocomplete));
?>

