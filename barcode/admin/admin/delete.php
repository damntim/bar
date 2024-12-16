<?php
$host = 'localhost';
$db = 'db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get the product ID from the URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($product_id) {
    try {
        // Delete product from the database
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $product_id]);

        header('Location: inventory.php');  // Redirect back to the product list
        exit();
    } catch (PDOException $e) {
        echo "Failed to delete product: " . $e->getMessage();
    }
} else {
    echo "No product ID provided.";
}
?>
