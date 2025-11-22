<?php
require_once '../config/database.php';

class Products {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Tüm ürünleri getir
    public function getAllProducts() {
        try {
            $stmt = $this->pdo->query("SELECT p.*, u.username as created_by_name FROM products p LEFT JOIN users u ON p.created_by = u.id ORDER BY p.created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return ['error' => 'Ürünler getirilemedi: ' . $e->getMessage()];
        }
    }

    // Ürün ekle
    public function addProduct($name, $description, $category, $price, $stock_quantity, $min_stock_level, $barcode, $image_url, $created_by) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO products (name, description, category, price, stock_quantity, min_stock_level, barcode, image_url, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $category, $price, $stock_quantity, $min_stock_level, $barcode, $image_url, $created_by]);

            return ['success' => true, 'message' => 'Ürün başarıyla eklendi!'];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Ürün eklenemedi: ' . $e->getMessage()];
        }
    }

    // Ürün güncelle
    public function updateProduct($id, $name, $description, $category, $price, $stock_quantity, $min_stock_level, $barcode, $image_url) {
        try {
            $stmt = $this->pdo->prepare("UPDATE products SET name = ?, description = ?, category = ?, price = ?, stock_quantity = ?, min_stock_level = ?, barcode = ?, image_url = ? WHERE id = ?");
            $stmt->execute([$name, $description, $category, $price, $stock_quantity, $min_stock_level, $barcode, $image_url, $id]);

            return ['success' => true, 'message' => 'Ürün başarıyla güncellendi!'];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Ürün güncellenemedi: ' . $e->getMessage()];
        }
    }

    // Ürün sil
    public function deleteProduct($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);

            return ['success' => true, 'message' => 'Ürün başarıyla silindi!'];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Ürün silinemedi: ' . $e->getMessage()];
        }
    }

    // ID'ye göre ürün getir
    public function getProductById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return ['error' => 'Ürün getirilemedi: ' . $e->getMessage()];
        }
    }

    // Kategorilere göre ürünleri getir
    public function getProductsByCategory($category) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY created_at DESC");
            $stmt->execute([$category]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return ['error' => 'Ürünler getirilemedi: ' . $e->getMessage()];
        }
    }

    // Stok seviyesi düşük ürünleri getir
    public function getLowStockProducts() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM products WHERE stock_quantity <= min_stock_level ORDER BY stock_quantity ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return ['error' => 'Düşük stoklu ürünler getirilemedi: ' . $e->getMessage()];
        }
    }
}
?>