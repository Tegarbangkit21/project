<?php
// admin/produk.php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$success_message = '';
$error_message = '';

// Handle single delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Get image filename before deleting from DB
    $stmt = $pdo->prepare("SELECT gambar FROM produk WHERE id = ?");
    $stmt->execute([$id]);
    $gambar = $stmt->fetchColumn();

    $stmt = $pdo->prepare("DELETE FROM produk WHERE id = ?");
    if ($stmt->execute([$id])) {
        // Delete the image file if it exists
        if ($gambar && file_exists('../assets/images/' . $gambar)) {
            unlink('../assets/images/' . $gambar);
        }
        $success_message = "Produk berhasil dihapus.";
    } else {
        $error_message = "Gagal menghapus produk.";
    }
}

// Handle bulk delete
if (isset($_POST['bulk_delete'])) {
    $ids = explode(',', $_POST['ids']);
    $ids = array_filter(array_map('intval', $ids));

    if (!empty($ids)) {
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';

        // Get all image filenames to delete them
        $stmt = $pdo->prepare("SELECT gambar FROM produk WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $stmt = $pdo->prepare("DELETE FROM produk WHERE id IN ($placeholders)");
        if ($stmt->execute($ids)) {
            // Delete all associated image files
            foreach ($images as $gambar) {
                if ($gambar && file_exists('../assets/images/' . $gambar)) {
                    unlink('../assets/images/' . $gambar);
                }
            }
            $success_message = count($ids) . " produk berhasil dihapus.";
        } else {
            $error_message = "Gagal menghapus produk yang dipilih.";
        }
    }
}

// Get all products with category and subcategory info
$stmt = $pdo->query("SELECT p.*, k.nama_kategori, s.nama_subkategori 
                      FROM produk p 
                      LEFT JOIN kategori k ON p.id_kategori = k.id 
                      LEFT JOIN subkategori s ON p.id_subkategori = s.id 
                      ORDER BY p.id DESC");
$products = $stmt->fetchAll();

$page_title = 'Kelola Produk';
include 'includes/admin_header.php';
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-box me-2"></i>Kelola Produk
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="add_product.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Produk Baru
            </a>
        </div>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= h($success_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= h($error_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div id="bulkActions" class="card mb-3" style="display: none;">
        <div class="card-body">
            <span class="me-3">Aksi untuk item terpilih:</span>
            <button class="btn btn-danger btn-sm" onclick="bulkDelete()">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                            <th width="10%">Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Subkategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada produk. Silakan tambahkan produk baru.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input item-checkbox" value="<?= $product['id'] ?>"></td>
                                    <td>
                                        <?php if ($product['gambar'] && file_exists('../assets/images/' . $product['gambar'])): ?>
                                            <img src="../assets/images/<?= h($product['gambar']) ?>" alt="<?= h($product['nama']) ?>" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= h($product['nama']) ?></strong></td>
                                    <td><span class="badge bg-primary"><?= h($product['nama_kategori']) ?></span></td>
                                    <td>
                                        <?php if ($product['nama_subkategori']): ?>
                                            <span class="badge bg-secondary"><?= h($product['nama_subkategori']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="products.php?delete=<?= $product['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Anda yakin ingin menghapus produk ini?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$extra_js = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    const selectAllCheckbox = document.getElementById("selectAll");
    const itemCheckboxes = document.querySelectorAll(".item-checkbox");
    const bulkActionsBar = document.getElementById("bulkActions");

    function toggleBulkActionsBar() {
        const anyChecked = document.querySelectorAll(".item-checkbox:checked").length > 0;
        if(bulkActionsBar) {
            bulkActionsBar.style.display = anyChecked ? "block" : "none";
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkActionsBar();
        });
    }

    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener("change", toggleBulkActionsBar);
    });
});

// Fungsi untuk bulk delete
function bulkDelete() {
    const checkedItems = document.querySelectorAll(".item-checkbox:checked");
    if (checkedItems.length === 0) {
        alert("Pilih setidaknya satu produk untuk dihapus.");
        return;
    }

    if (confirm(`Anda yakin ingin menghapus ${checkedItems.length} produk terpilih?`)) {
        const ids = Array.from(checkedItems).map(cb => cb.value);
        
        const form = document.createElement("form");
        form.method = "POST";
        
        const idsInput = document.createElement("input");
        idsInput.type = "hidden";
        idsInput.name = "ids";
        idsInput.value = ids.join(",");
        form.appendChild(idsInput);

        const actionInput = document.createElement("input");
        actionInput.type = "hidden";
        actionInput.name = "bulk_delete";
        actionInput.value = "1";
        form.appendChild(actionInput);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
';
include 'includes/admin_footer.php';
?>