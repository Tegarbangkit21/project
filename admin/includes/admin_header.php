<?php
// admin/includes/admin_header.php
if (!defined('ADMIN_ACCESS')) {
    define('ADMIN_ACCESS', true);
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Admin Panel CHIBOR</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2563eb;
            --gradient-primary: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fc;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--gradient-primary);
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand h4 {
            color: white;
            font-weight: 700;
            margin: 0;
        }

        .sidebar-brand small {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.75rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            padding: 0.75rem 1.5rem;
            margin: 0.25rem 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white !important;
            transform: translateX(4px);
        }

        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s;
        }

        .navbar-admin {
            background: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 1rem 2rem;
        }

        .sidebar-toggle {
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: inline-block;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: 12px;
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #d1d3e2;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .table th {
            background-color: #f8f9fc;
            border-top: none;
            font-weight: 600;
            color: #5a5c69;
        }

        .badge {
            font-weight: 500;
        }
    </style>
</head>

<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h4>CHIBOR</h4>
            <small>Admin Panel</small>
        </div>

        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <!-- TAMBAHKAN BARIS DI BAWAH INI -->
                    <a href="penjualan.php" class="nav-link <?= ($current_page == 'penjualan.php') ? 'active' : '' ?>">
                        <i class="fas fa-chart-line me-2"></i>Kelola Penjualan
                    </a>
                </li>
                <li class="nav-item">
                    <!-- TAMBAHKAN BARIS DI BAWAH INI -->
                    <a href="laporan.php" class="nav-link <?= ($current_page == 'laporan.php') ? 'active' : '' ?>">
                        <i class="fas fa-file-alt me-2"></i>Laporan Penjualan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="products.php" class="nav-link <?= in_array($current_page, ['products.php', 'add_product.php', 'edit_product.php']) ? 'active' : '' ?>">
                        <i class="fas fa-box"></i>
                        Kelola Produk
                    </a>
                </li>

                <li class="nav-item">
                    <a href="categories.php" class="nav-link <?= $current_page == 'categories.php' ? 'active' : '' ?>">
                        <i class="fas fa-tags"></i>
                        Kategori
                    </a>
                </li>

                <li class="nav-item">
                    <a href="subcategories.php" class="nav-link <?= $current_page == 'subcategories.php' ? 'active' : '' ?>">
                        <i class="fas fa-tag"></i>
                        Subkategori
                    </a>
                </li>

                <li class="nav-item">
                    <a href="testimonials.php" class="nav-link <?= $current_page == 'testimonials.php' ? 'active' : '' ?>">
                        <i class="fas fa-star"></i>
                        Testimoni
                    </a>
                </li>

                <li class="nav-item">
                    <a href="faq.php" class="nav-link <?= $current_page == 'faq.php' ? 'active' : '' ?>">
                        <i class="fas fa-question-circle"></i>
                        FAQ
                    </a>
                </li>

                <hr class="sidebar-divider my-3" style="border-color: rgba(255,255,255,0.1);">

                <li class="nav-item">
                    <a href="../index.php" class="nav-link" target="_blank">
                        <i class="fas fa-globe"></i>
                        Lihat Website
                    </a>
                </li>

                <li class="nav-item">
                    <a href="logout.php" class="nav-link" onclick="return confirm('Yakin ingin logout?')">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-admin">
            <div class="container-fluid">
                <button class="btn btn-link sidebar-toggle d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle fa-fw me-2"></i>
                            <span class="d-none d-lg-inline text-gray-600 small">Admin</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../index.php" target="_blank">
                                    <i class="fas fa-globe fa-sm fa-fw me-2 text-gray-400"></i>
                                    Lihat Website
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="logout.php" onclick="return confirm('Yakin ingin logout?')">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                    Logout
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->