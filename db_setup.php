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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_admin TINYINT(1) DEFAULT 0
)";

mysqli_query($conn, $sql);

$sqlInsert = "INSERT IGNORE INTO users (first_name, last_name, email, username, password, address, is_admin) VALUES
    ('John', 'Doe', 'johndoe@gmail.com', 'johndoe', 'password123', '123 Main St, NewYork, USA', 0)";
if (!mysqli_query($conn, $sqlInsert)) {
    echo "User insert error: " . mysqli_error($conn);
}

$sql = "CREATE TABLE IF NOT EXISTS tickets (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open','in_progress','resolved','closed') DEFAULT 'open',
    priority ENUM('low','medium','high') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

mysqli_query($conn, $sql);

$sql = "CREATE TABLE IF NOT EXISTS CreditCardsTable (
    card_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    card_number VARCHAR(50) NOT NULL UNIQUE,
    card_type VARCHAR(20) NOT NULL,
    card_expiration DATE NOT NULL,
    card_cvv VARCHAR(10) NOT NULL,
    card_balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (username) REFERENCES users(username)
)";


mysqli_query($conn, $sql);
    
$sqlInsert = "INSERT IGNORE INTO CreditCardsTable (username, card_number, card_type, card_expiration, card_cvv, card_balance) VALUES
    ('johndoe', '1234 5678 9012 3456', 'Visa', '2030-12-31', '123', 1000.00)";

if (!mysqli_query($conn, $sqlInsert)) {
    echo "User insert error: " . mysqli_error($conn);
}


$sql = "CREATE TABLE IF NOT EXISTS AccountsTable (
    account_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    account_type ENUM('checking', 'savings') NOT NULL,
    account_number VARCHAR(20) NOT NULL UNIQUE,
    balance DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if(!mysqli_query($conn, $sql)){
    echo "Error creating accounts: " .mysqli_error($conn);
}

$sql = "CREATE TABLE IF NOT EXISTS TransactionsTable (
    transaction_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    account_id INT UNSIGNED NOT NULL,
    transaction_type ENUM('deposit', 'withdrawal', 'transfer_in', 'transfer_out', 'payment') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES AccountsTable(account_id)
)";

if(!mysqli_query($conn, $sql)){
    echo "Error creating transactions: " .mysqli_error($conn);
}


mysqli_close($conn);
?>
