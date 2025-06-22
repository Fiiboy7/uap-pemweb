<?php
session_start();
include 'config/database.php';
include 'includes/auth.php';
checkLogin();
checkAdmin(); // Only admin can access this page

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $nama_lengkap = $_POST['nama_lengkap'];
        $role = $_POST['role'];
        
        $query = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password', '$nama_lengkap', '$role')";
        if (mysqli_query($conn, $query)) {
            // Mengubah ke notifikasi kuning
            $message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>User berhasil ditambahkan!</div>";
        } else {
            $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
    
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $nama_lengkap = $_POST['nama_lengkap'];
        $role = $_POST['role'];
        
        $query = "UPDATE users SET username='$username', nama_lengkap='$nama_lengkap', role='$role' WHERE id=$id";
        
        // Update password if provided
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $query = "UPDATE users SET username='$username', password='$password', nama_lengkap='$nama_lengkap', role='$role' WHERE id=$id";
        }
        
        if (mysqli_query($conn, $query)) {
            // Mengubah ke notifikasi kuning
            $message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>User berhasil diupdate!</div>";
        } else {
            $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Prevent deleting current user
    if ($id == $_SESSION['user_id']) {
        $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Tidak dapat menghapus user yang sedang login!</div>";
    } else {
        $query = "DELETE FROM users WHERE id=$id";
        if (mysqli_query($conn, $query)) {
            // Mengubah ke notifikasi kuning
            $message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>User berhasil dihapus!</div>";
        } else {
            $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($result);
}

// Get all users
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY nama_lengkap");

$title = "Users - HABIBI";
include 'includes/header.php';
?>

<!-- Pastikan semua konten user berada di dalam elemen <main> ini -->
<main class="flex-grow container mx-auto px-4 py-8 max-w-7xl">
    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-gray-700">
        <i class="fas fa-users text-yellow-500"></i> Kelola Users
    </h2>

    <?= $message ?>

    <!-- Tombol Tambah User -->
    <div class="mb-6">
        <button onclick="openModal()" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg shadow flex items-center gap-2 transition-colors">
            <i class="fas fa-plus"></i> Tambah User
        </button>
    </div>

    <!-- Tabel Users -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b px-4 py-3 flex items-center gap-2">
            <i class="fas fa-list text-yellow-500"></i>
            <span class="font-semibold text-gray-700">Daftar Users</span>
        </div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="py-3 px-4 font-semibold">No</th>
                            <th class="py-3 px-4 font-semibold">Username</th>
                            <th class="py-3 px-4 font-semibold">Nama Lengkap</th>
                            <th class="py-3 px-4 font-semibold">Role</th>
                            <th class="py-3 px-4 font-semibold">Tanggal Dibuat</th>
                            <th class="py-3 px-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($users)): 
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4"><?= $no++ ?></td>
                            <td class="py-3 px-4 font-medium"><?= htmlspecialchars($row['username']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2 py-1 rounded text-xs font-semibold <?= $row['role'] == 'admin' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                    <?= ucfirst(htmlspecialchars($row['role'])) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <button onclick="editUser(<?= htmlspecialchars(json_encode($row)) ?>)" 
                                            class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm flex items-center gap-1 transition-colors">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                    <a href="?delete=<?= $row['id'] ?>" 
                                       onclick="return confirm('Yakin ingin menghapus user ini?')"
                                       class="bg-gray-400 hover:bg-gray-500 text-white px-3 py-1 rounded text-sm flex items-center gap-1 transition-colors">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit User -->
    <div id="modalUser" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
            <div class="border-b px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-700" id="modalTitle">Tambah User</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" id="userForm">
                <div class="p-6">
                    <input type="hidden" name="id" id="userId">
                    
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="username" id="username" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" 
                                required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span id="passwordNote" class="text-gray-500 text-xs"></span>
                        </label>
                        <input type="password" name="password" id="password" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>
                    
                    <div class="mb-4">
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" 
                                required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select name="role" id="role" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" 
                                required>
                            <option value="">Pilih Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </div>
                
                <div class="border-t px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" name="tambah" id="submitBtn" 
                            class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    let isEditMode = false;

    function openModal() {
        document.getElementById('modalUser').classList.remove('hidden');
        document.getElementById('modalUser').classList.add('flex');
        document.getElementById('modalTitle').textContent = 'Tambah User';
        document.getElementById('userForm').reset();
        document.getElementById('submitBtn').name = 'tambah';
        document.getElementById('submitBtn').textContent = 'Simpan';
        document.getElementById('passwordNote').textContent = '';
        document.getElementById('password').required = true;
        isEditMode = false;
    }

    function editUser(userData) {
        document.getElementById('modalUser').classList.remove('hidden');
        document.getElementById('modalUser').classList.add('flex');
        document.getElementById('modalTitle').textContent = 'Edit User';
        
        document.getElementById('userId').value = userData.id;
        document.getElementById('username').value = userData.username;
        document.getElementById('nama_lengkap').value = userData.nama_lengkap;
        document.getElementById('role').value = userData.role;
        document.getElementById('password').value = '';
        
        document.getElementById('submitBtn').name = 'edit';
        document.getElementById('submitBtn').textContent = 'Update';
        document.getElementById('passwordNote').textContent = '(Kosongkan jika tidak ingin mengubah)';
        document.getElementById('password').required = false;
        isEditMode = true;
    }

    function closeModal() {
        document.getElementById('modalUser').classList.add('hidden');
        document.getElementById('modalUser').classList.remove('flex');
    }

    // Close modal when clicking outside
    document.getElementById('modalUser').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    // Show modal if editing from URL parameter
    <?php if ($edit_data): ?>
    document.addEventListener('DOMContentLoaded', function() {
        editUser(<?= json_encode($edit_data) ?>);
    });
    <?php endif; ?>
    </script>
</main>