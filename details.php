<?php

include 'inc/functions.php';

// This filters the id then retrieves the matching podcast as an object
if (isset($_GET["id"])) {
  $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
  $item = getPodcast($id);
  $episodes = getEpisodes($item->collection_id);
} else if (isset($_GET["review_id"])) {
    $id = filter_input(INPUT_GET, "review_id", FILTER_SANITIZE_NUMBER_INT);
    $item = getReview($id);
  } else {
      header("location:index.php");
      exit;
}

$title = 'Details';
require 'inc/header.php';


if (empty($item)) {
  $error = "Sorry, could not be found. Try searching again";
  echo '<div class="alert alert-danger" role="alert">';
  echo '<a href="index.php" class="alert-link">' . $error . '</a>';
  echo '</div>';
} else if ( !empty($item) && isset($_GET["id"]) ) {
    echo '<div class="details">';
    echo '<div class="details-panel details-header">';
    echo '<div class="header-col1">';
    echo '<h2 class="title">' . $item->title .'</h2>';
    echo '<h3 class="creator">' . $item->producer . '</h3>';
    echo '<h4 class="genre">' . $item->genre . '</h4>';
    echo '<h5>Country: ' . $item->country . '</h5>';
    echo '<h5><a href="reviews.php?id=' . $item->id  . '">Reviews</a></h5>';
    echo '<h5>' . $item->host . '</h5>';
    echo '<h5><a href="' . $item->website . '">' . $item->website . '</a></h5>';
    echo '<h4 class="rating">';
      $rating_count = $item->rating;
      $count = 0;
      while ($count < $rating_count) {
        echo '<span class="glyphicon glyphicon-star" aria-hidden="true"></span> ';
        $count++;
      }
    echo '</h4>';
    echo '<div class="subscribe-panel">';
    echo '<img class="subscribe" src="img/social/social-1_logo-lastfm.svg" alt="" />';
    echo '<img class="subscribe" src="img/social/social-1_logo-soundcloud.svg" alt="" />';
    echo '<img class="subscribe" src="img/social/social-1_logo-spotify.svg" alt="" />';
    echo '</div>';
    echo '</div>';
    echo '<div class="header-col2">';
    echo '<img class="details-img" src="' . $item->image . '" alt="image" />';
    echo '</div>';
    echo '</div>';

    echo '<div class="details-panel details-two">';
    echo '<h3>Description</h3>';
    echo '<p>' . $item->description . '</p>';
    echo '</div>';

    echo '<div class="details-panel details-three">';
    echo '<h3>GoCast Review</h3>';
    echo '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec aliquam magna ac velit volutpat, nec aliquam dolor lacinia. Donec luctus nunc quam, ac semper urna feugiat ac. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut urna ex, tristique at lorem sed, iaculis pretium nulla. Duis fringilla scelerisque ligula sed interdum. Pellentesque non nisi est. Integer tempus bibendum nunc, ut tempor orci sollicitudin nec. Phasellus interdum lectus massa, ut pellentesque libero euismod et. Duis blandit arcu eget lectus elementum faucibus. Ut hendrerit orci at nibh ornare luctus quis ut eros. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum fermentum placerat ante.<p>Phasellus bibendum sagittis urna at porta. Sed in ornare sapien, eget ultrices neque. Duis condimentum ipsum eget viverra vehicula. Fusce sit amet eros fringilla, ultrices mi at, bibendum diam. Vivamus lobortis dignissim placerat. Fusce gravida orci vel metus vulputate convallis. Ut eget eros at dui pulvinar imperdiet.</p>';
    echo '<h5><a href="reviews.php">read more reviews</a></h5>';
    echo '</div>';

    echo '<div class="details-panel details-four">';
    echo '<h3>Latest Episodes</h3>';
    echo '<div class="episodes">';
    $random = array_slice($episodes, 0, 3);
    foreach ( $random as $episode) {
      $episode = (array)$episode;
      echo '<div class="episode">';
      echo '<h5>Title: ' . $episode['title'] . '</h5>';
      echo '<h6>Published: ' . substr(($episode['pubDate']), 0, 16) . '</h6>';
      echo '<audio class="audio" controls="controls" src="' . $episode['enclosure']['url'] . '">';
      echo 'Your browser does not support the HTML5 Audio element.';
      echo '</audio><br />';
      echo '</div>';
    }
    echo '</div>';
    echo '</div>';


    echo '</div>';
  } else if ( !empty($item) && isset($_GET["review_id"])) {
      echo '<div class="details details-panel">';
      echo '<img class="feat-img" src="' . $item[0]['img'] . '" alt="image" />';
      echo '<h2 class="title">' . $item[0]['header'] . '</h2>';
      echo '<h3 class="tagline">' . $item[0]['tagline'] . '</h3>';
      echo '<h4 class="author">' . $item[0]['author'] . ' </h4>';
      echo '<h5>' . $item[0]['date'] . '</h5>';
      echo '<h5><a href="details.php?id=' . $item[0]['podcast_id']  . '">';
        $podcast = getPodcast($item[0]['podcast_id']);
      echo $podcast->title;
      echo '</a></h5>';
      echo '<p>' . $item[0]['body'] . '</p>';
      echo '</div>';
  }

require 'inc/footer.php';
