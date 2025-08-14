<?php
// admin/categories.php
session_start();
require_once '../includes/db.php';


// Check if user is logged in
requireLogin();

$success_message = '';
$error_message = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_category'])) {
        // Add new category
        $nama_kategori = trim($_POST['nama_kategori']);

        if (empty($nama_kategori)) {
            $error_message = "Nama kategori harus diisi.";
        } else {
            // Check if category already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM kategori WHERE nama_kategori = ?");
            $stmt->execute([$nama_kategori]);
            if ($stmt->fetchColumn() > 0) {
                $error_message = "Kategori '{$nama_kategori}' sudah ada.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO kategori (nama_kategori, created_at) VALUES (?, NOW())");
                if ($stmt->execute([$nama_kategori])) {
                    $success_message = "Kategori '{$nama_kategori}' berhasil ditambahkan.";
                } else {
                    $error_message = "Gagal menambahkan kategori.";
                }
            }
        }
    } elseif (isset($_POST['edit_category'])) {
        // Edit category
        $id = (int)$_POST['id'];
        $nama_kategori = trim($_POST['nama_kategori']);

        if (empty($nama_kategori)) {
            $error_message = "Nama kategori harus diisi.";
        } else {
            // Check if category already exists (excluding current)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM kategori WHERE nama_kategori = ? AND id != ?");
            $stmt->execute([$nama_kategori, $id]);
            if ($stmt->fetchColumn() > 0) {
                $error_message = "Kategori '{$nama_kategori}' sudah ada.";
            } else {
                $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori = ?, updated_at = NOW() WHERE id = ?");
                if ($stmt->execute([$nama_kategori, $id])) {
                    $success_message = "Kategori berhasil diupdate.";
                } else {
                    $error_message = "Gagal mengupdate kategori.";
                }
            }
        }
    } elseif (isset($_POST['bulk_delete'])) {
        // Bulk delete
        $ids = explode(',', $_POST['ids']);
        $ids = array_map('intval', $ids);

        if (!empty($ids)) {
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';

            // Check if any category has products
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE id_kategori IN ($placeholders)");
            $stmt->execute($ids);
            $product_count = $stmt->fetchColumn();

            if ($product_count > 0) {
                $error_message = "Tidak dapat menghapus kategori yang masih memiliki produk. Hapus produk terlebih dahulu.";
            } else {
                // Delete subcategories first
                $stmt = $pdo->prepare("DELETE FROM subkategori WHERE id_kategori IN ($placeholders)");
                $stmt->execute($ids);

                // Delete categories
                $stmt = $pdo->prepare("DELETE FROM kategori WHERE id IN ($placeholders)");
                if ($stmt->execute($ids)) {
                    $success_message = count($ids) . " kategori berhasil dihapus.";
                } else {
                    $error_message = "Gagal menghapus kategori.";
                }
            }
        }
    }
}

// Handle single delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Check if category has products
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE id_kategori = ?");
    $stmt->execute([$id]);
    $product_count = $stmt->fetchColumn();

    if ($product_count > 0) {
        $error_message = "Tidak dapat menghapus kategori yang masih memiliki {$product_count} produk.";
    } else {
        // Get category name for message
        $stmt = $pdo->prepare("SELECT nama_kategori FROM kategori WHERE id = ?");
        $stmt->execute([$id]);
        $category_name = $stmt->fetchColumn();

        if ($category_name) {
            // Delete subcategories first
            $stmt = $pdo->prepare("DELETE FROM subkategori WHERE id_kategori = ?");
            $stmt->execute([$id]);

            // Delete category
            $stmt = $pdo->prepare("DELETE FROM kategori WHERE id = ?");
            if ($stmt->execute([$id])) {
                $success_message = "Kategori '{$category_name}' berhasil dihapus.";
            } else {
                $error_message = "Gagal menghapus kategori.";
            }
        }
    }
}

// Get all categories with product count
$stmt = $pdo->query("SELECT k.*, COUNT(p.id) as jumlah_produk,
                     (SELECT COUNT(*) FROM subkategori WHERE id_kategori = k.id) as jumlah_subkategori
                     FROM kategori k
                     LEFT JOIN produk p ON k.id = p.id_kategori
                     GROUP BY k.id
                     ORDER BY k.nama_kategori");
$categories = $stmt->fetchAll();

$page_title = 'Kelola Kategori';
include 'includes/admin_header.php';
?>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-tags me-2"></i>Kelola Kategori
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-plus"></i> Tambah Kategori
                </button>
                <a href="subcategories.php" class="btn btn-outline-secondary">
                    <i class="fas fa-tag"></i> Kelola Subkategori
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= h($success_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?= h($error_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Bulk Actions -->
    <div id="bulkActions" class="card mb-3" style="display: none;">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <span class="me-3"><strong>Aksi untuk item yang dipilih:</strong></span>
                <button class="btn btn-danger btn-sm" onclick="bulkDelete('categories')">
                    <i class="fas fa-trash"></i> Hapus Terpilih
                </button>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                Daftar Kategori
                <span class="badge bg-primary ms-2"><?= count($categories) ?> kategori</span>
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($categories)): ?>
                <div class="table-responsive">
                    <table class="table table-hover data-table" id="categoriesTable">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>Nama Kategori</th>
                                <th>Jumlah Produk</th>
                                <th>Jumlah Subkategori</th>
                                <th>Tanggal Dibuat</th>
                                <th width="15%" class="no-sort">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input item-checkbox" value="<?= $category['id'] ?>">
                                    </td>
                                    <td>
                                        <strong><?= h($category['nama_kategori']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= $category['jumlah_produk'] ?> produk</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $category['jumlah_subkategori'] ?> subkategori</span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($category['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary"
                                                onclick="editCategory(<?= $category['id'] ?>, '<?= h($category['nama_kategori']) ?>')"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editCategoryModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="subcategories.php?kategori=<?= $category['id'] ?>"
                                                class="btn btn-outline-info"
                                                title="Lihat Subkategori">
                                                <i class="fas fa-tag"></i>
                                            </a>
                                            <?php if ($category['jumlah_produk'] == 0): ?>
                                                <a href="?delete=<?= $category['id'] ?>"
                                                    class="btn btn-outline-danger"
                                                    onclick="return confirmDelete('kategori <?= h($category['nama_kategori']) ?>')"
                                                    title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-outline-danger"
                                                    disabled
                                                    title="Tidak dapat dihapus karena memiliki produk">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-tags fa-4x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Belum ada kategori</h5>
                    <p class="text-gray-400">Mulai dengan menambahkan kategori pertama untuk produk Anda.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus"></i> Tambah Kategori Pertama
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_nama_kategori" name="nama_kategori" required>
                        <div class="form-text">Contoh: Retail, Specialties, Custom</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="add_category" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_kategori" name="nama_kategori" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="edit_category" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editCategory(id, nama) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nama_kategori').value = nama;
    }

    // Clear form when add modal is shown
    document.getElementById('addCategoryModal').addEventListener('show.bs.modal', function() {
        document.getElementById('add_nama_kategori').value = '';
    });
</script>

<?php include 'includes/admin_footer.php'; ?>