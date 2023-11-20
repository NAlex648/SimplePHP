<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h1></h1>
    <?php
    session_start();

    // Checks if the user is logged in
    if (isset($_SESSION['user_id'])) {
        echo '<p>Hello, ' . $_SESSION['username'] . '! You are logged in.</p>';
        echo '<a href="logout.php">Logout</a>';
    } else {
        echo '<p>You are not logged in.</p>';
        echo '<p><a href="login.php">Login</a></p>';
    }
    ?>
</body>
</html>