<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Cyber Security Events</title>
<link rel="stylesheet" type="text/css" href="dove.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>
<?php
if (isset($_GET['dove']))
{
        $dove= $_GET['dove'];
}
else {
	$dove='home';
}
?>

<!-- MAIN MENU PHP SCRIPT -->
<div class="container">
<div class="menu">
<?php include('menu.php'); ?>
</div>
<?php include('featured.php'); ?>
<br>
<!-- FACEBOOK CODE -->
<!-- BUILD THE CONTENT -->
<div class="content">
<?php
if (isset($dove))
{
	if ($dove == 'reset') {
		session_destroy();
		session_start();
	}
        switch ($dove)
        {
                default:
                include('main.php');
                break;

                case "home":
                include('main.php');
                break;

                case "search":
                include('search.php');
                break;

                case "import":
                include('import.php');
                break;

        }
}
else
{
        include('main.php');
}
 ?>
<br>
</div>
<div class="footer">
<center><?php include('footer.php'); ?></center></div>
</div>
</div>
</body>
</HTML>

