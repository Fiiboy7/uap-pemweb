<?php
session_start();
include 'config/database.php';
include 'includes/auth.php';
checkLogin();

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah'])) {
        $nama_kategori = $_POST['nama_kategori'];
        $deskripsi = $_POST['deskripsi'];
        
        $query = "INSERT INTO kategori (nama_kategori, deskripsi) VALUES ('$nama_kategori', '$deskripsi')";
        if (mysqli_query($conn, $query)) {
            $message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>Kategori berhasil ditambahkan!</div>";
        } else {
            $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
    
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $nama_kategori = $_POST['nama_kategori'];
        $deskripsi = $_POST['deskripsi'];
        
        $query = "UPDATE kategori SET nama_kategori='$nama_kategori', deskripsi='$deskripsi' WHERE id=$id";
        if (mysqli_query($conn, $query)) {
            $message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>Kategori berhasil diupdate!</div>";
        } else {
            $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM kategori WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        $message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>Kategori berhasil dihapus!</div>";
    } else {
        $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . mysqli_error($conn) . "</div>";
    }
}

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM kategori WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($result);
}

// Get all categories
$categories = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori");

$title = "Kategori - Sistem Inventaris";
include 'includes/header.php';
?>

<!-- Pastikan semua konten kategori berada di dalam elemen <main> ini -->
<main class="flex-grow container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-700 flex items-center gap-2">
            <i class="fas fa-tags text-yellow-500"></i> Kelola Kategori
        </h2>
        <button 
            class="mt-4 sm:mt-0 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded shadow flex items-center gap-2"
            onclick="document.getElementById('modalKategori').classList.remove('hidden')"
        >
            <i class="fas fa-plus"></i> Tambah Kategori
        </button>
    </div>

    <?= $message ?>

    <!-- Tabel Kategori -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Dibuat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($categories)): 
                    ?>
                    <tr>
                        <td class="px-4 py-2"><?= $no++ ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['nama_kategori']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['deskripsi']) ?></td>
                        <td class="px-4 py-2"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="?edit=<?= $row['id'] ?>" 
                                class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm flex items-center gap-1">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?delete=<?= $row['id'] ?>" 
                                class="bg-gray-400 hover:bg-gray-500 text-white px-3 py-1 rounded text-sm flex items-center gap-1"
                                onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah/Edit Kategori -->
    <div id="modalKategori" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 <?= $edit_data ? '' : 'hidden' ?>">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h5 class="text-lg font-semibold text-gray-700">
                    <?= $edit_data ? 'Edit Kategori' : 'Tambah Kategori' ?>
                </h5>
                <button type="button" class="text-gray-400 hover:text-gray-700 text-2xl font-bold"
                    onclick="document.getElementById('modalKategori').classList.add('hidden')">&times;</button>
            </div>
            <form method="POST" class="px-6 py-4">
                <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                <?php endif; ?>
                
                <div class="mb-4">
                    <label for="nama_kategori" class="block text-gray-700 font-medium mb-1">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400" 
                            value="<?= htmlspecialchars($edit_data['nama_kategori'] ?? '') ?>" required>
                </div>
                <div class="mb-4">
                    <label for="deskripsi" class="block text-gray-700 font-medium mb-1">Deskripsi</label>
                    <textarea name="deskripsi" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400" rows="3"><?= htmlspecialchars($edit_data['deskripsi'] ?? '') ?></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded" 
                        onclick="document.getElementById('modalKategori').classList.add('hidden')">Batal</button>
                    <button type="submit" name="<?= $edit_data ? 'edit' : 'tambah' ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded font-semibold">
                        <?= $edit_data ? 'Update' : 'Simpan' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($edit_data): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('modalKategori').classList.remove('hidden');
    });
    </script>
    <?php endif; ?>
</main>