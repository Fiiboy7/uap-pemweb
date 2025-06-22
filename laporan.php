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

$title = "Laporan - Sistem Inventaris";
include 'includes/header.php';
?>

<h2><i class="fas fa-chart-bar"></i> Laporan Transaksi</h2>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="tanggal_dari" class="form-label">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" class="form-control" value="<?= $tanggal_dari ?>">
            </div>
            <div class="col-md-3">
                <label for="tanggal_sampai" class="form-label">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" class="form-control" value="<?= $tanggal_sampai ?>">
            </div>
            <div class="col-md-3">
                <label for="jenis_transaksi" class="form-label">Jenis Transaksi</label>
                <select name="jenis_transaksi" class="form-control">
                    <option value="">Semua</option>
                    <option value="masuk" <?= $jenis_transaksi == 'masuk' ? 'selected' : '' ?>>Barang Masuk</option>
                    <option value="keluar" <?= $jenis_transaksi == 'keluar' ? 'selected' : '' ?>>Barang Keluar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <button type="button" class="btn btn-success" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $data_masuk['total_transaksi'] ?? 0 ?></h4>
                        <p>Total Transaksi Masuk</p>
                        <small>Total Item: <?= $data_masuk['total_jumlah'] ?? 0 ?></small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-arrow-down fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $data_keluar['total_transaksi'] ?? 0 ?></h4>
                        <p>Total Transaksi Keluar</p>
                        <small>Total Item: <?= $data_keluar['total_jumlah'] ?? 0 ?></small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-arrow-up fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Laporan Tabel -->
<div class="card">
    <div class="card-header">
        <h5>Laporan Periode: <?= date('d/m/Y', strtotime($tanggal_dari)) ?> - <?= date('d/m/Y', strtotime($tanggal_sampai)) ?></h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode Transaksi</th>
                        <th>Barang</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>User</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if (mysqli_num_rows($laporan) > 0):
                        while ($row = mysqli_fetch_assoc($laporan)): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_transaksi'])) ?></td>
                        <td><?= $row['kode_transaksi'] ?></td>
                        <td><?= $row['nama_barang'] ?></td>
                        <td><?= $row['nama_kategori'] ?></td>
                        <td><?= $row['nama_supplier'] ?></td>
                        <td>
                            <span class="badge <?= $row['jenis_transaksi'] == 'masuk' ? 'bg-success' : 'bg-danger' ?>">
                                <?= ucfirst($row['jenis_transaksi']) ?>
                            </span>
                        </td>
                        <td><?= $row['jumlah'] ?> <?= $row['satuan'] ?></td>
                        <td><?= $row['nama_lengkap'] ?></td>
                        <td><?= $row['keterangan'] ?></td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="10" class="text-center">Tidak ada data transaksi pada periode ini</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    .navbar, .btn, .card-header .btn { display: none !important; }
    .container { max-width: 100% !important; }
}
</style>

<?php include 'includes/footer.php'; ?>
