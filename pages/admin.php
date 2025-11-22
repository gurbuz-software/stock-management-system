<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/users.php';

$auth = new Auth($pdo);
$users = new Users($pdo);

// Giriş ve admin kontrolü
if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header('Location: panel');
    exit;
}

// Kullanıcıları getir
$allUsers = $users->getAllUsers();
$userStats = $users->getUserStats();

// Kullanıcı işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['user_id'] ?? '';

    switch ($action) {
        case 'delete':
            $result = $users->deleteUser($user_id);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header('Location: admin');
            exit;
            break;

        case 'update':
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $full_name = $_POST['full_name'] ?? '';
            $is_admin = isset($_POST['is_admin']) ? 1 : 0;

            $result = $users->updateUser($user_id, $username, $email, $full_name, $is_admin);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header('Location: admin');
            exit;
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Takip - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <h1 class="logo">Stok Takip</h1>
                <nav class="nav">
                    <a href="panel" class="nav-link">Dashboard</a>
                    <a href="urunler" class="nav-link">Ürünler</a>
                    <a href="admin" class="nav-link active">Admin Panel</a>
                    <a href="cikis" class="nav-link">Çıkış</a>
                </nav>
                <div class="user-info">
                    <span>Hoş geldin, <?php echo htmlspecialchars($_SESSION['full_name']); ?> (Admin)</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="dashboard-header">
                <h2>Admin Panel</h2>
                <p>Kullanıcı yönetimi ve sistem ayarları</p>
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

            <!-- Kullanıcı İstatistikleri -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-content">
                        <h3><?php echo $userStats['total_users'] ?? 0; ?></h3>
                        <p>Toplam Kullanıcı</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">👑</div>
                    <div class="stat-content">
                        <h3><?php echo $userStats['total_admins'] ?? 0; ?></h3>
                        <p>Admin Kullanıcı</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">👤</div>
                    <div class="stat-content">
                        <h3><?php echo $userStats['total_regular_users'] ?? 0; ?></h3>
                        <p>Normal Kullanıcı</p>
                    </div>
                </div>
            </div>

            <!-- Kullanıcı Listesi -->
            <div class="admin-section">
                <h3>Kullanıcı Yönetimi</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kullanıcı Adı</th>
                                <th>Email</th>
                                <th>Tam Ad</th>
                                <th>Yetki</th>
                                <th>Kayıt Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allUsers as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $user['is_admin'] ? 'badge-admin' : 'badge-user'; ?>">
                                            <?php echo $user['is_admin'] ? 'Admin' : 'Kullanıcı'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-primary" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)">Düzenle</button>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">Sil</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Düzenleme Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Kullanıcı Düzenle</h3>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="user_id" id="edit_user_id">

                <div class="form-group">
                    <label for="edit_username">Kullanıcı Adı</label>
                    <input type="text" id="edit_username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="edit_full_name">Tam Ad</label>
                    <input type="text" id="edit_full_name" name="full_name" required>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="edit_is_admin" name="is_admin">
                        <span class="checkmark"></span>
                        Admin Yetkisi
                    </label>
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
        function openEditModal(user) {
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_full_name').value = user.full_name;
            document.getElementById('edit_is_admin').checked = user.is_admin;

            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Modal dışına tıklayınca kapat
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }
    </script>
    <script src="../js/interactive-bg.js"></script>
</body>
</html>