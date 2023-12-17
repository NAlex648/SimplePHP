<?php
session_start();
include_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body style="background-image: url(assets/background.jpg);">
    <h1>Please Login or Register</h1>
    <?php
    if (isset($_SESSION['user_id'])) {
        header("Location: home.php");
        exit();
    } else {
        echo '<p>You are not logged in. <a href="login.php">Login</a> or <a href="register.php">Register</a></p>';
    }
    ?>
</body>
</html>
