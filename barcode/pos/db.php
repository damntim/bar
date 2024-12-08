<?php
// db.php
$servername = "localhost";
$username = "root";  // default XAMPP username
$password = "";      // default XAMPP password
$dbname = "barandhotel";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to handle special characters correctly
$conn->set_charset("utf8mb4");
?>