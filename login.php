<?php
session_start();
include_once("db_connect.php"); // Include the database connection file

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare an SQL statement with placeholders
    $sql = "SELECT id, username, password FROM users WHERE username=?";

    // Create a prepared statement
    $stmt = $conn->prepare($sql);

    // Bind the parameters and execute the statement
    $stmt->bind_param("s", $username);

    $stmt->execute();

    // Bind the result
    $stmt->bind_result($id, $username, $hashed_password);

    if ($stmt->fetch()) {
        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit;
        } else {
            echo "Login failed. Incorrect password.";
        }
    } else {
        echo "Login failed. User not found.";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Login</h1>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <div class="loginDiv">
            <input type="submit" name="login" value="Login" style="background-color: green;"
        </div>
        <p> don't have an account <a href="register.php">Register</a></p>
    </form>
</body>
</html>
