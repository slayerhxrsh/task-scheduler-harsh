<?php
require_once 'functions.php';

if (isset($_GET['email']) && isset($_GET['code'])) {
    $email = $_GET['email'];
    $code = $_GET['code'];

    $message = verifySubscription($email, $code)
        ? "<p style='color:green;'>Email verified successfully ✅</p>"
        : "<p style='color:red;'>Invalid or expired verification code ❌</p>";
} else {
    $message = "<p style='color:red;'>Invalid request 🚫</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Subscription</title>
</head>
<body>
    <?= $message ?>
</body>
</html>
