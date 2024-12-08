<?php
// get_products.php
header('Content-Type: application/json');
include 'db.php'; // Database connection

try {
    // Query to fetch all products
    $stmt = $pdo->prepare('SELECT id, name, details, image FROM products WHERE quantity > 0'); // Assuming 'stock' is a column that tracks product availability
    $stmt->execute();
    $products = $stmt->fetchAll();

    // Check if products are found
    if ($products) {
        echo json_encode(['success' => true, 'products' => $products]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No products available in stock.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
