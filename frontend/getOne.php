<?php
use Elasticsearch\ClientBuilder;
$client = include 'elastic.php';

if (isset($_GET['id']))
{
        $id = $_GET['id'];
}

        $queryKeyword = [ 'term' => [ '_id' => "$id" ] ];
        $filterBuilder[] = $queryKeyword;

$paramGetOne = [
    'index' => 'cyberdata',
    'type' => 'data',
    'size' => 1,
    'body' => [
        'query' => [ 'bool' => [ 'filter' => $filterBuilder ] ]
    ]
];

$response = $client->search($paramGetOne);

$buckets = $response['hits']['hits'];

foreach($buckets as $item) {
	$content = nl2br($item['_source']['content']);
	$title = nl2br($item['_source']['title']);
	
	print_r("<b>ID</b>: " . $id);
	print_r("<br>");
	print_r("<b>Title</b>: " . $title);
	print_r("<br>");
	print_r("<br>");
	print_r("<b>Content</b>: " . $content);
}
?>
