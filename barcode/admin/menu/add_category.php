<?php
// Database connection
require_once 'db.php'; // Update with your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = trim($_POST['category_name']); // Remove unnecessary whitespace

    if (!empty($category_name)) {
        try {
            // Check if the category already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM menu_cat WHERE name = :name");
            $stmt->bindParam(':name', $category_name, PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                // Redirect back with an error query parameter for duplicate
                header("Location: menu.php?error=exists");
                exit();
            }

            // Prepare and execute the query to insert a new category
            $stmt = $pdo->prepare("INSERT INTO menu_cat (name) VALUES (:name)");
            $stmt->bindParam(':name', $category_name, PDO::PARAM_STR);
            $stmt->execute();

            // Redirect back with success query parameter
            header("Location: menu.php?success=1");
            exit();
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log("Database Error: " . $e->getMessage());

            // Redirect back with a generic error query parameter
            header("Location: menu.php?error=1");
            exit();
        }
    } else {
        // Redirect back with an error query parameter for empty field
        header("Location: menu.php?error=empty");
        exit();
    }
}
?>
