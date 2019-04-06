<?php
use Elasticsearch\ClientBuilder;
$client = include 'elastic.php';

$autocompleteParam = [
    'index' => 'cyberdata',
    'type' => 'data',
    'size' => 0,
    'body' => [
        'aggs' => [
                'top_slices' => [
                        'terms' => [
                                'field' => 'user_category.keyword',
                                'size' => '25' # Can be increased if too small, but is a limit for protecting a crazy amount of adIds
                        ]
                ]
        ]
    ]
];

$autocompleteResponse = $client->search($autocompleteParam);

foreach ($autocompleteResponse['aggregations']['top_slices']['buckets'] as $ngram) {
        $keyword = $ngram['key'];
       if ($keyword != '') {
                $autocomplete[] = $keyword;
        }
}
print_r(json_encode($autocomplete));
?>


