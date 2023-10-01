<?php
session_start();

$username = $_SESSION['username'];
$uploadDirectory = __DIR__ . '/' . $username;

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

$currentDirectory = isset($_GET['folder']) ? $_GET['folder'] : $uploadDirectory;
$allowedMimeTypes = array("image/jpeg", "image/png", "application/pdf");

// Handle file uploads
if (isset($_POST['upload'])) {
    $uploadFile = $_FILES["fileToUpload"];
    $targetDir = $currentDirectory;

    if ($uploadFile["size"] > 0 && $uploadFile["error"] === 0) {
        // Generate a unique filename
        $timestamp = date("Ymd_His");
        $uuid = uniqid();
        $extension = pathinfo($uploadFile["name"], PATHINFO_EXTENSION);
        $newFileName = "{$timestamp}_{$uuid}.{$extension}";
        $targetFile = $targetDir . '/' . $newFileName;

        // Check if the MIME type is allowed
        if (in_array($uploadFile["type"], $allowedMimeTypes)) {
            if (move_uploaded_file($uploadFile["tmp_name"], $targetFile)) {
                echo "<p class='success-message'>File $newFileName has been uploaded.</p>";
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
    <title>File Upload Response</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            text-align: center;
            padding: 20px;
        }

        .success-message {
            color: #007bff;
        }

        .error-message {
            color: #dc3545;
        }

        a {
            color: #007bff;
        }
    </style>
</head>
<body>
    <p>Go <a href="index.php">back to the file manager</a></p>
</body>
</html>
