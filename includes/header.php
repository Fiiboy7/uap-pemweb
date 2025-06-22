<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'HABIBI Inventaris' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fefce8',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex flex-col bg-gray-50">
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav class="bg-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Tambahkan items-center di sini untuk menyelaraskan konten navbar secara vertikal -->
            <div class="flex justify-between items-center h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="dashboard.php" class="flex items-center text-white font-bold text-xl">
                            <i class="fas fa-boxes text-yellow-400 mr-2"></i>
                            HABIBI
                        </a>
                    </div>
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <a href="dashboard.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-home mr-2"></i>Dashboard
                        </a>
                        <a href="barang.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-box mr-2"></i>Barang
                        </a>
                        <a href="kategori.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-tags mr-2"></i>Kategori
                        </a>
                        <a href="supplier.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-truck mr-2"></i>Supplier
                        </a>
                        <a href="transaksi.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-exchange-alt mr-2"></i>Transaksi
                        </a>
                        <a href="laporan.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-chart-bar mr-2"></i>Laporan
                        </a>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="users.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-users mr-2"></i>Users
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- User dropdown -->
                <div class="hidden md:ml-4 md:flex-shrink-0 md:flex md:items-center">
                    <div class="ml-3 relative">
                        <div class="dropdown relative">
                            <button class="bg-gray-800 flex text-sm rounded-full text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 focus:ring-offset-gray-800 dropdown-toggle" id="user-menu-button" onclick="toggleDropdown()">
                                <i class="fas fa-user mr-2"></i>
                                <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>
                                <i class="fas fa-chevron-down ml-2"></i>
                            </button>
                            <div class="dropdown-menu absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden" id="dropdown-menu">
                                <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-600">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-yellow-400 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-yellow-400" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-gray-700">
                <a href="dashboard.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-600 block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-home mr-2"></i>Dashboard
                </a>
                <a href="barang.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-600 block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-box mr-2"></i>Barang
                </a>
                <a href="kategori.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-600 block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-tags mr-2"></i>Kategori
                </a>
                <a href="supplier.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-600 block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-truck mr-2"></i>Supplier
                </a>
                <a href="transaksi.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-600 block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-exchange-alt mr-2"></i>Transaksi
                </a>
                <a href="laporan.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-600 block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-bar mr-2"></i>Laporan
                </a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="users.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-600 block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-users mr-2"></i>Users
                </a>
                <?php endif; ?>
                <div class="border-t border-gray-600 pt-4">
                    <a href="logout.php" class="text-gray-300 hover:text-yellow-400 hover:bg-gray-600 block px-3 py-2 rounded-md text-base font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown-menu');
            dropdown.classList.toggle('hidden');
        }
        
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdown-menu');
            const button = document.getElementById('user-menu-button');
            
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    <?php endif; ?>