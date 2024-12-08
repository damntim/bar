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

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Category updated successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update category.']);
            }
        } else {
            // Deleting case
            // Ensure you delete related subcategories and products as well (if needed)
            try {
                $pdo->beginTransaction();

                // Delete products related to the subcategories of the category
                $stmt1 = $pdo->prepare("
                    DELETE p
                    FROM menu p
                    JOIN menu_sub_cat sc ON p.sub_category_id = sc.id
                    WHERE sc.category_id = :category_id
                ");
                $stmt1->execute(['category_id' => $categoryId]);

                // Delete subcategories related to the category
                $stmt2 = $pdo->prepare("DELETE FROM menu_sub_cat WHERE category_id = :category_id");
                $stmt2->execute(['category_id' => $categoryId]);

                // Finally, delete the category
                $stmt3 = $pdo->prepare("DELETE FROM menu_cat WHERE id = :category_id");
                $stmt3->execute(['category_id' => $categoryId]);

                $pdo->commit();

                echo json_encode(['status' => 'success', 'message' => 'Category and related subcategories/products deleted successfully.']);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'An error occurred while deleting: ' . $e->getMessage()]);
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid category ID.']);
    }
}
?>
