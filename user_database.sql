CREATE DATABASE IF NOT EXISTS user_database;
USE user_database;

-- Drop the table if it exists
DROP TABLE IF EXISTS users;

-- Create the table with id, username, password, and salt columns
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    salt VARCHAR(255) NOT NULL
);
