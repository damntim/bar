<?php
require 'db.php'; // Database connection

header('Content-Type: application/json');

// Query the latest order from the database
$query = "SELECT order_id FROM orders ORDER BY order_id DESC LIMIT 1";
$result = $db->query($query);

if ($result && $row = $result->fetch_assoc()) {
    echo json_encode(['newOrderId' => (int)$row['order_id']]);
} else {
    echo json_encode(['newOrderId' => 0]); // No orders yet
}
?>
