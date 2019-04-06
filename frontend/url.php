<?php use Elasticsearch\ClientBuilder;

$client = include 'elastic.php';

if(isset($_GET["url"])) {
        $url = $_GET["url"];
}

if(isset($_GET["category"])) {
        $category = $_GET["category"];
}

if(isset($url)) {
if (substr( $url, 0, 4 ) === "http") {
    $urlAdd = md5($url);
    $params = [
    'index' => 'url',
    'type' => 'data',
    'id' => "$urlAdd",
    'body' => [ 'url' => "$url", 'user_category' => "$category" ]
];

$response = $client->index($params);
        echo "<b>Added URL to Queue:</b>: <a href=\"http://research.dovestech.com/index.php?search=$urlAdd\">$url</a>";
	header("Location: http://research.dovestech.com/index.php?dove=import&url=$url", true, 302);
	exit;
}
else {
        echo "<b>Invalid URL</b>";
	header('Location: http://research.dovestech.com/index.php?dove=import&url=error', true, 302);
	exit;
}
}
?>
