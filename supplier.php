<?php
session_start();
include 'config/database.php';
include 'includes/auth.php';
checkLogin();

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah'])) {
        $nama_supplier = $_POST['nama_supplier'];
        $alamat = $_POST['alamat'];
        $telepon = $_POST['telepon'];
        $email = $_POST['email'];
        
        $query = "INSERT INTO supplier (nama_supplier, alamat, telepon, email) VALUES ('$nama_supplier', '$alamat', '$telepon', '$email')";
        if (mysqli_query($conn, $query)) {
            // Mengubah ke notifikasi kuning
            $message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>Supplier berhasil ditambahkan!</div>";
        } else {
            $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
    
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $nama_supplier = $_POST['nama_supplier'];
        $alamat = $_POST['alamat'];
        $telepon = $_POST['telepon'];
        $email = $_POST['email'];
        
        $query = "UPDATE supplier SET nama_supplier='$nama_supplier', alamat='$alamat', telepon='$telepon', email='$email' WHERE id=$id";
        if (mysqli_query($conn, $query)) {
            // Mengubah ke notifikasi kuning
            $message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>Supplier berhasil diupdate!</div>";
            // Redirect to avoid resubmission - keep this
            header("Location: supplier.php");
            exit();
        } else {
            $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM supplier WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        // Mengubah ke notifikasi kuning
        $message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>Supplier berhasil dihapus!</div>";
    } else {
        $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . mysqli_error($conn) . "</div>";
    }
}

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM supplier WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($result);
}

// Get all suppliers
$suppliers = mysqli_query($conn, "SELECT * FROM supplier ORDER BY nama_supplier");

$title = "Supplier - HABIBI";
include 'includes/header.php';
?>

<!-- Pastikan semua konten supplier berada di dalam elemen <main> ini -->
<main class="flex-grow container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold flex items-center gap-2 text-gray-700">
            <i class="fas fa-truck text-yellow-500"></i> Kelola Supplier
        </h2>
        <!-- Mengubah kelas tombol tambah supplier -->
        <button onclick="openModal()" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded shadow flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Supplier
        </button>
    </div>

    <?= $message ?>

    <!-- Tabel Supplier -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b px-4 py-3">
            <span class="font-semibold text-gray-700">Daftar Supplier</span>
        </div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="py-3 px-4 font-semibold">No</th>
                            <th class="py-3 px-4 font-semibold">Nama Supplier</th>
                            <th class="py-3 px-4 font-semibold">Alamat</th>
                            <th class="py-3 px-4 font-semibold">Telepon</th>
                            <th class="py-3 px-4 font-semibold">Email</th>
                            <th class="py-3 px-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($suppliers)): 
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4"><?= $no++ ?></td>
                            <td class="py-3 px-4 font-medium"><?= htmlspecialchars($row['nama_supplier']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['alamat']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['telepon']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <!-- Mengubah kelas tombol Edit -->
                                    <button onclick="editSupplier(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nama_supplier'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['alamat'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['telepon'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>')"
                                            class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm flex items-center gap-1 transition-colors">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <!-- Mengubah kelas tombol Hapus -->
                                    <a href="?delete=<?= $row['id'] ?>" 
                                       onclick="return confirm('Yakin ingin menghapus supplier ini?')"
                                       class="bg-gray-400 hover:bg-gray-500 text-white px-3 py-1 rounded text-sm flex items-center gap-1 transition-colors">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Supplier -->
    <div id="modalSupplier" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="border-b px-6 py-4 flex justify-between items-center">
                    <h3 id="modalTitle" class="text-lg font-semibold text-gray-700">Tambah Supplier</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" class="p-6">
                    <input type="hidden" id="supplierId" name="id" value="">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="nama_supplier" class="block text-sm font-medium text-gray-700 mb-2">Nama Supplier</label>
                            <input type="text" id="namaSupplier" name="nama_supplier" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                        
                        <div>
                            <label for="telepon" class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                            <input type="text" id="teleponSupplier" name="telepon" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                        <textarea id="alamatSupplier" name="alamat" rows="3" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"></textarea>
                    </div>
                    
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="emailSupplier" name="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" 
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" id="submitBtn" name="tambah"
                                class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    let isEditMode = false;

    function openModal() {
        isEditMode = false;
        document.getElementById('modalTitle').textContent = 'Tambah Supplier';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save mr-1"></i> Simpan';
        document.getElementById('submitBtn').name = 'tambah';
        
        // Clear form
        document.getElementById('supplierId').value = '';
        document.getElementById('namaSupplier').value = '';
        document.getElementById('alamatSupplier').value = '';
        document.getElementById('teleponSupplier').value = '';
        document.getElementById('emailSupplier').value = '';
        
        document.getElementById('modalSupplier').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function editSupplier(id, nama, alamat, telepon, email) {
        isEditMode = true;
        document.getElementById('modalTitle').textContent = 'Edit Supplier';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save mr-1"></i> Update';
        document.getElementById('submitBtn').name = 'edit';
        
        // Fill form with data
        document.getElementById('supplierId').value = id;
        document.getElementById('namaSupplier').value = nama;
        document.getElementById('alamatSupplier').value = alamat;
        document.getElementById('teleponSupplier').value = telepon;
        document.getElementById('emailSupplier').value = email;
        
        document.getElementById('modalSupplier').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('modalSupplier').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('modalSupplier').addEventListener('click', function(e) {
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

    // Auto-open modal if edit parameter exists
    <?php if ($edit_data): ?>
    document.addEventListener('DOMContentLoaded', function() {
        editSupplier(
            <?= $edit_data['id'] ?>, 
            '<?= htmlspecialchars($edit_data['nama_supplier'], ENT_QUOTES) ?>', 
            '<?= htmlspecialchars($edit_data['alamat'], ENT_QUOTES) ?>', 
            '<?= htmlspecialchars($edit_data['telepon'], ENT_QUOTES) ?>', 
            '<?= htmlspecialchars($edit_data['email'], ENT_QUOTES) ?>'
        );
    });
    <?php endif; ?>
    </script>
</main>