<?php
// get_product_details.php
header('Content-Type: application/json');
include 'db.php'; // Database connection

// Check if the product ID is provided
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    try {
        // Query to fetch product details by ID
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute(['id' => $productId]);
        $product = $stmt->fetch();

        // Check if product exists
        if ($product) {
            echo json_encode(['success' => true, 'product' => $product]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Product ID is required.']);
}
?>
