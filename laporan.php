<?php
session_start();
include 'config/database.php';
include 'includes/auth.php';
checkLogin();

// Filter parameters
$tanggal_dari = $_GET['tanggal_dari'] ?? date('Y-m-01');
$tanggal_sampai = $_GET['tanggal_sampai'] ?? date('Y-m-d');
$jenis_transaksi = $_GET['jenis_transaksi'] ?? '';

// Build query
$where_conditions = [];
$where_conditions[] = "DATE(t.tanggal_transaksi) BETWEEN '$tanggal_dari' AND '$tanggal_sampai'";

if ($jenis_transaksi) {
    $where_conditions[] = "t.jenis_transaksi = '$jenis_transaksi'";
}

$where_clause = implode(' AND ', $where_conditions);

// Get transaction report
$laporan = mysqli_query($conn, "
    SELECT t.*, b.nama_barang, b.satuan, u.nama_lengkap, k.nama_kategori, s.nama_supplier
    FROM transaksi t 
    JOIN barang b ON t.barang_id = b.id 
    JOIN users u ON t.user_id = u.id 
    LEFT JOIN kategori k ON b.kategori_id = k.id
    LEFT JOIN supplier s ON b.supplier_id = s.id
    WHERE $where_clause
    ORDER BY t.tanggal_transaksi DESC, t.created_at DESC
");

// Get summary statistics
$stats_masuk = mysqli_query($conn, "
    SELECT COUNT(*) as total_transaksi, SUM(jumlah) as total_jumlah
    FROM transaksi t 
    WHERE $where_clause AND t.jenis_transaksi = 'masuk'
");
$data_masuk = mysqli_fetch_assoc($stats_masuk);

$stats_keluar = mysqli_query($conn, "
    SELECT COUNT(*) as total_transaksi, SUM(jumlah) as total_jumlah
    FROM transaksi t 
    WHERE $where_clause AND t.jenis_transaksi = 'keluar'
");
$data_keluar = mysqli_fetch_assoc($stats_keluar);

$title = "Laporan - HABIBI";
include 'includes/header.php';
?>

<!-- Pastikan semua konten laporan berada di dalam elemen <main> ini -->
<main class="flex-grow container mx-auto px-4 py-8 max-w-7xl">
    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-gray-700">
        <i class="fas fa-chart-bar text-yellow-500"></i> Laporan Transaksi
    </h2>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b px-4 py-3 flex items-center gap-2">
            <i class="fas fa-filter text-yellow-500"></i>
            <span class="font-semibold text-gray-700">Filter Laporan</span>
        </div>
        <div class="p-4">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="tanggal_dari" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" 
                            value="<?= $tanggal_dari ?>">
                </div>
                <div>
                    <label for="tanggal_sampai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" 
                            value="<?= $tanggal_sampai ?>">
                </div>
                <div>
                    <label for="jenis_transaksi" class="block text-sm font-medium text-gray-700 mb-2">Jenis Transaksi</label>
                    <select name="jenis_transaksi" 
                             class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        <option value="">Semua</option>
                        <option value="masuk" <?= $jenis_transaksi == 'masuk' ? 'selected' : '' ?>>Barang Masuk</option>
                        <option value="keluar" <?= $jenis_transaksi == 'keluar' ? 'selected' : '' ?>>Barang Keluar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <button type="button" onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-green-500 text-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold"><?= $data_masuk['total_transaksi'] ?? 0 ?></div>
                    <div class="text-green-100">Total Transaksi Masuk</div>
                    <div class="text-sm text-green-100 mt-1">Total Item: <?= $data_masuk['total_jumlah'] ?? 0 ?></div>
                </div>
                <i class="fas fa-arrow-down fa-2x text-green-200"></i>
            </div>
        </div>
        <div class="bg-red-500 text-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold"><?= $data_keluar['total_transaksi'] ?? 0 ?></div>
                    <div class="text-red-100">Total Transaksi Keluar</div>
                    <div class="text-sm text-red-100 mt-1">Total Item: <?= $data_keluar['total_jumlah'] ?? 0 ?></div>
                </div>
                <i class="fas fa-arrow-up fa-2x text-red-200"></i>
            </div>
        </div>
    </div>

    <!-- Laporan Tabel -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b px-4 py-3 flex items-center gap-2">
            <i class="fas fa-table text-yellow-500"></i>
            <span class="font-semibold text-gray-700">
                Laporan Periode: <?= date('d/m/Y', strtotime($tanggal_dari)) ?> - <?= date('d/m/Y', strtotime($tanggal_sampai)) ?>
            </span>
        </div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="py-3 px-4 font-semibold">No</th>
                            <th class="py-3 px-4 font-semibold">Tanggal</th>
                            <th class="py-3 px-4 font-semibold">Kode Transaksi</th>
                            <th class="py-3 px-4 font-semibold">Barang</th>
                            <th class="py-3 px-4 font-semibold">Kategori</th>
                            <th class="py-3 px-4 font-semibold">Supplier</th>
                            <th class="py-3 px-4 font-semibold">Jenis</th>
                            <th class="py-3 px-4 font-semibold">Jumlah</th>
                            <th class="py-3 px-4 font-semibold">User</th>
                            <th class="py-3 px-4 font-semibold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if (mysqli_num_rows($laporan) > 0):
                            while ($row = mysqli_fetch_assoc($laporan)): 
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4"><?= $no++ ?></td>
                            <td class="py-3 px-4"><?= date('d/m/Y', strtotime($row['tanggal_transaksi'])) ?></td>
                            <td class="py-3 px-4 font-medium"><?= htmlspecialchars($row['kode_transaksi']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['nama_barang']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['nama_kategori']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['nama_supplier']) ?></td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2 py-1 rounded text-xs font-semibold <?= $row['jenis_transaksi'] == 'masuk' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                    <?= ucfirst(htmlspecialchars($row['jenis_transaksi'])) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 font-semibold"><?= htmlspecialchars($row['jumlah']) ?> <?= htmlspecialchars($row['satuan']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['keterangan']) ?></td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="10" class="py-8 px-4 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fas fa-inbox fa-2x text-gray-300"></i>
                                    <span>Tidak ada data transaksi pada periode ini</span>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (mysqli_num_rows($laporan) > 0): ?>
            <div class="mt-4 pt-4 border-t bg-gray-50 rounded-lg p-4">
                <div class="text-sm text-gray-600">
                    <div class="flex justify-between items-center">
                        <span><strong>Total Transaksi:</strong> <?= mysqli_num_rows($laporan) ?> transaksi</span>
                        <span class="text-xs text-gray-500">
                            Dicetak pada: <?= date('d/m/Y H:i') ?> WIB
                        </span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
@media print {
    /* Hide navigation and buttons */
    .print\:hidden,
    nav,
    .no-print,
    button[onclick="window.print()"],
    .bg-yellow-500,
    .bg-blue-500 {
        display: none !important;
    }
    
    /* Full width for print */
    .container {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Print-specific styling */
    body {
        font-size: 12px !important;
        line-height: 1.2 !important;
    }
    
    .bg-white {
        background: white !important;
        box-shadow: none !important;
    }
    
    .text-gray-700 {
        color: black !important;
    }
    
    .bg-gray-50 {
        background: #f8f8f8 !important;
    }
    
    .border-b,
    .border {
        border-color: #000 !important;
    }
    
    table {
        border-collapse: collapse !important;
    }
    
    th, td {
        border: 1px solid #000 !important;
        padding: 4px 8px !important;
    }
    
    .bg-green-100 {
        background: #e6f7e6 !important;
    }
    
    .bg-red-100 {
        background: #ffe6e6 !important;
    }
    
    .text-green-700 {
        color: #2d5a2d !important;
    }
    
    .text-red-700 {
        color: #5a2d2d !important;
    }
    
    /* Hide filter form when printing */
    form {
        display: none !important;
    }
    
    /* Statistics cards for print */
    .bg-green-500,
    .bg-red-500 {
        background: white !important;
        color: black !important;
        border: 1px solid #000 !important;
    }
    
    .text-green-100,
    .text-red-100,
    .text-green-200,
    .text-red-200 {
        color: #666 !important;
    }
}
</style>