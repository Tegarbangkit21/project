<?php
// admin/dashboard.php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
// Check if user is logged in
requireLogin();

// Get statistics
$stats = [
    'total_products' => $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn(),
    'total_categories' => $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn(),
    'total_subcategories' => $pdo->query("SELECT COUNT(*) FROM subkategori")->fetchColumn(),
    'total_testimonials' => $pdo->query("SELECT COUNT(*) FROM testimoni")->fetchColumn(),
    'total_faq' => $pdo->query("SELECT COUNT(*) FROM faq")->fetchColumn(),
];

// Get recent products
$recent_products = $pdo->query("SELECT p.*, k.nama_kategori, s.nama_subkategori 
                                FROM produk p 
                                LEFT JOIN kategori k ON p.id_kategori = k.id 
                                LEFT JOIN subkategori s ON p.id_subkategori = s.id 
                                ORDER BY p.created_at DESC 
                                LIMIT 5")->fetchAll();

// Get category statistics
$category_stats = $pdo->query("SELECT k.nama_kategori, COUNT(p.id) as jumlah_produk
                               FROM kategori k
                               LEFT JOIN produk p ON k.id = p.id_kategori
                               GROUP BY k.id, k.nama_kategori
                               ORDER BY jumlah_produk DESC")->fetchAll();

$page_title = 'Dashboard';
include 'includes/admin_header.php';
?>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
        <h1 class="h2">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="../index.php" class="btn btn-outline-primary" target="_blank">
                    <i class="fas fa-globe"></i> Lihat Website
                </a>
            </div>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="alert alert-primary mb-4">
        <i class="fas fa-info-circle me-2"></i>
        Selamat datang di Admin Panel CHIBOR! Kelola website Anda dengan mudah melalui panel ini.
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Produk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['total_products']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Kategori</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['total_categories']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Testimoni</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['total_testimonials']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                FAQ</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['total_faq']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-question-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Products -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Produk Terbaru</h6>
                    <a href="products.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_products)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Gambar</th>
                                        <th>Nama Produk</th>
                                        <th>Kategori</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_products as $product): ?>
                                        <tr>
                                            <td>
                                                <?php if ($product['gambar']): ?>
                                                    <img src="../assets/images/<?= h($product['gambar']) ?>"
                                                        alt="<?= h($product['nama']) ?>"
                                                        class="rounded"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= h($product['nama']) ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?= h($product['nama_kategori']) ?></span>
                                                <?php if ($product['nama_subkategori']): ?>
                                                    <span class="badge bg-secondary"><?= h($product['nama_subkategori']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($product['created_at'])) ?></td>
                                            <td>
                                                <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-box fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Belum ada produk yang ditambahkan.</p>
                            <a href="add_product.php" class="btn btn-primary">Tambah Produk Pertama</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Category Statistics -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Kategori</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($category_stats)): ?>
                        <?php foreach ($category_stats as $cat_stat): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-tag text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold"><?= h($cat_stat['nama_kategori']) ?></div>
                                    <div class="text-gray-500 small"><?= $cat_stat['jumlah_produk'] ?> produk</div>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge bg-primary"><?= $cat_stat['jumlah_produk'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-tags fa-2x text-gray-300 mb-2"></i>
                            <p class="text-gray-500 small">Belum ada kategori.</p>
                            <a href="categories.php" class="btn btn-sm btn-primary">Buat Kategori</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="add_product.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tambah Produk
                        </a>
                        <a href="categories.php" class="btn btn-outline-success">
                            <i class="fas fa-tags me-2"></i>Kelola Kategori
                        </a>
                        <a href="testimonials.php" class="btn btn-outline-info">
                            <i class="fas fa-star me-2"></i>Kelola Testimoni
                        </a>
                        <a href="faq.php" class="btn btn-outline-warning">
                            <i class="fas fa-question-circle me-2"></i>Kelola FAQ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }

    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }

    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }

    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }

    .icon-circle {
        height: 2.5rem;
        width: 2.5rem;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .text-xs {
        font-size: 0.7rem;
    }

    .text-gray-300 {
        color: #dddfeb !important;
    }

    .text-gray-500 {
        color: #858796 !important;
    }

    .text-gray-800 {
        color: #5a5c69 !important;
    }
</style>

<?php include 'includes/admin_footer.php'; ?>