<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

$auth = new Auth($pdo);

// Giriş işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = $auth->login($username, $password);

    if ($result['success']) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Takip - Giriş</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Stok Takip Sistemi</h1>
                <p>Hesabınıza giriş yapın</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">Kullanıcı Adı veya Email</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Şifre</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Giriş Yap</button>
            </form>

            <div class="auth-footer">
                <p>Hesabınız yok mu? <a href="register.php">Kayıt Ol</a></p>
            </div>

            <div class="demo-accounts">
                <h3>Demo Hesaplar:</h3>
                <p><strong>Admin:</strong> admin / password</p>
            </div>
        </div>
    </div>
    <script src="../js/interactive-bg.js"></script>
</body>
</html>