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

if (isset($_POST["folderName"])) {
    $folderName = $_POST["folderName"];
    $folderPath = $userDir . '/' . $folderName;
    
    if (!file_exists($folderPath)) {
        mkdir($folderPath);
        echo "Folder '$folderName' created successfully.";
        echo "<p>go <a href='index.php'> back.</a></p>";
    } else {
        echo "Folder '$folderName' already exists.";
        echo "<p>go <a href='index.php'> back.</a></p>";
    }
}
?>
