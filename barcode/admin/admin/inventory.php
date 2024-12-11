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
    if (isset($_POST['add_product'])) {
        echo("<script>alert('ddddddddd')</script>"); 
    $name = $_POST['name'];
    $details = $_POST['details'] ?? null;
    $beginning_stock_quantity = (int)$_POST['beginning_stock_quantity'];
    $quantity_alert = (int)$_POST['quantity_alert'];
    $store_type = $_POST['store_type'];
    $expiry_date = $_POST['expiry_date'];
    $collection_size = $_POST['collection_size'] ?? '1';
    $image = $_FILES['image'];

    if($collection_size==null || $collection_size==""){
        $collection_size=1;
    }

    if ($name && $quantity_alert && $store_type) {
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
                // Insert product into the database
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO products (name, image, details, collection_size, quantity_alert, store_type, expiry_date) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $target_file, $details, $collection_size, $quantity_alert, $store_type, $expiry_date]);

                // Get the last inserted product ID
                $product_id = $pdo->lastInsertId();

                // Calculate total_items and remain_quantity
                if ($store_type === 'collection') {
                    $total_items = $remain_quantity = $beginning_stock_quantity * $collection_size;
                } else {
                    $total_items = $remain_quantity = $beginning_stock_quantity;
                }

                // Insert into stock_transactions
                $stmt = $pdo->prepare("INSERT INTO stock_transactions (product_id, transaction_type, unit_quantity, total_items, remain_quantity) 
                                       VALUES (?, 'in', ?, ?, ?)");
                $stmt->execute([$product_id, $beginning_stock_quantity, $total_items, $remain_quantity]);

                $pdo->commit();
                $message = 'Product and stock transaction added successfully!';
            } catch (PDOException $e) {
                $pdo->rollBack();
                $message = 'Database error: ' . $e->getMessage().$collection_size;
            }
        } else {
            $message = 'Failed to upload image. Please check file permissions.';
        }
    } else {
        $message = 'All fields are required!';
    }
}elseif (isset($_POST['manage_stock'])) {


    // Handle Stock Management logic
    $product_id = $_POST['product_id'];
    $action = $_POST['action'];
    $quantity = (int)$_POST['quantity'];
    $store_type=$_POST['storet_ype'];
    $collection_size=$_POST['collection_size'];
    


    if ($product_id && $action && $quantity > 0) {
        // Fetch the latest remaining quantity for the product
        $stmt = $pdo->prepare("
            SELECT remain_quantity 
            FROM stock_transactions 
            WHERE product_id = :product_id 
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->execute(['product_id' => $product_id]);
        $last_remaining_quantity = $stmt->fetchColumn() ?? 0;

        // Calculate new remaining quantity
        if ($action === 'in') {

            
                $total_items  = $quantity * $collection_size;
                $new_remaining_quantity = $last_remaining_quantity + $total_items;
                // echo("<script>alert('" . addslashes($collection_size) . "')</script>");  

        } elseif ($action === 'out') {
            // $new_remaining_quantity = max(0, $last_remaining_quantity - $quantity);
        } else {
            $message = 'Invalid action!';
            return;
        }

        // Insert the stock transaction
        $stmt = $pdo->prepare("
            INSERT INTO stock_transactions (product_id, transaction_type, unit_quantity, remain_quantity,total_items) 
            VALUES (:product_id, :transaction_type, :unit_quantity, :remain_quantity,:total_items)
        ");
        $stmt->execute([
            'product_id' => $product_id,
            'transaction_type' => $action,
            'unit_quantity' => $quantity,
            'remain_quantity' => $new_remaining_quantity,
            'total_items'=>$total_items,
        ]);

        $message = 'Stock transaction recorded successfully!';
    } else {
        $message = 'All fields are required for stock management!';
    }
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

// Fetch products and their latest remaining quantity
$stmt = $pdo->prepare("
    SELECT p.*, 
           (SELECT st.remain_quantity 
            FROM stock_transactions st 
            WHERE st.product_id = p.id 
            ORDER BY st.id DESC 
            LIMIT 1) AS remaining_quantity 
    FROM products p 
    ORDER BY p.id DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total pages
$total_pages = ceil($total_records / $records_per_page);
?>


<main class="lg:ml-64 pt-16 px-4">
    <div class="py-6">
        <?php if ($message): ?>
        <div class="bg-green-100 text-green-800 p-4 mb-4 rounded"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Two Columns Layout -->
        <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-6">  
<div class="bg-white p-6 rounded-lg shadow h-full flex flex-col">
    <h2 class="text-xl font-bold mb-4">Products</h2>
    <div class="flex-1 overflow-y-auto">
        <table class="w-full border border-collapse">
        <thead>
    <tr class="bg-gray-50">
        <th class="px-4 py-2 border">#</th>
        <th class="px-4 py-2 border">Name</th>
        <th class="px-4 py-2 border">StoreType</th>
        <th class="px-4 py-2 border">Remaining Stock</th>
        <th class="px-4 py-2 border">Alert</th>
        <!-- <th class="px-4 py-2 border">Image</th> -->
        <th class="px-4 py-2 border">Actions</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($products as $index => $product): ?>
    <tr class="h-16"> <!-- Equal row height -->
        <td class="px-4 py-2 border text-center"><?php echo $index + 1; ?></td>
        <td class="px-4 py-2 border"><?php echo htmlspecialchars($product['name']); ?></td>
        <td class="px-4 py-2 border">  
    <?php   
    echo htmlspecialchars($product['store_type']);   
    if ($product['store_type'] === 'collection') {  
        echo ' (Size: ' . htmlspecialchars($product['collection_size']) . ')';  
    }  
    ?>  
</td>
        <td class="px-4 py-2 border"><?php echo $product['remaining_quantity'] .' Pieces'?? 'N/A'; ?></td>
        <td class="px-4 py-2 border"><?php echo $product['quantity_alert']; ?></td>
        <?php  
/*  
<td class="px-4 py-2 border text-center">  
    <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product Image" class="w-16 h-16 object-cover mx-auto">  
</td>  
*/  
?>
        <td class="px-4 py-2 border text-center">
        <a href="#" 
   class="text-blue-800 stock-link" 
   data-product-id="<?php echo $product['id']; ?>" 
   data-product-name="<?php echo htmlspecialchars($product['name']); ?>" 
   data-store-type="<?php echo htmlspecialchars($product['store_type']); ?>" 
   data-collection-size="<?php echo htmlspecialchars($product['collection_size'] ?? ''); ?>">Stock</a>
 |
            <a href="#" class="text-blue-800">Details</a> |
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

<div id="add-product-form" class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4">Add Product</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium">Product Name</label>
                <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>
            <div>
                <label for="image" class="block text-sm font-medium">Image</label>
                <input type="file" name="image" id="image" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="details" class="block text-sm font-medium">Details</label>
                <textarea name="details" id="details" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
            </div>
            <div>
                <label for="store_type" class="block text-sm font-medium">Store Type</label>
                <select name="store_type" id="store_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    <option value="single">Single</option>
                    <option value="collection">Collection</option>
                </select>
            </div>
            <!-- Hidden fields for Collection -->
            <div id="collection_fields" class="hidden">
                <div>
                    <label for="collection_size" class="block text-sm font-medium">Collection Size</label>
                    <input type="number" name="collection_size" id="collection_size" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
            <div>
                <label for="is_expire" class="block text-sm font-medium">Is Product Expire?</label>
                <select name="is_expire" id="is_expire" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    <option value="no">No</option>
                    <option value="yes">Yes</option>
                </select>
            </div>
            <!-- Hidden field for Expiry Date -->
            <div id="expiration_field" class="hidden">
                <label for="expiry_date" class="block text-sm font-medium">Expiry Date</label>
                <input type="date" name="expiry_date" id="expiry_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="beginning_stock_quantity" class="block text-sm font-medium">Beginning Stock</label>
                <input type="number" name="beginning_stock_quantity" id="beginning_stock_quantity" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>
            <div>
                <label for="quantity_alert" class="block text-sm font-medium">Quantity Alert</label>
                <input type="number" name="quantity_alert" id="quantity_alert" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>
        </div>
        <button type="submit" name="add_product" class="mt-4 bg-green-800 text-white px-4 py-2 rounded">Add Product</button>
    </form>
</div>

<div id="stock-management-form" class="hidden">
    <h2 class="text-xl font-bold mb-4">Manage Stock for <span id="product-name"></span></h2>
    <form action="" method="POST">
        <input type="hidden" name="product_id" id="product-id">
        <input type="hidden" name="store_type" id="store-type-input">
        <input type="hidden" name="collection_size" id="collection-size-input">

        <p>Store Type: <span id="store-type"></span></p>
        <p>Collection Size: <span id="collection-size"></span></p>

        <div>
            <label for="action" class="block text-sm font-medium">Select Action</label>
            <select name="action" id="action" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="in">Stock In</option>
                <option value="out">Stock Out</option>
            </select>
        </div>
        <div id="quantity-field" class="hidden mt-4">
            <label for="quantity" class="block text-sm font-medium">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div class="mt-4">
            <button type="submit" name="manage_stock" class="bg-blue-800 text-white px-4 py-2 rounded">Submit</button>
            <button type="button" id="back-to-product-form" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded">Back</button>
        </div>
    </form>
</div>




<script>
    document.querySelectorAll('.stock-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            // Retrieve data attributes from the clicked link
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const storeType = this.getAttribute('data-store-type');
            const collectionSize = this.getAttribute('data-collection-size');

            // Populate stock form fields
            document.getElementById('product-id').value = productId;
            document.getElementById('product-name').textContent = productName;
            document.getElementById('store-type').textContent = storeType;
            document.getElementById('store-type-input').value = storeType; // Hidden field
            document.getElementById('collection-size').textContent = collectionSize;
            document.getElementById('collection-size-input').value = collectionSize; // Hidden field

            // Hide the add product form and show the stock management form
            document.getElementById('add-product-form').classList.add('hidden');
            document.getElementById('stock-management-form').classList.remove('hidden');
        });
    });

    // Show quantity field based on action in the stock form
    document.getElementById('action').addEventListener('change', function () {
        const quantityField = document.getElementById('quantity-field');
        if (this.value) {
            quantityField.classList.remove('hidden');
        } else {
            quantityField.classList.add('hidden');
        }
    });

    // Add a "Back" button to return to the product form
    document.getElementById('back-to-product-form').addEventListener('click', function () {
        document.getElementById('add-product-form').classList.remove('hidden');
        document.getElementById('stock-management-form').classList.add('hidden');
    });

      // Toggle Expiry Date Field
      document.getElementById('is_expire').addEventListener('change', function () {
        const expirationField = document.getElementById('expiration_field');
        if (this.value === 'yes') {
            expirationField.classList.remove('hidden');
        } else {
            expirationField.classList.add('hidden');
        }
    });
</script>




        </div>
    </div>
</main>
