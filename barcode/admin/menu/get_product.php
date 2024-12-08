<?php
require_once 'db.php';

$product_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM menu WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($product);