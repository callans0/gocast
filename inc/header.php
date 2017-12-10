<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title><?php echo $title; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <!-- font-family: 'Montserrat', sans-serif; -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:200,300,500" rel="stylesheet">
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->

    <!-- Custom styles for this template -->
    <link href="css/sticky-footer-navbar.css" rel="stylesheet">

    <!-- JQuery library and scripts -->
    <script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
  <script src="../js/forms.js"></script>
  <script src="../js/test.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">gocast.com</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <?php
                $pages = ['reviews'];
                foreach ($pages as $page) {
                    echo '<li';
                    if (basename($_SERVER['REQUEST_URI'], '.php') == $page) {
                        echo ' class="active"';
                    }
                    echo '><a href="' . $page . '.php">' . ucwords($page) . '</a></li>';
                }
                ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li><a href="about.php">About</a></li>
              <li><a href="contact.php">Contact</a></li>
                <?php
                if (!isset($_SESSION['logged_in'])) {
                  echo '<li><a href="login.php">Login</a></li>';
                } else if ($_SESSION['logged_in']) {
                   echo '<li><a class="admin-nav" href="login.php?logout">Logout</a></li>';
                }
                ?>
            </ul>
        </div><!--/.nav-collapse -->
    </div><!--/.container-fluid -->
</nav>

<?php

if (isset($_GET['search'])) {
  $search = filter_input(INPUT_GET, "search", FILTER_SANITIZE_STRING);
}

// Explore banner

switch ($title) {
  case "explore":
    echo '<div class="banner banner-explore">';
    echo '<h1>Explore podcasts</h1>';
    echo '<form class="explore-form" method="get" action="index.php">';
    echo '<input class="form-control" type="text" name="search" id="search" placeholder="search title, author, keyword"';
           if (isset($search)) {
             echo 'value=';
             $str = str_replace(' ', '&nbsp;', $search);
             echo $str;
          }
    echo '>';
    echo '<input class="btn btn-primary btn-lg btn-explore" type="submit" value="search"/>';
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true ) {
      echo '<a href="add.php?new=podcast"><h5 class="admin-link">+ Add new podcast</h5></a>';
    }
    echo '</form>';
    echo '</div>';
  break;

  case "review":
    echo '<div class="banner banner-review">';
    echo '<h1>Search Reviews</h1>';
    echo '<form class="explore-form" method="get" action="reviews.php">';
    echo '<input class="form-control" type="text" name="search" id="search" placeholder="search by title, author or keyword"';
           if (isset($search)) {
             echo 'value=';
             $str = str_replace(' ', '&nbsp;', $search);
             echo $str;
          }
    echo '>';
    echo '<input class="btn btn-primary btn-lg btn-review" type="submit" value="search"/>';
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true ) {
      echo '<a href="add.php?new=review"><h5 class="admin-link">+ Add new review</h5></a>';
    }
    echo '</form>';
    echo '</div>';
  break;
}

?>

<!-- Begin page content -->
<div class="container">
