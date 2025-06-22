<?php
session_start();
include 'config/database.php';

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}

$title = "Login - Sistem Inventaris";
// Pastikan header.php sudah diatur dengan body class yang flexbox untuk sticky footer
include 'includes/header.php';
?>

<!-- Konten utama halaman login dibungkus dalam <main> -->
<main class="flex-grow container mx-auto px-4 py-8 max-w-7xl flex items-center justify-center min-h-[calc(100vh-theme(spacing.16))]">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <h3 class="text-center text-2xl font-bold text-gray-700 mb-6 flex items-center justify-center gap-2">
                <i class="fas fa-boxes text-yellow-500"></i> Login Sistem Inventaris
            </h3>
            
            <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg">
                <?= $error ?>
            </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" name="username" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors duration-200" id="username" required>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors duration-200" id="password" required>
                </div>
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded shadow flex items-center justify-center gap-2 w-full transition-colors">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
        </div>
    </div>
</main>