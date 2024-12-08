<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
require_once __DIR__ . '/db.php';

// Function to handle file upload
function handleImageUpload($file, $product_name) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    // Sanitize the product name to create a valid file name
    $sanitized_name = preg_replace("/[^a-zA-Z0-9-_]/", "_", $product_name); // Replace non-alphanumeric characters with underscores
    $new_filename = $sanitized_name . '.' . $file_extension; // Use product name for the file
    $target_file = $target_dir . $new_filename;
    
    // Check if image file is valid
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($file_extension, $allowed_types)) {
        return array('success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed.');
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return array('success' => true, 'path' => $target_file);
    }
    
    return array('success' => false, 'message' => 'Failed to upload image.');
}

// Initialize response array
$response = array('success' => false, 'message' => '');

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Validate required fields
    if (empty($_POST['sub_category_id']) || empty($_POST['product_name']) || 
        empty($_POST['preparing_time']) || empty($_POST['price'])) {
        throw new Exception('All required fields must be filled out.');
    }
    
    $image_path = '';
    // Handle image upload if new file is provided
    if (isset($_FILES['product_image']) && $_FILES['product_image']['size'] > 0) {
        $upload_result = handleImageUpload($_FILES['product_image'], $_POST['product_name']);
        if (!$upload_result['success']) {
            throw new Exception($upload_result['message']);
        }
        $image_path = $upload_result['path'];
    } elseif (isset($_POST['existing_image'])) {
        // Use existing image path if provided
        $image_path = $_POST['existing_image'];
    }
    
    // Prepare SQL statement
    $sql = "INSERT INTO menu (
        sub_category_id, 
        name, 
        details, 
        image, 
        preparing_time, 
        price,
        created_at
    ) VALUES (
        :sub_category_id,
        :name,
        :details,
        :image,
        :preparing_time,
        :price,
        NOW()
    )";
    
    $stmt = $pdo->prepare($sql);
    
    // Execute with parameters
    $stmt->execute([ 
        ':sub_category_id' => $_POST['sub_category_id'],
        ':name' => $_POST['product_name'],
        ':details' => $_POST['product_details'] ?? '',
        ':image' => $image_path,
        ':preparing_time' => $_POST['preparing_time'],
        ':price' => $_POST['price']
    ]);
    
    // Commit transaction
    $pdo->commit();
    
    $response['success'] = true;
    $response['message'] = 'Product added successfully!';
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    $response['success'] = false;
    $response['message'] = 'Error: ' . $e->getMessage();
    
    // Log error for debugging
    error_log("Error in add_product.php: " . $e->getMessage());
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
