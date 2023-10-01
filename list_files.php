<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$userDir = __DIR__ . '/' . $username;

if (!file_exists($userDir)) {
    mkdir($userDir);
}

$folder = isset($_GET['folder']) ? $_GET['folder'] : __DIR__;
$files = scandir($folder);

foreach ($files as $file) {
    if ($file != "." && $file != "..") {
        echo "<li>";
        echo "<a href='?folder=$folder/$file'>$file</a>";

        // Add a "View File" button for supported file types
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
        if (in_array($fileExtension, array("jpg", "jpeg", "png", "gif", "pdf"))) {
            echo " <a href='view.php?file=$folder/$file' target='_blank'>View File</a>";
        }

        echo "</li>";
    }
}
?>
