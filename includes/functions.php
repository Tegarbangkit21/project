<?php
// File: includes/functions.php
// TUGASNYA MENYIMPAN SEMUA FUNGSI BANTUAN

// Memulai sesi jika belum ada, penting untuk fungsi login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
        // Path ini relatif terhadap file di folder /admin
        redirect('login.php');
    }
}

// Function untuk upload gambar
function uploadImage($file, $folder)
{
    // Implementasi fungsi upload Anda di sini
    // ...
}
