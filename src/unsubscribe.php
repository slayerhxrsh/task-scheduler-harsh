<?php
require_once 'functions.php';

if (isset($_GET['email'])) {
    $email = $_GET['email'];
    unsubscribeEmail($email);
    echo "<p style='color:red;'>You have been unsubscribed successfully 😢</p>";
} else {
    echo "<p style='color:gray;'>No email provided.</p>";
}
?>
