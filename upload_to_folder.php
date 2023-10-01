<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Get the username from the session
$username = $_SESSION['username'];

// Define the upload directory as the user's folder
$uploadDirectory = __DIR__ . '/' . $username;

// Create the user's folder if it doesn't exist
if (!file_exists($uploadDirectory)) {
    mkdir($uploadDirectory);
}

// Get the selected folder from the form
if (isset($_POST['selectedFolder'])) {
    $selectedFolder = $_POST['selectedFolder'];

    // Validate that the selected folder exists within the user's directory
    $targetDirectory = $uploadDirectory . '/' . $selectedFolder;

    if (!file_exists($targetDirectory)) {
        echo "Invalid folder selected.";
        exit;
    }
} else {
    echo "No folder selected.";
    exit;
}

$allowedMimeTypes = array("image/jpeg", "image/png", "application/pdf");

// Handle file uploads
if (isset($_POST['upload'])) {
    $uploadFile = $_FILES["fileToUpload"];

    if ($uploadFile["size"] > 0 && $uploadFile["error"] === 0) {
        // Generate a unique filename
        $timestamp = date("Ymd_His");
        $uuid = uniqid();
        $extension = pathinfo($uploadFile["name"], PATHINFO_EXTENSION);
        $newFileName = "{$timestamp}_{$uuid}.{$extension}";
        $targetFile = $targetDirectory . '/' . $newFileName;

        // Check if the MIME type is allowed
        if (in_array($uploadFile["type"], $allowedMimeTypes)) {
            if (move_uploaded_file($uploadFile["tmp_name"], $targetFile)) {
                echo "<p class='success-message'>File $newFileName has been uploaded to folder '$selectedFolder'.</p>";
            } else {
                echo "<p class='error-message'>Sorry, there was an error uploading your file.</p>";
            }
        } else {
            echo "<p class='error-message'>Invalid file type. Allowed types: " . implode(', ', $allowedMimeTypes) . "</p>";
        }
    } else {
        echo "<p class='error-message'>Error uploading file.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>upload to folder</title>
</head>
<body>
<p>go <a href="index.php">back</a></p>
</body>
</html>
