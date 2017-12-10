<?php
include 'inc/functions.php';
$title = 'review';
$items_per_page = 4;
$search = '';
$podcast_id = '';


if (isset($_GET["pg"])) {
	$current_page = filter_input(INPUT_GET, "pg", FILTER_SANITIZE_NUMBER_INT);
}
if (empty($_GET["pg"])) {
	$current_page = 1;
}

if ($current_page <= 1) {
	$offset = 0;
} else {
		$offset = ($current_page - 1) * $items_per_page;
}


if (isset($_GET["id"])) {
	$podcast_id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_STRING);
	$reviews_array = getReviews( $search, $podcast_id, $offset, $items_per_page );
	if (empty($reviews_array)) {
    $message = "Sorry, your search for  <strong>\"$search\"</strong> brought back 0 results. Try another search.";
  }
} else if (isset($_GET["search"])) {
	$search = filter_input(INPUT_GET, "search", FILTER_SANITIZE_STRING);
	$reviews_array = getReviews( $search, $podcast_id, $offset, $items_per_page );
	if (empty($reviews_array)) {
    $message = "Sorry, your search for  <strong>\"$search\"</strong> brought back 0 results. Try another search.";
  }
} else {
    $reviews_array = getReviews( null, null, $offset, $items_per_page );
}

require 'inc/header.php';

//-------------- PAGINATION---------------------//
$total_items = countReviews( $search, $podcast_id );
$total_pages = ceil($total_items / $items_per_page);
//Redirect too-large numbers to the last page
if ($current_page > $total_pages && empty($message) ) {
	header("location:reviews.php?pg=$total_pages");
}
// Redirect too-small numbers to the first page
if ($current_page < 1) {
	header("location:reviews.php?pg=1");
}
//-----------------------------------------------//


if (!empty($message)) {
  echo '<div class="alert alert-info">' . $message . '</div>';
}

  foreach ($reviews_array as $review) {
    echo '<div class="container thumbnail review-flex-grid">';
    echo '<span class="review-col review-col-1">';
    echo '<h2><a href="details.php?review_id=' . $review['review_id'] . '">' . $review['header'] . '</a></h2>';
    echo '<h4 class="tagline">' . $review['tagline'] . '</h4>';
    echo '<h5>' . $review['date'] .'</h5>';
    echo '<p>';
    echo truncate_chars($review['body'], 300, '...<a href="#">more</a>');
    echo '</p>';
    echo '</span>';
    echo '<span class="review-col review-col-2">';
    echo '<img class="review-img" src="' . $review['img'] . '" />';
    echo '</span>';
    echo '</div>';
  }

	echo '<div class="text-center">';
	//Pagination
	//Use this to test variables
	// echo 'Total items: ' . $total_items . '<br />';
	// echo 'Items/page: ' . $items_per_page . '<br />';
	// echo 'Total pages: ' . $total_pages . '<br />';
	// echo 'Current page: ' . $current_page . '<br />';
	// echo 'Offset: ' . $offset . '<br />';
	echo '<ul class="pagination">';
		$i = 0;
		while ($i < $total_pages) {
			$i++;
			if ( $i == $current_page ) {
				echo '<li class="active"><a href="reviews.php?pg=' . $current_page  . '">' . $current_page . '</a></li>';
			} else {
					echo '<li><a href="reviews.php?search=' . $search . '&pg=' . $i . '">' . $i . '</a></li>';
			}
		}
	echo '</ul>';
	echo '</div>';

require 'inc/footer.php';
?>
