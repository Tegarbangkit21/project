<?php
// File: includes/db.php
// TUGASNYA HANYA KONEKSI DATABASE

// Definisikan BASE_URL untuk path yang konsisten
define('BASE_URL', '/Platform-Digital-UMKM/'); // Sesuaikan dengan nama folder proyek Anda

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chibor_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}
