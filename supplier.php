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
            $message = "<div class='alert alert-success'>Supplier berhasil ditambahkan!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
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
            $message = "<div class='alert alert-success'>Supplier berhasil diupdate!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM supplier WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert alert-success'>Supplier berhasil dihapus!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
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

$title = "Supplier - Sistem Inventaris";
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-truck"></i> Kelola Supplier</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSupplier">
        <i class="fas fa-plus"></i> Tambah Supplier
    </button>
</div>

<?= $message ?>

<!-- Tabel Supplier -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Supplier</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($suppliers)): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['nama_supplier'] ?></td>
                        <td><?= $row['alamat'] ?></td>
                        <td><?= $row['telepon'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td>
                            <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Yakin ingin menghapus supplier ini?')">
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

<!-- Modal Tambah/Edit Supplier -->
<div class="modal fade" id="modalSupplier" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?= $edit_data ? 'Edit Supplier' : 'Tambah Supplier' ?>
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
                                <label for="nama_supplier" class="form-label">Nama Supplier</label>
                                <input type="text" name="nama_supplier" class="form-control" 
                                       value="<?= $edit_data['nama_supplier'] ?? '' ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telepon" class="form-label">Telepon</label>
                                <input type="text" name="telepon" class="form-control" 
                                       value="<?= $edit_data['telepon'] ?? '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"><?= $edit_data['alamat'] ?? '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?= $edit_data['email'] ?? '' ?>">
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
    var modal = new bootstrap.Modal(document.getElementById('modalSupplier'));
    modal.show();
});
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
