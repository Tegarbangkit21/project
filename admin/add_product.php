<?php
// admin/add_product.php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
// Check if user is logged in
requireLogin();

$success_message = '';
$error_message = '';

// Handle form submission
if ($_POST) {
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $id_kategori = (int)$_POST['id_kategori'];
    $id_subkategori = !empty($_POST['id_subkategori']) ? (int)$_POST['id_subkategori'] : null;

    // Validation
    if (empty($nama)) {
        $error_message = "Nama produk harus diisi.";
    } elseif (empty($deskripsi)) {
        $error_message = "Deskripsi produk harus diisi.";
    } elseif ($id_kategori == 0) {
        $error_message = "Kategori harus dipilih.";
    } else {
        // Handle file upload
        $gambar = '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB

            if (!in_array($_FILES['gambar']['type'], $allowed_types)) {
                $error_message = "Format gambar tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.";
            } elseif ($_FILES['gambar']['size'] > $max_size) {
                $error_message = "Ukuran gambar maksimal 5MB.";
            } else {
                $upload_dir = '../assets/images/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $file_extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
                $gambar = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                $upload_path = $upload_dir . $gambar;

                if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                    $error_message = "Gagal mengupload gambar.";
                    $gambar = '';
                }
            }
        }

        if (empty($error_message)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO produk (nama, deskripsi, gambar, id_kategori, id_subkategori, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                if ($stmt->execute([$nama, $deskripsi, $gambar, $id_kategori, $id_subkategori])) {
                    $success_message = "Produk '{$nama}' berhasil ditambahkan.";
                    // Reset form
                    $_POST = [];
                } else {
                    $error_message = "Gagal menambahkan produk.";
                }
            } catch (PDOException $e) {
                $error_message = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Get categories for dropdown
$stmt = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori");
$categories = $stmt->fetchAll();

// Get subcategories for dropdown (will be populated via AJAX)
$stmt = $pdo->query("SELECT * FROM subkategori ORDER BY nama_subkategori");
$all_subcategories = $stmt->fetchAll();

$page_title = 'Tambah Produk';
include 'includes/admin_header.php';
?>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-plus me-2"></i>Tambah Produk
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="products.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
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

    <!-- Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Produk</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="productForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama"
                                    value="<?= h($_POST['nama'] ?? '') ?>" required>
                                <div class="invalid-feedback">
                                    Nama produk harus diisi.
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="id_kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_kategori" name="id_kategori" required onchange="loadSubcategories()">
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"
                                            <?= (isset($_POST['id_kategori']) && $_POST['id_kategori'] == $category['id']) ? 'selected' : '' ?>>
                                            <?= h($category['nama_kategori']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Kategori harus dipilih.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_subkategori" class="form-label">Subkategori</label>
                                <select class="form-select" id="id_subkategori" name="id_subkategori">
                                    <option value="">Pilih Subkategori (Opsional)</option>
                                </select>
                                <small class="form-text text-muted">Subkategori akan muncul setelah memilih kategori.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gambar" class="form-label">Gambar Produk</label>
                                <input type="file" class="form-control" id="gambar" name="gambar"
                                    accept="image/jpeg,image/png,image/gif,image/webp"
                                    onchange="previewImage(this, 'imagePreview')">
                                <small class="form-text text-muted">Format: JPG, PNG, GIF, WebP. Maksimal 5MB.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Produk <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required><?= h($_POST['deskripsi'] ?? '') ?></textarea>
                            <div class="invalid-feedback">
                                Deskripsi produk harus diisi.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="products.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Image Preview -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Preview Gambar</h6>
                </div>
                <div class="card-body text-center">
                    <img id="imagePreview" src="#" alt="Preview Gambar"
                        class="img-fluid rounded" style="display: none; max-height: 300px;">
                    <div id="imagePlaceholder" class="bg-light rounded d-flex align-items-center justify-content-center"
                        style="height: 200px;">
                        <div class="text-muted">
                            <i class="fas fa-image fa-3x mb-2"></i>
                            <p class="mb-0">Preview gambar akan muncul di sini</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Panduan
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Nama produk harus jelas dan menarik
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Deskripsi detail membantu pelanggan
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Gambar berkualitas tinggi lebih menarik
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Kategori "Custom" akan redirect ke WhatsApp
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Subcategories data
    const subcategoriesData = <?= json_encode($all_subcategories) ?>;

    // Load subcategories based on selected category
    function loadSubcategories() {
        const categoryId = document.getElementById('id_kategori').value;
        const subcategorySelect = document.getElementById('id_subkategori');

        // Clear current options
        subcategorySelect.innerHTML = '<option value="">Pilih Subkategori (Opsional)</option>';

        if (categoryId) {
            // Filter subcategories by category
            const filteredSubcategories = subcategoriesData.filter(sub => sub.id_kategori == categoryId);

            filteredSubcategories.forEach(subcategory => {
                const option = document.createElement('option');
                option.value = subcategory.id;
                option.textContent = subcategory.nama_subkategori;
                subcategorySelect.appendChild(option);
            });
        }
    }

    // Form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            const forms = document.getElementsByClassName('needs-validation');
            const validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    // Load subcategories on page load if category is selected
    document.addEventListener('DOMContentLoaded', function() {
        loadSubcategories();

        // Set selected subcategory if exists
        const selectedSubcategory = '<?= $_POST['id_subkategori'] ?? '' ?>';
        if (selectedSubcategory) {
            setTimeout(() => {
                document.getElementById('id_subkategori').value = selectedSubcategory;
            }, 100);
        }
    });

    // Image preview function
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById('imagePlaceholder');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
            placeholder.style.display = 'flex';
        }
    }
</script>

<?php include 'includes/admin_footer.php'; ?>