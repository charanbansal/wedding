<?php
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['subject'];
$mobile = $_POST['mobile'];
$formcontent=" From: $name \n message: $message \n mobile: $mobile \n";
$recipient = "linktocharan@gmail.com";
$subject = "wedding CONTACT";
$mailheader = "From: $email \r\n";
mail($recipient, $subject, $formcontent, $mailheader) or die("Error!");
echo "Thank You!" . " -" . "<a href='index.html' style='text-decoration:none;color:#ff0099;'> Return Home</a>";
?>