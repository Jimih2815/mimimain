<?php
// dashboard.php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'db.php';

// Kiểm tra session
if (!isset($_SESSION['user_id'])) {
    // 1) Session chưa có => kiểm tra cookie "remember_token"
    if (!empty($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];

        // 2) Tìm user với token này
        $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = :tok LIMIT 1");
        $stmt->execute(['tok' => $token]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userRow) {
            // 3) Tự động tạo lại session
            $_SESSION['user_id']   = $userRow['id'];
            $_SESSION['username']  = $userRow['username'];
        }
        else {
            // Token không hợp lệ => xóa cookie
            setcookie('remember_token', '', time() - 3600, '/');
            unset($_COOKIE['remember_token']);
        }
    }

    // 4) Nếu vẫn chưa có session => về index login
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }
}


$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? '';
/* Có task nào chưa xem không? */
$stmtNewTask = $conn->prepare("
    SELECT 1
    FROM task_assignees ta
    WHERE ta.user_id = :uid
      AND ta.seen = 0               -- chưa mở user_tasks
    LIMIT 1
");
$stmtNewTask->execute(['uid' => $user_id]);
$showNewTaskPopup = $stmtNewTask->fetchColumn() ? true : false;

$stmtHasTask = $conn->prepare("
    SELECT 1
    FROM task_assignees
    WHERE user_id = :uid
    LIMIT 1
");
$stmtHasTask->execute(['uid' => $user_id]);
$hasTasks = $stmtHasTask->fetchColumn() ? true : false;
// (1) Tìm “hôm nay đã checkin chưa (chưa checkout)?”
$dateToday = date('Y-m-d');
$stmt = $conn->prepare("
    SELECT id, check_in
    FROM attendance
    WHERE user_id = :uid
      AND DATE(check_in) = :today
      AND check_out IS NULL
    LIMIT 1
");
$stmt->execute([
    'uid'   => $user_id,
    'today' => $dateToday
]);
$attendance = $stmt->fetch(PDO::FETCH_ASSOC);

$isCheckedIn = false;
$diffMinutes = 9999;  // minutes từ check_in => now (nếu đang checkedIn)
if ($attendance) {
    $isCheckedIn = true;
    $checkInTime = strtotime($attendance['check_in']);
    $now         = time();
    $diffMinutes = ($now - $checkInTime)/60.0;
}

// (2) Tìm “lần attendance mới nhất” => xem vừa checkout <1 phút?
$stmtLast = $conn->prepare("
    SELECT check_in, check_out
    FROM attendance
    WHERE user_id = :uid
    ORDER BY id DESC
    LIMIT 1
");
$stmtLast->execute(['uid' => $user_id]);
$lastAtt = $stmtLast->fetch(PDO::FETCH_ASSOC);

$justCheckedOut = false;
if ($lastAtt && !empty($lastAtt['check_out'])) {
    $coTime = strtotime($lastAtt['check_out']);
    $now    = time();
    $diffMinCheckout = ($now - $coTime)/60.0;
    if ($diffMinCheckout < 1) {
        $justCheckedOut = true;
    }
}

// Thông báo session
if (!empty($_SESSION['msg'])) {
    echo "<p style='color:green;'>{$_SESSION['msg']}</p>";
    unset($_SESSION['msg']);
}

// Kiểm tra quên checkout hôm qua ...
$yesterday = date('Y-m-d', strtotime('-1 day'));
$stmtYes = $conn->prepare("
    SELECT id
    FROM attendance
    WHERE user_id = :uid
      AND DATE(check_in) = :yesterday
      AND check_out IS NULL
    LIMIT 1
");
$stmtYes->execute(['uid' => $user_id, 'yesterday' => $yesterday]);
$forgotCheckout = $stmtYes->fetch(PDO::FETCH_ASSOC) ? true : false;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chấm công</title>
    <link rel="stylesheet"
          href="dashboard.css?v=<?= filemtime(__DIR__ . '/dashboard.css') ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    

</head>
<body>
    <a href="dashboard.php" class="logo-link">
        <img src="logo.png" alt="Logo Công Ty" class="company-logo">
    </a>
    <h2>Xin chào, <?php echo htmlspecialchars($username); ?>!</h2>

    <?php if ($forgotCheckout): ?>
        <p style="color: red; font-weight: bold;">
            Hôm qua bạn quên Checkout, báo ngay với quản lý để chấm công bù!
        </p>
    <?php endif; ?>

    <?php if ($isCheckedIn): ?>
        <!-- ĐANG CHECK-IN => nút CHECK-OUT màu đỏ -->
        <p>Nhớ Check-Out trước khi ra về nha!</p>
        <?php
            // Nếu diffMinutes < 1 => confirm
            $confirmMsg = "";
            if ($diffMinutes < 3) {
                $confirmMsg = "return confirm('Bạn vừa check-in xong. Bạn có chắc chắn muốn check-out luôn?');";
            }
        ?>
        <a class="check-btn checkout" style="width: 250px; height: 80px; display: flex;
    justify-content: center;
    align-items: center;" 
           href="qr.php"
           onclick="<?php echo $confirmMsg; ?>">
           Check-out
        </a>

    <?php else: ?>
        <!-- CHƯA CHECK-IN => nút CHECK-IN màu xanh -->
        <p>Nhớ Check-In trước khi bắt đầu làm việc nhé!</p>
        <?php
            // Nếu user vừa checkout <1 phút => confirm
            $confirmCI = "";
            if ($justCheckedOut) {
                $confirmCI = "return confirm('Bạn vừa checkout xong. Bạn có chắc chắn muốn check-in luôn?');";
            }
        ?> 
        <a class="check-btn checkin"  style="    width: 250px;     height: 80px; align-items: center;
                justify-content: center;
                display: flex;"
           href="qr.php" 
           onclick="<?php echo $confirmCI; ?>">
           Check-in
        </a>
    <?php endif; ?>
    <?php if ($hasTasks): ?>
        <div style="margin-top:20px;width: 100%; height: 80px; display: flex;
    justify-content: center;
    align-items: center;">
            <a href="user_tasks.php"
            style="
                display:flex;
                padding:18px 40px;
                background:#d1a029;
                color:#fff;
                border-radius:10px;
                text-decoration:none; width: 250px; height: 100%;font-weight: 600;
                font-size: 1.3rem;
                align-items: center;
                    justify-content: center;

                "
            >Quản lý đầu việc</a>
        </div>
    <?php endif; ?>

    <div class="logout-container" style="margin-top:20px;">
        <ul style="width: 100%; justify-content: space-between; display: flex;">
            <li><a href="info.php">Thông tin</a></li>
            <li><a href="logout.php">Đăng xuất</a></li>
        </ul>
    </div>
    <?php if ($showNewTaskPopup): ?>
        <div id="newTaskOverlay" style="position:fixed;inset:0;
                background:rgba(0,0,0,0.55);display:flex;
                align-items:center;justify-content:center;z-index:9999;">
            <div style="background:#fff;padding:25px 30px;border-radius:6px;
                    max-width:320px;text-align:center;">
                <p style="font-weight:600;margin-bottom:18px;">
                    Bạn vừa được giao 1&nbsp;đầu&nbsp;việc mới, nhớ kiểm tra nhé!
                </p>
                <button onclick="document.getElementById('newTaskOverlay').remove();"
                style="padding:8px 18px;border:1px solid #888;
                background:#f5f5f5;border-radius:4px;cursor:pointer;">Đóng</button>
            </div>
        </div>
    <?php endif; ?>


</body>
</html>
