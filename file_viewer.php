<?php
session_start();

// Check if the 'username' session variable is set
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Sanitize and validate the username
    if (preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
        // Username is valid, proceed with constructing the directory path
        $uploadDirectory = __DIR__ . '/' . $username;

        // Get the file name from the query parameter
        if (isset($_GET['file'])) {
            $fileName = $_GET['file'];

            // Construct the full path to the file (decode the URL-encoded path)
            $filePath = $uploadDirectory . '/' . urldecode($fileName);

            // Check if the file exists
            if (file_exists($filePath)) {
                // Check if the file is readable
                if (is_readable($filePath)) {
                    // Determine the file's MIME type
                    $fileMimeType = mime_content_type($filePath);

                    // Set the appropriate content type header based on the MIME type
                    header("Content-type: $fileMimeType");

                    // Output the file content
                    readfile($filePath);

                    // Add a "Back to File Manager" button
                    echo "<div class='back-button'><a href='index.php'>Back to File Manager</a></div>";
                    exit; // Exit to prevent further output
                } else {
                    echo "File is not readable. Please check file permissions.";
                }
            } else {
                echo "File not found.";
            }
        } else {
            echo "File not specified.";
        }
    } else {
        // Invalid username, handle the error appropriately
        echo "Invalid username.";
    }
} else {
    echo "Username not set in the session. Please log in first.";
}
?>