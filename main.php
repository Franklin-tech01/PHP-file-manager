<?php

session_start();

// Get the file name from the query parameter
if (isset($_GET['file'])) {
    $fileName = $_GET['file'];

    $username = $_SESSION['username'];

    // Define the directory where the files are stored (adjust as needed)
    $uploadDirectory = __DIR__ . '/' . $username;

    // Construct the full path to the file (decode the URL-encoded path)
    $filePath = $uploadDirectory . '/' . urldecode($fileName);

     // Set appropriate permissions to the file
     chmod($filePath, 0644); // 0644 represents read/write for owner, read for group and others

    // Check if the file exists
    if (file_exists($filePath)) {
        // Determine the file's MIME type
        $fileMimeType = mime_content_type($filePath);

        // Set the appropriate content type header based on the MIME type
        header("Content-type: $fileMimeType");

        // Output the file content
        // readfile($filePath);
        readfile($filePath);

        // Add a "Back to File Manager" button
        echo "<div class='back-button'><a href='index.php'>Back to File Manager</a></div>";
    } else {
        echo "File not found. Check file name and directory path.";
    }
} else {
    echo "File not specified.";
}

?>