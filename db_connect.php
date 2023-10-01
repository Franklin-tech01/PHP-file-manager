<?php
// Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = "franklinmike01";
    $database = "user_database";

    // Create a database connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

   
?>