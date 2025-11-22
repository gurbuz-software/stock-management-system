<?php
// Veritabanı bağlantı ayarları - Docker ortam değişkenleri
$host = getenv('DB_HOST') ?: 'php-stok-takip-root-v4xf95';
$dbname = getenv('DB_NAME') ?: 'root';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'root';
$port = getenv('DB_PORT') ?: '3306';

// Bağlantı seçenekleri
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
];

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, $options);
} catch(PDOException $e) {
    // Daha detaylı hata mesajı
    $error_message = "Veritabanı bağlantı hatası: " . $e->getMessage();
    error_log($error_message);
    
    // Kullanıcı dostu hata mesajı
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        die("Veritabanı bulunamadı: '$dbname'. Lütfen veritabanının oluşturulduğundan emin olun.");
    } elseif (strpos($e->getMessage(), 'Access denied') !== false) {
        die("Veritabanı erişim hatası. Kullanıcı adı veya şifre yanlış.");
    } else {
        die("Veritabanı bağlantı hatası: " . $e->getMessage());
    }
}

// Veritabanı bağlantısı başarılı
// echo "Veritabanı bağlantısı başarılı!";
?>