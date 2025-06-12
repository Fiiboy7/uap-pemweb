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
            $message = "<div class='alert alert-success'>Kategori berhasil ditambahkan!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
    
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $nama_kategori = $_POST['nama_kategori'];
        $deskripsi = $_POST['deskripsi'];
        
        $query = "UPDATE kategori SET nama_kategori='$nama_kategori', deskripsi='$deskripsi' WHERE id=$id";
        if (mysqli_query($conn, $query)) {
            $message = "<div class='alert alert-success'>Kategori berhasil diupdate!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM kategori WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert alert-success'>Kategori berhasil dihapus!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tags"></i> Kelola Kategori</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKategori">
        <i class="fas fa-plus"></i> Tambah Kategori
    </button>
</div>

<?= $message ?>

<!-- Tabel Kategori -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($categories)): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['nama_kategori'] ?></td>
                        <td><?= $row['deskripsi'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" 
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
</div>

<!-- Modal Tambah/Edit Kategori -->
<div class="modal fade" id="modalKategori" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?= $edit_data ? 'Edit Kategori' : 'Tambah Kategori' ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label">Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" 
                               value="<?= $edit_data['nama_kategori'] ?? '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"><?= $edit_data['deskripsi'] ?? '' ?></textarea>
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
    var modal = new bootstrap.Modal(document.getElementById('modalKategori'));
    modal.show();
});
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
