<?php
// Database configuration
$host = 'localhost';
$db_name = 'barandhotel';
$username = 'root';
$password = '';



try {
    // Create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL query to fetch menu data
    $sql = "SELECT sub_category_id, name, details, image, preparing_time, price FROM menu";
    $stmt = $pdo->prepare($sql);

    // Execute the query
    $stmt->execute();

    // Fetch the results
    $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle any errors
    echo "Error: " . $e->getMessage();
}
?>

<body>

<h2>Menu Items</h2>

<table>
    <thead>
        <tr>
            <th>Sub-category ID</th>
            <th>Name</th>
            <th>Details</th>
            <th>Image</th>
            <th>Preparing Time (minutes)</th>
            <th>Price ($)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($menu_items as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['sub_category_id']); ?></td>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td><?php echo htmlspecialchars($item['details']); ?></td>
            <td>
                <?php
                    // Debug: output the image path to ensure it's correct
                    echo 'Image Path: ' . htmlspecialchars($item['image']) . '<br>';
                ?>
                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Menu Image">
            </td>
            <td><?php echo htmlspecialchars($item['preparing_time']); ?> minutes</td>
            <td>$<?php echo htmlspecialchars($item['price']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
