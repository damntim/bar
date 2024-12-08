<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

// Database connection (adjust path as needed)
require_once __DIR__ . '/db.php';

// Fetch categories from the database
try {
    $stmt = $pdo->query("SELECT * FROM menu_cat ORDER BY id ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $categories = [];
}

try {
  $stmt = $pdo->query("SELECT * FROM menu ORDER BY sub_category_id ASC");
  $producties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  error_log("Database Error: " . $e->getMessage());
  $producties = [];
}


try {
    $subCategoryStmt = $pdo->query("SELECT * FROM menu_sub_cat ORDER BY category_id, id ASC");
    $subCategories = $subCategoryStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $subCategories = [];
}

// Group subcategories by category_id
$subCategoriesByCategory = [];
foreach ($subCategories as $subCategory) {
    $subCategoriesByCategory[$subCategory['category_id']][] = $subCategory;
}

$menuProductBysubCategories = [];
foreach ($producties as $product) {
    $menuProductBysubCategories[$product['sub_category_id']][] = $product;
}
?>