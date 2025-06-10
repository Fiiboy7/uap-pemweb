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
            $message = "<div class='alert alert-success'>Barang berhasil ditambahkan!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
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
            $message = "<div class='alert alert-success'>Barang berhasil diupdate!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM barang WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert alert-success'>Barang berhasil dihapus!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-box"></i> Kelola Barang</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBarang">
        <i class="fas fa-plus"></i> Tambah Barang
    </button>
</div>

<?= $message ?>

<!-- Tabel Barang -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th>Stok</th>
                        <th>Stok Min</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Satuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($barang)): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['kode_barang'] ?></td>
                        <td><?= $row['nama_barang'] ?></td>
                        <td><?= $row['nama_kategori'] ?></td>
                        <td><?= $row['nama_supplier'] ?></td>
                        <td>
                            <span class="badge <?= $row['stok'] < $row['stok_minimum'] ? 'bg-danger' : 'bg-success' ?>">
                                <?= $row['stok'] ?>
                            </span>
                        </td>
                        <td><?= $row['stok_minimum'] ?></td>
                        <td>Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                        <td><?= $row['satuan'] ?></td>
                        <td>
                            <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Yakin ingin menghapus barang ini?')">
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

<!-- Modal Tambah/Edit Barang -->
<div class="modal fade" id="modalBarang" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?= $edit_data ? 'Edit Barang' : 'Tambah Barang' ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kode_barang" class="form-label">Kode Barang</label>
                                <input type="text" name="kode_barang" class="form-control" 
                                       value="<?= $edit_data['kode_barang'] ?? '' ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_barang" class="form-label">Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" 
                                       value="<?= $edit_data['nama_barang'] ?? '' ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kategori_id" class="form-label">Kategori</label>
                                <select name="kategori_id" class="form-control" required>
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
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select name="supplier_id" class="form-control" required>
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
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="stok" class="form-label">Stok</label>
                                <input type="number" name="stok" class="form-control" 
                                       value="<?= $edit_data['stok'] ?? '0' ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="stok_minimum" class="form-label">Stok Minimum</label>
                                <input type="number" name="stok_minimum" class="form-control" 
                                       value="<?= $edit_data['stok_minimum'] ?? '10' ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="harga_beli" class="form-label">Harga Beli</label>
                                <input type="number" name="harga_beli" class="form-control" 
                                       value="<?= $edit_data['harga_beli'] ?? '' ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="harga_jual" class="form-label">Harga Jual</label>
                                <input type="number" name="harga_jual" class="form-control" 
                                       value="<?= $edit_data['harga_jual'] ?? '' ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="satuan" class="form-label">Satuan</label>
                                <input type="text" name="satuan" class="form-control" 
                                       value="<?= $edit_data['satuan'] ?? '' ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="2"><?= $edit_data['deskripsi'] ?? '' ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="<?= $edit_data ? 'edit' : 'tambah' ?>" class="btn btn-primary">
                        <?= $edit_data ? 'Update' : 'Simpan' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($edit_data): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('modalBarang'));
    modal.show();
});
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
