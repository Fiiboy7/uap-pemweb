<?php
session_start();
include 'config/database.php'; // Pastikan path ini benar
include 'includes/auth.php'; // Pastikan path ini benar
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
include 'includes/header.php'; // Memuat header

// Konten spesifik Dashboard dimulai di sini
?>

<h2><i class="fas fa-home"></i> Dashboard</h2>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['total_barang'] ?></h4>
                        <p>Total Barang</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['total_kategori'] ?></h4>
                        <p>Total Kategori</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['total_supplier'] ?></h4>
                        <p>Total Supplier</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-truck fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['transaksi_hari_ini'] ?></h4>
                        <p>Transaksi Hari Ini</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exchange-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-exclamation-triangle text-warning"></i> Barang Stok Rendah</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($barang_stok_rendah) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Stok</th>
                                <th>Stok Min</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($barang_stok_rendah)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><span class="badge bg-danger"><?= htmlspecialchars($row['stok']) ?></span></td>
                                <td><?= htmlspecialchars($row['stok_minimum']) ?></td>
                                <td><?= htmlspecialchars($row['satuan']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">Tidak ada barang dengan stok di bawah minimum.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock"></i> Transaksi Terbaru</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($transaksi_terbaru) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($transaksi_terbaru)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td>
                                    <span class="badge <?= $row['jenis_transaksi'] == 'masuk' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ucfirst(htmlspecialchars($row['jenis_transaksi'])) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['jumlah']) ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_transaksi'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">Belum ada transaksi.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


        </div></main><?php include 'includes/footer.php'; ?>