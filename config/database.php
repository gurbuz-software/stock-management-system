<?php
// Veritabanı bağlantı ayarları - Docker ortam değişkenleri
$host = getenv('DB_HOST') ?: 'php-stok-takip-root-v4xf95';
$dbname = getenv('DB_NAME') ?: 'root';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>