<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

$auth = new Auth($pdo);
$result = $auth->logout();

header('Location: giris');
exit;
?>