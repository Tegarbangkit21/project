<?php
// includes/header.php
try {
    $stmt_cat_nav = $pdo->query("SELECT nama_kategori FROM kategori ORDER BY id ASC");
    $nav_categories = $stmt_cat_nav->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $nav_categories = [];
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>PT. Irgha Reksa Jasa - CHIBOR</title>
    <meta name="description" content="PT. Irgha Reksa Jasa - Produsen snack CHIBOR. CRUNCH it ONCE, CRAVE IT FOREVER!">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <img src="assets/images/logo_chibor.png" alt="CHIBOR" height="60" class="me-2">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'about.php') ? 'active' : '' ?>" href="about.php">Tentang Kami</a>
                    </li>
                    <li class="nav-item dropdown dropdown-hover">
                        <a class="nav-link dropdown-toggle <?= ($current_page == 'products.php' || $current_page == 'katalog.php') ? 'active' : '' ?>"
                            href="products.php"
                            id="navProduk" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Produk
                        </a>
                        <ul class="dropdown-menu shadow-lg" aria-labelledby="navProduk">
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <?php // Bagian ini akan mengambil kategori dari database secara dinamis 
                            ?>
                            <?php if (!empty($nav_categories)): ?>
                                <?php foreach ($nav_categories as $category): ?>
                                    <li>
                                        <a class="dropdown-item" href="products.php?category=<?= urlencode(h($category)) ?>">
                                            <?= h($category) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'testimoni.php') ? 'active' : '' ?>" href="testimoni.php">Testimoni</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'faq.php') ? 'active' : '' ?>" href="faq.php">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'contact.php') ? 'active' : '' ?>" href="contact.php">Kontak</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="https://wa.me/6282320208899" target="_blank">
                            <i class="fab fa-whatsapp me-1"></i>Pesan Sekarang
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content"><?php // Content will be inserted here 
                                ?>