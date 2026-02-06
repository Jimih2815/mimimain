<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'db.php';

// Nếu chưa đăng nhập, quay về index
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$username  = $_SESSION['username'] ?? '';

// ==========================
// A) PHÂN TRANG LỊCH SỬ CHẤM CÔNG (GỘP THEO NGÀY)
// ==========================

// 1) Số dòng hiển thị trên mỗi trang
$rows_per_page = 5;

// 2) Xác định trang hiện tại (nếu không có thì mặc định là 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// 3) Tính vị trí bắt đầu
$start_from = ($page - 1) * $rows_per_page;

// 4) Lấy tổng số "ngày" user đã chấm công
$stmtTotal = $conn->prepare("
    SELECT COUNT(DISTINCT DATE(check_in)) AS total_days
    FROM attendance
    WHERE user_id = :uid
");
$stmtTotal->execute(['uid' => $user_id]);
$rowTotal   = $stmtTotal->fetch(PDO::FETCH_ASSOC);
$total_rows = (int)($rowTotal['total_days'] ?? 0);

// 5) Tính tổng số trang
$total_pages = ($total_rows > 0) 
             ? ceil($total_rows / $rows_per_page)
             : 1;

// 6) Lấy dữ liệu gộp theo ngày, có LIMIT
$sqlGroup = "
SELECT 
  DATE(check_in) AS work_date,
  MIN(check_in)  AS earliest_in,
  MAX(check_out) AS latest_out
FROM attendance
WHERE user_id = :uid
GROUP BY DATE(check_in)
ORDER BY work_date DESC
LIMIT :start_from, :rows_per_page
";
$stmtGroup = $conn->prepare($sqlGroup);
// bindValue thay vì bindParam khi gán biến 
$stmtGroup->bindValue(':uid', $user_id, PDO::PARAM_INT);
$stmtGroup->bindValue(':start_from', $start_from, PDO::PARAM_INT);
$stmtGroup->bindValue(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
$stmtGroup->execute();
$groupedAtt = $stmtGroup->fetchAll(PDO::FETCH_ASSOC);

// ========== A) Xử lý ĐỔI MẬT KHẨU ==========
$passMsg = '';
if (isset($_POST['change_password'])) {
    $old_password         = trim($_POST['old_password']);
    $new_password         = trim($_POST['new_password']);
    $confirm_new_password = trim($_POST['confirm_new_password']);

    // 1) Lấy user từ DB
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = :uid");
    $stmt->execute(['uid' => $user_id]);
    $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userRow) {
        $passMsg = "Tài khoản không tồn tại!";
    } else {
        // 2) Kiểm tra password cũ
        if ($userRow['password'] !== $old_password) {
            $passMsg = "Mật khẩu cũ không đúng!";
        }
        else {
            // 2.5) Kiểm tra định dạng password mới
            // a) Chỉ cho phép: a-zA-Z0-9 + !@#$%^&*()_-+=
            $pattern = '/^[a-zA-Z0-9!@#$%^&*()_\-+=]+$/';
            if (!preg_match($pattern, $new_password)) {
                $passMsg = "Mật khẩu chỉ được chứa chữ cái (không dấu), chữ số, và các ký tự !@#$%^&*()_-+=";
            }
            // b) Không cho phép trùng với password cũ
            else if ($new_password === $old_password) {
                $passMsg = "Mật khẩu mới không được trùng với mật khẩu cũ!";
            }
            // c) Kiểm tra confirm
            else if ($new_password !== $confirm_new_password) {
                $passMsg = "Mật khẩu mới và xác nhận không trùng khớp!";
            } 
            else {
                // 5) Cập nhật
                $stmtUp = $conn->prepare("UPDATE users SET password = :p WHERE id = :uid LIMIT 1");
                $stmtUp->execute([
                    'p'   => $new_password,
                    'uid' => $user_id
                ]);
                $passMsg = "Đổi mật khẩu thành công!";
            }
        }
    }
}


// ========== C) Tính tổng giờ & lương tháng hiện tại ==========
// 1) Tính tổng phút tháng hiện tại
$stmtSum = $conn->prepare("
    SELECT SUM(TIMESTAMPDIFF(MINUTE, check_in, check_out)) AS total_mins
    FROM attendance
    WHERE user_id = :uid
      AND MONTH(check_in) = MONTH(CURRENT_DATE())
      AND YEAR(check_in)  = YEAR(CURRENT_DATE())
      AND check_out IS NOT NULL
");
$stmtSum->execute(['uid' => $user_id]);
$rowSum   = $stmtSum->fetch(PDO::FETCH_ASSOC);
$totalMins= (int)($rowSum['total_mins'] ?? 0);
$actualHoursThisMonth = $totalMins / 60.0;

// 2) Lấy thông tin user (employee_type, base_salary, required_hours, hourly_rate)
$stmtUser = $conn->prepare("
    SELECT employee_type, base_salary, required_hours, hourly_rate
    FROM users
    WHERE id = :uid
    LIMIT 1
");
$stmtUser->execute(['uid' => $user_id]);
$userData = $stmtUser->fetch(PDO::FETCH_ASSOC);

// 3) Tính lương tháng hiện tại
$currentSalary = 0;
if ($userData) {
    if ($userData['employee_type'] === 'chinh_thuc') {
        if ($userData['required_hours'] > 0) {
            // Lấy số giờ thực tế
            $hoursRequired = (float)$userData['required_hours'];
            $baseSalary    = (float)$userData['base_salary'];
            
            // Nếu làm đủ hoặc vượt giờ => chỉ hiển thị lương cơ bản
            if ($actualHoursThisMonth >= $hoursRequired) {
                $currentSalary = $baseSalary;
            } else {
                // Chưa đủ giờ => tính tỷ lệ
                $currentSalary = $baseSalary * ($actualHoursThisMonth / $hoursRequired);
            }
        }
    } else {
        // Thời vụ
        $currentSalary = $userData['hourly_rate'] * $actualHoursThisMonth;
    }
}


// ========== Hàm phụ format hiển thị ==========
function toHourMinute($ymdHis) {
    if (!$ymdHis) return '';
    $parts = explode(' ', $ymdHis);
    if (count($parts) < 2) return '';
    return substr($parts[1], 0, 5); // cắt giây => HH:<html lang="vi">

}
function ymdToDmy($ymd) {
    if (!$ymd) return '';
    $arr = explode('-', $ymd);
    if (count($arr) < 3) return $ymd;
    return $arr[2].'/'.$arr[1].'/'.$arr[0];
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thông tin cá nhân</title>
    <link rel="stylesheet" href="info.css?v=<?= filemtime(__DIR__ . '/info.css') ?>">    
    
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <style>
        /* Tuỳ chỉnh CSS cho bảng */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        .success { color: green; }
        .error   { color: red; }

        /* CSS cho modal popup */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 9999; 
            left: 0; 
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.5);
        }
        .modal.show {
            display: block; /* Hiện modal khi có class .show */
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto; 
            padding: 20px;
            width: 90%;
            max-width: 400px;
            border-radius: 5px;
            position: relative;
        }
        .close {
            position: absolute;
            right: 15px;
            top: 10px;
            cursor: pointer;
            font-size: 24px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 1rem;
        }
       
    </style>
</head>
<body>
<div class="container">
    <h2>Thông tin của bạn: <?php echo htmlspecialchars($username); ?></h2>

    <!-- B) LỊCH SỬ CHẤM CÔNG (GỘP THEO NGÀY) -->
    <h3>Lịch sử chấm công</h3>
    <table>
        <tr>
            <th>Ngày</th>
            <th>Giờ Check-In</th>
            <th>Giờ Check-Out</th>
        </tr>
        <?php foreach ($groupedAtt as $g): ?>
            <?php
            $dayString   = $g['work_date'];
            $earliestHM  = toHourMinute($g['earliest_in']);
            $latestHM    = toHourMinute($g['latest_out']);
            $dmy         = ymdToDmy($dayString);
            ?>
            <tr>
                <td><?php echo $dmy; ?></td>
                <td><?php echo $earliestHM; ?></td>
                <td><?php echo $latestHM; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php
    // Tính startPage, endPage cho 3 nút
    $startPage = $page - 1;
    $endPage   = $page + 1;

    // Nếu đang ở trang 1 hoặc 2, cố định [1..3]
    if ($page <= 2) {
        $startPage = 1;
        $endPage   = 3;
    }

    // Nếu đang ở trang cuối hoặc kế cuối => [t-2..t]
    if ($page >= $total_pages - 1) {
        $startPage = $total_pages - 2;
        $endPage   = $total_pages;
    }

    // Giới hạn không nhỏ hơn 1 và không lớn hơn tổng trang
    if ($startPage < 1) $startPage = 1;
    if ($endPage > $total_pages) $endPage = $total_pages;
    ?>

    <div class="pagination">
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <a href="?page=<?php echo $i; ?>"
            class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    <!-- Tổng giờ làm việc & Lương hiện tại -->
    <div class="summary">
        <p class="total-hours">
            <strong>Tổng giờ làm việc:</strong> 
            <span><?php echo round($actualHoursThisMonth, 2); ?> giờ</span>
        </p>
        <p class="current-salary">
            <strong>Lương hiện tại:</strong> 
            <span><?php echo number_format($currentSalary); ?> VNĐ</span>
        </p>
    </div>




    <!-- Nút mở popup đổi mật khẩu -->
    <button class="open-password-modal">Đổi mật khẩu</button>

    <!-- Popup modal đổi mật khẩu -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Đổi mật khẩu</h3>

            <!-- Thông báo lỗi hoặc thành công -->
            <?php if (!empty($passMsg)): ?>
                <p style="color: <?php echo ($passMsg === 'Đổi mật khẩu thành công!') ? 'green' : 'red'; ?>">
                    <?php echo htmlspecialchars($passMsg); ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="old_password">Mật khẩu cũ:</label>
                <input type="password" name="old_password" required>

                <label for="new_password">Mật khẩu mới:</label>
                <input type="password" name="new_password" required>

                <label for="confirm_new_password">Xác nhận mật khẩu mới:</label>
                <input type="password" name="confirm_new_password" required>

                <button type="submit" name="change_password">Xác nhận đổi mật khẩu</button>
            </form>
        </div>
    </div>

    <!-- Link quay lại -->
    <a href="dashboard.php" class="back-link">Quay lại trang chính</a>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("passwordModal");
    const btn = document.querySelector(".open-password-modal");
    const closeBtn = document.querySelector(".close");

    // Mở modal khi ấn nút "Đổi mật khẩu"
    if (btn) {
        btn.onclick = function() {
            modal.classList.add("show");
        }
    }
    // Đóng modal khi ấn dấu X
    if (closeBtn) {
        closeBtn.onclick = function() {
            modal.classList.remove("show");
        }
    }
    // Đóng modal khi click ra ngoài vùng content
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.classList.remove("show");
        }
    }

    // Nếu có lỗi (hoặc bất kỳ thông báo nào) mà KHÔNG phải "Đổi mật khẩu thành công!"
    // => Tự động mở modal để user thấy $passMsg
    <?php if (!empty($passMsg) && $passMsg !== "Đổi mật khẩu thành công!"): ?>
        modal.classList.add("show");
    <?php endif; ?>
});
</script>
</body>
</html>
