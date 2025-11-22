<?php
session_start();
require_once 'config/database.php';

// Veritabanı tablolarını kontrol et ve gerekirse oluştur
function checkAndCreateTables($pdo) {
    try {
        // users tablosunu kontrol et
        $pdo->query("SELECT 1 FROM users LIMIT 1");
        // Tablo mevcut, devam et
        return true;
    } catch (PDOException $e) {
        // Tablo yoksa oluştur
        echo "<div style='padding: 20px; background: #f8f9fa; border-left: 4px solid #007bff; margin: 20px;'>";
        echo "<h3>Veritabanı Kurulumu</h3>";
        echo "<p>Tablolar oluşturuluyor... Lütfen bekleyin.</p>";
        
        try {
            // SQL dosyasını oku ve çalıştır
            $sql = file_get_contents('config/init.sql');
            if ($sql === false) {
                throw new Exception("init.sql dosyası bulunamadı!");
            }
            
            $pdo->exec($sql);
            echo "<p style='color: green;'>✓ Tablolar başarıyla oluşturuldu!</p>";
            echo "<p>Sayfa 3 saniye içinde yeniden yönlendirilecek...</p>";
            echo "<script>setTimeout(function() { window.location.href = window.location.href; }, 3000);</script>";
        } catch (Exception $ex) {
            echo "<p style='color: red;'>✗ Tablo oluşturma hatası: " . htmlspecialchars($ex->getMessage()) . "</p>";
        }
        
        echo "</div>";
        exit;
    }
}

// Tabloları kontrol et
checkAndCreateTables($pdo);

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