<?php
// admin/logout.php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
// Destroy all session data
session_destroy();

// Clear any remember me cookies if they exist
if (isset($_COOKIE['admin_remember'])) {
    setcookie('admin_remember', '', time() - 3600, '/admin/');
}

// Redirect to login page
header("Location: login.php?logged_out=1");
exit();
