<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false ) {
  header('location: login.php');
} else {
  $page = 'Admin';
  include 'inc/header.php';
}

?>

<!DOCTYPE html>
<html>
  <body>
    <h2>What would you like to do?</h2>
  </body>
</html>

<?php
include 'inc/footer.php';
?>
