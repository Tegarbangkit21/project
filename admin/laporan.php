<?php
// admin/laporan.php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireLogin();

$page_title = 'Laporan Penjualan';

// -----------------------
// Filters (GET)
// -----------------------
$mode         = $_GET['mode']        ?? 'harian'; // harian|mingguan|bulanan|top
$start        = $_GET['start']       ?? date('Y-m-01');
$end          = $_GET['end']         ?? date('Y-m-d');
$id_kategori  = (int)($_GET['id_kategori'] ?? 0);
$id_produk    = (int)($_GET['id_produk']    ?? 0);
$detail       = (($_GET['detail']   ?? '0') === '1');  // tampilkan detail per-produk
$subtotal     = (($_GET['subtotal'] ?? '1') === '1');  // (dipakai export)

// validasi tanggal
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start)) $start = date('Y-m-01');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end))   $end   = date('Y-m-d');

// -----------------------
// Data untuk dropdown
// -----------------------
$kategori_rows = $pdo->query("SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori")->fetchAll(PDO::FETCH_ASSOC);

if ($id_kategori > 0) {
    $stmtP = $pdo->prepare("SELECT id, nama FROM produk WHERE id_kategori = :kid ORDER BY nama");
    $stmtP->execute([':kid' => $id_kategori]);
    $produk_rows = $stmtP->fetchAll(PDO::FETCH_ASSOC);
} else {
    $produk_rows = $pdo->query("SELECT id, nama FROM produk ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
}

// -----------------------
// Query builder by mode (untuk grafik & ringkasan)
// -----------------------
$params = [':start' => $start, ':end' => $end];
$needJoinProduk = ($id_kategori > 0) || ($mode === 'top'); // kalau filter kategori atau mode top, wajib join produk

$where = " WHERE pj.tanggal_penjualan BETWEEN :start AND :end ";
if ($id_kategori > 0) {
    $where .= " AND p.id_kategori = :kid ";
    $params[':kid'] = $id_kategori;
}
if ($id_produk > 0 && $mode !== 'top') {
    $where .= " AND pj.id_produk = :idp ";
    $params[':idp'] = $id_produk;
}

$label = [];
$values = [];
$table_rows = [];
$title_suffix = '';

try {
    if ($mode === 'harian') {
        $sql = "
          SELECT pj.tanggal_penjualan AS traw,
                 DATE_FORMAT(pj.tanggal_penjualan, '%d %b %Y') AS label,
                 SUM(pj.jumlah_terjual) AS total
            FROM penjualan pj
            " . ($needJoinProduk ? "JOIN produk p ON p.id = pj.id_produk" : "") . "
            $where
           GROUP BY pj.tanggal_penjualan
           ORDER BY pj.tanggal_penjualan ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $table_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $label  = array_column($table_rows, 'label');
        $values = array_map('intval', array_column($table_rows, 'total'));
        $title_suffix = ' (Harian)';
    } elseif ($mode === 'mingguan') {
        // label minggu ramah: min–max tanggal yang ADA di data tiap minggu
        $sql = "
          SELECT YEARWEEK(pj.tanggal_penjualan, 3) AS wk,
                 CONCAT('Minggu ',
                        DATE_FORMAT(MIN(pj.tanggal_penjualan),'%d %b'),
                        ' – ',
                        DATE_FORMAT(MAX(pj.tanggal_penjualan),'%d %b %Y')) AS label,
                 SUM(pj.jumlah_terjual) AS total
            FROM penjualan pj
            " . ($needJoinProduk ? "JOIN produk p ON p.id = pj.id_produk" : "") . "
            $where
           GROUP BY YEARWEEK(pj.tanggal_penjualan, 3)
           ORDER BY YEARWEEK(pj.tanggal_penjualan, 3) ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $table_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $label  = array_column($table_rows, 'label');
        $values = array_map('intval', array_column($table_rows, 'total'));
        $title_suffix = ' (Mingguan)';
    } elseif ($mode === 'bulanan') {
        $sql = "
          SELECT DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m') AS ym,
                 DATE_FORMAT(pj.tanggal_penjualan, '%b %Y') AS label,
                 SUM(pj.jumlah_terjual) AS total
            FROM penjualan pj
            " . ($needJoinProduk ? "JOIN produk p ON p.id = pj.id_produk" : "") . "
            $where
           GROUP BY DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m')
           ORDER BY DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m') ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $table_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $label  = array_column($table_rows, 'label');
        $values = array_map('intval', array_column($table_rows, 'total'));
        $title_suffix = ' (Bulanan)';
    } else { // top products
        // untuk top, join produk WAJIB
        $sql = "
          SELECT p.nama AS label,
                 SUM(pj.jumlah_terjual) AS total
            FROM penjualan pj
            JOIN produk p ON p.id = pj.id_produk
            $where
           GROUP BY p.id, p.nama
           ORDER BY total DESC
           LIMIT 10
        ";
        // $where sudah memuat filter kategori, tapi tidak memuat filter id_produk (sengaja)
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $table_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $label  = array_column($table_rows, 'label');
        $values = array_map('intval', array_column($table_rows, 'total'));
        $title_suffix = ' (Top Produk)';
    }
} catch (PDOException $e) {
    $table_rows = [];
    $label = [];
    $values = [];
}

$chart_labels = json_encode($label, JSON_UNESCAPED_UNICODE);
$chart_values = json_encode($values);

// -----------------------
// Query tabel detail per-produk (opsional, tidak mempengaruhi grafik)
// -----------------------
$detail_rows = [];
if ($detail && $mode !== 'top') {
    $params2 = [':start' => $start, ':end' => $end];
    $where2  = " WHERE pj.tanggal_penjualan BETWEEN :start AND :end ";
    if ($id_kategori > 0) {
        $where2 .= " AND p.id_kategori = :kid ";
        $params2[':kid'] = $id_kategori;
    }
    if ($id_produk > 0) {
        $where2 .= " AND pj.id_produk = :idp ";
        $params2[':idp'] = $id_produk;
    }

    if ($mode === 'harian') {
        $period_sql   = "DATE(pj.tanggal_penjualan)";
        $period_label = "DATE_FORMAT(pj.tanggal_penjualan, '%d %b %Y')";
        $order_key    = "MIN(pj.tanggal_penjualan)";
    } elseif ($mode === 'mingguan') {
        $period_sql   = "YEARWEEK(pj.tanggal_penjualan,3)";
        $period_label = "CONCAT('Minggu ', DATE_FORMAT(MIN(pj.tanggal_penjualan),'%d %b'),' – ', DATE_FORMAT(MAX(pj.tanggal_penjualan),'%d %b %Y'))";
        $order_key    = "YEARWEEK(pj.tanggal_penjualan,3)";
    } else { // bulanan
        $period_sql   = "DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m')";
        $period_label = "DATE_FORMAT(pj.tanggal_penjualan, '%b %Y')";
        $order_key    = "DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m')";
    }

    $sql_detail = "
        SELECT 
            $period_sql   AS periode_key,
            $period_label AS periode_label,
            p.nama AS produk,
            COALESCE(k.nama_kategori,'-')   AS kategori,
            COALESCE(s.nama_subkategori,'-') AS subkategori,
            SUM(pj.jumlah_terjual) AS qty
        FROM penjualan pj
        JOIN produk p           ON p.id = pj.id_produk
        LEFT JOIN kategori k    ON k.id = p.id_kategori
        LEFT JOIN subkategori s ON s.id = p.id_subkategori
        $where2
        GROUP BY periode_key, p.id, p.nama, k.nama_kategori, s.nama_subkategori
        ORDER BY $order_key ASC, p.nama ASC
    ";
    $stmt2 = $pdo->prepare($sql_detail);
    $stmt2->execute($params2);
    $detail_rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}

include 'includes/admin_header.php';
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-file-chart-line me-2"></i>Laporan Penjualan<?= h($title_suffix) ?></h1>
        <div>
            <a href="../index.php" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fas fa-globe me-1"></i> Lihat Situs</a>
        </div>
    </div>

    <!-- FILTERS -->
    <form class="card mb-3" method="GET">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Mode</label>
                <select name="mode" class="form-select">
                    <option value="harian" <?= $mode === 'harian'   ? 'selected' : ''; ?>>Harian</option>
                    <option value="mingguan" <?= $mode === 'mingguan' ? 'selected' : ''; ?>>Mingguan</option>
                    <option value="bulanan" <?= $mode === 'bulanan'  ? 'selected' : ''; ?>>Bulanan</option>
                    <option value="top" <?= $mode === 'top'      ? 'selected' : ''; ?>>Produk Terlaris</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="start" value="<?= h($start) ?>" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="end" value="<?= h($end) ?>" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Kategori (opsional)</label>
                <select name="id_kategori" class="form-select" onchange="this.form.submit()">
                    <option value="0">Semua Kategori</option>
                    <?php foreach ($kategori_rows as $k): ?>
                        <option value="<?= (int)$k['id'] ?>" <?= $id_kategori === (int)$k['id'] ? 'selected' : ''; ?>>
                            <?= h($k['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Produk (opsional)</label>
                <select name="id_produk" class="form-select">
                    <option value="0">Semua Produk</option>
                    <?php foreach ($produk_rows as $p): ?>
                        <option value="<?= (int)$p['id'] ?>" <?= $id_produk === (int)$p['id'] ? 'selected' : ''; ?>><?= h($p['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="detail" name="detail" value="1" <?= $detail ? 'checked' : '' ?>>
                    <label class="form-check-label" for="detail">Tampilkan detail per produk</label>
                </div>
            </div>

            <div class="col-12 d-flex flex-wrap gap-3 mt-2 align-items-center">
                <button class="btn btn-primary"><i class="fas fa-filter me-1"></i>Terapkan</button>

                <?php
                // rakit query string export (ikut semua filter)
                $qs = http_build_query([
                    'format'      => 'csv',
                    'scope'       => 'range',
                    'mode'        => $mode,
                    'start'       => $start,
                    'end'         => $end,
                    'id_kategori' => $id_kategori,
                    'id_produk'   => $id_produk,
                    'detail'      => $detail ? 1 : 0,
                    'subtotal'    => $subtotal ? 1 : 0,
                ]);
                $qs_xls = str_replace('format=csv', 'format=xls', $qs);
                ?>
                <a class="btn btn-outline-secondary" href="penjualan_export.php?<?= $qs ?>">
                    <i class="fas fa-file-csv me-1"></i>Export CSV
                </a>
                <a class="btn btn-outline-secondary" href="penjualan_export.php?<?= $qs_xls ?>">
                    <i class="fas fa-file-excel me-1"></i>Export Excel
                </a>
            </div>
        </div>
    </form>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Grafik <?= h(ucfirst($mode)) ?></h5>
                </div>
                <div class="card-body" style="height:360px;">
                    <canvas id="reportChart"></canvas>
                    <?php if (empty($table_rows)): ?>
                        <div class="text-center text-muted small mt-3">Tidak ada data pada rentang ini.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Ringkasan</h6>
                </div>
                <div class="card-body">
                    <div class="small text-muted mb-2">
                        <i class="fas fa-calendar me-1"></i>Rentang:
                        <b><?= h(date('d M Y', strtotime($start))) ?></b> – <b><?= h(date('d M Y', strtotime($end))) ?></b>
                    </div>

                    <?php if ($id_kategori > 0):
                        $kn = array_values(array_filter($kategori_rows, fn($r) => (int)$r['id'] === $id_kategori))[0]['nama_kategori'] ?? '';
                    ?>
                        <div class="small text-muted mb-2"><i class="fas fa-tag me-1"></i>Kategori: <b><?= h($kn) ?></b></div>
                    <?php else: ?>
                        <div class="small text-muted mb-2"><i class="fas fa-tags me-1"></i>Kategori: <b>Semua</b></div>
                    <?php endif; ?>

                    <?php if ($id_produk > 0):
                        $pn = array_values(array_filter($produk_rows, fn($r) => (int)$r['id'] === $id_produk))[0]['nama'] ?? '';
                    ?>
                        <div class="small text-muted mb-2"><i class="fas fa-box me-1"></i>Produk: <b><?= h($pn) ?></b></div>
                    <?php else: ?>
                        <div class="small text-muted mb-2"><i class="fas fa-boxes me-1"></i>Produk: <b>Semua</b></div>
                    <?php endif; ?>

                    <?php
                    $total = array_sum(array_map(fn($r) => (int)$r['total'], $table_rows));
                    $rows  = count($table_rows);
                    ?>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Unit</span>
                        <span class="fw-semibold"><?= number_format($total) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted"><?= $mode === 'top' ? 'Jumlah Produk' : 'Jumlah Periode' ?></span>
                        <span class="fw-semibold"><?= number_format($rows) ?></span>
                    </div>
                    <?php if ($mode !== 'top'): ?>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Rata-rata</span>
                            <span class="fw-semibold"><?= $rows ? number_format(round($total / $rows)) : 0 ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tabel data -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Data</h6>
                </div>
                <div class="card-body table-responsive">
                    <?php if ($detail && $mode !== 'top'): ?>
                        <!-- DETAIL PER PRODUK -->
                        <table class="table table-striped table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="min-width:160px;">Periode</th>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th>Subkategori</th>
                                    <th class="text-end" style="min-width:120px;">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($detail_rows)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada data.</td>
                                    </tr>
                                    <?php else:
                                    // kelompokkan per-periode (untuk subtotal tampilan)
                                    $grouped = [];
                                    foreach ($detail_rows as $r) {
                                        $grouped[$r['periode_label']][] = $r;
                                    }
                                    foreach ($grouped as $periode => $rows):
                                        $subt = array_sum(array_map(fn($x) => (int)$x['qty'], $rows));
                                    ?>
                                        <tr class="table-light">
                                            <td colspan="5" class="fw-semibold">
                                                <?= h($periode) ?> — <span class="text-muted">Subtotal: <?= number_format($subt) ?></span>
                                            </td>
                                        </tr>
                                        <?php foreach ($rows as $r): ?>
                                            <tr>
                                                <td><?= h($periode) ?></td>
                                                <td><?= h($r['produk']) ?></td>
                                                <td><?= h($r['kategori']) ?></td>
                                                <td><?= h($r['subkategori']) ?></td>
                                                <td class="text-end"><?= number_format((int)$r['qty']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                <?php endforeach;
                                endif; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <!-- RINGKAS -->
                        <table class="table table-striped table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th><?= $mode === 'top' ? 'Produk' : 'Periode' ?></th>
                                    <th class="text-end">Total Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($table_rows)): ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">Tidak ada data.</td>
                                    </tr>
                                    <?php else: foreach ($table_rows as $r): ?>
                                        <tr>
                                            <td><?= h($r['label'] ?? $r['periode'] ?? '') ?></td>
                                            <td class="text-end"><?= number_format((int)$r['total']) ?></td>
                                        </tr>
                                <?php endforeach;
                                endif; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('reportChart');
        if (!el) return;

        const labels = <?= $chart_labels ?>;
        const values = <?= $chart_values ?>;
        const mode = "<?= h($mode) ?>";

        new Chart(el, {
            type: (mode === 'top') ? 'bar' : 'line',
            data: {
                labels,
                datasets: [{
                    label: (mode === 'top') ? 'Total Unit (Top 10 Produk)' : 'Total Unit',
                    data: values,
                    borderWidth: 2,
                    tension: 0.3,
                    pointRadius: 0,
                    fill: (mode === 'top') ? false : true,
                    backgroundColor: (mode === 'top') ? undefined : 'rgba(0,37,143,0.10)',
                    borderColor: '#00258f'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grace: '5%'
                    },
                    x: {
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: 12
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });
    });
</script>

<?php include 'includes/admin_footer.php'; ?>