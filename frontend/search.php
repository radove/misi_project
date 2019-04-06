<html>
<head>
<script>
function popupDialog(id) {
$.get( "getOne.php?id=" + id, function( data ) {
	
	$('<div></div>').dialog({
        modal: true,
	height: 600,
	width: 1200,
        title: "Metadata",
        open: function() {
          $(this).html(data);
        }
      });  //end confirm dialog
});
}
</script>
</head>
<?php

include('settings.php');
include('searchform.php');
$from = $page * 10;
$params = [
    'index' => 'cyberdata',
    'type' => 'data',
    'size' => 10,
    'from' => $from,
    'body' => [
#        'query' => [ 'bool' => [ 'filter' => $queryBuilder, 'should' => $shouldBuilder, 'must' => [ 'exists' => [ 'field' => 'entities.dates' ] ], 'must' => [ 'exists' => [ 'field' => 'entities.locations' ] ] ] ],
        'query' => [ 'bool' => [ 'filter' => $queryBuilder, 'should' => $shouldBuilder, 'must' => [ 'bool' => [ 'should' => $shouldMustBuilder ] ] ] ],
	'highlight' => [ 'fields' => [ '*' => (object)[] ], 'pre_tags' => '<mark>', 'post_tags' => '</mark>', 'fragment_size' => 0 ]
    ]
];

$response = $client->search($params);

$totalHits = $response['hits']['total'];
$pages = $totalHits / 10;
$pagesDisplay = $totalHits / 10;
if (($page + 3) < $pages) {
	$pages = $page + 3;
}

?>
  <ul class="pagination float-right">
<?php
    if ($page > 0) {
	    $prev = $page - 1;
	    echo "<li class=\"page-item\"><a class=\"page-link\" href=\"index.php?dove=search&search=$search&page=$prev\">Previous</a></li>";
	}
	else {
	    echo "<li class=\"page-item\"><a class=\"page-link\" href=\"index.php?dove=search&search=$search&page=0\">Previous</a></li>";
	}



for ($p=$page; $p<$pages; $p++) {
    $display = $p + 1;
    echo "<li class=\"page-item\"><a class=\"page-link\" href=\"index.php?dove=search&search=$search&page=$p\">$display</a></li>";
	}
    if ($page < $pages) {
            $next = $page + 1;
            echo "<li class=\"page-item\"><a class=\"page-link\" href=\"index.php?dove=search&search=$search&page=$next\">Next</a></li>";
        }
	else
	{
            echo "<li class=\"page-item\"><a class=\"page-link\" href=\"#\">Next</a></li>";

	}

	?>
  </ul>
<p class="float-left">
<b>Total Hits</b>: <?php echo $totalHits; ?> <b>Page</b>: (<?php echo $page + 1; ?> of <?php echo ceil($pagesDisplay); ?>)
</p>
<table class="table table-condensed">
  <thead>
    <tr>
      <th scope="col"></th>
      <th scope="col"></th>
      <th scope="col">Topics</th>
      <th scope="col">Title</th>
      <th scope="col">Description</th>
      <th scope="col">Extractions</th>
    </tr>
  </thead>
  <tbody>

<?php

$buckets = $response['hits']['hits'];

$i = 1;
foreach($buckets as $item) {
	$topic_links = array();
	$extraction_links = array();
	$location_links = array();

	$title = $item['_source']['title'];
	$data_type = $item['_source']['data_type'];
	if (substr( $data_type, 0, 3 ) === "web") {
		$icon = "<img src=\"images/web.png\" height=\"30px\" width=\"30px\">";
	}
	if ($data_type == "twitter") {
                $icon = "<img src=\"images/tweet.png\" height=\"30px\" width=\"30px\">";	
	}
	if ($data_type == "import") {
                $icon = "<img src=\"images/file.png\" height=\"30px\" width=\"30px\">";
	}

	$desc = $item['_source']['content'];
	$url = $item['_source']['url'];
	$timestamp = $item['_source']['timestamp'];
	$id = $item['_id'];

	$locations = $item['_source']['entities']['locations'];
	$orgs = $item['_source']['entities']['organizations'];
	$persons = $item['_source']['entities']['persons'];
	$dates = $item['_source']['entities']['dates'];
	$ngrams = $item['_source']['ngram'];

	$topic_count = array_count_values($ngrams); // get count of occurrence for each number
	arsort($topic_count);
	$top_topics = array_slice($topic_count, 0, 5);
	foreach (array_keys($top_topics) as $value) { $topic_links[] = "<a href=\"index.php?dove=search&search=$value\">$value</a>"; }

        $location_count = array_count_values($locations); // get count of occurrence for each number
        arsort($location_count);
        $top_locations = array_slice($location_count, 0, 1);
        foreach (array_keys($top_locations) as $value) { $location_links[] = "<a href=\"index.php?dove=search&search=$value\">$value</a>"; }


        $people_count = array_count_values($persons); // get count of occurrence for each number
        arsort($people_count);
        $top_people = array_slice($people_count, 0, 2);

        $orgs_count = array_count_values($orgs); // get count of occurrence for each number
        arsort($orgs_count);
        $top_orgs = array_slice($orgs_count, 0, 2);

        $date_count = array_count_values($dates); // get count of occurrence for each number
        arsort($date_count);
        $top_dates = array_slice($date_count, 0, 1);

        $desc = htmlspecialchars($desc);
	$title = htmlspecialchars($title);

	foreach ($item['highlight']['content'] as $desc_highlight) {
		$no_highlights = str_replace("<mark>","",$desc_highlight);
		$no_highlights = str_replace("</mark>","",$no_highlights);
		$desc = str_replace($no_highlights, $desc_highlight, $desc);
	}

        foreach ($item['highlight']['title'] as $title_highlight) {
                $no_highlights = str_replace("<mark>","",$title_highlight);
                $no_highlights = str_replace("</mark>","",$no_highlights);
                $title = str_replace($no_highlights, $title_highlight, $title);
        }

	if (!empty($url)) {
		$icon = "<a href=\"".$url."\" target=\"_blank\">".$icon."</a>";
	}
	if ($data_type == "import") {
        	$icon = "<a href=\"data/" . $id . "\" target=\"_blank\">" . $icon . "</a>";
	}



    echo "<tr>";

    echo "<th scope=\"row\"><a href=\"\" onclick=\"event.preventDefault(); popupDialog('" . $id . "'); return false;\"><img src=\"images/explore.png\" width=\"30px\" height=\"30px\"></a>";
    echo "<th scope=\"row\">" . $icon . "</th>";
    echo "<td>" . implode(", ",$topic_links) . "</td>";
    echo "<td>" . $title . "</td>";
    if (strlen($desc) > 400) {
	    $less = substr($desc, 0, 400);
	    $more = substr($desc, 400);
		?>
		<td>
			<?php echo $less ?>...
			<div id="demo<?php echo $i ?>" class="collapse">
				<?php echo $more ?>
			</div> 
<br><br>
			 <p data-toggle="collapse" data-target="#demo<?php echo $i ?>"><a href="" onclick="return false;">Read More</a></p>
		</td>
<?php
    }
    else {
	    echo "<td>" . $desc . "</td>";
    }
    if ($data_type == "import") {
	$epoch = substr($timestamp, 0, 10);
	$dt = new DateTime("@$epoch");  // convert UNIX timestamp to PHP DateTime
	$timestamp = $dt->format('Y-m-d'); // output = 2017-01-01 00:00:00
    }
    else {
	#echo "<td>" . implode(", ",array_keys($top_dates)) . "</td>";
    }
	$combined = array_merge($top_people, $top_orgs);
        foreach (array_keys($combined) as $value) { $extraction_links[] = "<a href=\"index.php?dove=search&search=$value\">$value</a>"; }

	echo "<td>" . implode(", ",$extraction_links) . "</td>";
	echo "</tr>";
}
?>
</tbody>
</table>

