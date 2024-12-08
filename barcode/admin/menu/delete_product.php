<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $product_id = $_GET['id'];
    
    $stmt = $pdo->prepare("DELETE FROM menu WHERE id = ?");
    $result = $stmt->execute([$product_id]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
}