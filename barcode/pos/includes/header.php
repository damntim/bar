<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        #language-selector {
    cursor: pointer;
    outline: none;
    transition: all 0.3s ease;
}

#language-selector:hover {
    border-color: #6366f1;
    box-shadow: 0 0 5px rgba(99, 102, 241, 0.5);
}

        
        @media (max-width: 768px) {
            .cart-panel {
                position: fixed;
                top: 64px;
                right: 0;
                bottom: 0;
                width: 100%;
                max-width: 100%;
                z-index: 50;
            }
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
    
                <nav class="hidden md:flex space-x-8">
                    <button class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">
                        Sales
                    </button>
                    <button class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">
                        Inventory
                    </button>
                    <button class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">
                        Reports
                    </button>
                </nav>
    
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center text-indigo-600">
                        <i class="fas fa-clock"></i>
                        <span class="ml-2 text-sm" id="current-time">00:00:00</span>
                    </div>
    
                    <button onclick="toggleCart()" class="p-2 rounded-md hover:bg-indigo-50 relative">
                        <i class="fas fa-shopping-cart text-indigo-600"></i>
                        <span id="cart-count" class="absolute -top-1 -right-1 bg-pink-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                    </button>
    
                    <button class="p-2 rounded-md hover:bg-indigo-50">
                        <i class="fas fa-user text-indigo-600"></i>
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
    
        <div id="mobile-menu" class="md:hidden menu-closed">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <button class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50">
                    Sales
                </button>
                <button class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50">
                    Inventory
                </button>
                <button class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50">
                    Reports
                </button>
            </div>
        </div>
    </header>