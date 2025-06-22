<?php
session_start();
include 'config/database.php';
include 'includes/auth.php';
checkLogin();

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah'])) {
        $kode_barang = $_POST['kode_barang'];
        $nama_barang = $_POST['nama_barang'];
        $kategori_id = $_POST['kategori_id'];
        $supplier_id = $_POST['supplier_id'];
        $stok = $_POST['stok'];
        $stok_minimum = $_POST['stok_minimum'];
        $harga_beli = $_POST['harga_beli'];
        $harga_jual = $_POST['harga_jual'];
        $satuan = $_POST['satuan'];
        $deskripsi = $_POST['deskripsi'];
        
        $query = "INSERT INTO barang (kode_barang, nama_barang, kategori_id, supplier_id, stok, stok_minimum, harga_beli, harga_jual, satuan, deskripsi) 
                    VALUES ('$kode_barang', '$nama_barang', '$kategori_id', '$supplier_id', '$stok', '$stok_minimum', '$harga_beli', '$harga_jual', '$satuan', '$deskripsi')";
        if (mysqli_query($conn, $query)) {
            // Mengubah ke notifikasi kuning
            $message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>Barang berhasil ditambahkan!</div>";
        } else {
            $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
    
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $kode_barang = $_POST['kode_barang'];
        $nama_barang = $_POST['nama_barang'];
        $kategori_id = $_POST['kategori_id'];
        $supplier_id = $_POST['supplier_id'];
        $stok = $_POST['stok'];
        $stok_minimum = $_POST['stok_minimum'];
        $harga_beli = $_POST['harga_beli'];
        $harga_jual = $_POST['harga_jual'];
        $satuan = $_POST['satuan'];
        $deskripsi = $_POST['deskripsi'];
        
        $query = "UPDATE barang SET kode_barang='$kode_barang', nama_barang='$nama_barang', kategori_id='$kategori_id', 
                    supplier_id='$supplier_id', stok='$stok', stok_minimum='$stok_minimum', harga_beli='$harga_beli', harga_jual='$harga_jual', 
                    satuan='$satuan', deskripsi='$deskripsi' WHERE id=$id";
        if (mysqli_query($conn, $query)) {
            // Mengubah ke notifikasi kuning
            $message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>Barang berhasil diupdate!</div>";
        } else {
            $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM barang WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        // Mengubah ke notifikasi kuning
        $message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>Barang berhasil dihapus!</div>";
    } else {
        $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . mysqli_error($conn) . "</div>";
    }
}

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM barang WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($result);
}

// Get all items with category and supplier info
$barang = mysqli_query($conn, "
    SELECT b.*, k.nama_kategori, s.nama_supplier 
    FROM barang b 
    LEFT JOIN kategori k ON b.kategori_id = k.id 
    LEFT JOIN supplier s ON b.supplier_id = s.id 
    ORDER BY b.nama_barang
");

// Get categories and suppliers for dropdown
$categories = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori");
$suppliers = mysqli_query($conn, "SELECT * FROM supplier ORDER BY nama_supplier");

$title = "Barang - Sistem Inventaris";
include 'includes/header.php';
?>

<!-- Pastikan semua konten barang berada di dalam elemen <main> ini -->
<main class="flex-grow container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-box text-yellow-500 mr-3"></i>Kelola Barang
        </h2>
        <button onclick="openModal()" class="bg-gradient-to-r from-yellow-400 to-yellow-600 hover:from-yellow-500 hover:to-yellow-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg">
            <i class="fas fa-plus mr-2"></i>Tambah Barang
        </button>
    </div>

    <?= $message ?>

    <!-- Tabel Barang -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Min</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($barang)): 
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++ ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $row['kode_barang'] ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900"><?= $row['nama_barang'] ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600"><?= $row['nama_kategori'] ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600"><?= $row['nama_supplier'] ?></td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $row['stok'] < $row['stok_minimum'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' ?>">
                                    <?= $row['stok'] ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900"><?= $row['stok_minimum'] ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900"><?= $row['satuan'] ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="?edit=<?= $row['id'] ?>" class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 transition-colors duration-150 mr-2">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus barang ini?')" class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors duration-150">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Barang -->
    <div id="modalBarang" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-2xl bg-white">
            <div class="flex items-center justify-between p-4 border-b rounded-t">
                <h3 class="text-xl font-semibold text-gray-900">
                    <?= $edit_data ? 'Edit Barang' : 'Tambah Barang' ?>
                </h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                    <i class="fas fa-times w-5 h-5"></i>
                </button>
            </div>
            
            <form method="POST" class="p-6">
                <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="kode_barang" class="block text-sm font-medium text-gray-700 mb-2">Kode Barang</label>
                        <input type="text" name="kode_barang" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-colors duration-200" 
                                value="<?= $edit_data['kode_barang'] ?? '' ?>" required>
                    </div>
                    <div>
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-2">Nama Barang</label>
                        <input type="text" name="nama_barang" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-colors duration-200" 
                                value="<?= $edit_data['nama_barang'] ?? '' ?>" required>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="kategori_id" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select name="kategori_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-colors duration-200" required>
                            <option value="">Pilih Kategori</option>
                            <?php 
                            mysqli_data_seek($categories, 0);
                            while ($cat = mysqli_fetch_assoc($categories)): 
                            ?>
                            <option value="<?= $cat['id'] ?>" <?= ($edit_data && $edit_data['kategori_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= $cat['nama_kategori'] ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                        <select name="supplier_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-colors duration-200" required>
                            <option value="">Pilih Supplier</option>
                            <?php 
                            mysqli_data_seek($suppliers, 0);
                            while ($sup = mysqli_fetch_assoc($suppliers)): 
                            ?>
                            <option value="<?= $sup['id'] ?>" <?= ($edit_data && $edit_data['supplier_id'] == $sup['id']) ? 'selected' : '' ?>>
                                <?= $sup['nama_supplier'] ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                    <div>
                        <label for="stok" class="block text-sm font-medium text-gray-700 mb-2">Stok</label>
                        <input type="number" name="stok" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-colors duration-200" 
                                value="<?= $edit_data['stok'] ?? '0' ?>" required>
                    </div>
                    <div>
                        <label for="stok_minimum" class="block text-sm font-medium text-gray-700 mb-2">Stok Minimum</label>
                        <input type="number" name="stok_minimum" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-colors duration-200" 
                                value="<?= $edit_data['stok_minimum'] ?? '10' ?>" required>
                    </div>
                    <div>
                        <label for="harga_beli" class="block text-sm font-medium text-gray-700 mb-2">Harga Beli</label>
                        <input type="number" name="harga_beli" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-colors duration-200" 
                                value="<?= $edit_data['harga_beli'] ?? '' ?>" required>
                    </div>
                    <div>
                        <label for="harga_jual" class="block text-sm font-medium text-gray-700 mb-2">Harga Jual</label>
                        <input type="number" name="harga_jual" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-colors duration-200" 
                                value="<?= $edit_data['harga_jual'] ?? '' ?>" required>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                        <input type="text" name="satuan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-colors duration-200" 
                                value="<?= $edit_data['satuan'] ?? '' ?>" required>
                    </div>
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition-colors duration-200"><?= $edit_data['deskripsi'] ?? '' ?></textarea>
                    </div>
                </div>
                
                <div class="flex items-center justify-end p-6 border-t border-gray-200 rounded-b space-x-4">
                    <button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" name="<?= $edit_data ? 'edit' : 'tambah' ?>" class="px-6 py-3 text-white bg-gradient-to-r from-yellow-400 to-yellow-600 hover:from-yellow-500 hover:to-yellow-700 rounded-lg focus:ring-2 focus:ring-yellow-300 transition-all duration-200 transform hover:scale-105">
                        <?= $edit_data ? 'Update' : 'Simpan' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openModal() {
        document.getElementById('modalBarang').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modalBarang').classList.add('hidden');
    }

    <?php if ($edit_data): ?>
    document.addEventListener('DOMContentLoaded', function() {
        openModal();
    });
    <?php endif; ?>

    // Close modal when clicking outside
    document.getElementById('modalBarang').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    </script>
</main>