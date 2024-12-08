<?php
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || empty($data['id']) || empty($data['name'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
            exit;
        }

        $subCategoryId = intval($data['id']);
        $subCategoryName = trim($data['name']);

        // Database connection
        require 'db.php';

        // Log debug info
        error_log("Editing Subcategory ID: $subCategoryId, New Name: $subCategoryName");

        $stmt = $pdo->prepare("UPDATE menu_sub_cat SET name = ? WHERE id = ?");
        $result = $stmt->execute([$subCategoryName, $subCategoryId]);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Subcategory updated successfully.']);
        } else {
            throw new Exception("Failed to execute update query.");
        }
    } else {
        throw new Exception("Invalid request method.");
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while updating the subcategory.']);
}
