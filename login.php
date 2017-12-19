<?php
// session_start();
session_unset();

if (isset($_GET['logout'])) {
  $logout_message = "You have logged out. See you again soon!";
}

include 'inc/functions.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
  $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
  $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
  $result = userCredentials( $username, $password );

  if ($result !== 'success' ) {
    $error_message = $result;
  } else {
      $_SESSION['logged_in'] = true;
      header('location: index.php');
      exit;
  }
}

$title = 'Login';
require 'inc/header.php';
?>

<form class="login-form form-horizontal details-panel" action="login.php" method="post">
  <h2 class="login-header">Login to Gocast</h2>
  <?php
    if (!empty($error_message)) {
      echo '<div class="alert alert-danger">' . $error_message . '</div>';
    } else if (!empty($logout_message)){
        echo '<div class="alert alert-info">' . $logout_message . '</div>';
    }
  ?>
  <div class="form-group">
    <label for="username" class="col-sm-2 control-label">Username</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="username" id="username" placeholder="Username">
    </div>
  </div>
  <div class="form-group">
    <label for="password" class="col-sm-2 control-label">Password</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" name="password" id="password" placeholder="Password">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <div class="checkbox">
        <label>
          <input type="checkbox"> Remember me
        </label>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-default btn-explore">Log in</button>
    </div>
  </div>
</form>

<?php
include 'inc/footer.php';
 ?>
