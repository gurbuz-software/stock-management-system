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

// Ürün işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $category = $_POST['category'] ?? '';
            $price = $_POST['price'] ?? '';
            $stock_quantity = $_POST['stock_quantity'] ?? '';
            $min_stock_level = $_POST['min_stock_level'] ?? '';
            $barcode = $_POST['barcode'] ?? '';
            $image_url = $_POST['image_url'] ?? '';

            $result = $products->addProduct($name, $description, $category, $price, $stock_quantity, $min_stock_level, $barcode, $image_url, $_SESSION['user_id']);

            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            break;

        case 'update':
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $category = $_POST['category'] ?? '';
            $price = $_POST['price'] ?? '';
            $stock_quantity = $_POST['stock_quantity'] ?? '';
            $min_stock_level = $_POST['min_stock_level'] ?? '';
            $barcode = $_POST['barcode'] ?? '';
            $image_url = $_POST['image_url'] ?? '';

            $result = $products->updateProduct($id, $name, $description, $category, $price, $stock_quantity, $min_stock_level, $barcode, $image_url);

            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            break;

        case 'delete':
            $id = $_POST['id'] ?? '';
            $result = $products->deleteProduct($id);

            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            break;
    }

    header('Location: products.php');
    exit;
}

// Ürünleri getir
$allProducts = $products->getAllProducts();
$categories = ['Elektronik', 'Giyim', 'Ev & Yaşam', 'Kırtasiye', 'Diğer'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Takip - Ürünler</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <script src="../js/interactive-bg.js"></script>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <h1 class="logo">Stok Takip</h1>
                <nav class="nav">
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <a href="products.php" class="nav-link active">Ürünler</a>
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
                <h2>Ürün Yönetimi</h2>
                <p>Ürün ekleme, düzenleme ve silme işlemleri</p>
            </div>

            <!-- Mesajlar -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Ürün Ekleme Formu -->
            <div class="form-section">
                <h3>Yeni Ürün Ekle</h3>
                <form method="POST" class="product-form">
                    <input type="hidden" name="action" value="add">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Ürün Adı</label>
                            <input type="text" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="category">Kategori</label>
                            <select id="category" name="category" required>
                                <option value="">Kategori Seçin</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Açıklama</label>
                        <textarea id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Fiyat (₺)</label>
                            <input type="number" id="price" name="price" step="0.01" min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="stock_quantity">Stok Miktarı</label>
                            <input type="number" id="stock_quantity" name="stock_quantity" min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="min_stock_level">Minimum Stok</label>
                            <input type="number" id="min_stock_level" name="min_stock_level" min="0" value="5" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="barcode">Barkod</label>
                            <input type="text" id="barcode" name="barcode">
                        </div>

                        <div class="form-group">
                            <label for="image_url">Resim URL</label>
                            <input type="url" id="image_url" name="image_url" placeholder="https://example.com/image.jpg">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Ürün Ekle</button>
                </form>
            </div>

            <!-- Ürün Listesi -->
            <div class="products-section">
                <h3>Ürün Listesi</h3>
                <div class="products-grid">
                    <?php if (is_array($allProducts) && count($allProducts) > 0): ?>
                        <?php foreach ($allProducts as $product): ?>
                            <div class="product-card <?php echo $product['stock_quantity'] <= $product['min_stock_level'] ? 'warning' : ''; ?>" onclick="openProductModal(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                <div class="product-header">
                                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                    <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                                </div>

                                <div class="product-info">
                                    <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                    <p class="product-price">₺<?php echo number_format($product['price'], 2); ?></p>
                                    <p class="product-stock <?php echo $product['stock_quantity'] <= $product['min_stock_level'] ? 'low-stock' : ''; ?>">
                                        Stok: <?php echo $product['stock_quantity']; ?>
                                        <?php if ($product['stock_quantity'] <= $product['min_stock_level']): ?>
                                            <span class="stock-warning">⚠️</span>
                                        <?php endif; ?>
                                    </p>
                                    <?php if ($product['barcode']): ?>
                                        <p class="product-barcode">Barkod: <?php echo htmlspecialchars($product['barcode']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <div class="product-actions">
                                    <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); openEditModal(<?php echo htmlspecialchars(json_encode($product)); ?>)">Düzenle</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="event.stopPropagation(); return confirm('Bu ürünü silmek istediğinizden emin misiniz?')">Sil</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>Henüz hiç ürün eklenmemiş.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Ürün Detay Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Ürün Detayları</h3>
                <span class="close" onclick="closeProductModal()">&times;</span>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div id="productModalContent">
                    <!-- Ürün detayları buraya yüklenecek -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeProductModal()">Kapat</button>
            </div>
        </div>
    </div>

    <!-- Düzenleme Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Ürün Düzenle</h3>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_name">Ürün Adı</label>
                        <input type="text" id="edit_name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_category">Kategori</label>
                        <select id="edit_category" name="category" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_description">Açıklama</label>
                    <textarea id="edit_description" name="description" rows="3"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_price">Fiyat (₺)</label>
                        <input type="number" id="edit_price" name="price" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_stock_quantity">Stok Miktarı</label>
                        <input type="number" id="edit_stock_quantity" name="stock_quantity" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_min_stock_level">Minimum Stok</label>
                        <input type="number" id="edit_min_stock_level" name="min_stock_level" min="0" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_barcode">Barkod</label>
                        <input type="text" id="edit_barcode" name="barcode">
                    </div>

                    <div class="form-group">
                        <label for="edit_image_url">Resim URL</label>
                        <input type="url" id="edit_image_url" name="image_url">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/script.js"></script>
    <script>
        function openProductModal(product) {
            const content = `
                <div class="product-detail">
                    <div class="product-header" style="margin-bottom: 1.5rem;">
                        <h2 style="color: var(--text-primary); margin-bottom: 0.5rem;">${product.name}</h2>
                        <span class="product-category">${product.category}</span>
                    </div>
                    
                    <div class="product-info-grid" style="display: grid; gap: 1rem;">
                        <div class="info-item">
                            <strong>Açıklama:</strong>
                            <p style="margin: 0.5rem 0; color: var(--text-secondary);">${product.description || 'Açıklama bulunmuyor'}</p>
                        </div>
                        
                        <div class="info-row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            <div class="info-item">
                                <strong>Fiyat:</strong>
                                <p style="margin: 0.5rem 0; color: var(--primary-color); font-size: 1.2rem; font-weight: 600;">₺${parseFloat(product.price).toFixed(2)}</p>
                            </div>
                            
                            <div class="info-item">
                                <strong>Stok Miktarı:</strong>
                                <p style="margin: 0.5rem 0; color: ${product.stock_quantity <= product.min_stock_level ? 'var(--warning-color)' : 'var(--text-primary)'};">
                                    ${product.stock_quantity} adet
                                    ${product.stock_quantity <= product.min_stock_level ? '⚠️ Düşük Stok' : ''}
                                </p>
                            </div>
                        </div>
                        
                        <div class="info-row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            <div class="info-item">
                                <strong>Minimum Stok:</strong>
                                <p style="margin: 0.5rem 0;">${product.min_stock_level} adet</p>
                            </div>
                            
                            ${product.barcode ? `
                            <div class="info-item">
                                <strong>Barkod:</strong>
                                <p style="margin: 0.5rem 0; font-family: monospace;">${product.barcode}</p>
                            </div>
                            ` : ''}
                        </div>
                        
                        ${product.image_url ? `
                        <div class="info-item">
                            <strong>Ürün Resmi:</strong>
                            <div style="margin-top: 0.5rem;">
                                <img src="${product.image_url}" alt="${product.name}" style="max-width: 100%; max-height: 200px; border-radius: var(--border-radius);">
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
            
            document.getElementById('productModalContent').innerHTML = content;
            document.getElementById('productModal').style.display = 'block';
        }

        function closeProductModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        function openEditModal(product) {
            document.getElementById('edit_id').value = product.id;
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_description').value = product.description;
            document.getElementById('edit_category').value = product.category;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_stock_quantity').value = product.stock_quantity;
            document.getElementById('edit_min_stock_level').value = product.min_stock_level;
            document.getElementById('edit_barcode').value = product.barcode || '';
            document.getElementById('edit_image_url').value = product.image_url || '';

            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Modal dışına tıklayınca kapat
        window.onclick = function(event) {
            const productModal = document.getElementById('productModal');
            const editModal = document.getElementById('editModal');
            
            if (event.target === productModal) {
                closeProductModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
        }

        // ESC tuşu ile modal kapatma
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeProductModal();
                closeEditModal();
            }
        });
    </script>
</body>
</html>