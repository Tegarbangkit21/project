<?php
// admin/penjualan_export_view.php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireLogin();


// Ambil filter dari query string (harus sama seperti di penjualan.php)
$q = trim($_GET['q'] ?? '');
$start_date = trim($_GET['start_date'] ?? '');
$end_date = trim($_GET['end_date'] ?? '');
$fkategori = trim($_GET['kategori'] ?? '');
$fsubkat = trim($_GET['subkategori'] ?? '');
$fproduk = (int)($_GET['id_produk'] ?? 0);
$ftype = trim($_GET['type_customer'] ?? '');


$where = [];
$params = [];


if ($q !== '') {
    $where[] = "(\n pj.nama_customer LIKE :q OR\n pj.type_customer LIKE :q OR\n p.nama LIKE :q OR\n k.nama_kategori LIKE :q OR\n s.nama_subkategori LIKE :q OR\n DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m-%d') LIKE :q OR\n DATE_FORMAT(pj.tanggal_penjualan, '%d-%m-%Y') LIKE :q\n )";
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


$sql = "\n SELECT DATE_FORMAT(pj.tanggal_penjualan, '%Y-%m-%d') AS tgl,\n pj.nama_customer, pj.type_customer,\n p.nama AS nama_produk,\n k.nama_kategori AS kategori,\n s.nama_subkategori AS subkategori,\n pj.jumlah_terjual AS jumlah\n FROM penjualan pj\n JOIN produk p ON pj.id_produk = p.id\n LEFT JOIN kategori k ON p.id_kategori = k.id\n LEFT JOIN subkategori s ON p.id_subkategori = s.id\n $whereSQL\n ORDER BY pj.tanggal_penjualan DESC, pj.id DESC\n";


$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


$filename = 'penjualan_view_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);


$out = fopen('php://output', 'w');
// Header sesuai tampilan
fputcsv($out, ['Tanggal', 'Nama Customer', 'Tipe', 'Produk', 'Kategori', 'Subkategori', 'Jumlah']);
foreach ($data as $r) {
    fputcsv($out, [
        $r['tgl'],
        $r['nama_customer'],
        $r['type_customer'],
        $r['nama_produk'],
        $r['kategori'],
        $r['subkategori'],
        (int)$r['jumlah'],
    ]);
}


fclose($out);
exit;
