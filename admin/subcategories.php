<?php
// admin/subcategories.php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
// Check if user is logged in
requireLogin();

$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_subcategory'])) {
        // Add new subcategory
        $nama_subkategori = trim($_POST['nama_subkategori']);
        $id_kategori = (int)$_POST['id_kategori'];

        if (empty($nama_subkategori)) {
            $error_message = "Nama subkategori harus diisi.";
        } elseif ($id_kategori == 0) {
            $error_message = "Kategori harus dipilih.";
        } else {
            // Check if subcategory already exists in the same category
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM subkategori WHERE nama_subkategori = ? AND id_kategori = ?");
            $stmt->execute([$nama_subkategori, $id_kategori]);
            if ($stmt->fetchColumn() > 0) {
                $error_message = "Subkategori '" . h($nama_subkategori) . "' sudah ada dalam kategori ini.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO subkategori (nama_subkategori, id_kategori) VALUES (?, ?)");
                if ($stmt->execute([$nama_subkategori, $id_kategori])) {
                    $success_message = "Subkategori '" . h($nama_subkategori) . "' berhasil ditambahkan.";
                } else {
                    $error_message = "Gagal menambahkan subkategori.";
                }
            }
        }
    } elseif (isset($_POST['edit_subcategory'])) {
        // Edit subcategory
        $id = (int)$_POST['id'];
        $nama_subkategori = trim($_POST['nama_subkategori']);
        $id_kategori = (int)$_POST['id_kategori'];

        if (empty($nama_subkategori)) {
            $error_message = "Nama subkategori harus diisi.";
        } elseif ($id_kategori == 0) {
            $error_message = "Kategori harus dipilih.";
        } else {
            // Check if subcategory already exists (excluding current)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM subkategori WHERE nama_subkategori = ? AND id_kategori = ? AND id != ?");
            $stmt->execute([$nama_subkategori, $id_kategori, $id]);
            if ($stmt->fetchColumn() > 0) {
                $error_message = "Subkategori '" . h($nama_subkategori) . "' sudah ada dalam kategori ini.";
            } else {
                $stmt = $pdo->prepare("UPDATE subkategori SET nama_subkategori = ?, id_kategori = ? WHERE id = ?");
                if ($stmt->execute([$nama_subkategori, $id_kategori, $id])) {
                    $success_message = "Subkategori berhasil diupdate.";
                } else {
                    $error_message = "Gagal mengupdate subkategori.";
                }
            }
        }
    } elseif (isset($_POST['bulk_delete'])) {
        // Bulk delete
        $ids = explode(',', $_POST['ids']);
        $ids = array_filter(array_map('intval', $ids));

        if (!empty($ids)) {
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';

            // Check if any subcategory has products
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE id_subkategori IN ($placeholders)");
            $stmt->execute($ids);
            $product_count = $stmt->fetchColumn();

            if ($product_count > 0) {
                $error_message = "Tidak dapat menghapus. Salah satu subkategori yang dipilih masih memiliki produk.";
            } else {
                $stmt = $pdo->prepare("DELETE FROM subkategori WHERE id IN ($placeholders)");
                if ($stmt->execute($ids)) {
                    $success_message = count($ids) . " subkategori berhasil dihapus.";
                } else {
                    $error_message = "Gagal menghapus subkategori.";
                }
            }
        }
    }
}

// Handle single delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Check if subcategory has products
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE id_subkategori = ?");
    $stmt->execute([$id]);
    $product_count = $stmt->fetchColumn();

    if ($product_count > 0) {
        $error_message = "Tidak dapat menghapus subkategori yang masih memiliki {$product_count} produk.";
    } else {
        $stmt = $pdo->prepare("SELECT nama_subkategori FROM subkategori WHERE id = ?");
        $stmt->execute([$id]);
        $subcategory_name = $stmt->fetchColumn();

        if ($subcategory_name) {
            $stmt = $pdo->prepare("DELETE FROM subkategori WHERE id = ?");
            if ($stmt->execute([$id])) {
                $success_message = "Subkategori '" . h($subcategory_name) . "' berhasil dihapus.";
            } else {
                $error_message = "Gagal menghapus subkategori.";
            }
        }
    }
}

// Get filter
$kategori_filter = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;

// Get all subcategories with category info and product count
$query = "SELECT s.*, k.nama_kategori, COUNT(p.id) as jumlah_produk
          FROM subkategori s
          LEFT JOIN kategori k ON s.id_kategori = k.id
          LEFT JOIN produk p ON s.id = p.id_subkategori";

if ($kategori_filter > 0) {
    $query .= " WHERE s.id_kategori = :kategori_filter";
}

$query .= " GROUP BY s.id ORDER BY k.nama_kategori, s.nama_subkategori";

$stmt = $pdo->prepare($query);
if ($kategori_filter > 0) {
    $stmt->execute(['kategori_filter' => $kategori_filter]);
} else {
    $stmt->execute();
}
$subcategories = $stmt->fetchAll();

// Get all categories for dropdown
$categories = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll();

$page_title = 'Kelola Subkategori';
include 'includes/admin_header.php';
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-tag me-2"></i>Kelola Subkategori
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubcategoryModal">
                    <i class="fas fa-plus"></i> Tambah Subkategori
                </button>
                <a href="categories.php" class="btn btn-outline-secondary">
                    <i class="fas fa-tags"></i> Kelola Kategori
                </a>
            </div>
        </div>
    </div>

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

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="filterKategori" class="form-label">Filter Berdasarkan Kategori</label>
                    <select id="filterKategori" name="kategori" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= ($kategori_filter == $category['id']) ? 'selected' : '' ?>>
                                <?= h($category['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <a href="subcategories.php" class="btn btn-outline-secondary">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div id="bulkActions" class="card mb-3" style="display: none;">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <span class="me-3">Aksi untuk item terpilih:</span>
                <button class="btn btn-danger btn-sm" onclick="bulkDelete()">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="subcategoriesTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Nama Subkategori</th>
                            <th>Kategori Induk</th>
                            <th>Jumlah Produk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($subcategories)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada subkategori.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($subcategories as $subcategory): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input item-checkbox" value="<?= $subcategory['id'] ?>">
                                    </td>
                                    <td>
                                        <strong><?= h($subcategory['nama_subkategori']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?= h($subcategory['nama_kategori']) ?></span>
                                    </td>
                                    <td>
                                        <a href="produk.php?subkategori=<?= $subcategory['id'] ?>" class="badge bg-info text-decoration-none">
                                            <?= $subcategory['jumlah_produk'] ?> produk
                                        </a>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary"
                                                onclick="editSubcategory(<?= $subcategory['id'] ?>, '<?= h($subcategory['nama_subkategori'], ENT_QUOTES) ?>', <?= $subcategory['id_kategori'] ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="subcategories.php?delete=<?= $subcategory['id'] ?>"
                                                class="btn btn-outline-danger"
                                                onclick="return confirm('Anda yakin ingin menghapus subkategori ini? Aksi ini tidak dapat dibatalkan.')">
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

<div class="modal fade" id="addSubcategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Subkategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="add_subcategory" value="1">
                    <div class="mb-3">
                        <label for="addNamaSubkategori" class="form-label">Nama Subkategori</label>
                        <input type="text" class="form-control" id="addNamaSubkategori" name="nama_subkategori" required>
                    </div>
                    <div class="mb-3">
                        <label for="addIdKategori" class="form-label">Termasuk dalam Kategori</label>
                        <select class="form-select" id="addIdKategori" name="id_kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= h($category['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editSubcategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Subkategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="edit_subcategory" value="1">
                    <input type="hidden" name="id" id="editSubcategoryId">
                    <div class="mb-3">
                        <label for="editNamaSubkategori" class="form-label">Nama Subkategori</label>
                        <input type="text" class="form-control" id="editNamaSubkategori" name="nama_subkategori" required>
                    </div>
                    <div class="mb-3">
                        <label for="editIdKategori" class="form-label">Termasuk dalam Kategori</label>
                        <select class="form-select" id="editIdKategori" name="id_kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= h($category['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$extra_js = '
<script>
// Fungsi untuk membuka modal edit dan mengisi datanya
function editSubcategory(id, name, categoryId) {
    document.getElementById("editSubcategoryId").value = id;
    document.getElementById("editNamaSubkategori").value = name;
    document.getElementById("editIdKategori").value = categoryId;
    
    var myModal = new bootstrap.Modal(document.getElementById("editSubcategoryModal"));
    myModal.show();
}

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
        alert("Pilih setidaknya satu item untuk dihapus.");
        return;
    }

    if (confirm(`Anda yakin ingin menghapus ${checkedItems.length} subkategori terpilih?`)) {
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