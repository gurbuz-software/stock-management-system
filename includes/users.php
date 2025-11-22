<?php
require_once '../config/database.php';

class Users {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Tüm kullanıcıları getir (sadece admin)
    public function getAllUsers() {
        try {
            $stmt = $this->pdo->query("SELECT id, username, email, full_name, is_admin, created_at FROM users ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return ['error' => 'Kullanıcılar getirilemedi: ' . $e->getMessage()];
        }
    }

    // Kullanıcı bilgilerini güncelle
    public function updateUser($id, $username, $email, $full_name, $is_admin) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET username = ?, email = ?, full_name = ?, is_admin = ? WHERE id = ?");
            $stmt->execute([$username, $email, $full_name, $is_admin, $id]);

            return ['success' => true, 'message' => 'Kullanıcı başarıyla güncellendi!'];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Kullanıcı güncellenemedi: ' . $e->getMessage()];
        }
    }

    // Kullanıcı sil
    public function deleteUser($id) {
        try {
            // Kendi hesabını silmeyi engelle
            if ($id == $_SESSION['user_id']) {
                return ['success' => false, 'message' => 'Kendi hesabınızı silemezsiniz!'];
            }

            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            return ['success' => true, 'message' => 'Kullanıcı başarıyla silindi!'];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Kullanıcı silinemedi: ' . $e->getMessage()];
        }
    }

    // Kullanıcı istatistikleri
    public function getUserStats() {
        try {
            $totalUsers = $this->pdo->query("SELECT COUNT(*) as total FROM users")->fetch()['total'];
            $totalAdmins = $this->pdo->query("SELECT COUNT(*) as total FROM users WHERE is_admin = TRUE")->fetch()['total'];
            $totalRegularUsers = $totalUsers - $totalAdmins;

            return [
                'total_users' => $totalUsers,
                'total_admins' => $totalAdmins,
                'total_regular_users' => $totalRegularUsers
            ];
        } catch(PDOException $e) {
            return ['error' => 'İstatistikler getirilemedi: ' . $e->getMessage()];
        }
    }
}
?>