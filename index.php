<?php
include 'inc/functions.php';
$search = '';
$items_per_page = 8;

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

if (isset($_GET["search"])) {
	$search = filter_input(INPUT_GET, "search", FILTER_SANITIZE_STRING);
  $podcasts = getPodcasts( $search, $offset, $items_per_page );
  if (empty($podcasts)) {
    $message = "Sorry, your search for  <strong>\"$search\"</strong> brought back 0 results. Try another search.";
  }
} else {
    $podcasts = getPodcasts( ' ', $offset, $items_per_page );
}

//-------------- PAGINATION---------------------//

$total_items = countPodcasts($search);
$total_pages = ceil($total_items / $items_per_page);

//Redirect too-large numbers to the last page
// if ( $current_page > $total_pages) {
// 	header("location:index.php?search=" . $search . "?pg=" . $total_pages);
// }

// Redirect too-small numbers to the first page
// if ($current_page < 1) {
// 	header("location:index.php?pg=1");
// }

//-----------------------------------------------//


$title = 'explore';
require 'inc/header.php';

if (!empty($message)) {
  echo '<div class="alert alert-info">' . $message . '</div>';
}


echo '<div class="gallery">';

if (is_array($podcasts)) {
  foreach ($podcasts as $podcast) {
          echo '<div class="gallery-link thumbnail">';
          echo '<a href="details.php?id=' . $podcast["id"] . '">';
          echo '<div class="gallery-pic">';
          echo '<img src="' . $podcast['image'] . '" alt="' . $podcast['title'] . '" />';
          echo '</div>';

          echo '<div class="gallery-content">';
          echo '<h4><a href="details.php?id=' . $podcast["id"]  . '">' . $podcast['title'] . '</a></h4>';
          echo '<h5>' . $podcast['genre'] . '</h5>';
          $rating_count = $podcast['rating'];
          $count = 0;
          while ($count < $rating_count) {
            echo '<span class="glyphicon glyphicon-star" aria-hidden="true"></span> ';
            $count++;
          }
          echo '<a href="#"><p>Reviews</p></a>';
          echo '</div>';
          echo '</a>';
          echo '</div>';
      }
  }

echo '</div>';

echo '<div class="text-center">';
//Pagination
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
			echo '<li class="active"><a href="index.php?search=' . $search . '&pg=' . $current_page  . '">' . $current_page . '</a></li>';
		} else {
				echo '<li><a href="index.php?search=' . $search . '&pg=' . $i . '">' . $i . '</a></li>';
		}
	}
echo '</ul>';
echo '</div>';


require 'inc/footer.php';
