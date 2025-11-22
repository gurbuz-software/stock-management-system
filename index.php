<?php
session_start();
require_once 'config/database.php';

// Kullanıcı giriş durumunu kontrol et
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

// Ana sayfa içeriği
if ($isLoggedIn) {
    header('Location: pages/dashboard.php');
    exit;
} else {
    header('Location: pages/login.php');
    exit;
}
?>