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
// Can't go to home.php if not logged in
if (!isset($_SESSION['username'])) {
    //goes back to index for log in
    header("Location: index.php");
    exit();
}
$username = $_SESSION['username'];
// CSRF token validation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && validateCSRFToken($_POST['csrf_token'])) {
    // Your logic for processing forms or other actions
} else {
    $error_message = "CSRF Token validation failed.";
}
// Generate a new CSRF token for the next form submission
$_SESSION['csrf_token'] = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h1><?php echo "Welcome $username"?></h1>
    <?php
    // For logging out
    echo '<a href="logout.php">Logout</a>';
    ?>
</body>
</html>
