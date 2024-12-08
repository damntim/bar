<?php
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
        exit;
    }

    $categoryId = intval($data['id']);
    $categoryName = isset($data['name']) ? trim($data['name']) : null;

    if ($categoryId > 0) {
        if ($categoryName !== null) {
            // Editing case
            $stmt = $pdo->prepare("UPDATE menu_cat SET name = ? WHERE id = ?");
            $result = $stmt->execute([$categoryName, $categoryId]);
            echo json_encode(['status' => $result ? 'success' : 'error', 'message' => $result ? 'Category updated successfully.' : 'Failed to update category.']);
        } else {
            // Deleting case
            $stmt = $pdo->prepare("DELETE FROM menu_cat WHERE id = ?");
            $result = $stmt->execute([$categoryId]);
            echo json_encode(['status' => $result ? 'success' : 'error', 'message' => $result ? 'Category deleted successfully.' : 'Failed to delete category.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid category ID.']);
    }
}

?>
