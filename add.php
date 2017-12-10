<?php
session_start();
$title = 'Add';
$add_new = '';
$error_message = '';
include 'inc/functions.php';

// Redirects user to the login page if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
  header('location: login.php');
}

// Sets '$add_new' either podcast or review. This will determine which form appears on the page
if (isset($_GET['new'])) {
  $add_new = filter_input(INPUT_GET, 'new', FILTER_SANITIZE_STRING);
}

// Clears form when 'Reset' button is selected
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset"])) {
  unset($results_array);
  $_POST = [];
}


// Search for podcast titles, returning matches in an array

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"]) && $add_new == "podcast") {
  if (empty($_POST['podcast_title'])) {
    $error_message = "- Podcast title must be entered.<br/>";
  } else {
      $podcast_title = trim(filter_input(INPUT_POST, "podcast_title", FILTER_SANITIZE_STRING));
      $results_array = searchiTunes($podcast_title);
  }
}

//********************************* ADD PODCAST **********************************************//
// Sets podcast variables based on submitted form data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"]) && $add_new == "podcast") {
  $podcast_title = trim(filter_input(INPUT_POST, "podcast_title", FILTER_SANITIZE_STRING));
  $genre = trim(filter_input(INPUT_POST, "genre", FILTER_SANITIZE_STRING));
  $producer = trim(filter_input(INPUT_POST, "producer", FILTER_SANITIZE_STRING));
  $rating = intval(trim(filter_input(INPUT_POST, "rating", FILTER_SANITIZE_NUMBER_INT)));
  $website = trim(filter_input(INPUT_POST, "website", FILTER_SANITIZE_URL));
  $country = trim(filter_input(INPUT_POST, "country", FILTER_SANITIZE_STRING));
  $hosts = trim(filter_input(INPUT_POST, "hosts", FILTER_SANITIZE_STRING));
  $image = trim(filter_input(INPUT_POST, "image", FILTER_SANITIZE_URL));
  $collection_id = intval(trim(filter_input(INPUT_POST, "collection_id", FILTER_SANITIZE_NUMBER_INT)));
  $description = trim(filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING));

  // Podcast validation: checks that title is not empty
  if (empty($podcast_title)) {
    $error_message = "- Podcast title is empty.<br/>";
  }

  // Podcast validation: checks that genre is not empty
  if (empty($genre)) {
    $error_message .= "- Genre is empty.<br/>";
  }

  // Podcast validation: checks that rating is not empty
  if (empty($rating) || !is_int($rating)) {
    $error_message .= "- Rating must be entered and must be an integer value between 1 and 5. " . $rating  . "<br/>";
  }

  // Podcast validation: checks that collection_id is not empty
  if (empty($collection_id) || !is_int($collection_id)) {
    $error_message .= "- iTunes Id must be entered and must be a number greater than 9-digits.";
  }

  // Submits podcast variables to the server. If successful, user is redirected to details page
    if (empty($error_message)) {
      $id = nextPodcastId();
      $new_podcast = [
        'id' => $id,
        'collection_id' => $collection_id,
        'title' => $podcast_title,
        'genre' => $genre,
        'producer' => $producer,
        'rating' => $rating,
        'description' => $description,
        'website' => $website,
        'image' => $image,
        'country' => $country,
        'host' => $hosts,
      ];
      $result = postPodcast( $new_podcast );
      if (!empty($result) && $result == 1) {
        // Redirect to another page
          header('location: details.php?id=' . $id);
      }
    }
}
// ******************************** END PODCAST *********************************************//


//********************************* ADD R.EVIEW **********************************************//
// Sets review variables based on submitted form data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"]) && $add_new == "review") {
  $id = nextReviewId();
  $related_podcast_title = trim(filter_input(INPUT_POST, "related_podcast_title", FILTER_SANITIZE_STRING));
  $headline = trim(filter_input(INPUT_POST, "headline", FILTER_SANITIZE_STRING));
  $tagline = trim(filter_input(INPUT_POST, "tagline", FILTER_SANITIZE_STRING));
  $body = trim(filter_input(INPUT_POST, "body", FILTER_SANITIZE_STRING));
  $image = trim(filter_input(INPUT_POST, "review_image", FILTER_SANITIZE_URL));

  // Validation on input fields
  if (empty($related_podcast_title) || empty($headline) || empty($tagline) || empty($body)) {
    $error_message = "- Please complete all mandatory fields; title, headline, tagline and body.<br/>";
  }

}
// ******************************** END R.EVIEW *********************************************//



include 'inc/header.php';
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
  </head>

  <body>

      <?php
      if (isset($add_new) && $add_new == 'podcast') { ?>
        <!-- START FORM -->
        <div class="details-panel">
          <h2>Add Podcast</h2>
          <!-- Display message -->
          <?php
          if (isset($result) && $result == 1 ) {
            echo '<p class="alert alert-success">Your podcast "' . $podcast_title . '" was added successfully.</p>';
            $_POST = [];
            var_dump($_POST);
          } else if ( isset($result) && $result !== 0 ) {
              echo '<p class="alert alert-danger">' . $result  . ' - There was an error adding your podcast. Please try again</p>';
              $_POST = [];
          }?>

          <?php
          if (!empty($error_message)) {
            echo "<p class='alert alert-danger'>" . $error_message . "</p>";
          }?>
          <form class="login-form form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?new=podcast'; ?>" method="post" id="contact-form">

            <div class="form-group">
              <label for="title" class="col-sm-2 control-label">Title</label>
              <div class="col-sm-10">
                <?php
                if (isset($results_array)) { ?>
                  <select class="form-control" name="podcast_title" id="podcast_title">
                    <option>Please select...</option>
                    <?php foreach ($results_array as $item) {
                      echo '<option value="' . $item["podcast_title"] . '"'; // Changed from $item["collection_id"]
                      echo 'data-id="' . $item["collection_id"]  . '"';
                      if (isset($results_array) && $item["podcast_title"] == $results_array ) {
                        echo " selected";
                      };
                      echo '">' . truncate_chars($item["podcast_title"] , 40) . '</option>';
                    }; ?>
                  </select>
           <?php  } else {
                      echo '<input type="text" class="form-control" name="podcast_title" id="podcast_title"';
                        if (!empty($_POST['podcast_title'])) {
                          echo 'value="' . $_POST['podcast_title'] . '">';
                        } else {
                            echo 'placeholder="Title">';
                        }
                      }
                    ?>
              </div>
            </div>

            <div class="form-group" style="display: none;"> <!-- hidden field -->
              <label for="collection_id" class="col-sm-2 control-label">iTunes id</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="collection_id" id="collection_id">
              </div>
            </div>

            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-sml btn-default" name="search" id="contact-form" <?php if (isset($results_array)){ echo "disabled";}  ?>>Search</button>
              </div>
            </div>

            <div id="section-two"> <!-- start section-two -->

              <div class="form-group">
                <label for="genre" class="col-sm-2 control-label">Genre</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control add-form" name="genre" id="genre" <?php
                    if (!empty($_POST['genre'])) {
                      echo 'value="' . $_POST['genre'] . '"';
                    } else {
                       echo 'placeholder="Genre"';
                    }
                ?> >
                </div>
              </div>

              <div class="form-group">
                <label for="producer" class="col-sm-2 control-label">Producer</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control add-form" name="producer" id="producer" <?php
                    if (!empty($_POST['producer'])) {
                      echo 'value="' . $_POST['producer'] . '"';
                    } else {
                        echo 'placeholder="Producer"';
                    }
                ?> >
                </div>
              </div>

              <div class="form-group">
                <label for="rating" class="col-sm-2 control-label">Rating</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control add-form" name="rating" id="rating" <?php
                    if (!empty($_POST['rating'])) {
                      echo 'value="' . $_POST['rating'] . '"';
                    } else {
                        echo 'placeholder="Rating"';
                    }
                ?> >
                </div>
              </div>

              <div class="form-group">
                <label for="website" class="col-sm-2 control-label">Website</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control add-form" name="website" id="website" <?php
                    if (!empty($_POST['website'])) {
                      echo 'value="' . $_POST['website'] . '"';
                    } else {
                        echo 'placeholder="Website"';
                    }
                ?> >
                </div>
              </div>

              <div class="form-group">
                <label for="country" class="col-sm-2 control-label">Country</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control add-form" name="country" id="country" <?php
                    if (!empty($_POST['country'])) {
                      echo 'value="' . $_POST['country'] . '"';
                    } else {
                        echo 'placeholder="Country"';
                    }
                ?> >
                </div>
              </div>

              <div class="form-group">
                <label for="hosts" class="col-sm-2 control-label">Hosts</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control add-form" name="hosts" id="hosts" <?php
                    if (!empty($_POST['hosts'])) {
                      echo 'value="' . $_POST['hosts'] . '"';
                    } else {
                        echo 'placeholder="Hosts"';
                    }
                ?> >
                </div>
              </div>

              <div class="form-group">
                <label for="image" class="col-sm-2 control-label">Image</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control add-form" name="image" id="image" <?php
                    if (!empty($_POST['image'])) {
                      echo 'value="' . $_POST['image'] . '"';
                    } else {
                        echo 'placeholder="Image"';
                    }
                ?> >
                </div>
              </div>

              <div class="form-group">
                <label for="description" class="col-sm-2 control-label">Description</label>
                <div class="col-sm-10">
                  <textarea rows="4" class="form-control add-form" name="description" id="description" <?php
                    if (!empty($_POST['description'])) {
                      echo 'value="' . $_POST['description'] . '"';
                    } else {
                        echo 'placeholder="Description"';
                    }
                ?> ></textarea>
                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <button type="submit" class="btn btn-explore add-form" name="reset" id="contact-form">Reset</button>
                  <button type="submit" class="btn btn-explore add-form" name="submit" id="contact-form">Submit</button>
                </div>
              </div>

            </div> <!--end section two -->
          </form>
        </div>
        <!-- END PODCAST FORM -->
<?php  } elseif (isset($add_new) && $add_new == 'review') { ?>
          <!-- START FORM -->
          <!-- Form fields: Review ID(int), podcast_id(int), date(date eg. Saturday, 11 June 2016), author(string), image(url), header(string), tagline(string), body(string) -->
          <div class="details-panel">
            <h2>Add Review</h2>
            <!-- Display message -->
            <?php
            if (isset($error_message) && $add_new == "review") {
              echo '<p class="alert alert-danger">' . $error_message  . '</p>';
            }?>

            <form class="login-form form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?new=review'; ?>" method="post" id="contact-form">

                <div class="form-group">
                  <label for="related_podcast_title" class="col-sm-2 control-label">Podcast Title</label>
                  <div class="col-sm-10">
                    <select type="text" class="form-control" name="related_podcast_title" id="related_podcast_title" value="" placeholder="Podcast title">
                      <option>Select podcast title...</option>
                      <?php
                      $podcast_titles = getPodcasts(null, 0);
                      foreach ($podcast_titles as $title) {
                        echo "<option>" . $title["title"] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="headline" class="col-sm-2 control-label">Headline</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="headline" id="headline" value="" placeholder="Headline">
                  </div>
                </div>

                <div class="form-group">
                  <label for="tagline" class="col-sm-2 control-label">Tagline</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="tagline" id="tagline" placeholder="Tagline">
                  </div>
                </div>

                <div class="form-group">
                  <label for="body" class="col-sm-2 control-label">Body</label>
                  <div class="col-sm-10">
                    <textarea rows="4" class="form-control" name="body" id="body"></textarea>
                  </div>
                </div>

                <!-- Image -->
                <div class="form-group">
                  <label for="review_image" class="col-sm-2 control-label">Image</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="review_image" id="review_image">
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-sml btn-default" name="submit" id="contact-form">Submit</button>
                  </div>
                </div>

              </form> <!-- END FORM -->
            </div> <!-- END DETAILS PANEL -->
    <?php  }
          ?>
  </body>
</html>

<?php include 'inc/footer.php'; ?>
