<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Get the username from the session
$username = $_SESSION['username'];
$fileName = isset($_GET['file']) ? $_GET['file'] : '';
$uploadDirectory = __DIR__ . '/' . $username;

// Define the upload directory as the user's folder
$uploadDirectory = __DIR__ . '/' . $username;

// Create the user's folder if it doesn't exist
if (!file_exists($uploadDirectory)) {
    mkdir($uploadDirectory);
}

$currentDirectory = isset($_GET['folder']) ? $_GET['folder'] : $uploadDirectory;
$allowedMimeTypes = array("image/jpeg", "image/png", "application/pdf");

// Handle file deletions
if (isset($_POST['delete'])) {
    $fileToDelete = $_POST['fileToDelete'];
    $filePath = $currentDirectory . '/' . $fileToDelete;

    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            echo "File $fileToDelete has been deleted.";
        } else {
            echo "Error deleting file.";
        }
    } else {
        echo "File not found.";
    }
}

// Function to list files in a directory and its subfolders recursively
function listFilesRecursive($directory) {
    $results = [];
    $files = scandir($directory);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $filePath = $directory . '/' . $file;
        if (is_dir($filePath)) {
            $results = array_merge($results, listFilesRecursive($filePath));
        } else {
            $results[] = $filePath;
        }
    }
    return $results;
}

// Handle search functionality here
if (isset($_POST['search'])) {
    $searchTerm = $_POST['searchTerm'];

    // List files in the current directory and its subfolders recursively
    $allFiles = listFilesRecursive($currentDirectory);

    // Filter uploaded files based on the search term
    $matchingFiles = array_filter($allFiles, function ($file) use ($searchTerm) {
        // Case-insensitive search for files that contain the search term in their name
        return stripos(basename($file), $searchTerm) !== false;
    });

    if (empty($matchingFiles)) {
        echo "No matching files found.";
    } else {
        echo "Matching files:<br>";
        echo "<ul>";
        foreach ($matchingFiles as $file) {
            // Display the relative path for user's convenience
            $relativePath = str_replace($currentDirectory . '/', '', $file);
        
            echo "<div>";
            echo "<form>";
            echo "<li>$relativePath";
            echo "<a class='action-button' href='file_viewer.php?file=$relativePath' target='_blank'>View</a>";
            echo "</li>";
            echo "</form>";
            echo "</div>";
        }
        echo "</ul>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Manager</title>
    <link rel="stylesheet" href="style.css">
    <style>
        
        body{
            background-color: white;
        }

        /* File list container */
        .file-list-container {
            background-color: tomato;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        /* File list header */
        .file-list-header {
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* File list item */
        .file-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ddd;
            padding: 10px;
            margin: 5px 0;
            background: #007bff;
        }

        /* File details */
        .file-details {
            flex-grow: 1;
        }

        /* File actions */
        .file-actions {
            text-align: right;
        }

        /* View and Delete buttons */
        .action-button {
            padding: 5px 10px;
            margin-left: 10px;
            background-color: black;
            border-radius:10px;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .action-button:hover {
            background-color: green;
        }

        /* Create folder form */
        .create-folder-form {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .folder-name-input {
            flex-grow: 1;
            padding: 5px;
            margin-right: 10px;
        }

        /* File upload form */
        .file-upload-form {
            display: flex;
            align-items: center;
        }

        .file-upload-input {
            margin-right: 10px;
        }

        .navbar{
            background: blue;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
           
        }

        .navbar a {
            color: white;
            font-size: 20px;
            text-decoration: none;
            margin: 0 10px;
        }

        .navbar a:hover {
            color: greenyellow;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <h2 style="color: white;">PHP Group Four</h2>
        </div>
        <div>
            <a href="index.php">File Manager</a>
            <a href="download.php">Download File</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <h1>File Manager</h1>
    
    
    
    <div class="file-list-container">
        <h2 class="file-list-header">Uploaded Files List:</h2>
        <ul class="file-list">
            <?php
            // List only uploaded files in the current directory
            $files = scandir($currentDirectory);
            $uploadedFiles = array_filter($files, function ($file) {
                return !in_array($file, array(".", ".."));
            });

            foreach ($uploadedFiles as $file) {
                // Get file size and upload timestamp
                $filePath = $currentDirectory . '/' . $file;
                $fileSize = filesize($filePath);
                $uploadTimestamp = date("Y-m-d H:i:s", filemtime($filePath));

                echo "<li class='file-list-item'>";
                echo "<div class='file-details'>";
                echo "<p>File Name: $file</p>";
                echo "<p>File Size: $fileSize bytes</p>";
                echo "<p>Upload Timestamp: $uploadTimestamp</p>";
                echo "</div>";
                echo "<div class='file-actions'>";
                echo "<form method='POST' action='index.php?folder=$currentDirectory'>";
                echo "<input type='hidden' name='fileToDelete' value='$file'>";
                echo "<button class='action-button' type='submit' name='delete'>Delete</button>";
                echo "</form>";
                echo "<a  class='action-button' href='file_viewer.php?file=" . urlencode($file) . "' target='_blank'   >View File</a>";
                echo "</div>";
                echo "</li>";
            }
            ?>
        </ul>
    </div>

    <div class="search-bar-container">
        <!-- Create folder form -->
        <p style="text-align: center;">Create a folder</p>
        <form action="create_folder.php?folder=<?php echo $currentDirectory; ?>" method="post" class="create-folder-form">
            <input class="folder-name-input" type="text" name="folderName" placeholder="Folder Name">
            <button class="action-button" type="submit" name="submit">Create Folder</button>
        </form>

        <!-- File upload form -->
        <p style="text-align: center;">Upload file</p>
        <form action="upload.php" method="post" enctype="multipart/form-data" class="file-upload-form">
            <input class="file-upload-input" type="file" name="fileToUpload" id="fileToUpload">
            <button class="action-button" type="submit" name="upload">Upload File</button>
        </form>

        <!-- File upload to folder form 2-->
        <p style="text-align: center;">Upload file to a folder</p>
        <form action="upload_to_folder.php" method="post" enctype="multipart/form-data" class="file-upload-form">
            <input class="file-upload-input" type="file" name="fileToUpload" id="fileToUpload"; style="padding-right: 100px">
            <select name="selectedFolder">
                <?php
                // List folders in the user's directory
                $folders = scandir($uploadDirectory);
                foreach ($folders as $folder) {
                    if ($folder != "." && $folder != ".." && is_dir($uploadDirectory . '/' . $folder)) {
                        echo "<option value='$folder'>$folder</option>";
                    }
                }
                ?>
            </select>
            <button class="action-button" type="submit" name="upload">Upload File</button>
        </form>

    </div> <br>
</body>
</html>