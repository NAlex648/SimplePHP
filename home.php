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
// Can't go to home.php if not logged in
if (!isset($_SESSION['username'])) {
    // Goes back to index for log in
    header("Location: index.php");
    exit();
}
$username = $_SESSION['username'];
// CSRF token validation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && validateCSRFToken($_POST['csrf_token'])) {
    // Check artwork submission form
    if (isset($_FILES['artwork']) && $_FILES['artwork']['error'] === UPLOAD_ERR_OK) {
        // Check file type
        $allowedExtensions = ['png', 'jpeg', 'jpg'];
        $fileExtension = strtolower(pathinfo($_FILES['artwork']['name'], PATHINFO_EXTENSION));
        if (in_array($fileExtension, $allowedExtensions)) {
            // Upload
            $uploadDirectory = 'uploads/';
            $newFileName = $username . '_' . date('Y_m_d_H_i_s') . '.' . $fileExtension;
            $uploadPath = $uploadDirectory . $newFileName;
            if (move_uploaded_file($_FILES['artwork']['tmp_name'], $uploadPath)) {
                echo 'Artwork successfully uploaded!';
            } else {
                echo 'Error uploading artwork.';
            }
        } else {
            echo 'Invalid file type. Please upload a .png, .jpeg, or .jpg file.';
        }
    }
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
    <h1><?php echo "Hello $username! Welcome to the home page"?></h1>
    <form action="home.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <label for="artwork">Upload Artwork:</label>
        <input type="file" name="artwork" accept=".png, .jpeg, .jpg" required>
        <input type="submit" value="Submit Artwork">
    </form>
    <button onclick="toggleFileListing()">Show Uploaded Files</button>
    <div id="fileListing" style="display: none;">
        <h2>Uploaded Files</h2>
        <?php
        $uploadedFiles = glob("uploads/$username*.{png,jpeg,jpg}", GLOB_BRACE);
        if ($uploadedFiles) {
            echo '<ul>';
            foreach ($uploadedFiles as $file) {
                echo "<li>$file</li>";
                echo '<img src="' . $file . '" alt="Uploaded Artwork" style="max-width: 300px; max-height: 300px;">';
            }
            echo '</ul>';
        } else {
            echo '<p>No files uploaded yet.</p>';
        }
        ?>
    </div>
    <?php
    // For logging out
    echo '<a href="logout.php">Logout</a>';
    ?>
    <script>
        function toggleFileListing() {
            var fileListing = document.getElementById('fileListing');
            fileListing.style.display = fileListing.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
