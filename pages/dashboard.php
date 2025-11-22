<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/products.php';

$auth = new Auth($pdo);
$products = new Products($pdo);

// Giriş kontrolü
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Ürünleri getir
$allProducts = $products->getAllProducts();
$lowStockProducts = $products->getLowStockProducts();

// İstatistikler
$totalProducts = is_array($allProducts) ? count($allProducts) : 0;
$totalLowStock = is_array($lowStockProducts) ? count($lowStockProducts) : 0;
$totalCategories = array_unique(array_column($allProducts, 'category'));
$totalCategoriesCount = count($totalCategories);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Takip - Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <h1 class="logo">Stok Takip</h1>
                <nav class="nav">
                    <a href="dashboard.php" class="nav-link active">Dashboard</a>
                    <a href="products.php" class="nav-link">Ürünler</a>
                    <?php if ($auth->isAdmin()): ?>
                        <a href="admin.php" class="nav-link">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php" class="nav-link">Çıkış</a>
                </nav>
                <div class="user-info">
                    <span>Hoş geldin, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="dashboard-header">
                <h2>Dashboard</h2>
                <p>Sistem genel bakış</p>
            </div>

            <!-- İstatistik Kartları -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-content">
                        <h3><?php echo $totalProducts; ?></h3>
                        <p>Toplam Ürün</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">⚠️</div>
                    <div class="stat-content">
                        <h3><?php echo $totalLowStock; ?></h3>
                        <p>Düşük Stok</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📂</div>
                    <div class="stat-content">
                        <h3><?php echo $totalCategoriesCount; ?></h3>
                        <p>Kategori</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">👤</div>
                    <div class="stat-content">
                        <h3><?php echo $auth->isAdmin() ? 'Admin' : 'Kullanıcı'; ?></h3>
                        <p>Yetki Seviyesi</p>
                    </div>
                </div>
            </div>

            <!-- Düşük Stok Uyarıları -->
            <?php if ($totalLowStock > 0): ?>
                <div class="alert-section">
                    <div class="alert alert-warning">
                        <h3>⚠️ Düşük Stok Uyarısı</h3>
                        <p><?php echo $totalLowStock; ?> ürünün stok seviyesi kritik seviyede!</p>
                    </div>

                    <div class="low-stock-grid">
                        <?php foreach ($lowStockProducts as $product): ?>
                            <div class="product-card warning">
                                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                <p>Mevcut Stok: <?php echo $product['stock_quantity']; ?></p>
                                <p>Minimum: <?php echo $product['min_stock_level']; ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Son Eklenen Ürünler -->
            <div class="recent-products">
                <h3>Son Eklenen Ürünler</h3>
                <div class="products-grid">
                    <?php
                    $recentProducts = array_slice($allProducts, 0, 6);
                    foreach ($recentProducts as $product):
                    ?>
                        <div class="product-card">
                            <div class="product-header">
                                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                            </div>
                            <div class="product-info">
                                <p class="product-price">₺<?php echo number_format($product['price'], 2); ?></p>
                                <p class="product-stock">Stok: <?php echo $product['stock_quantity']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/script.js"></script>
</body>
</html>