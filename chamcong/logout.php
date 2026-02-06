<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'db.php';

// 1) Xoá token DB
if (!empty($_SESSION['user_id'])) {
    $stmt = $conn->prepare("UPDATE users SET remember_token=NULL WHERE id=:uid LIMIT 1");
    $stmt->execute(['uid' => $_SESSION['user_id']]);
}

// 2) Xoá session
session_destroy();

// 3) Xoá cookie
setcookie('remember_token', '', time() - 3600, '/');
unset($_COOKIE['remember_token']);

// 4) Quay về login
header("Location: index.php");
exit;

