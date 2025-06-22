<?php
session_start();
include 'config/database.php';
include 'includes/auth.php';
checkLogin();

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah'])) {
        $kode_transaksi = 'TRX' . date('YmdHis');
        $barang_id = $_POST['barang_id'];
        $jenis_transaksi = $_POST['jenis_transaksi'];
        $jumlah = $_POST['jumlah'];
        $keterangan = $_POST['keterangan'];
        $tanggal_transaksi = $_POST['tanggal_transaksi'];
        $user_id = $_SESSION['user_id'];
        
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Insert transaksi
            $query = "INSERT INTO transaksi (kode_transaksi, barang_id, jenis_transaksi, jumlah, keterangan, tanggal_transaksi, user_id) 
                      VALUES ('$kode_transaksi', '$barang_id', '$jenis_transaksi', '$jumlah', '$keterangan', '$tanggal_transaksi', '$user_id')";
            mysqli_query($conn, $query);
            
            // Update stok barang
            if ($jenis_transaksi == 'masuk') {
                $query_stok = "UPDATE barang SET stok = stok + $jumlah WHERE id = $barang_id";
            } else {
                $query_stok = "UPDATE barang SET stok = stok - $jumlah WHERE id = $barang_id";
            }
            mysqli_query($conn, $query_stok);
            
            mysqli_commit($conn);
            // Mengubah ke notifikasi kuning
            $message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>Transaksi berhasil ditambahkan dan stok telah diupdate!</div>";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . $e->getMessage() . "</div>";
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get transaksi data first
    $result = mysqli_query($conn, "SELECT * FROM transaksi WHERE id = $id");
    $transaksi = mysqli_fetch_assoc($result);
    
    if ($transaksi) {
        mysqli_begin_transaction($conn);
        
        try {
            // Reverse stok update
            if ($transaksi['jenis_transaksi'] == 'masuk') {
                $query_stok = "UPDATE barang SET stok = stok - {$transaksi['jumlah']} WHERE id = {$transaksi['barang_id']}";
            } else {
                $query_stok = "UPDATE barang SET stok = stok + {$transaksi['jumlah']} WHERE id = {$transaksi['barang_id']}";
            }
            mysqli_query($conn, $query_stok);
            
            // Delete transaksi
            $query = "DELETE FROM transaksi WHERE id = $id";
            mysqli_query($conn, $query);
            
            mysqli_commit($conn);
            // Mengubah ke notifikasi kuning
            $message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>Transaksi berhasil dihapus dan stok telah dikembalikan!</div>";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . $e->getMessage() . "</div>";
        }
    }
}

// Get all transactions with item and user info
$transaksi = mysqli_query($conn, "
    SELECT t.*, b.nama_barang, b.satuan, u.nama_lengkap 
    FROM transaksi t 
    JOIN barang b ON t.barang_id = b.id 
    JOIN users u ON t.user_id = u.id 
    ORDER BY t.created_at DESC
");

// Get items for dropdown
$barang = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama_barang");

$title = "Transaksi - HABIBI";
include 'includes/header.php';
?>

<!-- Pastikan semua konten transaksi berada di dalam elemen <main> ini -->
<main class="flex-grow container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold flex items-center gap-2 text-gray-700">
            <i class="fas fa-exchange-alt text-yellow-500"></i> Kelola Transaksi
        </h2>
        <!-- Mengubah kelas tombol tambah transaksi -->
        <button onclick="openModal()" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded shadow flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Transaksi
        </button>
    </div>

    <?= $message ?>

    <!-- Tabel Transaksi -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b px-4 py-3">
            <span class="font-semibold text-gray-700">Daftar Transaksi</span>
        </div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="py-3 px-4 font-semibold">No</th>
                            <th class="py-3 px-4 font-semibold">Kode Transaksi</th>
                            <th class="py-3 px-4 font-semibold">Barang</th>
                            <th class="py-3 px-4 font-semibold">Jenis</th>
                            <th class="py-3 px-4 font-semibold">Jumlah</th>
                            <th class="py-3 px-4 font-semibold">Tanggal</th>
                            <th class="py-3 px-4 font-semibold">User</th>
                            <th class="py-3 px-4 font-semibold">Keterangan</th>
                            <th class="py-3 px-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($transaksi)): 
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4"><?= $no++ ?></td>
                            <td class="py-3 px-4 font-mono text-xs"><?= htmlspecialchars($row['kode_transaksi']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['nama_barang']) ?></td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                                    <?= $row['jenis_transaksi'] == 'masuk' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-200 text-gray-700' ?>">
                                    <?= ucfirst(htmlspecialchars($row['jenis_transaksi'])) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['jumlah']) ?> <?= htmlspecialchars($row['satuan']) ?></td>
                            <td class="py-3 px-4"><?= date('d/m/Y', strtotime($row['tanggal_transaksi'])) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['keterangan']) ?></td>
                            <td class="py-3 px-4">
                                <a href="?delete=<?= $row['id'] ?>" 
                                   onclick="return confirm('Yakin ingin menghapus transaksi ini? Stok akan dikembalikan.')"
                                   class="bg-gray-400 hover:bg-gray-500 text-white px-3 py-1 rounded text-sm flex items-center gap-1 w-fit transition-colors">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Transaksi -->
    <div id="modalTransaksi" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="border-b px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-700">Tambah Transaksi</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="barang_id" class="block text-sm font-medium text-gray-700 mb-2">Barang</label>
                            <select name="barang_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                                <option value="">Pilih Barang</option>
                                <?php 
                                mysqli_data_seek($barang, 0);
                                while ($item = mysqli_fetch_assoc($barang)): 
                                ?>
                                <option value="<?= $item['id'] ?>">
                                    <?= htmlspecialchars($item['nama_barang']) ?> (Stok: <?= $item['stok'] ?> <?= htmlspecialchars($item['satuan']) ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="jenis_transaksi" class="block text-sm font-medium text-gray-700 mb-2">Jenis Transaksi</label>
                            <select name="jenis_transaksi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                                <option value="">Pilih Jenis</option>
                                <option value="masuk">Barang Masuk</option>
                                <option value="keluar">Barang Keluar</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                            <input type="number" name="jumlah" min="1" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                        
                        <div>
                            <label for="tanggal_transaksi" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Transaksi</label>
                            <input type="date" name="tanggal_transaksi" value="<?= date('Y-m-d') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" rows="3" placeholder="Keterangan transaksi..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" 
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" name="tambah"
                                class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openModal() {
        document.getElementById('modalTransaksi').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('modalTransaksi').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('modalTransaksi').addEventListener('click', function(e) {
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
    </script>
</main>