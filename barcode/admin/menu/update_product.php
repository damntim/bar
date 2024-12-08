<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $details = $_POST['details'];
    $price = $_POST['price'];
    $preparing_time = $_POST['preparing_time'];
    
    // Handle image upload if new image is provided
    $image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    // Get file extension
    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    
    // Get product name and sanitize it for use as filename
    $product_name = $_POST['name'];
    $sanitized_name = preg_replace('/[^a-z0-9]+/', '-', strtolower($product_name));
    
    // Create unique filename using product name and timestamp to avoid overwriting
    $filename = $sanitized_name . '-' . time() . '.' . $file_extension;
    
    $upload_dir = 'uploads/products/';
    $image_path = $upload_dir . $filename;
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
}
    
    // Update query
    $sql = "UPDATE menu SET details = ?, price = ?, preparing_time = ?";
    $params = [$details, $price, $preparing_time];
    
    if ($image_path) {
        $sql .= ", image = ?";
        $params[] = $image_path;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $product_id;
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    
    if ($result) {
        header('Location: menu.php?success=1');
    } else {
        header('Location: menu.php?error=1');
    }
}