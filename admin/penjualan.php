<?php
// admin/penjualan.php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireLogin();

$success_message = '';
$error_message   = '';

// -------------------------------
// Handle actions: create/update/delete
// -------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';

    if ($action === 'create') {
        $id_produk         = (int)($_POST['id_produk'] ?? 0);
        $nama_customer     = trim($_POST['nama_customer'] ?? '');
        $type_customer     = trim($_POST['type_customer'] ?? '');
        $jumlah_terjual    = (int)($_POST['jumlah_terjual'] ?? 0);
        $tanggal_penjualan = trim($_POST['tanggal_penjualan'] ?? '');

        if ($id_produk <= 0 || $jumlah_terjual <= 0 || $tanggal_penjualan === '' || $nama_customer === '' || $type_customer === '') {
            $error_message = 'Semua field harus diisi dengan benar.';
        } else {
            try {
                $stmt = $pdo->prepare("\n                    INSERT INTO penjualan (id_produk, nama_customer, type_customer, jumlah_terjual, tanggal_penjualan)\n                    VALUES (?, ?, ?, ?, ?)\n                ");
                $stmt->execute([$id_produk, $nama_customer, $type_customer, $jumlah_terjual, $tanggal_penjualan]);
                $success_message = 'Data penjualan berhasil ditambahkan.';
            } catch (PDOException $e) {
                $error_message = 'Database error: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'update') {
        $id                = (int)($_POST['id'] ?? 0);
        $id_produk         = (int)($_POST['id_produk'] ?? 0);
        $nama_customer     = trim($_POST['nama_customer'] ?? '');
        $type_customer     = trim($_POST['type_customer'] ?? '');
        $jumlah_terjual    = (int)($_POST['jumlah_terjual'] ?? 0);
        $tanggal_penjualan = trim($_POST['tanggal_penjualan'] ?? '');

        if ($id <= 0 || $id_produk <= 0 || $jumlah_terjual <= 0 || $tanggal_penjualan === '' || $nama_customer === '' || $type_customer === '') {
            $error_message = 'Semua field harus diisi dengan benar.';
        } else {
            try {
                $stmt = $pdo->prepare("\n                    UPDATE penjualan\n                       SET id_produk = :id_produk,\n                           nama_customer = :nama_customer,\n                           type_customer = :type_customer,\n                           jumlah_terjual = :jumlah,\n                           tanggal_penjualan = :tanggal\n                     WHERE id = :id\n                ");
                $stmt->execute([
                    ':id_produk'     => $id_produk,
                    ':nama_customer' => $nama_customer,
                    ':type_customer' => $type_customer,
                    ':jumlah'        => $jumlah_terjual,
                    ':tanggal'       => $tanggal_penjualan,
                    ':id'            => $id
                ]);
                $success_message = 'Data penjualan berhasil diperbarui.';
            } catch (PDOException $e) {
                $error_message = 'Database error: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM penjualan WHERE id = ?");
                $stmt->execute([$id]);
                $success_message = 'Data penjualan berhasil dihapus.';
            } catch (PDOException $e) {
                $error_message = 'Database error: ' . $e->getMessage();
            }
        } else {
            $error_message = 'ID tidak valid.';
        }
    }
}

// -------------------------------
// Dropdown produk + kategori & subkategori (untuk form & autofill)
// -------------------------------
$products = $pdo->query("\n    SELECT p.id, p.nama,\n           k.nama_kategori AS kategori,\n           s.nama_subkategori AS subkategori\n    FROM produk p\n    LEFT JOIN kategori k    ON p.id_kategori = k.id\n    LEFT JOIN subkategori s ON p.id_subkategori = s.id\n    ORDER BY p.nama ASC\n")->fetchAll(PDO::FETCH_ASSOC);

// Distinct opsi filter
$kategori_opts = $pdo->query("SELECT DISTINCT k.nama_kategori AS kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori=k.id WHERE k.nama_kategori IS NOT NULL ORDER BY k.nama_kategori")->fetchAll(PDO::FETCH_COLUMN);
$subkat_opts   = $pdo->query("SELECT DISTINCT s.nama_subkategori AS subkategori FROM produk p LEFT JOIN subkategori s ON p.id_subkategori=s.id WHERE s.nama_subkategori IS NOT NULL ORDER BY s.nama_subkategori")->fetchAll(PDO::FETCH_COLUMN);
$produk_opts   = $pdo->query("SELECT id, nama FROM produk ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
$type_opts     = $pdo->query("SELECT DISTINCT type_customer FROM penjualan WHERE type_customer IS NOT NULL AND type_customer<>'' ORDER BY type_customer")->fetchAll(PDO::FETCH_COLUMN);

// Peta untuk JS autofill
$productMap = [];
foreach ($products as $pr) {
    $productMap[(int)$pr['id']] = [
        'kategori'    => $pr['kategori'] ?? '',
        'subkategori' => $pr['subkategori'] ?? ''
    ];
}

// -------------------------------
// Filters & Search (GET)
// -------------------------------
$q           = trim($_GET['q'] ?? '');
$start_date  = trim($_GET['start_date'] ?? ''); // YYYY-MM-DD
$end_date    = trim($_GET['end_date'] ?? '');   // YYYY-MM-DD
$fkategori   = trim($_GET['kategori'] ?? '');
$fsubkat     = trim($_GET['subkategori'] ?? '');
$fproduk     = (int)($_GET['id_produk'] ?? 0);
$ftype       = trim($_GET['type_customer'] ?? '');

$where = [];
$params = [];

if ($q !== '') {
    // cari bebas di banyak kolom, termasuk tanggal sebagai string
    $where[] = "(\n        pj.nama_customer LIKE :q OR\n        pj.type_customer LIKE :q OR\n        p.nama LIKE :q OR\n        k.nama_kategori LIKE :q OR\n        s.nama_subkategori LIKE :q OR\n        DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m-%d') LIKE :q OR\n        DATE_FORMAT(pj.tanggal_penjualan, '%d-%m-%Y') LIKE :q\n    )";
    $params[':q'] = "%{$q}%";
}
if ($start_date !== '') {
    $where[] = 'pj.tanggal_penjualan >= :start_date';
    $params[':start_date'] = $start_date;
}
if ($end_date !== '') {
    $where[] = 'pj.tanggal_penjualan <= :end_date';
    $params[':end_date'] = $end_date;
}
if ($fkategori !== '') {
    $where[] = 'k.nama_kategori = :fkategori';
    $params[':fkategori'] = $fkategori;
}
if ($fsubkat !== '') {
    $where[] = 's.nama_subkategori = :fsubkat';
    $params[':fsubkat'] = $fsubkat;
}
if ($fproduk > 0) {
    $where[] = 'p.id = :fproduk';
    $params[':fproduk'] = $fproduk;
}
if ($ftype !== '') {
    $where[] = 'pj.type_customer = :ftype';
    $params[':ftype'] = $ftype;
}

$whereSQL = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// -------------------------------
// Pagination
// -------------------------------
$allowed_page_sizes = [10, 25, 50];
$per_page = (int)($_GET['per_page'] ?? 10);
if (!in_array($per_page, $allowed_page_sizes, true)) $per_page = 10;

$page = max(1, (int)($_GET['page'] ?? 1));

// Total rows (ikut filter)
$sql_count = "SELECT COUNT(*)\n             FROM penjualan pj\n             JOIN produk p      ON pj.id_produk = p.id\n             LEFT JOIN kategori k    ON p.id_kategori = k.id\n             LEFT JOIN subkategori s ON p.id_subkategori = s.id\n             $whereSQL";
$stmt = $pdo->prepare($sql_count);
$stmt->execute($params);
$total_rows = (int)$stmt->fetchColumn();

$total_pages = max(1, (int)ceil($total_rows / $per_page));
if ($page > $total_pages) $page = $total_pages;

$offset = ($page - 1) * $per_page;

// Data page ini (ikutkan kolom customer + join kategori/subkategori untuk tampilan)
$sql_page = "\n    SELECT pj.id, pj.jumlah_terjual, pj.tanggal_penjualan,\n           pj.nama_customer, pj.type_customer,\n           p.id AS id_produk, p.nama AS nama_produk,\n           k.nama_kategori AS kategori,\n           s.nama_subkategori AS subkategori\n    FROM penjualan pj\n    JOIN produk p      ON pj.id_produk = p.id\n    LEFT JOIN kategori k    ON p.id_kategori = k.id\n    LEFT JOIN subkategori s ON p.id_subkategori = s.id\n    $whereSQL\n    ORDER BY pj.tanggal_penjualan DESC, pj.id DESC\n    LIMIT :limit OFFSET :offset\n";
$stmt = $pdo->prepare($sql_page);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':limit',  $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
$stmt->execute();
$sales_page = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper untuk rebuild query string (mempertahankan filter saat ganti page / download)
function keepQuery(array $extra = [])
{
    $qs = $_GET;
    foreach ($extra as $k => $v) {
        if ($v === null) unset($qs[$k]);
        else $qs[$k] = $v;
    }
    return http_build_query($qs);
}

$page_title = 'Kelola Penjualan';
include 'includes/admin_header.php';
?>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-chart-line me-2"></i>Kelola Penjualan</h1>
    </div>

    <!-- Alerts -->
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


    <!-- Form Input Penjualan -->
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Input Data Penjualan Harian</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="create">

                        <div class="mb-3">
                            <label for="nama_customer" class="form-label">Nama Customer</label>
                            <input type="text" class="form-control" id="nama_customer" name="nama_customer" placeholder="cth: Toko Sembako Jaya" required>
                        </div>

                        <!-- Tipe Customer -->
                        <div class="mb-3">
                            <label for="type_customer" class="form-label">Tipe Customer</label>
                            <input type="text" class="form-control" id="type_customer" name="type_customer" placeholder="cth: Retail / Spesial / dll" required>
                        </div>

                        <div class="mb-3">
                            <label for="id_produk" class="form-label">Produk</label>
                            <select class="form-select" id="id_produk" name="id_produk" required>
                                <option value="">-- Pilih Produk --</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= (int)$product['id'] ?>">
                                        <?= h($product['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Kategori/Subkategori otomatis -->
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <input type="text" class="form-control" id="kategori_auto" value="" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subkategori</label>
                            <input type="text" class="form-control" id="subkategori_auto" value="" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah_terjual" class="form-label">Jumlah Terjual (Unit)</label>
                            <input type="number" class="form-control" id="jumlah_terjual" name="jumlah_terjual" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_penjualan" class="form-label">Tanggal Penjualan</label>
                            <input type="date" class="form-control" id="tanggal_penjualan" name="tanggal_penjualan" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Simpan Data
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Filter + Tabel Penjualan + Pagination -->
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filter & Pencarian</h5>
                </div>
                <div class="card-body">
                    <form method="GET" id="filterForm" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Kata kunci</label>
                            <input type="text" name="q" value="<?= h($q) ?>" class="form-control" placeholder="cari: tanggal/nama/tipe/produk/kategori/...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="<?= h($start_date) ?>" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" value="<?= h($end_date) ?>" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-select">
                                <option value="">Semua</option>
                                <?php foreach ($kategori_opts as $opt): ?>
                                    <option value="<?= h($opt) ?>" <?= $fkategori === $opt ? 'selected' : ''; ?>><?= h($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Subkategori</label>
                            <select name="subkategori" class="form-select">
                                <option value="">Semua</option>
                                <?php foreach ($subkat_opts as $opt): ?>
                                    <option value="<?= h($opt) ?>" <?= $fsubkat === $opt ? 'selected' : ''; ?>><?= h($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Produk</label>
                            <select name="id_produk" class="form-select">
                                <option value="0">Semua</option>
                                <?php foreach ($produk_opts as $opt): ?>
                                    <option value="<?= (int)$opt['id'] ?>" <?= $fproduk === (int)$opt['id'] ? 'selected' : ''; ?>><?= h($opt['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipe Customer</label>
                            <select name="type_customer" class="form-select">
                                <option value="">Semua</option>
                                <?php foreach ($type_opts as $opt): ?>
                                    <option value="<?= h($opt) ?>" <?= $ftype === $opt ? 'selected' : ''; ?>><?= h($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 d-flex gap-2 mt-1">
                            <input type="hidden" name="page" value="1">
                            <button class="btn btn-primary"><i class="fas fa-filter me-2"></i>Terapkan</button>
                            <a class="btn btn-outline-secondary" href="penjualan.php"><i class="fas fa-undo me-2"></i>Reset</a>

                            <?php // tombol download sesuai tampilan (file baru)
                            $dl_qs = keepQuery();
                            ?>
                            <a class="btn btn-success ms-auto" href="penjualan_export_view.php?<?= $dl_qs ?>">
                                <i class="fas fa-file-download me-2"></i>Download csv
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Data Penjualan</h5>
                        <form method="GET" class="d-flex align-items-center gap-2">
                            <label class="small me-2">Tampil</label>
                            <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                                <?php foreach ($allowed_page_sizes as $s): ?>
                                    <option value="<?= $s ?>" <?= $s === $per_page ? 'selected' : ''; ?>><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="small ms-2">baris / halaman</span>
                            <?php // pertahankan filter saat ubah per_page 
                            ?>
                            <input type="hidden" name="<?= htmlspecialchars(keepQuery(['per_page' => null, 'page' => null])) ?>" value="">
                            <input type="hidden" name="page" value="1">
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:120px;">Tanggal</th>
                                        <th>Nama Customer</th>
                                        <th>Tipe</th>
                                        <th>Produk</th>
                                        <th class="d-none d-lg-table-cell">Kategori</th>
                                        <th class="d-none d-lg-table-cell">Subkategori</th>
                                        <th style="width:120px;" class="text-end">Jumlah</th>
                                        <th style="width:140px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($sales_page)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">Tidak ada data dengan filter saat ini.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($sales_page as $row): ?>
                                            <tr>
                                                <td><?= date('d M Y', strtotime($row['tanggal_penjualan'])) ?></td>
                                                <td><?= h($row['nama_customer'] ?? '-') ?></td>
                                                <td><?= h($row['type_customer'] ?? '-') ?></td>
                                                <td><?= h($row['nama_produk']) ?></td>
                                                <td class="d-none d-lg-table-cell"><?= h($row['kategori'] ?? '-') ?></td>
                                                <td class="d-none d-lg-table-cell"><?= h($row['subkategori'] ?? '-') ?></td>
                                                <td class="text-end"><?= number_format((int)$row['jumlah_terjual']) ?></td>
                                                <td class="text-center">
                                                    <!-- Edit: buka modal -->
                                                    <button
                                                        class="btn btn-sm btn-outline-primary me-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal"
                                                        data-id="<?= (int)$row['id'] ?>"
                                                        data-id_produk="<?= (int)$row['id_produk'] ?>"
                                                        data-nama_customer="<?= h($row['nama_customer'] ?? '') ?>"
                                                        data-type_customer="<?= h($row['type_customer'] ?? '') ?>"
                                                        data-jumlah="<?= (int)$row['jumlah_terjual'] ?>"
                                                        data-tanggal="<?= h($row['tanggal_penjualan']) ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <!-- Delete: submit form -->
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination (angka + prev/next) -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Pagination" class="mt-2">
                                <ul class="pagination pagination-sm justify-content-end mb-1">
                                    <?php
                                    // helper link
                                    function pageLink($p, $per_page)
                                    {
                                        $base = 'penjualan.php?';
                                        $qs = keepQuery(['page' => $p, 'per_page' => $per_page]);
                                        return $base . $qs;
                                    }
                                    $window = 2;
                                    ?>

                                    <!-- Prev -->
                                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="<?= $page <= 1 ? '#' : pageLink($page - 1, $per_page) ?>">
                                            &laquo; <span class="d-none d-sm-inline">Sebelumnya</span>
                                        </a>
                                    </li>

                                    <?php
                                    $start = max(1, $page - $window);
                                    $end   = min($total_pages, $page + $window);

                                    if ($start > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="' . pageLink(1, $per_page) . '">1</a></li>';
                                        if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                                    }

                                    for ($i = $start; $i <= $end; $i++) {
                                        $active = $i === $page ? ' active' : '';
                                        echo '<li class="page-item' . $active . '"><a class="page-link" href="' . pageLink($i, $per_page) . '">' . $i . '</a></li>';
                                    }

                                    if ($end < $total_pages) {
                                        if ($end < $total_pages - 1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                                        echo '<li class="page-item"><a class="page-link" href="' . pageLink($total_pages, $per_page) . '">' . $total_pages . '</a></li>';
                                    }
                                    ?>

                                    <!-- Next -->
                                    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                        <a class="page-link" href="<?= $page >= $total_pages ? '#' : pageLink($page + 1, $per_page) ?>">
                                            <span class="d-none d-sm-inline">Berikutnya</span> &raquo;
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>

                        <div class="small text-muted">
                            Menampilkan <b><?= count($sales_page) ?></b> dari total <b><?= number_format($total_rows) ?></b> catatan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit-id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Penjualan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Customer</label>
                        <input type="text" class="form-control" name="nama_customer" id="edit-nama-customer" required>
                    </div>
                    <!-- Tipe Customer -->
                    <div class="mb-3">
                        <label class="form-label">Tipe Customer</label>
                        <input type="text" class="form-control" name="type_customer" id="edit-type-customer" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Produk</label>
                        <select class="form-select" name="id_produk" id="edit-id-produk" required>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= (int)$product['id'] ?>"><?= h($product['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Terjual (Unit)</label>
                        <input type="number" class="form-control" name="jumlah_terjual" id="edit-jumlah" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Penjualan</label>
                        <input type="date" class="form-control" name="tanggal_penjualan" id="edit-tanggal" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>Simpan</button>
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Map produk -> kategori/subkategori untuk autofill form create
        const PRODUCTS = <?= json_encode($productMap, JSON_UNESCAPED_UNICODE) ?>;

        document.addEventListener('DOMContentLoaded', function() {
            // Autofill kategori/subkategori (form create)
            const selProduk = document.getElementById('id_produk');
            const inKat = document.getElementById('kategori_auto');
            const inSubkat = document.getElementById('subkategori_auto');

            function fillCat() {
                const pid = selProduk.value;
                if (pid && PRODUCTS[pid]) {
                    inKat.value = PRODUCTS[pid].kategori || '';
                    inSubkat.value = PRODUCTS[pid].subkategori || '';
                } else {
                    inKat.value = '';
                    inSubkat.value = '';
                }
            }
            selProduk.addEventListener('change', fillCat);
            fillCat();

            // Prefill modal edit
            var editModal = document.getElementById('editModal');
            editModal.addEventListener('show.bs.modal', function(event) {
                var btn = event.relatedTarget;
                document.getElementById('edit-id').value = btn.getAttribute('data-id');
                document.getElementById('edit-id-produk').value = btn.getAttribute('data-id_produk');
                document.getElementById('edit-nama-customer').value = btn.getAttribute('data-nama_customer') || '';
                document.getElementById('edit-type-customer').value = btn.getAttribute('data-type_customer') || '';
                document.getElementById('edit-jumlah').value = btn.getAttribute('data-jumlah');
                document.getElementById('edit-tanggal').value = btn.getAttribute('data-tanggal');
            });

            // Submit otomatis saat Enter di input keyword
            const filterForm = document.getElementById('filterForm');
            filterForm.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    filterForm.submit();
                }
            });
        });
    </script>

    <?php include 'includes/admin_footer.php'; ?>