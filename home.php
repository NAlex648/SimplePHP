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
<body>
    <h1>Hello user! Welcome to the Home Page</h1>
    <?php
    // For logging out
    echo '<a href="logout.php">Logout</a>';
    ?>
</body>
</html>