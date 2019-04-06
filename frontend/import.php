<?php include('config.php');?>
<?php
use Elasticsearch\ClientBuilder;

$client = include 'elastic.php';

if(isset($_GET["url"])) {
        $url = $_GET["url"];
	if ($url == "error") {
		echo "<b>Invalid URL</b>. We couldn't add this URL to the queue because it appears to be invalid (doesn't start with http).<br><br>";
	}
	else
	{
	        echo "<b>Added URL to Queue:</b>: <a href=\"http://research.dovestech.com/index.php?search=$url\">$url</a>. Please wait a few seconds and then attempt searching for your web URL by  <a href=\"http://research.dovestech.com/index.php?search=$url\">clicking here</a>. <br><br><b>Note:</b> URL Web Crawling goes two href URL levels deep and stops.<br><br>";
	}
}
if (isset($_POST['category'])) {
	$category = $_POST['category'];
}
if(isset($_FILES['file']))
{
	$fileName = $_FILES['file']['name'];
	$destFile = "/opt/dropbox/".$_FILES['file']['name'] . $category;
	move_uploaded_file( $_FILES['file']['tmp_name'], $destFile );
	?>
	<li>Sent file: <?php echo $_FILES['file']['name'];  ?>
	<li>File size: <?php echo $_FILES['file']['size'];  ?> bytes
	<li>File type: <?php echo $_FILES['file']['type'];  ?>
	<p>Please search for your data by clicking here: <a href="http://research.dovestech.com/index.php?search=<?php echo $fileName ?>"><?php echo $fileName ?></a></p>
	<?php
}
else {
?>

<?php
}
?>
<h3>Import</h3>
<p>Import data from cyber security articles, resumes, white papers and more! Currently accepting most data types.</b></p>
<form action="index.php?dove=import" method="post" enctype="multipart/form-data">
  <div class="form-group">
   <div class="form-inline">
  <div class="form-group mb-2">
    <input type="file" class="form-control" id="file" name="file">
  </div>
  <div class="form-group mb-2 mx-sm-3">
    <select class="form-control mx-sm-3a" id="category" name="category">
        <option value="Marketing">Marketing</option>
        <option value="White_Paper">White Paper</option>
        <option value="Resume">Resume</option>
    </select>
  </div>
  <div class="form-group mb-2 mx-sm-3">
  <button type="submit" class="btn btn-primary">Submit File</button>
  </div>
</div>
</form>
<br><br>
<h3>Import/Webcrawl URL</h3>
<form action="url.php" method="get">
<div class="form-inline">
  <div class="form-group mb-2">
    <input type="text" class="form-control" id="url" placeholder="URL" name="url">
  </div>
  <div class="form-group mb-2 mx-sm-3">
    <select class="form-control mx-sm-3a" id="category" name="category">
	<option value="Business Website">Business Website</option>
	<option value="Government Website">Government Website</option>
	<option value="News Website">News Website</option>
	<option value="Event Website">Event Website</option>
    </select>
  </div>
  <div class="form-group mb-2 mx-sm-3">
  <button type="submit" class="btn btn-primary">Submit URL</button>
 </div>
</div>
</form>

<br><br>
<h3>List of URL's for Crawling</h3>
<br><ul>
<?php

$params = [
    'index' => 'url',
    'type' => 'data',
    'size' => 500
];


$response = $client->search($params);

$buckets = $response['hits']['hits'];

foreach($buckets as $item) {
	echo "<li>" . $item['_source']['url'] . " | " . $item['_source']['user_category'] . "<br></li>";
}

?>
</ul>
