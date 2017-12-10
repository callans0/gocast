<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") { //open
  $name = trim(filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING));
  $email = trim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL));
  $details = trim(filter_input(INPUT_POST, "details", FILTER_SANITIZE_SPECIAL_CHARS));

  // Ensures all madatory fields have been completed
  if ( $name == "" || $email == "" || $details == "" ) {
    $error_message = "Please fill out the required fields: Name, Email, Category and Title";
    echo $error_message;
  }

  // Checks if the address field contains an input and prevents progress if yes
  if ( !isset($error_message) && $_POST["address"] != "" ) {
    $error_message = "Bad form request";
    exit;
  }

  require "inc/phpmailer/class.phpmailer.php";

  $mail = new PHPMailer;

  if (!$mail->ValidateAddress($email)) {
    $error_message = "Invalid email address";
    exit;
  }

  if (!isset($error_message)) {
    $email_body = "";
    $email_body .= "Name " . $name . "\n";
    $email_body .= "Email " . $email . "\n";
    $email_body .= "Details " . $details . "\n";

    $mail->setFrom($email, $name);
    $mail->addAddress('clareallanson@localhost', 'Clare');     // Add a recipient

    $mail->isHTML(false);                                  // Set email format to HTML

    $mail->Subject = 'Personal Media Library Suggestion from ' . $name;
    $mail->Body    = $email_body;

    if($mail->send()) {
            header("location:contact.php?status=thanks");
            exit;
    }
    $error_message = 'Message could not be sent.';
    $error_message .= 'Mailer Error: ' . $mail->ErrorInfo;
  }
} // close

$title = 'Contact';
require 'inc/header.php';

?>
<div class="details-panel">
<h2>Get in touch!</h2>

<?php
   if (isset($_GET["status"]) && $_GET["status"] == "thanks") {
     echo '<div class="alert alert-success">';
     echo "<p>Thanks for the email. We&rsquo;ll reply to you shortly.</p>";
     echo '</div>';
   } else {
      if ( isset($error_message) ) {
         echo "<p class='alert alert-danger'>" . $error_message . "</p>";
       } ?>
          <p>Got a suggestion for us or feedback on GoCast? Send us an email using the form below.</p>
          <form method="post" action="contact.php">
            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="<?php if (!empty($name)) { echo $name; } else { echo "Enter name"; } ?>">
            </div>
            <div class="form-group">
              <label for="email">Email address</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="<?php if (!empty($email)) { echo $email; } else { echo "Enter email"; } ?>">
            </div>
            <div class="form-group">
              <label for="details">Details</label>
              <textarea class="form-control" rows="4" id="details" name="details" placeholder="<?php if (!empty($details)) { echo $details; } else { echo ""; } ?>"></textarea>
            </div>
            <div style="display:none" class="form-group">
              <label for="address">Address</label>
              <input type="text" class="form-control" id="address" name="address" placeholder="please leave this field blank">
            </div>
            <button type="submit" class="btn btn-primary btn-explore">Send</button>
          </form> <?php
      }
?>
</div>


<?php
require 'inc/footer.php';
