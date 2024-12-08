<?php
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || empty($data['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
            exit;
        }

        $subCategoryId = intval($data['id']);

        // Database connection
        require 'db.php';

        // Log debug info
        error_log("Deleting Subcategory ID: $subCategoryId");

        // Start transaction for safe deletion of related products and subcategory
        $pdo->beginTransaction();

        // First, delete products related to the subcategory
        $stmt1 = $pdo->prepare("DELETE FROM menu WHERE sub_category_id = ?");
        $stmt1->execute([$subCategoryId]);

        // Then, delete the subcategory
        $stmt2 = $pdo->prepare("DELETE FROM menu_sub_cat WHERE id = ?");
        $result = $stmt2->execute([$subCategoryId]);

        if ($stmt1->rowCount() >= 0 && $result) {
            $pdo->commit(); // Commit the transaction if everything is successful
            echo json_encode(['status' => 'success', 'message' => 'Subcategory and related products deleted successfully.']);
        } else {
            $pdo->rollBack(); // Rollback transaction if anything fails
            throw new Exception("Failed to delete subcategory or related products.");
        }
    } else {
        throw new Exception("Invalid request method.");
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while deleting the subcategory.']);
}
