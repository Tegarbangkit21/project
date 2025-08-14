<?php
// includes/functions.php

// Memulai sesi jika belum ada, penting untuk fungsi login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Fungsi shortcut untuk htmlspecialchars untuk keamanan.
 */
function h(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Fungsi untuk redirect ke URL lain.
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit();
}

/**
 * Fungsi untuk memeriksa apakah admin sudah login.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Fungsi yang mewajibkan login untuk mengakses halaman admin.
 * Harus dipanggil dari dalam folder /admin.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

// Anda bisa memindahkan fungsi uploadImage ke sini juga jika ingin
// atau biarkan di file yang relevan saja. Untuk kerapian, kita pindahkan ke sini.

/**
 * Fungsi untuk meng-handle upload gambar.
 * @param array $file - Data dari $_FILES['nama_input']
 * @param string $folder - Path folder tujuan dari root proyek
 * @return array Hasil upload ['success' => bool, 'message' => string, 'filename' => string]
 */
function uploadImage(array $file, string $folder = '../assets/images/'): array
{
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan (hanya JPG, PNG, GIF, WebP).'];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (maksimal 5MB).'];
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'img_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $target_path = $folder . $filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Gagal memindahkan file yang diupload.'];
    }
}
