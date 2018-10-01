<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title; ?></title>  
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  	<link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css" />
    <!-- our custom CSS -->
    <link rel="stylesheet" href="css/custom.css" />
    <?=($page_title == "View Timeline"?"<link rel=\"stylesheet\" href=\"css/timeline.css?time=".time()."\" />":"")?>
</head>
<body>
<div class="container">
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Photo Logger 1.0</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li <?=($page_title=="Upload Images"?"class=\"active\"":"")?>><a href="index.php">Upload Images</a></li>
        <li <?=($page_title=="View Listing"?"class=\"active\"":"")?>><a href="listing.php">View Listing</a></li>
        <li <?=($page_title=="View Timeline"?"class=\"active\"":"")?>><a href="timeline.php">View Timeline</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
        <?php
        // show page header
        echo "<div class='page-header'>
                <h1>{$page_title}</h1>
            </div>";
        ?>