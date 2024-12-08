<?php
require_once __DIR__ . '/db.php';

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = $_POST['category_id'];
    $subCategoryName = $_POST['sub_category_name'];

    // Input validation
    if (empty($categoryId) || empty($subCategoryName)) {
        $response['message'] = 'Invalid input. Please provide all required fields.';
    } else {
        try {
            // Insert into the database
            $stmt = $pdo->prepare("INSERT INTO menu_sub_cat (category_id, name) VALUES (?, ?)");
            if ($stmt->execute([$categoryId, $subCategoryName])) {
                $response['success'] = true;
            } else {
                $response['message'] = 'Failed to add subcategory.';
            }
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            $response['message'] = 'Database error occurred.';
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
