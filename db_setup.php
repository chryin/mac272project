<?php
require 'config.php';

$conn = mysqli_connect($servername, $username, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// IF NOT EXISTS handles the check for you
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";

if (!mysqli_query($conn, $sql)) {
    die("Error creating database: " . mysqli_error($conn));
}


mysqli_select_db($conn, $dbname);

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $sql);

mysqli_close($conn);
?>