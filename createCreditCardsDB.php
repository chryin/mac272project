<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myDB";


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}


// sql to create table
$sql = "CREATE TABLE CreditCardsTable(
username VARCHAR(30) PRIMARY KEY UNIQUE,
card_number VARCHAR(50),
card_type VARCHAR(20),
card_expiration DATE,
card_cvv VARCHAR(10),
card_balance DECIMAL(10, 2)
)";

$sqlInsert = "INSERT INTO CreditCardsTable (username, card_number, card_type, card_expiration, card_cvv, card_balance) VALUES
('johndoe', '1234 5678 9012 3456', 'Visa', '2030-12-31', '123', 1000.00),
('janedoe', '9876 5432 1098 7654', 'MasterCard', '2028-11-30', '456', 500.00)";


if (mysqli_query($conn, $sql)) {
  echo "Table MyGuests created successfully";
} else {
  echo "Error creating table: " . mysqli_error($conn);
}

if (mysqli_query($conn, $sqlInsert)) {
  echo "Sample data inserted successfully";
} else {
  echo "Error inserting data: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
