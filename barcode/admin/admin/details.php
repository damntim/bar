<?php
$host = 'localhost';
$db = 'db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
// Initialize message
$message = '';
if (!isset($_GET['id'])) {
    die("Product ID not provided.");
}

$product_id = (int)$_GET['id'];

// Fetch product details
$stmt = $pdo->prepare("
    SELECT p.*, 
           (SELECT st.remain_quantity 
            FROM stock_transactions st 
            WHERE st.product_id = p.id 
            ORDER BY st.id DESC 
            LIMIT 1) AS remaining_quantity 
    FROM products p 
    WHERE p.id = :id
");
$stmt->execute(['id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}
?>
<main class="lg:ml-56 pt-16 px-2">
    <div class="py-6">
        <div class="grid grid-cols-1 md:grid-cols-[3fr_1fr] gap-6">
            <div class="bg-white p-6 rounded-lg shadow h-full flex flex-col lg:w-[90%] mx-auto">
                <h2 class="text-xl font-bold mb-4">Products</h2>
                <div class="flex-1 overflow-y-auto overflow-x-auto">
        <div class="relative">
            <img 
                src="<?php echo htmlspecialchars($product['image']); ?>" 
                alt="Product Image" 
                class="w-full h-64 object-cover"
            >
            <div class="absolute bottom-0 left-0 bg-black bg-opacity-50 text-white p-4 w-full">
                <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($product['name']); ?></h1>
            </div>
        </div>

        <!-- Product Details -->
        <div class="p-6 space-y-4">
            <div>
                <h2 class="text-lg font-semibold">Product Information</h2>
                <p><strong>Store Type:</strong> <?php echo htmlspecialchars($product['store_type']); ?></p>
                <?php if ($product['store_type'] === 'collection'): ?>
                    <p><strong>Collection Size:</strong> <?php echo htmlspecialchars($product['collection_size']); ?></p>
                <?php endif; ?>
                <p><strong>Details:</strong> <?php echo htmlspecialchars($product['details'] ?? 'No details available.'); ?></p>
            </div>

            <div>
                <h2 class="text-lg font-semibold">Stock Information</h2>
                <p><strong>Remaining Stock:</strong> <?php echo htmlspecialchars($product['remaining_quantity'] ?? '0'); ?> Pieces</p>
                <p><strong>Quantity Alert:</strong> <?php echo htmlspecialchars($product['quantity_alert']); ?></p>
            </div>

            <?php if ($product['expiry_date']): ?>
                <div>
                    <h2 class="text-lg font-semibold">Expiry Date</h2>
                    <p><?php echo htmlspecialchars($product['expiry_date']); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Actions -->
        <div class="p-6 border-t bg-gray-50 flex justify-between">
            <!-- <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded">
                Edit Product
            </a>
            <form action="delete_product.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">
                    Delete Product
                </button> -->
            </form>
            <a href="inventory.php" class="bg-gray-600 text-white px-4 py-2 rounded">Back to Products</a>
        </div>
    </div>
</div>

    </main>


