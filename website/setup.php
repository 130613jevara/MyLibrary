<?php
$host = "localhost";
$username = "root";
$password = "mysql"; // if error later, change to ""

try {
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->exec("CREATE DATABASE IF NOT EXISTS simple_library");
    $conn->exec("USE website");

    $conn->exec("
        CREATE TABLE IF NOT EXISTS books (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            year INT,
            genre VARCHAR(100),
            description TEXT,
            added_by VARCHAR(100),
            date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    echo 'Database and table created successfully!';
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}