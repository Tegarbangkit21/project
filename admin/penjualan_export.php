<?php
// admin/penjualan_export.php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireLogin();

/**
 * Params
 * -------
 * format : csv|xls
 * scope  : all|page|range
 * page, per_page : untuk scope=page
 * mode   : harian|mingguan|bulanan|top (hanya untuk scope=range)
 * start, end, id_produk : filter scope=range
 * detail : 1 untuk keluarkan detail (periode + produk), 0 untuk agregat saja
 * subtotal : 1 untuk menambahkan baris subtotal per-periode (hanya berlaku saat detail=1)
 */

$format   = ($_GET['format'] ?? 'csv');                 // csv|xls
$scope    = ($_GET['scope']  ?? 'all');                 // all|page|range
$mode     = ($_GET['mode']   ?? 'harian');              // harian|mingguan|bulanan|top
$start    = $_GET['start']   ?? null;
$end      = $_GET['end']     ?? null;
$idProduk = (int)($_GET['id_produk'] ?? 0);

$detail   = (($_GET['detail'] ?? '0') === '1');         // default: tidak detail
$includeSubtotal = (($_GET['subtotal'] ?? '1') === '1'); // default: ya (kalau detail)

$page       = max(1, (int)($_GET['page'] ?? 1));
$per_page   = (int)($_GET['per_page'] ?? 10);
$allowed_pp = [10, 25, 50];
if (!in_array($per_page, $allowed_pp, true)) $per_page = 10;
$offset     = ($page - 1) * $per_page;

$rows = [];
$stamp = date('Ymd_His');
$filename_base = "penjualan_{$scope}_{$mode}_{$stamp}";

try {
    if ($scope === 'page') {
        // ekspor data pada halaman (untuk tabel admin/penjualan.php)
        $sql = "
            SELECT pj.id,
                   pj.tanggal_penjualan,
                   p.nama AS nama_produk,
                   pj.jumlah_terjual
              FROM penjualan pj
              JOIN produk p ON pj.id_produk = p.id
             ORDER BY pj.tanggal_penjualan ASC, pj.id ASC
             LIMIT :limit OFFSET :offset
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($scope === 'all') {
        // ekspor semua data mentah
        $sql = "
            SELECT pj.id,
                   pj.tanggal_penjualan,
                   p.nama AS nama_produk,
                   pj.jumlah_terjual
              FROM penjualan pj
              JOIN produk p ON pj.id_produk = p.id
             ORDER BY pj.tanggal_penjualan ASC, pj.id ASC
        ";
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($scope === 'range') {
        // ekspor sesuai rentang + mode
        if (!$start || !$end) {
            $start = date('Y-m-01');
            $end   = date('Y-m-d');
        }
        // validasi tanggal
        $validDate = fn($d) => preg_match('/^\d{4}-\d{2}-\d{2}$/', $d);
        if (!$validDate($start)) $start = date('Y-m-01');
        if (!$validDate($end))   $end   = date('Y-m-d');

        $params = [':start' => $start, ':end' => $end];

        // filter per-produk (kecuali top)
        $whereProduk = '';
        if ($idProduk > 0 && $mode !== 'top') {
            $whereProduk = " AND pj.id_produk = :idp ";
            $params[':idp'] = $idProduk;
        }

        // DETAIL (periode + produk + kategori + subkategori)
        if ($detail && $mode !== 'top') {

            if ($mode === 'harian') {
                $periodeExpr = "DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m-%d')";
                $periodeSort = "pj.tanggal_penjualan";
            } elseif ($mode === 'mingguan') {
                $periodeExpr = "CONCAT('W', YEARWEEK(pj.tanggal_penjualan,3))";
                $periodeSort = "YEARWEEK(pj.tanggal_penjualan,3)";
            } else { // bulanan
                $periodeExpr = "DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m')";
                $periodeSort = "DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m')";
            }

            $sql = "
                SELECT {$periodeExpr} AS periode,
                       p.nama AS produk,
                       k.nama_kategori AS kategori,
                       COALESCE(s.nama_subkategori, '') AS subkategori,
                       SUM(pj.jumlah_terjual) AS jumlah
                  FROM penjualan pj
                  JOIN produk p   ON p.id = pj.id_produk
             LEFT JOIN kategori k ON k.id = p.id_kategori
             LEFT JOIN subkategori s ON s.id = p.id_subkategori
                 WHERE pj.tanggal_penjualan BETWEEN :start AND :end
                       {$whereProduk}
              GROUP BY periode, p.id, p.nama, k.nama_kategori, s.nama_subkategori
              ORDER BY {$periodeSort} ASC, p.nama ASC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ===== Tambahkan SUBTOTAL per-periode (opsional) =====
            if ($includeSubtotal) {
                $grouped = [];
                foreach ($rows as $r) {
                    $grouped[$r['periode']][] = $r;
                }
                $withSub = [];
                foreach ($grouped as $periode => $items) {
                    $subtotal = 0;
                    foreach ($items as $it) {
                        $withSub[] = $it;
                        $subtotal += (int)$it['jumlah'];
                    }
                    // baris subtotal
                    $withSub[] = [
                        'periode'        => $periode,
                        'produk'         => 'Subtotal',
                        'kategori'       => '',
                        'subkategori'    => '',
                        'jumlah'         => $subtotal,
                        '__is_subtotal'  => 1,
                    ];
                }
                $rows = $withSub;
            }
        } else {
            // AGREGAT (tanpa detail) atau TOP
            if ($mode === 'harian') {
                $sql = "
                    SELECT DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m-%d') AS periode,
                           SUM(pj.jumlah_terjual) AS total
                      FROM penjualan pj
                     WHERE pj.tanggal_penjualan BETWEEN :start AND :end
                           {$whereProduk}
                  GROUP BY pj.tanggal_penjualan
                  ORDER BY pj.tanggal_penjualan ASC
                ";
            } elseif ($mode === 'mingguan') {
                $sql = "
                    SELECT CONCAT('W', YEARWEEK(pj.tanggal_penjualan,3)) AS periode,
                           SUM(pj.jumlah_terjual) AS total
                      FROM penjualan pj
                     WHERE pj.tanggal_penjualan BETWEEN :start AND :end
                           {$whereProduk}
                  GROUP BY YEARWEEK(pj.tanggal_penjualan,3)
                  ORDER BY YEARWEEK(pj.tanggal_penjualan,3) ASC
                ";
            } elseif ($mode === 'bulanan') {
                $sql = "
                    SELECT DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m') AS periode,
                           SUM(pj.jumlah_terjual) AS total
                      FROM penjualan pj
                     WHERE pj.tanggal_penjualan BETWEEN :start AND :end
                           {$whereProduk}
                  GROUP BY DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m')
                  ORDER BY DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m') ASC
                ";
            } else { // top products
                $sql = "
                    SELECT p.nama AS periode,
                           SUM(pj.jumlah_terjual) AS total
                      FROM penjualan pj
                      JOIN produk p ON p.id = pj.id_produk
                     WHERE pj.tanggal_penjualan BETWEEN :start AND :end
                  GROUP BY p.id, p.nama
                  ORDER BY total DESC
                     LIMIT 100
                ";
                // top tidak pakai filter produk tunggal
                unset($params[':idp']);
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Samakan nama kolom agar generik
            $rows = array_map(function ($r) {
                return [
                    'periode' => $r['periode'] ?? '',
                    'total'   => (int)($r['total'] ?? 0),
                ];
            }, $rows);
        }
    }
} catch (PDOException $e) {
    $rows = [];
}

/* =========================
   OUTPUT: CSV / XLS (HTML)
   ========================= */

if ($format === 'csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header("Content-Disposition: attachment; filename=\"{$filename_base}.csv\"");
    echo "\xEF\xBB\xBF"; // BOM UTF-8
    $out = fopen('php://output', 'w');

    if ($scope === 'range') {
        if ($detail && $mode !== 'top') {
            // header detail
            fputcsv($out, ['Periode', 'Produk', 'Kategori', 'Subkategori', 'Jumlah']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r['periode']     ?? '',
                    $r['produk']      ?? '',
                    $r['kategori']    ?? '',
                    $r['subkategori'] ?? '',
                    (int)($r['jumlah'] ?? 0),
                ]);
            }
        } else {
            // header agregat
            fputcsv($out, ['Periode/Label', 'Total Unit']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r['periode'] ?? '',
                    (int)($r['total'] ?? 0),
                ]);
            }
        }
    } else {
        // scope page/all (raw)
        fputcsv($out, ['ID', 'Tanggal', 'Produk', 'Jumlah']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id'] ?? '',
                isset($r['tanggal_penjualan']) ? date('Y-m-d', strtotime($r['tanggal_penjualan'])) : '',
                $r['nama_produk'] ?? '',
                (int)($r['jumlah_terjual'] ?? 0)
            ]);
        }
    }
    fclose($out);
    exit;
}

if ($format === 'xls') {
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header("Content-Disposition: attachment; filename=\"{$filename_base}.xls\"");
    echo "<table border='1'><thead><tr>";

    if ($scope === 'range') {
        if ($detail && $mode !== 'top') {
            echo "<th>Periode</th><th>Produk</th><th>Kategori</th><th>Subkategori</th><th>Jumlah</th></tr></thead><tbody>";
            foreach ($rows as $r) {
                $isSubtotal = !empty($r['__is_subtotal']);
                echo "<tr" . ($isSubtotal ? " style='font-weight:bold;background:#f6f6f6;'" : "") . ">";
                echo "<td>" . htmlspecialchars($r['periode'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($r['produk'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($r['kategori'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($r['subkategori'] ?? '') . "</td>";
                echo "<td>" . (int)($r['jumlah'] ?? 0) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<th>Periode/Label</th><th>Total Unit</th></tr></thead><tbody>";
            foreach ($rows as $r) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($r['periode'] ?? '') . "</td>";
                echo "<td>" . (int)($r['total'] ?? 0) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        }
    } else {
        echo "<th>ID</th><th>Tanggal</th><th>Produk</th><th>Jumlah</th></tr></thead><tbody>";
        foreach ($rows as $r) {
            echo "<tr>";
            echo "<td>" . (int)($r['id'] ?? 0) . "</td>";
            echo "<td>" . htmlspecialchars(isset($r['tanggal_penjualan']) ? date('Y-m-d', strtotime($r['tanggal_penjualan'])) : '') . "</td>";
            echo "<td>" . htmlspecialchars($r['nama_produk'] ?? '') . "</td>";
            echo "<td>" . (int)($r['jumlah_terjual'] ?? 0) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    }
    exit;
}

http_response_code(400);
echo "Format tidak dikenali.";
