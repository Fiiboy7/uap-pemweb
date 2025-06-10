<?php
session_start();
include 'config/database.php';
include 'includes/auth.php';
checkLogin();
checkAdmin(); // Only admin can access this page

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $nama_lengkap = $_POST['nama_lengkap'];
        $role = $_POST['role'];
        
        $query = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password', '$nama_lengkap', '$role')";
        if (mysqli_query($conn, $query)) {
            $message = "<div class='alert alert-success'>User berhasil ditambahkan!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
    
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $nama_lengkap = $_POST['nama_lengkap'];
        $role = $_POST['role'];
        
        $query = "UPDATE users SET username='$username', nama_lengkap='$nama_lengkap', role='$role' WHERE id=$id";
        
        // Update password if provided
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $query = "UPDATE users SET username='$username', password='$password', nama_lengkap='$nama_lengkap', role='$role' WHERE id=$id";
        }
        
        if (mysqli_query($conn, $query)) {
            $message = "<div class='alert alert-success'>User berhasil diupdate!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Prevent deleting current user
    if ($id == $_SESSION['user_id']) {
        $message = "<div class='alert alert-danger'>Tidak dapat menghapus user yang sedang login!</div>";
    } else {
        $query = "DELETE FROM users WHERE id=$id";
        if (mysqli_query($conn, $query)) {
            $message = "<div class='alert alert-success'>User berhasil dihapus!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($result);
}

// Get all users
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY nama_lengkap");

$title = "Users - Sistem Inventaris";
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users"></i> Kelola Users</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUser">
        <i class="fas fa-plus"></i> Tambah User
    </button>
</div>

<?= $message ?>

<!-- Tabel Users -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($users)): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['nama_lengkap'] ?></td>
                        <td>
                            <span class="badge <?= $row['role'] == 'admin' ? 'bg-danger' : 'bg-primary' ?>">
                                <?= ucfirst($row['role']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <?php if ($row['id'] != $_SESSION['user_id']): ?>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Yakin ingin menghapus user ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit User -->
<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?= $edit_data ? 'Edit User' : 'Tambah User' ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" 
                               value="<?= $edit_data['username'] ?? '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password <?= $edit_data ? '(Kosongkan jika tidak ingin mengubah)' : '' ?>
                        </label>
                        <input type="password" name="password" class="form-control" 
                               <?= $edit_data ? '' : 'required' ?>>
                    </div>
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" 
                               value="<?= $edit_data['nama_lengkap'] ?? '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" class="form-control" required>
                            <option value="">Pilih Role</option>
                            <option value="admin" <?= ($edit_data && $edit_data['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                            <option value="user" <?= ($edit_data && $edit_data['role'] == 'user') ? 'selected' : '' ?>>User</option>
                        </select>
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
    var modal = new bootstrap.Modal(document.getElementById('modalUser'));
    modal.show();
});
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
