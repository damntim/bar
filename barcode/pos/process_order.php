
<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_name'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['items']) || empty($data['items']) || !isset($data['total_price'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

try {
    mysqli_begin_transaction($conn);
    
    $seller_name = mysqli_real_escape_string($conn, $_SESSION['user_name']);
    $total_price = floatval($data['total_price']);
    $order_date = date('Y-m-d H:i:s');
    
    $order_query = "INSERT INTO orders (seller_name, order_date, total_price) 
                    VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $order_query);
    mysqli_stmt_bind_param($stmt, "ssd", $seller_name, $order_date, $total_price);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);
    
    $item_query = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price, subtotal) 
                   VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $item_query);
    
    foreach ($data['items'] as $item) {
        $product_id = intval($item['id']);
        $product_name = $item['name'];
        $quantity = intval($item['quantity']);
        $price = floatval($item['price']);
        $subtotal = floatval($item['subtotal']);
        
        mysqli_stmt_bind_param($stmt, "iisids", 
            $order_id, $product_id, $product_name, $quantity, $price, $subtotal);
        mysqli_stmt_execute($stmt);
    }
    
    mysqli_commit($conn);
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

mysqli_close($conn);
?>