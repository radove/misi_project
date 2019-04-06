<?php

use Elasticsearch\ClientBuilder;

$client = include 'elastic.php';

if (isset($_GET['page']))
{
        $page = $_GET['page'];
}
else {
        $page=0;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_SESSION['socialFeed'] = false;
        $_SESSION['importFeed'] = false;
        $_SESSION['webFeed'] = false;
        $_SESSION["search"] = "*";
	$_SESSION["from"] = "";
	$_SESSION["to"] = "";

        if (isset($_POST['from']))
        {
                $from = $_POST['from'];
                $_SESSION["from"] = $from;
        }

        if (isset($_POST['to']))
        {
                $to = $_POST['to'];
                $_SESSION["to"] = $to;
        }

        if (isset($_POST['search']))
        {
                $search = $_POST['search'];
                $_SESSION["search"] = $search;
        }
        if (isset($_POST['category']))
        {
                $category = $_POST['category'];
                $_SESSION["category"] = $category;
        }

        if (isset($_POST['socialFeed']))
        {
                if ($_POST['socialFeed']) {
                        $socialFeed = true;
                        $_SESSION['socialFeed'] = true;
                }
        }

        if (isset($_POST['importFeed']))
        {
                if ($_POST['importFeed']) {
                        $importFeed = true;
                        $_SESSION['importFeed'] = true;
                }
        }
        if (isset($_POST['webFeed']))
        {
                if ($_POST['webFeed']) {
                        $webFeed = true;
                        $_SESSION['webFeed'] = true;
                }
        }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['search']))
        {
                $search = $_GET['search'];
                $_SESSION["search"] = $search;
        }
        if (isset($_GET['category']))
        {
                $category = $_GET['category'];
                $_SESSION["category"] = $category;
        }

}



$socialFeed = $_SESSION['socialFeed'];
$importFeed = $_SESSION['importFeed'];
$webFeed = $_SESSION['webFeed'];
$category = $_SESSION['category'];
$search = $_SESSION['search'];
$from = $_SESSION['from'];
$to = $_SESSION['to'];

if (empty($search)) {
	$search = "*";
}

if (empty($category) || $category == 'All') {
	$category = "*";
}


if (empty($socialFeed)) {
        $socialFeed = false;
}

if (empty($webFeed)) {
        $webFeed = false;
}

if (empty($importFeed)) {
        $importFeed = false;
}
if ($socialFeed == false && $importFeed == false && $webFeed == false) {
        $socialFeed = true;
        $importFeed = true;
        $webFeed = true;
}
        $filterKeyword = [ 'bool' => [ 'must' => [ 'query_string' => [ 'fields' => ['_id', 'data_type', 'screen_name', 'title', 'content', 'ngram.keyword', 'entities.dates.keyword', 'entities.locations.keyword', 'entities.persons.keyword', 'fileName'], 'query' => "$search", 'default_operator' => 'AND' ] ] ] ];
        $queryBuilder[] = $filterKeyword;
        $filterKeyword = [ 'bool' => [ 'must' => [ 'query_string' => [ 'fields' => [ 'user_category' ], 'query' => "$category", 'default_operator' => 'AND' ] ] ] ];
        $queryBuilder[] = $filterKeyword;
        $filterKeyword = [ 'bool' => [ 'must_not' => [ 'query_string' => [ 'fields' => [ 'content' ], 'query' => "*obama* *trump* *russian*", 'default_operator' => 'OR' ] ] ] ];
        $queryBuilder[] = $filterKeyword;
	if ($to != '' && $from != '') {
	        $filterKeyword = [ 'bool' => [ 'must' => [ 'range' => [ 'timestamp' => [ 'gte' => "$from", 'lte' => "$to", 'format' => 'MM/dd/yyyy' ] ] ] ] ];
	        $queryBuilder[] = $filterKeyword;
	}

        $shouldKeyword = [ 'term' => [ 'data_type' => [ 'value' => 'webcustom', 'boost' => 2.0 ] ] ];
        $shouldBuilder[] = $shouldKeyword;
        $shouldKeyword = [ 'term' => [ 'data_type' => [ 'value' => 'webcrawl', 'boost' => 1.0 ] ] ];
        $shouldBuilder[] = $shouldKeyword;
        $shouldKeyword = [ 'term' => [ 'data_type' => [ 'value' => 'webharvest', 'boost' => 0.5 ] ] ];
        $shouldBuilder[] = $shouldKeyword;
        $shouldKeyword = [ 'term' => [ 'data_type' => [ 'value' => 'import', 'boost' => 1.0 ] ] ];
        $shouldBuilder[] = $shouldKeyword;
        $shouldKeyword = [ 'term' => [ 'data_type' => [ 'value' => 'twitter', 'boost' => 1.0  ] ] ];
        $shouldBuilder[] = $shouldKeyword;


if ($webFeed) {
        $mustKeyword = [ 'wildcard' => [ 'data_type' => 'web*' ] ];
        $shouldMustBuilder[] = $mustKeyword;
}
if ($importFeed) {
        $mustKeyword = [ 'wildcard' => [ 'data_type' => 'import' ] ];
        $shouldMustBuilder[] = $mustKeyword;
}
if ($socialFeed) {
        $mustKeyword = [ 'wildcard' => [ 'data_type' => 'twitter' ] ];
        $shouldMustBuilder[] = $mustKeyword;
}

ob_start();
include "categories.php";
$categories = ob_get_clean();

?>
