<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "barandhotel";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $type = $_GET['type'] ?? 'all';
    $id = $_GET['id'] ?? null;
    
    // Query to Retrieve Products
    $query = "SELECT 
                m.id,
                m.name,
                m.price,
                m.image,
                m.details,
                m.preparing_time,
                msc.name as subcategory_name,
                mc.name as category_name 
              FROM menu m 
              LEFT JOIN menu_sub_cat msc ON m.sub_category_id = msc.id 
              LEFT JOIN menu_cat mc ON msc.category_id = mc.id";
    
    if ($type === 'category') {
        $query .= " WHERE mc.id = :id";
    } else if ($type === 'subcategory') {
        $query .= " WHERE msc.id = :id";
    }
    
    $stmt = $conn->prepare($query);
    if ($type !== 'all') {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    }
    $stmt->execute();
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>