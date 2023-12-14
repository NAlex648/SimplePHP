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
// Checking if the user is already logged in to prevent pararel session
if (isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}
$csrfInput = '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);
    // CSRF token validation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && validateCSRFToken($_POST['csrf_token'])) {
        $sql = "SELECT username, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($db_username, $db_password);
            $stmt->fetch();
            if (hash('sha256', $password) === $db_password) {
                $_SESSION['username'] = $db_username;
                // Regenerate session ID for Cookie Session Hijack Prevention
                session_regenerate_id(true);
                header("Location: home.php");
                exit();
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
        $stmt->close();
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
    <link rel="stylesheet" href="login.css">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form action="login.php" method="post">
            <?php if (isset($csrfInput)) echo $csrfInput; ?>
            <label for="username">Username:</label>
            <input type="text" name="username" required><br>
            <label for="password">Password:</label>
            <input type="password" name="password" required><br>
            <input type="submit" value="Login">
        </form>
        <div class="message"></div>
    </div>
</body>
</html>
