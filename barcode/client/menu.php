
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Digital Menu</title>
    <style>
        .notification {
            transform: translateY(150%);
            transition: transform 0.3s ease-in-out;
        }
        .notification.show {
            transform: translateY(0);
        }
        .menu-card:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease-in-out;
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
<body class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50">
    <!-- Header -->
    <header class="sticky top-0 bg-white shadow-lg z-50">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-utensils text-indigo-600 text-2xl"></i>
                    <h1 class="text-2xl font-bold text-gray-800">Digital Menu</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="callWaiter()" class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-bell mr-2"></i>
                        Call Waiter
                    </button>
                    <button onclick="checkBill()" class="flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        <i class="fas fa-receipt mr-2"></i>
                        My Bill
                    </button>
                      <!-- Language Selector -->
                      <div class="relative">
                        <select id="language-selector" onchange="changeLanguage()" class="text-gray-600 hover:text-indigo-600 bg-white border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="en">English</option>
                            <option value="fr">French</option>
                            <option value="sw">Swahili</option>
                            <option value="kin">Kinyarwanda</option>
                        </select>
                    </div>
                   
                </div>
            </div>
        </div>
    </header>

    <!-- Search and Categories -->
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="relative mb-6">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input
                type="text"
                placeholder="Search menu..."
                class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                oninput="filterMenu(this.value)"
            >
        </div>

        <div class="flex overflow-x-auto gap-4 pb-4" id="categories">
            <!-- Categories will be added here -->
        </div>
    </div>

    <!-- Menu Grid and Cart -->
    <div class="max-w-7xl mx-auto px-4 pb-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="menu-grid">
                    <!-- Menu items will be added here -->
                </div>
            </div>

            <!-- Cart -->
            <div class="bg-white rounded-lg shadow-lg p-4">
                <h2 class="text-xl font-bold mb-4">Your Order</h2>
                <div id="cart-items" class="space-y-4">
                    <!-- Cart items will be added here -->
                </div>
                <div class="mt-6 pt-4 border-t">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span id="cart-total">$0.00</span>
                    </div>
                    <button 
                        onclick="placeOrder()"
                        class="w-full mt-4 bg-gradient-to-r from-indigo-500 to-purple-500 text-white py-3 rounded-lg hover:from-indigo-600 hover:to-purple-600 disabled:opacity-50"
                        id="place-order-btn"
                        disabled
                    >
                        Place Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div id="notification" class="notification fixed bottom-4 right-4 bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-lg">
        <span id="notification-text"></span>
    </div>

    <script>
        // Menu data
        const categories = [
            { id: 'all', name: 'All Items', icon: 'fa-th-large' },
            { id: 'starters', name: 'Starters', icon: 'fa-carrot' },
            { id: 'mains', name: 'Main Course', icon: 'fa-hamburger' },
            { id: 'drinks', name: 'Drinks', icon: 'fa-glass-martini-alt' },
            { id: 'desserts', name: 'Desserts', icon: 'fa-ice-cream' }
        ];

        const menuItems = [
            { id: 1, name: 'Caesar Salad', price: 12.99, category: 'starters', description: 'Fresh romaine lettuce, croutons, parmesan' },
            { id: 2, name: 'Margherita Pizza', price: 18.99, category: 'mains', description: 'Fresh tomatoes, mozzarella, basil' },
            { id: 3, name: 'Grilled Salmon', price: 24.99, category: 'mains', description: 'Atlantic salmon with seasonal vegetables' },
            { id: 4, name: 'Red Wine', price: 8.99, category: 'drinks', description: 'House red wine, 175ml' },
            { id: 5, name: 'Tiramisu', price: 9.99, category: 'desserts', description: 'Classic Italian dessert' },
            { id: 6, name: 'Bruschetta', price: 10.99, category: 'starters', description: 'Toasted bread with tomatoes and garlic' }
        ];

        let cart = [];
        let activeCategory = 'all';
        let searchTerm = '';

        // Initialize categories
        function initializeCategories() {
            const categoriesContainer = document.getElementById('categories');
            categories.forEach(category => {
                const button = document.createElement('button');
                button.className = `flex items-center px-4 py-2 rounded-full whitespace-nowrap ${
                    category.id === 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'
                }`;
                button.innerHTML = `
                    <i class="fas ${category.icon} mr-2"></i>
                    ${category.name}
                `;
                button.onclick = () => filterByCategory(category.id);
                categoriesContainer.appendChild(button);
            });
        }

        // Initialize menu
        function initializeMenu() {
            renderMenuItems();
        }

        // Render menu items
        function renderMenuItems() {
            const grid = document.getElementById('menu-grid');
            grid.innerHTML = '';

            const filteredItems = menuItems.filter(item => {
                const matchesCategory = activeCategory === 'all' || item.category === activeCategory;
                const matchesSearch = item.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                                    item.description.toLowerCase().includes(searchTerm.toLowerCase());
                return matchesCategory && matchesSearch;
            });

            filteredItems.forEach(item => {
                const div = document.createElement('div');
                div.className = 'menu-card bg-white rounded-lg shadow-md p-4 hover:shadow-lg';
                div.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-800">${item.name}</h3>
                            <p class="text-gray-600 text-sm mt-1">${item.description}</p>
                        </div>
                        <span class="text-indigo-600 font-bold">$${item.price}</span>
                    </div>
                    <button
                        onclick="addToCart(${item.id})"
                        class="mt-4 w-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white py-2 rounded-lg hover:from-indigo-600 hover:to-purple-600"
                    >
                        Add to Order
                    </button>
                `;
                grid.appendChild(div);
            });
        }

        // Filter by category
        function filterByCategory(categoryId) {
            activeCategory = categoryId;
            updateCategoryButtons();
            renderMenuItems();
        }

        // Update category buttons
        function updateCategoryButtons() {
            const buttons = document.getElementById('categories').children;
            Array.from(buttons).forEach(button => {
                const isActive = button.textContent.trim().toLowerCase().includes(activeCategory);
                button.className = `flex items-center px-4 py-2 rounded-full whitespace-nowrap ${
                    isActive ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'
                }`;
            });
        }

        // Filter menu
        function filterMenu(value) {
            searchTerm = value;
            renderMenuItems();
        }

        // Cart functions
        function addToCart(itemId) {
            const item = menuItems.find(i => i.id === itemId);
            const existingItem = cart.find(i => i.id === itemId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({ ...item, quantity: 1 });
            }
            
            updateCart();
            showNotification(`Added ${item.name} to your order`);
        }

        function updateQuantity(itemId, delta) {
            const item = cart.find(i => i.id === itemId);
            if (item) {
                item.quantity = Math.max(0, item.quantity + delta);
                if (item.quantity === 0) {
                    cart = cart.filter(i => i.id !== itemId);
                }
                updateCart();
            }
        }

        function updateCart() {
            const cartItems = document.getElementById('cart-items');
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            cartItems.innerHTML = cart.map(item => `
                <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded-lg">
                    <div>
                        <h4 class="font-medium">${item.name}</h4>
                        <p class="text-sm text-gray-600">$${item.price} each</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="updateQuantity(${item.id}, -1)" class="px-2 py-1 bg-gray-100 rounded">-</button>
                        <span>${item.quantity}</span>
                        <button onclick="updateQuantity(${item.id}, 1)" class="px-2 py-1 bg-gray-100 rounded">+</button>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('cart-total').textContent = `$${total.toFixed(2)}`;
            document.getElementById('place-order-btn').disabled = cart.length === 0;
        }

        // Action functions
        function callWaiter() {
            showNotification('A waiter has been called and will be with you shortly!');
        }

        function checkBill() {
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            showNotification(`Your current bill is $${total.toFixed(2)}`);
        }

        function placeOrder() {
            showNotification('Your order has been sent to the kitchen!');
            cart = [];
            updateCart();
        }

        // Notification system
        function showNotification(message) {
            const notification = document.getElementById('notification');
            const notificationText = document.getElementById('notification-text');
            notificationText.textContent = message;
            notification.classList.add('show');
            setTimeout(() => notification.classList.remove('show'), 3000);
        }

        // Initialize
        initializeCategories();
        initializeMenu();
    </script>


<script>

const translations = {
    en: {
        header: {
            title: "Digital Menu",
            callWaiter: "Call Waiter",
            myBill: "My Bill"
        },
        search: {
            placeholder: "Search menu..."
        },
        categories: {
            allItems: "All Items",
            starters: "Starters",
            mainCourse: "Main Course",
            drinks: "Drinks",
            desserts: "Desserts"
        },
        cart: {
            yourOrder: "Your Order",
            total: "Total",
            placeOrder: "Place Order",
            addToOrder: "Add to Order",
            each: "each"
        },
        notifications: {
            waiterCalled: "A waiter has been called and will be with you shortly!",
            orderSent: "Your order has been sent to the kitchen!",
            currentBill: "Your current bill is",
            itemAdded: "Added {item} to your order"
        }
    },
    fr: {
        header: {
            title: "Menu Numérique",
            callWaiter: "Appeler Serveur",
            myBill: "Mon Addition"
        },
        search: {
            placeholder: "Rechercher dans le menu..."
        },
        categories: {
            allItems: "Tous les Plats",
            starters: "Entrées",
            mainCourse: "Plats Principaux",
            drinks: "Boissons",
            desserts: "Desserts"
        },
        cart: {
            yourOrder: "Votre Commande",
            total: "Total",
            placeOrder: "Commander",
            addToOrder: "Ajouter",
            each: "l'unité"
        },
        notifications: {
            waiterCalled: "Un serveur a été appelé et sera bientôt là !",
            orderSent: "Votre commande a été envoyée en cuisine !",
            currentBill: "Votre addition s'élève à",
            itemAdded: "{item} ajouté à votre commande"
        }
    },
    sw: {
        header: {
            title: "Menyu ya Dijitali",
            callWaiter: "Ita Muhudumu",
            myBill: "Bill Yangu"
        },
        search: {
            placeholder: "Tafuta kwenye menyu..."
        },
        categories: {
            allItems: "Vyakula Vyote",
            starters: "Vitafunio",
            mainCourse: "Chakula Kikuu",
            drinks: "Vinywaji",
            desserts: "Vitamu"
        },
        cart: {
            yourOrder: "Oda Yako",
            total: "Jumla",
            placeOrder: "Weka Oda",
            addToOrder: "Ongeza kwenye Oda",
            each: "kila moja"
        },
        notifications: {
            waiterCalled: "Muhudumu ameitwa na atakuja hivi karibuni!",
            orderSent: "Oda yako imetumwa jikoni!",
            currentBill: "Bill yako ya sasa ni",
            itemAdded: "{item} imeongezwa kwenye oda yako"
        }
    },
    kin: {
        header: {
            title: "Menu y'Ikoranabuhanga",
            callWaiter: "Hamagara Umukozi",
            myBill: "Fagitire Yanjye"
        },
        search: {
            placeholder: "Shakisha menu..."
        },
        categories: {
            allItems: "Ibintu Byose",
            starters: "Ibitangiza",
            mainCourse: "Ibiryo Bikuru",
            drinks: "Ibinyobwa",
            desserts: "Desserts"
        },
        cart: {
            yourOrder: "Commande Yawe",
            total: "Igiteranyo",
            placeOrder: "Ohereza Commande",
            addToOrder: "Ongeramo",
            each: "buri kimwe"
        },
        notifications: {
            waiterCalled: "Umukozi arahamagawe, aragera aho uri bidatinze!",
            orderSent: "Commande yawe yoherejwe mu gikoni!",
            currentBill: "Fagitire yawe ingana na",
            itemAdded: "{item} yongewemo kuri commande yawe"
        }
    }
};

// Menu item translations
const menuItemTranslations = {
    en: {
        "Caesar Salad": {
            name: "Caesar Salad",
            description: "Fresh romaine lettuce, croutons, parmesan"
        },
        "Margherita Pizza": {
            name: "Margherita Pizza",
            description: "Fresh tomatoes, mozzarella, basil"
        },
        "Grilled Salmon": {
            name: "Grilled Salmon",
            description: "Atlantic salmon with seasonal vegetables"
        },
        "Red Wine": {
            name: "Red Wine",
            description: "House red wine, 175ml"
        },
        "Tiramisu": {
            name: "Tiramisu",
            description: "Classic Italian dessert"
        },
        "Bruschetta": {
            name: "Bruschetta",
            description: "Toasted bread with tomatoes and garlic"
        }
    },
    fr: {
        "Caesar Salad": {
            name: "Salade César",
            description: "Laitue romaine fraîche, croûtons, parmesan"
        },
        "Margherita Pizza": {
            name: "Pizza Margherita",
            description: "Tomates fraîches, mozzarella, basilic"
        },
        "Grilled Salmon": {
            name: "Saumon Grillé",
            description: "Saumon atlantique avec légumes de saison"
        },
        "Red Wine": {
            name: "Vin Rouge",
            description: "Vin rouge maison, 175ml"
        },
        "Tiramisu": {
            name: "Tiramisu",
            description: "Dessert italien classique"
        },
        "Bruschetta": {
            name: "Bruschetta",
            description: "Pain grillé aux tomates et à l'ail"
        }
    },
    sw: {
        "Caesar Salad": {
            name: "Saladi ya Caesar",
            description: "Mboga za majani, krutoni, parmesan"
        },
        "Margherita Pizza": {
            name: "Piza ya Margherita",
            description: "Nyanya mbichi, mozzarella, basiliko"
        },
        "Grilled Salmon": {
            name: "Samaki wa Kuokwa",
            description: "Samaki wa Atlantic na mboga za msimu"
        },
        "Red Wine": {
            name: "Divai Nyekundu",
            description: "Divai nyekundu ya nyumbani, 175ml"
        },
        "Tiramisu": {
            name: "Tiramisu",
            description: "Kitamu cha Kiitaliano"
        },
        "Bruschetta": {
            name: "Bruschetta",
            description: "Mkate wa kuokwa na nyanya na kitunguu saumu"
        }
    },
    kin: {
        "Caesar Salad": {
            name: "Saladi ya Caesar",
            description: "Imboga, croutons na parmesan"
        },
        "Margherita Pizza": {
            name: "Pizza Margherita",
            description: "Inyanya, mozzarella na basilic"
        },
        "Grilled Salmon": {
            name: "Ifiriti y'Isambaza",
            description: "Isambaza rya Atlantic n'imboga z'igihe"
        },
        "Red Wine": {
            name: "Divayi Itukura",
            description: "Divayi y'inzu, 175ml"
        },
        "Tiramisu": {
            name: "Tiramisu",
            description: "Dessert y'Ubutaliyani"
        },
        "Bruschetta": {
            name: "Bruschetta",
            description: "Umugati wokeje n'inyanya n'utunyu"
        }
    }
};

function updateLanguage(lang) {
    // Update header content
    document.querySelector('h1').textContent = translations[lang].header.title;
    document.querySelector('button[onclick="callWaiter()"]').innerHTML = 
        `<i class="fas fa-bell mr-2"></i>${translations[lang].header.callWaiter}`;
    document.querySelector('button[onclick="checkBill()"]').innerHTML = 
        `<i class="fas fa-receipt mr-2"></i>${translations[lang].header.myBill}`;

    // Update search placeholder
    document.querySelector('input[type="text"]').placeholder = translations[lang].search.placeholder;

    // Update categories
    updateCategories(lang);

    // Update menu items
    updateMenuItems(lang);

    // Update cart
    updateCartTranslations(lang);
}

function updateCategories(lang) {
    const categoriesContainer = document.getElementById('categories');
    categoriesContainer.innerHTML = '';
    
    categories.forEach(category => {
        const button = document.createElement('button');
        button.className = `flex items-center px-4 py-2 rounded-full whitespace-nowrap ${
            category.id === activeCategory ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'
        }`;
        button.innerHTML = `
            <i class="fas ${category.icon} mr-2"></i>
            ${translations[lang].categories[category.id === 'all' ? 'allItems' : category.id]}
        `;
        button.onclick = () => filterByCategory(category.id);
        categoriesContainer.appendChild(button);
    });
}

function updateMenuItems(lang) {
    menuItems.forEach(item => {
        const translation = menuItemTranslations[lang][item.name];
        if (translation) {
            item.displayName = translation.name;
            item.displayDescription = translation.description;
        }
    });
    renderMenuItems();
}

function updateCartTranslations(lang) {
    document.querySelector('#cart-items').parentElement.querySelector('h2').textContent = 
        translations[lang].cart.yourOrder;
    document.querySelector('.font-bold.text-lg span').textContent = 
        translations[lang].cart.total;
    document.querySelector('#place-order-btn').textContent = 
        translations[lang].cart.placeOrder;
}

function showNotification(messageKey, params = {}) {
    const lang = document.getElementById('language-selector').value;
    let message = translations[lang].notifications[messageKey];
    
    // Replace parameters in message
    Object.keys(params).forEach(key => {
        message = message.replace(`{${key}}`, params[key]);
    });
    
    const notification = document.getElementById('notification');
    const notificationText = document.getElementById('notification-text');
    notificationText.textContent = message;
    notification.classList.add('show');
    setTimeout(() => notification.classList.remove('show'), 3000);
}

// Update the changeLanguage function
function changeLanguage() {
    const selectedLanguage = document.getElementById('language-selector').value;
    updateLanguage(selectedLanguage);
}

</script>

</body>
</html>