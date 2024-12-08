<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
          /* Ensure main content is below header */
       

        /* Adjust sidebar top position */
        #sidebar {
            top: 64px; /* Matches header height */
            height: calc(100vh - 64px); /* Full height minus header */

        } 
        
  


        @media (max-width: 1023px) {
            .sidebar-closed {
                left: -100%;
            }
            .sidebar-open {
                left: 0;
            }
        }
    </style>
</head>
<body class="bg-gray-50 no-scrollbar">
    <!-- Mobile Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-20 hidden lg:hidden"></div>

    <!-- Header -->
    <header class="bg-white shadow-sm fixed w-full z-30">
        <div class="flex items-center justify-between px-4 py-3">
            <!-- Logo and Toggle -->
            <div class="flex items-center">
                <button id="sidebarToggle" class="text-gray-600 hover:text-gray-800 lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <span class="text-xl font-bold text-purple-600 ml-3">RestaurantPro</span>
            </div>
     
            <!-- Search and Actions -->
            <div class="flex items-center space-x-4">
                <!-- Mobile Menu Button -->
                <button id="mobileMenuToggle" class="lg:hidden text-gray-600">
                    <i class="fas fa-ellipsis-v"></i>
                </button>

                <!-- Desktop Menu Items -->
                <div id="desktopMenu" class="hidden lg:flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="Search..." class="w-64 pl-10 pr-4 py-2 rounded-lg border focus:outline-none focus:border-purple-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    
                    <!-- Language Selector -->
                    <select class="border rounded-lg px-3 py-2">
                        <option>EN</option>
                        <option>ES</option>
                        <option>FR</option>
                    </select>
                    
                    <!-- Notifications -->
                    <div class="relative group">
                        <button class="relative">
                            <i class="fas fa-bell text-gray-600 text-xl"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 text-xs flex items-center justify-center">3</span>
                        </button>
                        <!-- Notifications Dropdown -->
                        <div class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg hidden group-hover:block">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold mb-2">Notifications</h3>
                                <div class="space-y-3">
                                    <div class="flex items-start space-x-3">
                                        <div class="p-2 bg-blue-100 rounded-full">
                                            <i class="fas fa-shopping-cart text-blue-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium">New order received</p>
                                            <p class="text-xs text-gray-500">2 minutes ago</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div class="p-2 bg-red-100 rounded-full">
                                            <i class="fas fa-exclamation-circle text-red-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium">Low stock alert</p>
                                            <p class="text-xs text-gray-500">15 minutes ago</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile -->
                    <div class="relative group">
                        <div class="flex items-center space-x-2 cursor-pointer">
                            <img src="/api/placeholder/32/32" alt="Profile" class="w-8 h-8 rounded-full">
                            <span class="hidden md:inline text-gray-700">John Doe</span>
                        </div>
                        <!-- Profile Dropdown -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden group-hover:block">
                            <div class="py-1">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="lg:hidden hidden bg-white border-t">
            <div class="p-4 space-y-4">
                <div class="relative">
                    <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 rounded-lg border focus:outline-none focus:border-purple-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                <select class="w-full border rounded-lg px-3 py-2">
                    <option>EN</option>
                    <option>ES</option>
                    <option>FR</option>
                </select>
            </div>
        </div>
        
    </header>
    
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('overlay');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');

        // Toggle Sidebar
        function toggleSidebar() {
            if (window.innerWidth < 1024) {
                sidebar.classList.toggle('sidebar-closed');
                sidebar.classList.toggle('sidebar-open');
                overlay.classList.toggle('hidden');
                
                // Prevent body scroll when sidebar is open
                document.body.style.overflow = sidebar.classList.contains('sidebar-open') ? 'hidden' : '';
            }
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

        // Mobile Menu Toggle
        mobileMenuToggle?.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                // Reset everything for desktop view
                mobileMenu?.classList.add('hidden');
                overlay.classList.add('hidden');
                sidebar.classList.remove('sidebar-open', 'sidebar-closed');
                document.body.style.overflow = '';
            } else {
                // Reset to closed state on mobile if not already closed
                if (!sidebar.classList.contains('sidebar-closed')) {
                    sidebar.classList.add('sidebar-closed');
                    sidebar.classList.remove('sidebar-open');
                }
            }
        });

        // Initialize correct state on load
        window.dispatchEvent(new Event('resize'));



        
    </script>