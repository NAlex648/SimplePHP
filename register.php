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
// Function: checking for registered username
function isUsernameExists($conn, $username) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->num_rows > 0;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);
    // CSRF token validation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && validateCSRFToken($_POST['csrf_token'])) {    
        // Check if username already exists
        if (isUsernameExists($conn, $username)) {
            echo "Error: Username already exists.";
            exit();
        }
        // Password hashing
        $hashed_password = hash('sha256', $password);
        // Use prepared statement to avoid SQL injection
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hashed_password);
        if ($stmt->execute()) {
            // Registration successful
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['username'] = $username;
            // Redirect to login
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
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
