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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch form data
    $name = $_POST['name'];
    $details = $_POST['details'] ?? null;
    $beginning_stock_quantity = (int)$_POST['beginning_stock_quantity'];
    $quantity_alert = (int)$_POST['quantity_alert'];
    $store_type = $_POST['store_type'];
    $expiry_date = $_POST['expiry_date'] ?? null;
    $image = $_FILES['image'];

    if ($name && $beginning_stock_quantity && $quantity_alert && $store_type) {
        // Define target directory and file path
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $target_file = $target_dir . uniqid() . "_" . basename($image['name']);
        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Allowed file types
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Validate the file
        if (!in_array($file_extension, $allowed_extensions)) {
            $message = 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.';
        } elseif ($image['size'] > 2 * 1024 * 1024) { // 2MB limit
            $message = 'File size exceeds the 2MB limit.';
        } elseif (move_uploaded_file($image['tmp_name'], $target_file)) {
            try {
                // Check if editing or adding
    if (isset($product_data)) {
        // Update product in database
        $update_stmt = $pdo->prepare("UPDATE products SET name = ?, details = ?, beginning_stock_quantity = ?, quantity_alert = ?, store_type = ?, expiry_date = ? WHERE id = ?");
        $update_stmt->execute([$name, $details, $beginning_stock_quantity, $quantity_alert, $store_type, $expiry_date, $product_data['id']]);
        $message = 'Product updated successfully!';
    }else{
                // Insert product into the database
                $stmt = $pdo->prepare("INSERT INTO products (name, image, details, beginning_stock_quantity, quantity_alert, store_type, expiry_date) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $target_file, $details, $beginning_stock_quantity, $quantity_alert, $store_type, $expiry_date]);
                $message = 'Product added successfully!';        
    }

            } catch (PDOException $e) {
                $message = 'Database error: ' . $e->getMessage();
            }
        } else {
            $message = 'Failed to upload image. Please check file permissions.';
        }
    } else {
        $message = 'All fields are required!';
    }
}

?>
<?php
// Pagination settings
$records_per_page = 5;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max($current_page, 1); // Ensure the current page is at least 1
$offset = ($current_page - 1) * $records_per_page;

// Fetch total number of records
$total_records_stmt = $pdo->query("SELECT COUNT(*) FROM products");
$total_records = $total_records_stmt->fetchColumn();

// Fetch products for the current page
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total pages
$total_pages = ceil($total_records / $records_per_page);

// Check if an ID is passed for editing
$product_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$edit_id]);
    $product_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<main class="lg:ml-64 pt-16 px-4">
    <div class="py-6">
        <?php if ($message): ?>
        <div class="bg-green-100 text-green-800 p-4 mb-4 rounded"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Two Columns Layout -->
        <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-6">  
  <!-- <div class="bg-blue-500">First Column (Wider)</div>  
  <div class="bg-green-500">Second Column (Narrower)</div>  
</div> -->
<div class="bg-white p-6 rounded-lg shadow h-full flex flex-col">
    <h2 class="text-xl font-bold mb-4">Products</h2>
    <div class="flex-1 overflow-y-auto">
        <table class="w-full border border-collapse">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-2 border">#</th>
                    <th class="px-4 py-2 border">Name</th>
                    <th class="px-4 py-2 border">Stock</th>
                    <th class="px-4 py-2 border">Alert</th>
                    <th class="px-4 py-2 border">Image</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $index => $product): ?>
                <tr class="h-16"> <!-- Equal row height -->
                    <td class="px-4 py-2 border text-center"><?php echo $index + 1; ?></td>
                    <td class="px-4 py-2 border"><?php echo htmlspecialchars($product['name']); ?></td>
                    <td class="px-4 py-2 border"><?php echo $product['beginning_stock_quantity']; ?></td>
                    <td class="px-4 py-2 border"><?php echo $product['quantity_alert']; ?></td>
                    <td class="px-4 py-2 border text-center">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product Image" class="w-16 h-16 object-cover mx-auto">
                    </td>
                    <td class="px-4 py-2 border text-center">
                        <a href="#" class="text-blue-800">Details</a> |
                        <a href="?edit=<?php echo $product['id']; ?>" class="text-green-700">Edit</a> |
                        <a href="#" class="text-red-600">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex justify-center space-x-2">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="px-4 py-2 rounded <?php echo $i == $page ? 'bg-blue-500 text-white' : 'bg-gray-200'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
</div>



<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4"><?php echo $product_data ? 'Edit Product' : 'Add Product'; ?></h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium">Product Name</label>
                <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo $product_data['name'] ?? ''; ?>" required>
            </div>
            <div>
                <label for="image" class="block text-sm font-medium">Image</label>
                <?php if ($product_data && $product_data['image']): ?>
                    <img src="<?php echo $product_data['image']; ?>" alt="Product Image" class="w-16 h-16 object-cover mx-auto mb-2">
                <?php endif; ?>
                <input type="file" name="image" id="image" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="details" class="block text-sm font-medium">Details</label>
                <textarea name="details" id="details" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><?php echo $product_data['details'] ?? ''; ?></textarea>
            </div>
            <div>
                <label for="beginning_stock_quantity" class="block text-sm font-medium">Beginning Stock</label>
                <input type="number" name="beginning_stock_quantity" id="beginning_stock_quantity" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo $product_data['beginning_stock_quantity'] ?? ''; ?>" required>
            </div>
            <div>
                <label for="quantity_alert" class="block text-sm font-medium">Quantity Alert</label>
                <input type="number" name="quantity_alert" id="quantity_alert" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo $product_data['quantity_alert'] ?? ''; ?>" required>
            </div>
            <div>
                <label for="store_type" class="block text-sm font-medium">Store Type</label>
                <select name="store_type" id="store_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    <option value="collection" <?php echo $product_data['store_type'] == 'collection' ? 'selected' : ''; ?>>Collection</option>
                    <option value="single" <?php echo $product_data['store_type'] == 'single' ? 'selected' : ''; ?>>Single</option>
                </select>
            </div>
            <div>
                <label for="expiry_date" class="block text-sm font-medium">Expiry Date</label>
                <input type="date" name="expiry_date" id="expiry_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo $product_data['expiry_date'] ?? ''; ?>">
            </div>
        </div>
        <button type="submit" class="mt-4 bg-green-800 text-white px-4 py-2 rounded"><?php echo $product_data ? 'Update Product' : 'Add Product'; ?></button>
    </form>
</div>

        </div>
    </div>
</main>
