<?php
// includes/db.php
$servername = "localhost";
$username = "root"; // sesuaikan dengan username database Anda
$password = ""; // sesuaikan dengan password database Anda
$dbname = "chibor_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function untuk escape HTML
function h($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Function untuk redirect
function redirect($url)
{
    header("Location: $url");
    exit();
}

// Function untuk cek apakah user sudah login (untuk admin)
function isLoggedIn()
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Function untuk require login (untuk halaman admin)
function requireLogin()
{
    if (!isLoggedIn()) {
        redirect('/admin/login.php');
    }
}

// Function untuk upload gambar
function uploadImage($file, $folder = 'assets/images/')
{
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan. Hanya JPG, PNG, dan GIF yang diperbolehkan.'];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal 5MB.'];
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $target_path = $folder . $filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Gagal mengupload file.'];
    }
}
