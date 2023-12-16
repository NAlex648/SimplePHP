<?php
session_start();
include_once 'config.php';
// XSS Prevention
function sanitizeInput($input)
{
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}
// CSRF Prevention
function generateCSRFToken()
{
    return bin2hex(random_bytes(32));
}
function validateCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
// Check if CSRF token is already set, if not, generate and set it
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generateCSRFToken();
}
// Checking if the user is already logged in to prevent parallel sessions
if (isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}
$csrfInput = '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);
    // CSRF token validation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && validateCSRFToken($_POST['csrf_token'])) {
        // Retrieve user data including login attempts and IP address
        $sql = "SELECT username, password, ip_address, login_attempts, last_login_attempt FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($db_username, $db_password, $db_ip_address, $db_login_attempts, $db_last_login_attempt);
            $stmt->fetch();
            // Check if the user is currently locked out due to too many failed attempts
            $lockout_time = 5 * 60; // 5 minutes lockout time
            $now = time();
            if (!isset($db_login_attempts)) {
                $db_login_attempts = 0;
            }
            if ($db_login_attempts >= 5 && $now - strtotime($db_last_login_attempt) < $lockout_time) {
                $error_message = "Too many failed login attempts. Please try again later.";
            } else {
                if (hash('sha256', $password) === $db_password) {
                    // Reset login attempts on successful login
                    $login_attempts = 0;
                    $last_login_attempt = date('Y-m-d H:i:s');
                    $_SESSION['username'] = $db_username;
                    // Regenerate Session ID for Cookie Session Hijack Prevention
                    session_regenerate_id(true);
                    // Update user data in the database
                    $update_sql = "UPDATE users SET login_attempts = ?, last_login_attempt = ? WHERE username = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_login_attempts = 0;
                    $update_last_login_attempt = date('Y-m-d H:i:s');
                    $update_stmt->bind_param("iss", $update_login_attempts, $update_last_login_attempt, $username);
                    $update_stmt->execute();
                    $update_stmt->close();
                    header("Location: home.php");
                    exit();
                } else {
                    // Increment login attempts on failed login
                    $login_attempts = $db_login_attempts + 1;
                    $last_login_attempt = date('Y-m-d H:i:s');
                    // Update user data in the database
                    $update_sql = "UPDATE users SET login_attempts = ?, last_login_attempt = ? WHERE username = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("iss", $login_attempts, $last_login_attempt, $username);
                    $update_stmt->execute();
                    $update_stmt->close();
                    // Display invalid credentials message
                    $error_message = "Invalid username or password. Attempts left: " . (5 - $login_attempts);
                }
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
            <?php if (!empty($error_message)) echo '<p>' . $error_message . '</p>'; ?>
        </form>
    </div>
</body>
</html>
