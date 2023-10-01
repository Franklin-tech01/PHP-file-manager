<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Define the directory where files are stored
$username = $_SESSION['username'];
$downloadDir = __DIR__ . '/' . $username;

if (isset($_POST['downloadFile'])) {
    $fileOrFolder = $_POST['downloadFile'];

    // Ensure the requested file or folder is within the allowed directory (for security)
    $fullPath = $downloadDir . '/' . $fileOrFolder;

    if (file_exists($fullPath)) {
        // Check if it's a directory or a file
        if (is_dir($fullPath)) {
            // If it's a directory, create a zip archive and offer it for download
            $zipFileName = tempnam(sys_get_temp_dir(), 'download_');
            $zip = new ZipArchive();
            if ($zip->open($zipFileName, ZipArchive::CREATE)) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fullPath));
                foreach ($iterator as $key => $value) {
                    if (!$value->isDir()) {
                        $filePath = $value->getRealPath();
                        $relativePath = substr($filePath, strlen($downloadDir) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
                $zip->close();

                // Set headers for a binary file download
                header('Content-Description: File Transfer');
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . basename($fullPath) . '.zip"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($zipFileName));

                // Output the zip file
                readfile($zipFileName);
                unlink($zipFileName);

                exit;
            } else {
                echo "Error creating zip archive.";
            }
        } elseif (is_file($fullPath)) {
            // If it's a file, set headers for a binary file download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fullPath));

            // Output the file content
            readfile($fullPath);

            exit;
        }
    } else {
        echo "File or folder not found or not readable.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Download</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    .navbar{
        background: green;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        color: ;
    }

    .navbar a {
        color: white;
        text-decoration: none;
        margin: 0 10px;
    }

    .navbar a:hover {
        background-color: greenyellow;
    }
    
    .action-button {
        padding: 5px 10px;
        margin-left: 10px;
        background-color: yellowgreen;
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
</style>
<body>
    <div class="navbar">
        <div>
        <h2 style="color: #f9f9f9;">PHP Group Work</h2>
        </div>
        <div>
            <a href="index.php">File Manager</a>
            <a href="download.php">Download File/Folder</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <h1>File Download</h1>
    
    <h2 style="text-align: center;">Select a File or Folder to Download:</h2> <br>
    <form action="download.php" method="post">
        <select name="downloadFile">
            <?php
            // List files and folders in the download directory and its subfolders
            function listFilesAndFoldersRecursively($directory, $basePath = "") {
                $items = scandir($directory);
                foreach ($items as $item) {
                    if ($item != "." && $item != "..") {
                        $path = $directory . '/' . $item;
                        $relativePath = $basePath . '/' . $item;
                        if (is_dir($path)) {
                            echo "<option value='$relativePath'>$item (Folder)</option>";
                            listFilesAndFoldersRecursively($path, $relativePath);
                        } elseif (is_file($path)) {
                            echo "<option value='$relativePath'>$item (File)</option>";
                        }
                    }
                }
            }

            listFilesAndFoldersRecursively($downloadDir);
            ?>
        </select>
        
        <button class="action-button" type="submit" value="Download">Download</button>
    </form>
    <p>Go back to <a href="index.php">File Manager</a></p>
</body>
</html>
