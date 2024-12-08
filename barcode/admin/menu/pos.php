<?php

require_once 'auth_check.php';
require_once 'db.php'; // Add your database connection details

// Get logged in user details
$logged_user = $_SESSION['user_name'] ?? '';

// Fetch categories
$categories_query = "SELECT * FROM menu_cat";
$categories = mysqli_query($conn, $categories_query);

// Fetch subcategories if category is selected
$selected_category = $_GET['category_id'] ?? 'all';
$subcategories_query = "SELECT * FROM menu_sub_cat WHERE category_id = '$selected_category'";
$subcategories = mysqli_query($conn, $subcategories_query);

// Fetch products based on subcategory
$selected_subcategory = $_GET['subcategory_id'] ?? 'all';
if ($selected_subcategory != 'all') {
    $products_query = "SELECT * FROM menu WHERE sub_category_id = '$selected_subcategory'";
} else {
    $products_query = "SELECT * FROM menu";
}
$products = mysqli_query($conn, $products_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern POS System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --secondary-color: #f472b6;
            --accent-color: #8b5cf6;
        }
        
        .menu-closed { display: none; }
        .cart-panel { transition: transform 0.3s ease-in-out; }
        .cart-panel.closed { transform: translateX(100%); }
        
        .gradient-bg {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        }
        
        .product-card {
            transition: transform 0.2s ease-in-out;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }

        .category-container {
            position: relative;
        }

        .category-container .subcategory-menu {
            display: none;
            position: absolute;
            top: 80%;
            left: 0;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 10;
            width: 200px;
        }

        .category-container:hover .subcategory-menu {
            display: block;
        }

        #language-selector {
            cursor: pointer;
            outline: none;
            transition: all 0.3s ease;
        }

        #language-selector:hover {
            border-color: #6366f1;
            box-shadow: 0 0 5px rgba(99, 102, 241, 0.5);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-pink-50">
    <!-- Header -->
    <header class="bg-white shadow-lg fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <button onclick="toggleMenu()" class="md:hidden p-2 rounded-md hover:bg-indigo-50">
                        <i class="fas fa-bars text-indigo-600"></i>
                    </button>
                    <div class="flex items-center ml-2 md:ml-0">
                        <i class="fas fa-store text-indigo-600"></i>
                        <span class="ml-2 text-xl font-bold text-gray-900">POS System</span>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center text-indigo-600">
                        <i class="fas fa-clock"></i>
                        <span class="ml-2 text-sm" id="current-time">00:00:00</span>
                    </div>

                    <button onclick="toggleCart()" class="p-2 rounded-md hover:bg-indigo-50 relative">
                        <i class="fas fa-shopping-cart text-indigo-600"></i>
                        <span id="cart-count" class="absolute -top-1 -right-1 bg-pink-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                    </button>

                    <div class="relative flex items-center">
    <img src="/api/placeholder/32/32" alt="Profile" class="w-8 h-8 rounded-full cursor-pointer" id="profileMenuButton">
    <span class="ml-2 text-sm text-gray-700"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>

    <!-- Dropdown menu -->
    <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg hidden" id="profileMenu">
        <div class="px-4 py-2">
            <a href="#" class="block text-sm text-gray-700 hover:bg-gray-100 px-4 py-2">My Account</a>
            <a href="logout.php" class="block text-sm text-gray-700 hover:bg-gray-100 px-4 py-2">Logout</a>
        </div>
    </div>
</div>




                    <select id="language-selector" class="text-gray-600 hover:text-indigo-600 bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option value="en">English</option>
                        <option value="fr">French</option>
                        <option value="sw">Swahili</option>
                        <option value="kin">Kinyarwanda</option>
                    </select>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-20 px-4 pb-4">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-4">
            <!-- Products Section -->
            <div class="flex-1">
                <div class="mb-4">
                    <input type="text" placeholder="Search products..." 
                           class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 outline-none">
                </div>

                <!-- Categories -->
                <div class="flex gap-2 mb-4  pb-2">
                    <?php foreach($categories as $category): ?>
                    <div class="category-container">
                        <button class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-full whitespace-nowrap shadow-md hover:from-indigo-600 hover:to-purple-600">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </button>
                        <div class="subcategory-menu">
                            <?php 
                            $sub_query = "SELECT * FROM menu_sub_cat WHERE category_id = '{$category['id']}'";
                            $subcategories = mysqli_query($conn, $sub_query);
                            while($sub = mysqli_fetch_assoc($subcategories)):
                            ?>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                                <?php echo htmlspecialchars($sub['name']); ?>
                            </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    <?php while($product = mysqli_fetch_assoc($products)): ?>
    <div class="product-card bg-white rounded-md shadow-md p-4">
        <img src="<?php echo htmlspecialchars($product['image'] ?? '/api/placeholder/400/300'); ?>" 
             alt="<?php echo htmlspecialchars($product['name']); ?>"
             class="w-full h-40 object-cover">
        <h3 class="font-semibold mt-2"><?php echo htmlspecialchars($product['name']); ?></h3>
        <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($product['details']); ?></p><br>
        <p class="text-gray-600 text-sm">Takes <?php echo htmlspecialchars($product['preparing_time']); ?> minutes</p>
        <div class="flex justify-between items-center mt-2">
            <span class="text-indigo-600">$<?php echo number_format($product['price'], 2); ?></span>

            <button onclick='addToCart(<?php echo json_encode($product); ?>)'
        class="bg-green-500 text-white p-1.5 rounded">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
    </svg>
</button>

</button>
        </div>
    </div>
    <?php endwhile; ?>
</div>
            </div>

            <!-- Cart Panel -->
            <div id="cart-panel" class="cart-panel bg-white p-4 shadow-lg w-full md:w-96 closed">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-indigo-600">Shopping Cart</h2>
                    <button onclick="toggleCart()" class="md:hidden">
                        <i class="fas fa-times text-gray-600"></i>
                    </button>
                </div>
                
                <div id="cart-items" class="space-y-4 mb-4">
                    <!-- Cart items will be dynamically added here -->
                </div>

                <div class="border-t pt-4">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span id="cart-total">$0.00</span>
                    </div>
                    <button onclick="processOrder()" 
                            class="w-full mt-4 bg-gradient-to-r from-indigo-500 to-purple-500 text-white py-3 rounded-lg hover:from-indigo-600 hover:to-purple-600 shadow-md">
                        Process Order
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        let cart = [];

        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('menu-closed');
        }

        function toggleCart() {
            const cart = document.getElementById('cart-panel');
            cart.classList.toggle('closed');
        }

        function updateTime() {
            const timeElement = document.getElementById('current-time');
            timeElement.textContent = new Date().toLocaleTimeString();
        }

       

// =----------------------------------------------------------------------------order--------------------------
        function addToCart(product) {
    if (!product?.id || !product?.price) {
        console.error('Invalid product data');
        return;
    }

    const existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
        existingItem.quantity++;
        existingItem.subtotal = existingItem.quantity * existingItem.price;
    } else {
        cart.push({
            ...product,
            quantity: 1,
            subtotal: product.price
        });
    }
    
    updateCartDisplay();
    saveCartToLocalStorage();
}

function updateCartDisplay() {
    const cartItems = document.getElementById('cart-items');
    const cartCount = document.getElementById('cart-count');
    const cartTotal = document.getElementById('cart-total');
    
    if (!cartItems || !cartCount || !cartTotal) return;

    cartItems.innerHTML = '';
    const total = cart.reduce((sum, item) => sum + item.subtotal, 0);

    cart.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.className = 'flex justify-between items-center p-2 rounded-lg hover:bg-indigo-50';
        
        // Sanitize item data to prevent XSS
        const sanitizedName = escapeHtml(item.name);
        const sanitizedPrice = Number(item.price).toFixed(2);
        const sanitizedQuantity = Number(item.quantity);
        
        itemElement.innerHTML = `
            <div>
                <h4 class="font-medium text-gray-800">${sanitizedName}</h4>
                <div class="text-sm text-indigo-600">$${sanitizedPrice} × ${sanitizedQuantity}</div>
            </div>
            <div class="flex items-center gap-2">
                <button 
                    onclick="updateQuantity(${item.id}, -1)"
                    class="px-2 py-1 bg-indigo-100 hover:bg-indigo-200 rounded text-indigo-600"
                    aria-label="Decrease quantity"
                >-</button>
                <span class="text-gray-800">${sanitizedQuantity}</span>
                <button 
                    onclick="updateQuantity(${item.id}, 1)"
                    class="px-2 py-1 bg-indigo-100 hover:bg-indigo-200 rounded text-indigo-600"
                    aria-label="Increase quantity"
                >+</button>
            </div>
        `;
        cartItems.appendChild(itemElement);
    });

    cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartTotal.textContent = `$${total.toFixed(2)}`;
}

function updateQuantity(productId, delta) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity = Math.max(0, item.quantity + delta);
        item.subtotal = item.quantity * item.price;
        
        if (item.quantity === 0) {
            cart = cart.filter(i => i.id !== productId);
        }
        
        updateCartDisplay();
        saveCartToLocalStorage();
    }
}

function processOrder() {
    if (cart.length === 0) {
        alert('Please add items to your cart first.');
        return;
    }

    const orderData = {
        items: cart.map(item => ({
            id: item.id,
            name: item.name,
            quantity: item.quantity,
            price: item.price,
            subtotal: item.subtotal
        })),
        total_price: cart.reduce((sum, item) => sum + item.subtotal, 0)
    };

    fetch('process_order.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content
        },
        credentials: 'same-origin',
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Order processed successfully!');
            cart = [];
            updateCartDisplay();
            saveCartToLocalStorage();
            if (typeof toggleCart === 'function') toggleCart();
        } else {
            throw new Error(data.message || 'Order processing failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error processing order: ' + error.message);
    });
}

// Cart Management
function saveCartToLocalStorage() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

function loadCartFromLocalStorage() {
    try {
        const savedCart = localStorage.getItem('cart');
        if (savedCart) {
            cart = JSON.parse(savedCart);
            updateCartDisplay();
        }
    } catch (error) {
        console.error('Error loading cart:', error);
    }
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Add to cart button component
function renderAddToCartButton(product) {
    return `
        <button onclick='addToCart(${JSON.stringify(product)})'
                class="bg-green-500 text-white p-1.5 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50"
                aria-label="Add to cart">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </button>
    `;
}

// Initialize cart on page load
document.addEventListener('DOMContentLoaded', loadCartFromLocalStorage);
// ----------------------------------------------------------------------------------------------------------------------------------------
        // Initialize
        setInterval(updateTime, 1000);
        updateTime();
    </script>

<script>
    const profileMenuButton = document.getElementById('profileMenuButton');
    const profileMenu = document.getElementById('profileMenu');

    profileMenuButton.addEventListener('click', () => {
        profileMenu.classList.toggle('hidden');
    });

    // Close the dropdown if clicked outside of the menu
    window.addEventListener('click', (event) => {
        if (!profileMenu.contains(event.target) && event.target !== profileMenuButton) {
            profileMenu.classList.add('hidden');
        }
    });
</script>
</body>
</html>