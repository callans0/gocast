<?php

if (!empty($_GET['name'])) {
  $name = filter_input(INPUT_GET, "name", FILTER_SANITIZE_STRING);
  $itunes_url = "https://itunes.apple.com/search?media=podcast&term=" . urlencode($name) . "&limit=1";
  $itunes_json = file_get_contents($itunes_url);
  $itunes_array = json_decode($itunes_json, true);
} else {
    $itunes_array = 'error';
}

if (is_array($itunes_array)) {
  $feed_url = $itunes_array['results'][0]['feedUrl'];
  $feed_xml = simplexml_load_file($feed_url);
  // echo gettype($feed_xml);
  // var_dump(get_class($feed_xml));
  // var_dump($feed_xml);
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>podcast search</title>
  </head>
  <body>
    <form action="itunes.php" method="get">
      <input type="text" name="name" id="name" />
      <button type="submit">Search</button>
    </form>
    <?php

    // foreach ( $feed_xml->channel->item as $feed ) {
    //   echo '<p><strong>Title: ' . $feed->title . '</strong></p><br />';
    //   echo '<div class="episode">';
    //   echo '<p>Published: ' . $feed->pubDate . '</p><br />';
    //   // echo $feed->link . '<br />';
    //   echo '<audio controls="controls" src="' . $feed->link . '">';
    //   echo 'Your browser does not support the HTML5 Audio element.';
    //   echo '</audio><br />';
    //   echo '</div>';
    // }


      if (is_array($itunes_array)) {
        var_dump($itunes_array['results'][0]);
        echo '<br />';
        echo "Name: " . $itunes_array['results'][0]['collectionId'] . '<br />';
        echo "Name: " . $itunes_array['results'][0]['trackName'] . '<br />';
        echo "Genre: " . $itunes_array['results'][0]['primaryGenreName'] . '<br />';
        echo "Artist: " . $itunes_array['results'][0]['artistName'] . '<br />';
        echo '<img src="' . $itunes_array['results'][0]['artworkUrl60'] . '" />';
        foreach ($itunes_array['results'] as $details) {
          foreach ($details['genres'] as $genre) {
            echo $genre . '<br />';
          }
        }
        echo '<h2 class="title">' . $itunes_array['results'][0]['trackName'] .'</h2>';
        echo '<h3 class="creator">' . $itunes_array['results'][0]['artistName'] . '</h3>';
        echo '<h4 class="genre">';
        foreach ($itunes_array['results'] as $details) {
          foreach ($details['genres'] as $genre) {
            echo '<h5>' . $genre . '</h5>';
        echo '</h4>';
      }
        echo '<h5>Country: ' . $itunes_array['results'][0]['country'] . '</h5>';
    }
      }
    ?>


  </body>
</html>
