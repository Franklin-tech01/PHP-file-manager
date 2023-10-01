<?php
session_start();
include_once("db_connect.php"); // Include the database connection file

function generateSalt($length = 16) {
    // Generate a random salt of the specified length
    try {
        return bin2hex(random_bytes($length));
    } catch (Exception $e) {
        // Handle any exceptions that may occur during salt generation
        // You might want to log the error or handle it in a different way
        die("Error generating salt: " . $e->getMessage());
    }
}

function hashed($password) {
    // Generate a hashed password using the PASSWORD_DEFAULT algorithm
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if ($hashedPassword === false) {
        // Handle the case where password_hash fails (unlikely)
        die("Password hash failed.");
    }

    return $hashedPassword;
}

// Execute the SQL query to create the users table
// Drop the table if it exists
$dropTableQuery = "DROP TABLE IF EXISTS users";
if ($conn->query($dropTableQuery) === TRUE) {
    echo "Table 'users' dropped successfully<br>";
} else {
    echo "Error dropping table: " . $conn->error;
}

// Create the table
$createTableQuery = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    salt VARCHAR(255) NOT NULL
)";

if ($conn->query($createTableQuery) === TRUE) {
    echo "Table 'users' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}



function register($conn) {
    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check if the username is at least 4 characters long
        if (strlen($username) < 4) {
            echo "Username must be at least 4 characters long.";
        }
        // Check if the password is at least 6 characters long
        elseif (strlen($password) < 6) {
            echo "Password must be at least 6 characters long.";
        } else {
            
            $salt = generateSalt();
            
            // Hash the password with the generated salt
            $hashed_password = hashed($password, $salt);
            
            // Check if the username already exists in the database
            $checkUsernameQuery = "SELECT * FROM users WHERE username = ?";
            $stmt = $conn->prepare($checkUsernameQuery);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "Username already exists. Please choose a different username.";
            } else {
                // Insert the new user into the database with hashed password and salt
                $insertUserQuery = "INSERT INTO users (username, password, salt) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insertUserQuery);
                $stmt->bind_param("sss", $username, $hashed_password, $salt);

                if ($stmt->execute()) {
                    echo "Registration successful. You can now log in.";
                } else {
                    echo "Error: Registration failed.";
                }
            }

            // Close the statement
            $stmt->close();
        }
    }
}

// Usage:
// Call the register function and pass the database connection as an argument
register($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Register</h1>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" name="register" value="Register" style="background-color: green;">
        <p>Already have an account? <a href="login.php">Login</a></p>
    </form>
</body>
</html>
