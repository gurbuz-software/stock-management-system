<?php
require_once '../config/database.php';

class Auth {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Kullanıcı kayıt
    public function register($username, $email, $password, $full_name) {
        try {
            // Şifreyi hashle
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password, $full_name]);

            return ['success' => true, 'message' => 'Kayıt başarılı!'];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Kayıt başarısız: ' . $e->getMessage()];
        }
    }

    // Kullanıcı giriş
    public function login($username, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['is_admin'] = $user['is_admin'];

                return ['success' => true, 'message' => 'Giriş başarılı!', 'is_admin' => $user['is_admin']];
            } else {
                return ['success' => false, 'message' => 'Kullanıcı adı/email veya şifre hatalı!'];
            }
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Giriş hatası: ' . $e->getMessage()];
        }
    }

    // Kullanıcı çıkış
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Çıkış başarılı!'];
    }

    // Kullanıcı giriş kontrolü
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Admin kontrolü
    public function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
    }
}
?>