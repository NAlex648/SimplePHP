<?php
session_start();
include_once 'config.php';
// XSS Prevention
function sanitizeInput($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}
// CSRF Prevention
function generateCSRFToken() {
    return bin2hex(random_bytes(32));
}
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
// Check if CSRF token is already set, if not, generate and set it
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generateCSRFToken();
}
$csrfInput = '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);
    // CSRF token validation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && validateCSRFToken($_POST['csrf_token'])) {
        // Password hashing
        $hashed_password = hash('sha256', $password);
        // Inserts user information into the database
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
        if ($conn->query($sql) === TRUE) {
            // Registration successful
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['username'] = $username;
            session_unset();
            session_destroy();
            session_start();
            session_regenerate_id(true);
            // Redirect to login
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $error_message = "CSRF Token validation failed.";
    }
    // Generate a new CSRF token for the next form submission
    $_SESSION['csrf_token'] = generateCSRFToken();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <title>Register</title>
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <form action="register.php" method="post">
            <?php if (isset($csrfInput)) echo $csrfInput; ?>
            <label for="username">Username:</label>
            <input type="text" name="username" required><br>
            <label for="password">Password:</label>
            <input type="password" name="password" required><br>
            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
