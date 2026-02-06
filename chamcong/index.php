<?php
// index.php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'db.php';

// Nếu đã đăng nhập, chuyển về dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Xử lý form đăng nhập
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Lấy user từ DB
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Nếu dùng password_hash, hãy thay thế đoạn so sánh bằng password_verify
        // if (password_verify($password, $user['password'])) { ... }
        if ($user['password'] === $password) {
            // 1) Tạo session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
        
            // 2) Kiểm tra nếu có "remember_me"
            if (!empty($_POST['remember_me'])) {
                // a) Tạo token ngẫu nhiên (64 ký tự hex)
                $token = bin2hex(random_bytes(32));
        
                // b) Lưu token vào DB
                $stmtUpdate = $conn->prepare("UPDATE users SET remember_token = :tok WHERE id = :uid LIMIT 1");
                $stmtUpdate->execute([
                    'tok' => $token,
                    'uid' => $user['id']
                ]);
        
                // c) Set cookie 30 ngày
                setcookie('remember_token', $token, time() + 30*24*3600, '/', '', false, true);
                // Tham số cuối true => httponly (chặn JS đọc cookie)
                // Tham số thứ 5 '' => web chưa https => false => secure = false
            }
        
            // 3) Chuyển hướng
            header("Location: dashboard.php");
            exit;
        }     
    }
    $error = "Sai tài khoản hoặc mật khẩu!";
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>MiMi-Chấm Công</title>
    <link rel="stylesheet" href="index.css?v=<?= filemtime(__DIR__ . '/index.css') ?>">    
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>
<body>
    <a href="dashboard.php" class="logo-link">
        <img src="logo.png" alt="Logo Công Ty" class="company-logo">
    </a>
    <div class="index-body">
        <h2>Đăng nhập</h2>
        <?php if (!empty($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Tài khoản: </label>
            <input type="text" name="username" required><br><br>

            <label for="password">Mật khẩu: </label>
            <input type="password" name="password" required><br><br>
            <!-- Thêm checkbox "remember_me" -->
            <label style="margin: 0; height: 30px; display: flex; justify-content: flex-start;">
                <div style="height: 100%; display: flex; justify-content: center; padding: 5px; align-items: center;"><input type="checkbox" name="remember_me" value="1"></div>
                <p style="align-items: center; justify-content: center; display: flex; padding: 5px;">Ghi nhớ đăng nhập</p>
            </label>
            <br><br>
            <button type="submit">Đăng nhập</button>
        </form>
    </div>
</body>
</html>
