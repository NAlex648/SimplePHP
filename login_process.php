<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dummy username and password for testing
    $dummy_username = "user";
    $dummy_password = "password";

    // Input
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Dummy check for valid credentials
    if ($username == $dummy_username && $password == $dummy_password) {
        $_SESSION['user_id'] = 1; // Dummy user ID
        $_SESSION['username'] = $username;

        // Redirect to index
        header("Location: index.php");
        exit();
    } else {
        echo "Invalid username and/or password.";
    }
}
?>
