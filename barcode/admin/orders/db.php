<?php
// db.php
$db = new mysqli('localhost', 'root', '', 'barandhotel');

// Check the connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>
