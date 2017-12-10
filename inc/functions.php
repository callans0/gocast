<?php
/*************PODCAST FUNCTIONS***************/

// This function counts the number of podcasts. Takes the search term as an optional parameter. Return type int.
function countPodcasts( $search = null )
{
  $search = strtolower($search);
  $podcasts = json_decode(file_get_contents('data/podcasts.json'), true);
  $count = 0;

  foreach ($podcasts['collection'] as $podcasts) {
    foreach ($podcasts as $podcast) {
      if (!empty($search)) {
        $string = implode($podcast);
        if (stripos($string, $search) !== false) {
          $count ++;
        }
      } else {
          $count ++;
      }
    }
    return $count;
  }
}


// Function to get all podcasts or those that match a given search term. Returns an array
function getPodcasts( $search = null, $offset, $limit = null )
{
    $podcasts = json_decode(file_get_contents('data/podcasts.json'), true);
    $results = [];
    foreach ($podcasts['collection'] as $podcasts) {
      foreach($podcasts as $podcast) {
        if (!empty($search)) {
          $string = implode($podcast);
          if (stripos($string, $search) !== false) {
            $results[] = $podcast;
          }
        } else {
            $results[] = $podcast;
        }
      }
        if (!empty($limit)) {
          $results = array_slice($results, $offset, $limit, $preserve_keys = true);
        }
        return $results;
    }
}


// Function to get a single podcast by id. Returns an object
function getPodcast( $id = null )
{
  $podcasts = json_decode(file_get_contents('data/podcasts.json'));

    foreach ($podcasts->collection->podcasts as $podcast) {
      if ($id == $podcast->id) {
        $item = $podcast;
        return $item;
      }
    }
}


//Function retrieves episodes for a given podcast
function getEpisodes( $collection_id )
{
    $itunes_url = "https://itunes.apple.com/lookup?entity=podcast&id=" . $collection_id;
    if ($itunes_json = file_get_contents($itunes_url)) {
      if ( gettype($itunes_json) === 'string' ) {
        $itunes_array = json_decode($itunes_json, true);
        $feed_url = $itunes_array["results"][0]["feedUrl"];
      } else {
          $itunes_array = 'Error';
      }
      $feed_xml = simplexml_load_file($feed_url);
      $episodes = [];
      foreach ($feed_xml->channel->item as $episode) {
        $episodes[] = $episode;
      }
      return $episodes;
    } else {
        return 'error';
    }
}

// Function searches iTunes for matching titles and returns the title and collection id as an array
function searchiTunes( $podcast_title )
{
  $podcast_title = urlencode($podcast_title);
  $itunes_url = "https://itunes.apple.com/search?term=" . $podcast_title . "&entity=podcast";
  if ($itunes_json = file_get_contents($itunes_url)) {
    if ( gettype($itunes_json) === 'string' ) {
      $itunes_array = json_decode($itunes_json, true);
      $results_array = [];
      foreach ( $itunes_array["results"] as $result ) {
        $results_array[] =  [
           "podcast_title" => $result['trackName'],
           "collection_id" => $result['collectionId']
         ];
      }
    } else {
        return 'error1';
    }
  } else {
      return 'error2';
  }
  return $results_array;
}

searchiTunes("developer");


/// Function adds a new podcast
function postPodcast( $new_podcast )
{
  if ( is_array($new_podcast) ) {
    $file = "data/podcasts.json";
    $podcasts = json_decode(file_get_contents( $file ));

    if (is_object( $podcasts->collection->podcasts[0])) {
      $podcasts->collection->podcasts[] = $new_podcast;
      $json = json_encode( $podcasts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
      file_put_contents( $file, $json );
      return 1;
    } else {
        return 2;
    }
  } else {
      return 0;
  }

}


/*************REVIEWS FUNCTIONS***************/

// Counts all reviews optionally by a given search term
function countReviews( $search = null, $podcast_id = null )
{
  if (($fh = fopen('data/reviews.csv', 'r')) !== false) {
    $keys = fgetcsv($fh);
    $reviews_array = [];
    while (!feof($fh)) {
      if (!empty($podcast_id)) {
        $row = fgetcsv($fh);
        if ($row[1] == $podcast_id ) {
          $reviews_array[] = array_combine($keys, $row);
          }
        } else if (!empty($search)) {
        $row = implode("~~", fgetcsv($fh));
        if ( stripos( $row , $search) !== false ) {
          $row = explode("~~", $row);
          $reviews_array[] = array_combine($keys, $row);
          }
        } else {
            $reviews_array[] = array_combine($keys, fgetcsv($fh));
        }
    }
    fclose($fh);
    $result = count($reviews_array);
    return $result;
  }
}


// Function returns all reviews by a given search term or by associated podcast_id. Returns all reviews if none supplied
function getReviews( $search = null, $podcast_id = null, $offset = 0, $limit = null )
{
  if (($fh = fopen('data/reviews.csv', 'r')) !== false) {
    $reviews_array = [];
    $keys = fgetcsv($fh);
    while (!feof($fh)) {
      if (!empty($podcast_id)) {
        $row = fgetcsv($fh);
        if ($row[1] == $podcast_id ) {
          $reviews_array[] = array_combine($keys, $row);
        }
      } else if (!empty($search)) {
        $row = implode("~~", fgetcsv($fh));
        if ( stripos( $row , $search) !== false ) {
          $row = explode("~~", $row);
          $reviews_array[] = array_combine($keys, $row);
          }
        } else {
            $reviews_array[] = array_combine($keys, fgetcsv($fh));
        }
    }
    fclose($fh);
    $result = array_slice( $reviews_array, $offset, $limit, $preserve_keys = true );
    return $result;
  }
}



// Function to get a single review by id. Returns an array
function getReview( $id )
{
  if (($fh = fopen('data/reviews.csv', 'r')) !== false) {
    $keys = fgetcsv($fh);
    while (!feof($fh)) {
      $reviews[] = array_combine($keys, fgetcsv($fh));
    }
    foreach ($reviews as $review) {
      if ( $review['review_id'] == $id ) {
        $results[] = $review;
      }
    }
    fclose($fh);
    return $results;
  }
}


// Function to create a new review
function postReview( $new_review ) {
  if ( ($fh = $fopen('data/reviews.csv', 'a')) !== false ) {
    fputcsv( $fh, $new_review );
    fclose( $fh );
    return 1;
  } else {
      return 0;
  }
}



/*************GENERIC FUNCTIONS***************/

// Function to truncate a character set
function truncate_chars($text, $limit, $ellipsis = '...') {
  if( strlen($text) > $limit ) {
      $endpos = strpos(str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $text), ' ', $limit);
      if($endpos !== FALSE)
          $text = trim(substr($text, 0, $endpos)) . $ellipsis;
  }
  return $text;
}

// Function checks whether the username and password entered matches that stored
// Returns true if yes, false if not
function userCredentials( $username, $password )
{
  $username = trim(strtolower($username));
  $password = trim(strtolower($password));

  $stored_username = 'admin';
  $stored_password = 'admin';

  if ( $username !== $stored_username && $password !== $stored_password ) {
    $result = 'incorrect username and password';
  } else if ( $username == $stored_username && $password !== $stored_password ) {
      $result = 'incorrect password';
  } else if ( $username !== $stored_username && $password == $stored_password ) {
      $result = 'incorrect username';
  } else if ( $username == $stored_username && $password == $stored_password ) {
      $result = 'success';
  }
  return $result;
}

// Function returns next podcast id in sequence
function nextPodcastId ()
{
  $podcasts = json_decode(file_get_contents('data/podcasts.json'), true);

  $counter = 0;
  foreach ($podcasts['collection'] as $podcasts) {
    foreach ($podcasts as $podcast) {
      $i = 0;
      while ( $i < $podcast['id'] ) {
        $i = $podcast['id'];
      }
    }
  } return $i + 1;
}

// Function returns next review id in sequence
function nextReviewId ()
{
  if (($fh = fopen('data/reviews.csv', 'r')) !== false ){
    $counter = 0;
    while(($reviews = fgetcsv($fh)) !== false){
      $counter++;
    }
    return $counter;
  };
}
