<?php
session_start();
include 'config/database.php';
include 'includes/auth.php';
checkLogin();

// Statistik dashboard
$stats = [];

// Total barang
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang");
$stats['total_barang'] = mysqli_fetch_assoc($result)['total'];

// Total kategori
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori");
$stats['total_kategori'] = mysqli_fetch_assoc($result)['total'];

// Total supplier
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM supplier");
$stats['total_supplier'] = mysqli_fetch_assoc($result)['total'];

// Total transaksi hari ini
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()");
$stats['transaksi_hari_ini'] = mysqli_fetch_assoc($result)['total'];

// Barang dengan stok rendah (di bawah stok minimum)
$barang_stok_rendah = mysqli_query($conn, "SELECT * FROM barang WHERE stok < stok_minimum ORDER BY stok ASC LIMIT 5");

// Transaksi terbaru
$transaksi_terbaru = mysqli_query($conn, "
    SELECT t.*, b.nama_barang, u.nama_lengkap 
    FROM transaksi t 
    JOIN barang b ON t.barang_id = b.id 
    JOIN users u ON t.user_id = u.id 
    ORDER BY t.created_at DESC 
    LIMIT 5
");

$title = "Dashboard - HABIBI";
// Sertakan header.php yang sudah kita perbarui sebelumnya
include 'includes/header.php';
?>

<!-- Pastikan semua konten dashboard berada di dalam elemen <main> ini -->
<main class="flex-grow container mx-auto px-4 py-8 max-w-7xl">
    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-gray-700">
        <i class="fas fa-home text-yellow-500"></i> Dashboard
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-100 rounded-lg shadow p-4 flex items-center justify-between">
            <div>
                <div class="text-2xl font-bold text-gray-800"><?= $stats['total_barang'] ?></div>
                <div class="text-gray-500">Total Barang</div>
            </div>
            <i class="fas fa-box fa-2x text-yellow-500"></i>
        </div>
        <div class="bg-gray-100 rounded-lg shadow p-4 flex items-center justify-between">
            <div>
                <div class="text-2xl font-bold text-gray-800"><?= $stats['total_kategori'] ?></div>
                <div class="text-gray-500">Total Kategori</div>
            </div>
            <i class="fas fa-tags fa-2x text-yellow-500"></i>
        </div>
        <div class="bg-gray-100 rounded-lg shadow p-4 flex items-center justify-between">
            <div>
                <div class="text-2xl font-bold text-gray-800"><?= $stats['total_supplier'] ?></div>
                <div class="text-gray-500">Total Supplier</div>
            </div>
            <i class="fas fa-truck fa-2x text-yellow-500"></i>
        </div>
        <div class="bg-gray-100 rounded-lg shadow p-4 flex items-center justify-between">
            <div>
                <div class="text-2xl font-bold text-gray-800"><?= $stats['transaksi_hari_ini'] ?></div>
                <div class="text-gray-500">Transaksi Hari Ini</div>
            </div>
            <i class="fas fa-exchange-alt fa-2x text-yellow-500"></i>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow">
            <div class="border-b px-4 py-3 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                <span class="font-semibold text-gray-700">Barang Stok Rendah</span>
            </div>
            <div class="p-4">
                <?php if (mysqli_num_rows($barang_stok_rendah) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="py-2 px-3 font-semibold">Nama Barang</th>
                                <th class="py-2 px-3 font-semibold">Stok</th>
                                <th class="py-2 px-3 font-semibold">Stok Min</th>
                                <th class="py-2 px-3 font-semibold">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($barang_stok_rendah)): ?>
                            <tr class="border-b">
                                <td class="py-2 px-3"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td class="py-2 px-3">
                                    <span class="inline-block px-2 py-1 rounded bg-yellow-100 text-yellow-700 font-semibold"><?= htmlspecialchars($row['stok']) ?></span>
                                </td>
                                <td class="py-2 px-3"><?= htmlspecialchars($row['stok_minimum']) ?></td>
                                <td class="py-2 px-3"><?= htmlspecialchars($row['satuan']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-gray-400">Tidak ada barang dengan stok di bawah minimum.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow">
            <div class="border-b px-4 py-3 flex items-center gap-2">
                <i class="fas fa-clock text-yellow-500"></i>
                <span class="font-semibold text-gray-700">Transaksi Terbaru</span>
            </div>
            <div class="p-4">
                <?php if (mysqli_num_rows($transaksi_terbaru) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="py-2 px-3 font-semibold">Barang</th>
                                <th class="py-2 px-3 font-semibold">Jenis</th>
                                <th class="py-2 px-3 font-semibold">Jumlah</th>
                                <th class="py-2 px-3 font-semibold">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($transaksi_terbaru)): ?>
                            <tr class="border-b">
                                <td class="py-2 px-3"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td class="py-2 px-3">
                                    <span class="inline-block px-2 py-1 rounded 
                                        <?= $row['jenis_transaksi'] == 'masuk' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-200 text-gray-700' ?>">
                                        <?= ucfirst(htmlspecialchars($row['jenis_transaksi'])) ?>
                                    </span>
                                </td>
                                <td class="py-2 px-3"><?= htmlspecialchars($row['jumlah']) ?></td>
                                <td class="py-2 px-3"><?= date('d/m/Y', strtotime($row['tanggal_transaksi'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-gray-400">Belum ada transaksi.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>