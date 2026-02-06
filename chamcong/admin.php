<?php
// admin.php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'db.php';
require __DIR__ . '/../auth.php';

// =============== FLASH MESSAGE ===============
function setFlash($msg) {
    $_SESSION['flash_msg'] = $msg;
}
function showFlash() {
    if (isset($_SESSION['flash_msg'])) {
        echo $_SESSION['flash_msg'];
        unset($_SESSION['flash_msg']);
    }
}


// Hàm chuyển "YYYY-mm-dd HH:ii:ss" => "HH:ii"
function toHourMinute($ymdHis) {
    if (!$ymdHis) return '';
    $parts = explode(' ', $ymdHis);
    if (count($parts) < 2) return '';
    return substr($parts[1], 0, 5);
}
// Hàm chuyển "YYYY-mm-dd" => "dd/mm/yyyy"
function ymdToDmy($ymd) {
    if (!$ymd) return '';
    $arr = explode('-', $ymd);
    if (count($arr) < 3) return $ymd;
    // arr[0] = YYYY, arr[1] = mm, arr[2] = dd
    return $arr[2].'/'.$arr[1].'/'.$arr[0];
}

// ============================
// X) ADMIN CHECKIN/CHECKOUT HỘ
// ============================
if (isset($_POST['toggle_status'])) {
    $uid = (int)$_POST['user_id'];

    // 1) Lấy record “chưa checkout” trong hôm nay
    $today = date('Y-m-d');
    $stmt = $conn->prepare("
        SELECT id
        FROM attendance
        WHERE user_id = :uid
          AND DATE(check_in) = :today
          AND check_out IS NULL
        LIMIT 1
    ");
    $stmt->execute([
        'uid'   => $uid,
        'today' => $today
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // (A) ĐÃ check-in => ta forced CHECK-OUT
        // Trước khi check-out, xóa các record cũ cùng ngày, chỉ giữ lại record đang làm
        $stmtDel = $conn->prepare("
            DELETE FROM attendance
            WHERE user_id = :uid
              AND DATE(check_in) = :today
              AND id <> :keepID
        ");
        $stmtDel->execute([
            'uid'   => $uid,
            'today' => $today,
            'keepID'=> $row['id']
        ]);

        $checkoutTime = date('Y-m-d H:i:s');
        $stmtUp = $conn->prepare("
            UPDATE attendance
            SET check_out = :cout
            WHERE id = :aid
        ");
        $stmtUp->execute([
            'cout' => $checkoutTime,
            'aid'  => $row['id']
        ]);

        setFlash("<p style='color:blue;'>Đã CHECK-OUT hộ user ID $uid lúc $checkoutTime</p>");
    } else {
        // (B) CHƯA check-in => ta forced CHECK-IN
        // Xóa toàn bộ record cũ trong ngày hôm nay, rồi tạo record mới
        $stmtDel = $conn->prepare("
            DELETE FROM attendance
            WHERE user_id = :uid
              AND DATE(check_in) = :today
        ");
        $stmtDel->execute([
            'uid'   => $uid,
            'today' => $today
        ]);

        $checkinTime = date('Y-m-d H:i:s');
        $stmtIn = $conn->prepare("
            INSERT INTO attendance (user_id, check_in, ip_in, lat_in, lng_in)
            VALUES (:uid, :cin, 'ADMIN', 0, 0)
        ");
        $stmtIn->execute([
            'uid' => $uid,
            'cin' => $checkinTime
        ]);

        setFlash("<p style='color:blue;'>Đã CHECK-IN hộ user ID $uid lúc $checkinTime</p>");
    }
    // CHUYỂN HƯỚNG -> tránh lặp lại POST khi F5
    header("Location: admin.php");
    exit;
}


// ========================
// A) Xử lý thêm / xóa user
// ========================

// 1) XÓA USER
if (isset($_POST['delete_user'])) {
    $uid = (int)$_POST['user_id'];
    $stmtDel = $conn->prepare("DELETE FROM users WHERE id = :id LIMIT 1");
    $stmtDel->execute(['id' => $uid]);
    setFlash("<p style='color:red;'>Đã xóa user ID $uid</p>");

    header("Location: admin.php");
    exit;
}

// 2) THÊM USER MỚI
if (isset($_POST['add_user'])) {
    $username       = trim($_POST['username']);
    $password       = trim($_POST['password']);
    $employee_type  = $_POST['employee_type'];
    $base_salary    = (int)$_POST['base_salary'];
    $required_hours = (int)$_POST['required_hours'];
    $hourly_rate    = (int)$_POST['hourly_rate'];

    $stmtAdd = $conn->prepare("
        INSERT INTO users (username, password, employee_type, base_salary, required_hours, hourly_rate)
        VALUES (:u, :p, :type, :bs, :rh, :hr)
    ");
    $stmtAdd->execute([
        'u'    => $username,
        'p'    => $password,
        'type' => $employee_type,
        'bs'   => $base_salary,
        'rh'   => $required_hours,
        'hr'   => $hourly_rate
    ]);
    setFlash("<p style='color:green;'>Đã thêm user mới: $username</p>");

    header("Location: admin.php");
    exit;
}


// ==========================
// B) Cập nhật user
// ==========================
if (isset($_POST['update_user'])) {
    $user_id        = $_POST['user_id'];
    $employee_type  = $_POST['employee_type'];
    $base_salary    = (int)$_POST['base_salary'];
    $required_hours = (int)$_POST['required_hours'];
    $hourly_rate    = (int)$_POST['hourly_rate'];

    // checkbox "ignore_location"
    $ignoreLoc = isset($_POST['ignore_location']) ? 1 : 0;

    $stmt = $conn->prepare("
        UPDATE users
        SET employee_type   = :type,
            base_salary     = :baseSal,
            required_hours = :reqH,
            hourly_rate    = :hrRate,
            ignore_location= :ign
        WHERE id = :id
    ");
    $stmt->execute([
        'type'   => $employee_type,
        'baseSal'=> $base_salary,
        'reqH'   => $required_hours,
        'hrRate' => $hourly_rate,
        'ign'    => $ignoreLoc,
        'id'     => $user_id
    ]);

    setFlash("<p style='color:green;'>Đã cập nhật user ID $user_id</p>");
    header("Location: admin.php");
    exit;
}


// ===============================
// C) Xử lý thêm/cập nhật chấm công
// ===============================

// 1) THÊM CHẤM CÔNG
if (isset($_POST['add_attendance'])) {
    $user_id = (int)$_POST['user_id'];
    $theDate = $_POST['the_date'];
    $inTime  = $_POST['in_time'];
    $outTime = $_POST['out_time'];

    $check_in  = $theDate . " " . $inTime . ":00";
    $check_out = $theDate . " " . $outTime. ":00";

    $stmtAddAtt = $conn->prepare("
        INSERT INTO attendance (user_id, check_in, check_out)
        VALUES (:uid, :cin, :cout)
    ");
    $stmtAddAtt->execute([
        'uid'  => $user_id,
        'cin'  => $check_in,
        'cout' => $check_out
    ]);
    setFlash("<p style='color:green;'>Đã thêm chấm công cho user $user_id vào ngày $theDate</p>");

    header("Location: admin.php");
    exit;
}

// 2) CẬP NHẬT attendance
if (isset($_POST['update_attendance'])) {
    $att_id    = (int)$_POST['attendance_id'];
    $check_in  = trim($_POST['check_in']);
    $check_out = trim($_POST['check_out']);

    $check_in  = preg_replace('/:\d\d$/', ':00', str_replace('T',' ', $check_in));
    $check_out = preg_replace('/:\d\d$/', ':00', str_replace('T',' ', $check_out));

    if ($check_out === '') {
        $stmt = $conn->prepare("
            UPDATE attendance
            SET check_in = :cin
            WHERE id = :aid
        ");
        $stmt->execute([
            'cin' => $check_in,
            'aid' => $att_id
        ]);
    } else {
        $stmt = $conn->prepare("
            UPDATE attendance
            SET check_in = :cin,
                check_out = :cout
            WHERE id = :aid
        ");
        $stmt->execute([
            'cin'  => $check_in,
            'cout' => $check_out,
            'aid'  => $att_id
        ]);
    }
    setFlash("<p style='color:green;'>Đã cập nhật attendance ID $att_id</p>");

    header("Location: admin.php");
    exit;
}


// =======================
// D) Lấy danh sách user
// =======================
$stmtUsers = $conn->query("SELECT * FROM users");
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);


// =======================
// E) Tính lương
// =======================
// 1) XỬ LÝ POST TÍNH LƯƠNG
if (isset($_POST['calculate_salary'])) {
    $month = (int)$_POST['month'];
    $year  = (int)$_POST['year'];

    $totalResult = []; // khai báo mảng cục bộ

    foreach ($users as $u) {
        $uid = $u['id'];

        $stmtMins = $conn->prepare("
            SELECT SUM(TIMESTAMPDIFF(MINUTE, check_in, check_out)) AS total_mins
            FROM attendance
            WHERE user_id = :uid
              AND MONTH(check_in) = :m
              AND YEAR(check_in)  = :y
              AND check_out IS NOT NULL
        ");
        $stmtMins->execute([
            'uid' => $uid,
            'm'   => $month,
            'y'   => $year
        ]);
        $rowMins = $stmtMins->fetch(PDO::FETCH_ASSOC);
        $total_mins = (int)($rowMins['total_mins'] ?? 0);

        $actual_hours = $total_mins / 60.0;
        $salary = 0.0;

        if ($u['employee_type'] === 'chinh_thuc') {
            $base = (float)$u['base_salary'];
            $reqH = (float)$u['required_hours'];
            if ($reqH > 0) {
                $salary = $base * ($actual_hours / $reqH);
            }
        } else {
            $rate = (float)$u['hourly_rate'];
            $salary = $rate * $actual_hours;
        }
        $totalResult[] = [
            'user_id'       => $u['id'],
            'username'      => $u['username'],
            'employee_type' => $u['employee_type'],
            'total_mins'    => $total_mins,
            'actual_hours'  => $actual_hours,
            'salary'        => $salary
        ];
    }

    // Lưu vào session
    $_SESSION['calcResult'] = $totalResult;
    $_SESSION['calcMonth']  = $month;
    $_SESSION['calcYear']   = $year;

    setFlash("<p style='color:blue;'>Đã tính lương xong cho tháng $month/$year</p>");
    header("Location: admin.php#bang-luong");
    exit;
}


// 2) LẤY LẠI KẾT QUẢ TỪ SESSION (trước khi vào phần HTML hiển thị)
$calcMonth = 0;
$calcYear  = 0;
if (isset($_SESSION['calcResult'])) {
    $totalResult = $_SESSION['calcResult'];
    $calcMonth   = $_SESSION['calcMonth'];
    $calcYear    = $_SESSION['calcYear'];
    unset($_SESSION['calcResult'], $_SESSION['calcMonth'], $_SESSION['calcYear']);
} else {
    // Mặc định rỗng
    $totalResult = [];
}


// ==========================
// F) QUẢN LÝ CHẤM CÔNG 
// ==========================

// Xoá chấm công 1 ngày
if (isset($_POST['delete_day_attendance'])) {
    $user_id = (int)$_POST['user_id'];
    $the_date = $_POST['the_date'];
    $stmtDelDay = $conn->prepare("
        DELETE FROM attendance
        WHERE user_id = :uid
          AND DATE(check_in) = :d
    ");
    $stmtDelDay->execute([
        'uid' => $user_id,
        'd'   => $the_date
    ]);
    setFlash("<p style='color:red;'>Đã xóa toàn bộ chấm công ngày $the_date của user_id=$user_id</p>");

    header("Location: admin.php#table-chamcong");
    exit;
}


// ============================
// G) CẬP NHẬT GIỜ SỚM NHẤT & GIỜ MUỘN NHẤT TRONG NGÀY
// ============================
if (isset($_POST['update_earliest_latest'])) {
    $earliest_id = (int)$_POST['earliest_id'];
    $latest_id   = (int)$_POST['latest_id'];
    $the_date    = $_POST['the_date'];       // YYYY-mm-dd
    $earliest_in = trim($_POST['earliest_in']); // HH:mm
    $latest_out  = trim($_POST['latest_out']);  // HH:mm

    // Chuyển HH:mm => YYYY-mm-dd HH:mm:00
    $check_in  = $the_date . " " . $earliest_in . ":00";
    $check_out = $the_date . " " . $latest_out  . ":00";

    if ($earliest_id === $latest_id) {
        // Cập nhật 1 record
        $stmt = $conn->prepare("
            UPDATE attendance
            SET check_in = :cin, check_out = :cout
            WHERE id = :aid
        ");
        $stmt->execute([
            'cin' => $check_in,
            'cout'=> $check_out,
            'aid' => $earliest_id
        ]);
    } else {
        // Có 2 record => gộp lại thành 1 record
        $stmtUp = $conn->prepare("
            UPDATE attendance
            SET check_in = :cin, check_out = :cout
            WHERE id = :aid
        ");
        $stmtUp->execute([
            'cin' => $check_in,
            'cout'=> $check_out,
            'aid' => $earliest_id
        ]);

        // Xóa record còn lại
        $stmtDel = $conn->prepare("DELETE FROM attendance WHERE id = :bid LIMIT 1");
        $stmtDel->execute(['bid' => $latest_id]);
    }

    setFlash("<p style='color:green;'>Đã cập nhật chấm công cho ngày $the_date</p>");
    header("Location: admin.php#table-chamcong");
    exit;
}



// --------------------
// 1) LẤY BIẾN start_date, end_date TỪ GET (nếu có)
// --------------------
// Lấy filter user (nếu có)
$filterUID = !empty($_GET['filter_user_id']) ? (int)$_GET['filter_user_id'] : 0;
$startDate = !empty($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate   = !empty($_GET['end_date'])   ? $_GET['end_date']   : '';
$startDateDmy = ymdToDmy($startDate);
$endDateDmy   = ymdToDmy($endDate);

if (empty($startDate) || empty($endDate)) {
    // Lấy đầu tháng & cuối tháng hiện tại
    $firstDay = date('Y-m-01');  // ví dụ: 2025-03-01
    $lastDay  = date('Y-m-t');   // ví dụ: 2025-03-31

    $startDate = $firstDay;
    $endDate   = $lastDay;
}

// A) rowsPerPage
$rowsPerPage = isset($_GET['rows_per_page']) ? (int)$_GET['rows_per_page'] : 10;
if ($rowsPerPage <= 0) $rowsPerPage = 10;

// B) page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Tính start
$start = ($page - 1) * $rowsPerPage;


// --------------------
// 2) ĐẾM TỔNG SỐ “NGÀY” ĐỂ TÍNH PHÂN TRANG
// --------------------
$sqlCount = "
SELECT COUNT(*) AS total_days
FROM (
    SELECT DATE(a.check_in) AS work_date, a.user_id
    FROM attendance a
";

// Mảng chứa các điều kiện
$cond = [];

// Nếu chọn user cụ thể (nếu bạn vẫn muốn có bộ lọc user_id)
if ($filterUID > 0) {
    $cond[] = " a.user_id = :filterUID ";
}

// Thêm điều kiện LỌC TỪ NGÀY / ĐẾN NGÀY
if (!empty($startDate)) {
    $cond[] = " DATE(a.check_in) >= :startDate ";
}
if (!empty($endDate)) {
    $cond[] = " DATE(a.check_in) <= :endDate ";
}

// Ghép các điều kiện
if (!empty($cond)) {
    $sqlCount .= " WHERE " . implode(" AND ", $cond);
}

$sqlCount .= "
    GROUP BY a.user_id, DATE(a.check_in)
) AS sub
";

// Chuẩn bị statement
$stmtCount = $conn->prepare($sqlCount);

// Bind các biến
if ($filterUID > 0) {
    $stmtCount->bindValue(':filterUID', $filterUID, PDO::PARAM_INT);
}
if (!empty($startDate)) {
    $stmtCount->bindValue(':startDate', $startDate, PDO::PARAM_STR);
}
if (!empty($endDate)) {
    $stmtCount->bindValue(':endDate', $endDate, PDO::PARAM_STR);
}

$stmtCount->execute();
$rowCount   = $stmtCount->fetch(PDO::FETCH_ASSOC);
$total_days = (int)($rowCount['total_days'] ?? 0);
$total_pages = ($total_days > 0) ? ceil($total_days / $rowsPerPage) : 1;


// --------------------
// 3) TRUY VẤN LẤY DỮ LIỆU GROUP-BY NGÀY
// --------------------
$sqlGroup = "
SELECT
  u.id AS user_id,
  u.username,
  DATE(a.check_in) AS work_date,
  MIN(a.check_in) AS earliest_in,
  MAX(a.check_out) AS latest_out,
  -- ...
  -- Lấy earliest_id, latest_id như trong code gốc
  SUBSTRING_INDEX(GROUP_CONCAT(a.id ORDER BY a.check_in ASC SEPARATOR ','), ',', 1) AS earliest_id,
  SUBSTRING_INDEX(GROUP_CONCAT(a.id ORDER BY a.check_out ASC SEPARATOR ','), ',', -1) AS latest_id
FROM attendance a
JOIN users u ON a.user_id = u.id
";

// Ghép điều kiện y như trên
$cond = [];
if ($filterUID > 0) {
    $cond[] = " a.user_id = :filterUID ";
}
if (!empty($startDate)) {
    $cond[] = " DATE(a.check_in) >= :startDate ";
}
if (!empty($endDate)) {
    $cond[] = " DATE(a.check_in) <= :endDate ";
}
if (!empty($cond)) {
    $sqlGroup .= " WHERE " . implode(" AND ", $cond);
}

$sqlGroup .= "
GROUP BY u.id, DATE(a.check_in)
ORDER BY work_date DESC, u.id ASC
LIMIT :start, :rpp
";

$stmtGroup = $conn->prepare($sqlGroup);
// Bind biến
if ($filterUID > 0) {
    $stmtGroup->bindValue(':filterUID', $filterUID, PDO::PARAM_INT);
}
if (!empty($startDate)) {
    $stmtGroup->bindValue(':startDate', $startDate, PDO::PARAM_STR);
}
if (!empty($endDate)) {
    $stmtGroup->bindValue(':endDate', $endDate, PDO::PARAM_STR);
}
$stmtGroup->bindValue(':start', $start, PDO::PARAM_INT);
$stmtGroup->bindValue(':rpp', $rowsPerPage, PDO::PARAM_INT);
$stmtGroup->execute();

$groupedAtt = $stmtGroup->fetchAll(PDO::FETCH_ASSOC);

$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>MiMi Quản Lý Chấm Công</title>
    <link rel="stylesheet" href="admin.css?v=<?= filemtime(__DIR__ . '/admin.css') ?>">    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/logoday.css">

    <style>
        .disabled {
            background-color: #eee;
        }
    </style>
    <!-- CSS daterangepicker -->
    <link rel="stylesheet" href="assets/daterangepicker.min.css">

    <!-- jQuery -->
    <script src="assets/jquery.min.js"></script>

    <!-- moment.js -->
    <script src="assets/moment.min.js"></script>

    <!-- JS daterangepicker -->
    <script src="assets/daterangepicker.min.js"></script>
</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/logoday.php'; ?>
<h1>Quản lý Nhân viên</h1>

<div class="container">
    <!-- =====  TAB NAV ===== -->
    <div class="tabs">
      <button class="tab-btn active" data-target="modul1">Thêm nhân viên</button>
      <button class="tab-btn"        data-target="modul2">Danh sách nhân viên</button>
      <button class="tab-btn"        data-target="modul3">Tính lương</button>
      <button class="tab-btn"        data-target="modul4">Quản lý chấm công</button>
      <button class="tab-btn"        data-target="modul5">Chấm công bù</button>
    </div>


    
    <!-- ================== FORM THÊM NHÂN VIÊN ================== -->
    <div id="modul1" class="tab-content active">
        <h2>Thêm nhân viên</h2>
        <form method="POST" action="" class="form-add-employee">
            <table>
                <tr>
                    <td><label>Username:</label></td>
                    <td><input type="text" name="username" required></td>
                    <td><label>Password:</label></td>
                    <td><input type="text" name="password" required></td>
                </tr>
                <tr>
                    <td><label>Loại nhân viên:</label></td>
                    <td>
                        <!-- Hàm toggleAddUser() sẽ được gọi khi thay đổi -->
                        <select name="employee_type">
                            <option value="chinh_thuc">Chính thức</option>
                            <option value="thoi_vu">Thời vụ</option>
                        </select>
                    </td>
                    <td><label>Lương tháng:</label></td>
                    <td>
                        <!-- class .base_new -->
                        <input type="number" name="base_salary" class="base_new">
                    </td>
                </tr>
                <tr>
                    <td><label>Tổng giờ yêu cầu làm:</label></td>
                    <td>
                        <!-- class .req_new -->
                        <input type="number" name="required_hours" class="req_new">
                    </td>
                    <td><label>Lương/giờ:</label></td>
                    <td>
                        <!-- class .hr_new -->
                        <input type="number" name="hourly_rate" class="hr_new">
                    </td>
                </tr>
            </table>
            <button type="submit" name="add_user">Thêm user</button>
        </form>
    </div>

    <div id="modul2" class="tab-content">
        <!-- ================== DANH SÁCH NHÂN VIÊN ================== -->
        <h2>Danh sách nhân viên</h2>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <!-- XÓA cột ID, thay bằng cột “Trạng thái” -->
                <th>Trạng thái</th>
                <th>Username</th>
                <th>Bỏ qua GPS?</th>
                <th>Loại NV</th>
                <th>Lương tháng (NV chính thức)</th>
                <th>Tổng giờ yêu cầu</th>
                <th>Lương/giờ (NV thời vụ)</th>
                <th colspan="2">Hành động</th>
            </tr>

            <?php
            // CHUẨN BỊ câu lệnh kiểm tra “đã check-in hôm nay chưa?” 
            // (sẽ dùng nhiều lần, ta chuẩn bị 1 statement)
            $stmtCheckInToday = $conn->prepare("
                SELECT id
                FROM attendance
                WHERE user_id = :uid
                AND DATE(check_in) = CURDATE()
                AND check_out IS NULL
                LIMIT 1
            ");

            foreach ($users as $u):
                // 1) Kiểm tra user này hôm nay đã check-in mà chưa check-out chưa?
                $stmtCheckInToday->execute(['uid' => $u['id']]);
                $rowAtt = $stmtCheckInToday->fetch(PDO::FETCH_ASSOC);

                // 2) Nếu có record => đang đi làm => xanh
                //    Nếu không => đỏ
                $isWorking = $rowAtt ? true : false;

                // Tạo HTML “chấm tròn” hoặc “icon” 
                //  - Màu xanh (#139b13) => Đang làm
                //  - Màu đỏ (#b83232) => Nghỉ
                // Cách đơn giản: <div style="width:10px; height:10px; border-radius:50%; background:#139b13;"></div>
                // Hoặc hiển thị text “Đang làm” / “Nghỉ”
                
                $color = $isWorking ? '#139b13' : '#b83232';
                if ($isWorking) {
                    // Đang làm => Check-out
                    $confirmMsg = "Bạn có chắc chắn muốn Check-out hộ nhân viên {$u['username']} không?";
                } else {
                    // Chưa làm => Check-in
                    $confirmMsg = "Bạn có chắc chắn muốn Check-in hộ nhân viên {$u['username']} không?";
                }
                
                $statusHTML = '
                <form method="POST" action="" 
                    onsubmit="return confirm(\''.$confirmMsg.'\');" 
                    style="display:inline;">
                    <input type="hidden" name="user_id" value="'.$u['id'].'">
                    <button type="submit" name="toggle_status" 
                            style="border:none; background:none; cursor:pointer;">
                        <div style="width:20px; height:20px; border-radius:50%; background:'.$color.'; margin:auto;"></div>
                    </button>
                </form>';
                        
            ?>
                <tr>
                    <!-- CỘT 1: TRẠNG THÁI (Checkin/Checkout hộ) -->
                    <td>
                        <?php
                        // Vẫn giữ code tính $isWorking, $color, $confirmMsg
                        // Và tạo $statusHTML = '<form> ... </form>';
                        echo $statusHTML;
                        ?>
                    </td>

                    <!-- CỘT 2..8: Sửa thông tin user (update_user) -->
                    <form method="POST" action="">
                        <!-- Cột 2: Username (chỉ hiển thị text) -->
                        <td><?php echo htmlspecialchars($u['username']); ?></td>

                        <!-- Cột 3: Bỏ qua GPS? -->
                        <td>
                            <input type="checkbox" name="ignore_location" value="1"
                                <?php if($u['ignore_location'] == 1) echo 'checked'; ?>>
                        </td>

                        <!-- Cột 4: Loại NV -->
                        <td>
                            <select name="employee_type">
                                <option value="chinh_thuc"
                                    <?php if($u['employee_type']=='chinh_thuc') echo 'selected'; ?>>
                                    Chính thức
                                </option>
                                <option value="thoi_vu"
                                    <?php if($u['employee_type']=='thoi_vu') echo 'selected'; ?>>
                                    Thời vụ
                                </option>
                            </select>
                        </td>

                        <!-- Cột 5: Lương tháng -->
                        <td>
                            <input type="number" name="base_salary"
                                value="<?php echo $u['base_salary']; ?>"
                                class="base_salary">
                        </td>

                        <!-- Cột 6: Tổng giờ yêu cầu -->
                        <td>
                            <input type="number" name="required_hours"
                                value="<?php echo $u['required_hours']; ?>"
                                class="required_hours">
                        </td>

                        <!-- Cột 7: Lương/giờ -->
                        <td>
                            <input type="number" name="hourly_rate"
                                value="<?php echo $u['hourly_rate']; ?>"
                                class="hourly_rate">
                        </td>

                        <!-- Cột 8: Nút Lưu -->
                        <td>
                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                            <button type="submit" name="update_user">Lưu</button>
                        </td>
                    </form>

                    <!-- CỘT 9: Xóa user -->
                    <td>
                        <form method="POST" action=""
                            onsubmit="return confirm('Bạn chắc chắn muốn xóa user <?php echo $u['username']; ?>?');">
                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                            <button type="submit" name="delete_user" style="color:red;">Xóa</button>
                        </form>
                    </td>
                </tr>

            <?php endforeach; ?>
        </table>
    </div>

    <!-- =========== TÍNH LƯƠNG ============ -->
    <div id="modul3" class="tab-content">
        <h2>Tính lương</h2>
        <form method="POST">
            <label>Tháng: </label>
            <input type="number" name="month" value="<?php echo date('m'); ?>" style="width:60px;">
            <label>Năm: </label>
            <input type="number" name="year" value="<?php echo date('Y'); ?>" style="width:70px;">
            <button class ="dat-mau-vang" type="submit" name="calculate_salary">Tính lương</button>
        </form>

        <?php if (!empty($totalResult)): ?>
            <h3 id="bang-luong" style="color: #b83232; border-bottom: 2px solid #b83232;">
                Kết quả lương tháng <?php echo $calcMonth . "/" . $calcYear; ?>:
            </h3>
            <table border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <th style="background-color: #b83232;">ID</th>
                    <th style="background-color: #b83232;">Tài khoản</th>
                    <th style="background-color: #b83232;">Loại NV</th>
                    <th style="background-color: #b83232;">Tổng phút</th>
                    <th style="background-color: #b83232;">Tổng giờ</th>
                    <th style="background-color: #b83232;">Lương</th>
                </tr>
                <?php
                $totalSalary = 0;
                foreach ($totalResult as $res):
                    $totalSalary += $res['salary'];
                ?>
                <tr>
                    <td><?php echo $res['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($res['username']); ?></td>
                    <td><?php echo $res['employee_type']; ?></td>
                    <td><?php echo $res['total_mins']; ?></td>
                    <td><?php echo round($res['actual_hours'], 2); ?></td>
                    <td><?php echo number_format($res['salary']); ?> VNĐ</td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="5" style="text-align:right; font-weight:bold; color:#b83232;">
                        Tổng lương nhân viên:
                    </td>
                    <td style="font-weight:bold; color:#b83232; font-size:20px;">
                        <?php echo number_format($totalSalary); ?> VNĐ
                    </td>
                </tr>
            </table>
        <?php endif; ?>
    </div>



    <!-- =========== QUẢN LÝ CHẤM CÔNG (THEO NGÀY) =========== -->
    <div id="modul4" class="tab-content">
        <h2 id="table-chamcong" >Quản lý chấm công</h2>

        <!-- =========== FORM LỌC THEO KHOẢNG NGÀY =========== -->
        <form method="GET" action="#table-chamcong" style="margin-bottom: 1rem;" class="filter-row">
            <!-- Bộ lọc user_id (nếu còn dùng) -->
            <label>Chọn nhân viên:</label>
            <select name="filter_user_id" onchange="this.form.submit()">
            <option value="">-- Tất cả --</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?php echo $u['id']; ?>"
                        <?php if (!empty($_GET['filter_user_id']) && $_GET['filter_user_id'] == $u['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($u['username']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Input để pick range -->
            <label>Khoảng ngày:</label>
            <input type="text" 
                id="daterange" 
                style="width:200px; text-align:center;"
                value="<?php echo $startDateDmy . ' - ' . $endDateDmy; ?>"
                readonly />

            <!-- 2 input ẩn để gửi lên GET -->
            <input type="hidden" name="start_date" id="start_date" value="<?php echo $startDate; ?>">
            <input type="hidden" name="end_date"   id="end_date"   value="<?php echo $endDate; ?>">


            <!-- Số hàng mỗi trang -->
            <label>Số hàng/trang:</label>
            <select name="rows_per_page"
                onchange="this.form.submit()">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
            </select>
        </form>


        <!-- Bảng gộp theo ngày (6 cột) -->
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Nhân viên</th>
                <th>Ngày chấm công</th>
                <th>Giờ Check-In</th>
                <th>Giờ Check-Out</th>
                <th>Thời gian làm việc</th>
                <th>Hành động</th>
            </tr>

            <?php if (count($groupedAtt) === 0): ?>
                <tr>
                    <td colspan="6" style="color:red; font-weight:bold;">
                        Không có dữ liệu chấm công tháng này
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($groupedAtt as $g): ?>
                    <?php
                        // Tính dailyHours
                        $earliestTS = strtotime($g['earliest_in']);
                        $latestTS   = strtotime($g['latest_out']);
                        $dailyHours = 0;
                        if ($earliestTS && $latestTS && $latestTS > $earliestTS) {
                            $diffSec    = $latestTS - $earliestTS;
                            $dailyHours = round($diffSec / 3600, 2);
                        }

                        // Chuyển sang dạng HH:ii
                        $earliestHM = toHourMinute($g['earliest_in']);
                        $latestHM   = toHourMinute($g['latest_out']);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($g['username']); ?> (user_id=<?php echo $g['user_id']; ?>)</td>
                        <td><?php echo ymdToDmy($g['work_date']); ?></td>
                        
                        <!-- FORM LƯU -->
                        <form method="POST" action="">
                            <input type="hidden" name="earliest_id" value="<?php echo $g['earliest_id']; ?>">
                            <input type="hidden" name="latest_id"   value="<?php echo $g['latest_id']; ?>">
                            <input type="hidden" name="the_date"    value="<?php echo $g['work_date']; ?>">

                            <td>
                                <input type="text" name="earliest_in" value="<?php echo $earliestHM; ?>" size="5">
                            </td>
                            <td>
                                <input type="text" name="latest_out" value="<?php echo $latestHM; ?>" size="5">
                            </td>
                            <td><?php echo $dailyHours; ?> giờ</td>
                            <td class="action-buttons">
                                <!-- Nút Lưu -->
                                <button type="submit" name="update_earliest_latest">Lưu</button>
                        </form>

                        <!-- FORM XÓA -->
                        <form method="POST" action="" style="display:inline;"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa toàn bộ chấm công ngày <?php echo $g['work_date']; ?> của user <?php echo $g['username']; ?>?');">
                            <input type="hidden" name="delete_day_attendance" value="1">
                            <input type="hidden" name="user_id" value="<?php echo $g['user_id']; ?>">
                            <input type="hidden" name="the_date" value="<?php echo $g['work_date']; ?>">
                            <button type="submit" style="color:white; background-color:#b83232;">Xóa</button>
                        </form>
                            </td>
                    </tr>
                <?php endforeach; ?>


            <?php endif; ?>
        </table>
        <div class="detail-link-wrapper">
            <a class="btn btn-detail" href="detail.php?filter_user_id=<?php echo $filterUID; ?>
                &start_date=<?php echo $startDate; ?>
                &end_date=<?php echo $endDate; ?>
                &rows_per_page=<?php echo $rowsPerPage; ?>">

                    Xem chi tiết nhân viên & lương
            </a>
        </div>
        <!-- Hiển thị pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?filter_user_id=<?php echo $filterUID; ?>&rows_per_page=<?php echo $rowsPerPage; ?>&page=<?php echo ($page-1); ?>#table-chamcong">&laquo;</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?filter_user_id=<?php echo $filterUID; ?>&rows_per_page=<?php echo $rowsPerPage; ?>&page=<?php echo $i; ?>#table-chamcong"
                class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?filter_user_id=<?php echo $filterUID; ?>&rows_per_page=<?php echo $rowsPerPage; ?>&page=<?php echo ($page+1); ?>#table-chamcong">&raquo;</a>
            <?php endif; ?>
        </div>
    </div>
    <!-- Form Chấm Công Bù -->
    <div id="modul5" class="tab-content">
            <h2>Chấm công bù</h2>
            <form method="POST" action="" class="form-add-attendance">
                <table>
                    <tr>
                        <td><label>Chọn nhân viên:</label></td>
                        <td>
                            <select name="user_id" required>
                                <?php foreach ($users as $u): ?>
                                <option value="<?php echo $u['id']; ?>">
                                    <?php echo htmlspecialchars($u['username']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><label>Thời gian:</label></td>
                        <td><input type="date" name="the_date" required></td>
                    </tr>
                    <tr>
                        <td><label>Giờ check-in:</label></td>
                        <td><input type="time" name="in_time" required></td>
                        <td><label>Giờ check-out:</label></td>
                        <td><input type="time" name="out_time" required></td>
                    </tr>
                </table>
                <button type="submit" name="add_attendance">Thêm</button>
            </form>
        </div>
    </div>
<!-- ================== SCRIPT TẮT/BẬT TRƯỜNG NHẬP ================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // ===== 1) Tab navigation =====
  function activateTab(btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
    btn.classList.add('active');
    const target = btn.getAttribute('data-target');
    document.getElementById(target).classList.add('active');
    localStorage.setItem('activeTab', target);
  }

  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => activateTab(btn));
  });

  // on load: use #hash first, then saved tab
  let initial = null;
  const hash = window.location.hash.replace('#','');
  if (hash && /^modul[1-5]$/.test(hash) && document.getElementById(hash)) {
    initial = hash;
  } else {
    const saved = localStorage.getItem('activeTab');
    if (saved && document.getElementById(saved)) {
      initial = saved;
    }
  }
  if (initial) {
    const btn = document.querySelector(`.tab-btn[data-target="${initial}"]`);
    if (btn) activateTab(btn);
  }

  // ===== 2) Thêm nhân viên: toggle các trường theo Loại nhân viên =====
  const addSelect = document.querySelector('.form-add-employee select[name="employee_type"]');
  if (addSelect) {
    toggleAddUser(addSelect);
    addSelect.addEventListener('change', () => toggleAddUser(addSelect));
  }

  // ===== 3) Danh sách nhân viên: toggleFields cho mỗi dòng =====
  document.querySelectorAll('table tr select[name="employee_type"]').forEach(s => {
    toggleFields(s);
    s.addEventListener('change', () => toggleFields(s));
  });
});

    // 2) Bảng danh sách nhân viên => toggleFields() cho mỗi dòng
    var editSelects = document.querySelectorAll('table tr select[name="employee_type"]');
    editSelects.forEach(function(s) {
        // Gọi 1 lần ngay khi load
        toggleFields(s);
        // Gắn sự kiện change
        s.addEventListener('change', function() {
            toggleFields(this);
        });
    });

// Hàm tắt/bật cho Form Thêm Nhân Viên (class .base_new, .req_new, .hr_new)
function toggleAddUser(sel) {
  const baseNew = document.querySelector('.form-add-employee .base_new');
  const reqNew  = document.querySelector('.form-add-employee .req_new');
  const hrNew   = document.querySelector('.form-add-employee .hr_new');
  if (!baseNew || !reqNew || !hrNew) return;

  if (sel.value === 'chinh_thuc') {
    baseNew.readOnly = false; reqNew.readOnly = false; hrNew.readOnly = true;
    baseNew.classList.remove('disabled');
    reqNew.classList.remove('disabled');
    hrNew.classList.add('disabled');
  } else {
    baseNew.readOnly = true;  reqNew.readOnly = true;  hrNew.readOnly = false;
    baseNew.classList.add('disabled');
    reqNew.classList.add('disabled');
    hrNew.classList.remove('disabled');
  }
}

// Hàm tắt/bật cho Bảng Danh Sách Nhân Viên (class .base_salary, .required_hours, .hourly_rate)
function toggleFields(selectEl) {
  const tr = selectEl.closest('tr');
  const base    = tr.querySelector('.base_salary');
  const req     = tr.querySelector('.required_hours');
  const hourly  = tr.querySelector('.hourly_rate');
  if (!base || !req || !hourly) return;

  if (selectEl.value === 'chinh_thuc') {
    base.readOnly   = false; req.readOnly    = false; hourly.readOnly = true;
    base.classList.remove('disabled');
    req.classList.remove('disabled');
    hourly.classList.add('disabled');
  } else {
    base.readOnly   = true;  req.readOnly    = true;  hourly.readOnly = false;
    base.classList.add('disabled');
    req.classList.add('disabled');
    hourly.classList.remove('disabled');
  }
}
$(document).ready(function(){
  // Đoạn cài đặt daterangepicker bình thường:
  var phpStart = '<?php echo $startDate; ?>'; // Ví dụ: "2025-03-01"
  var phpEnd   = '<?php echo $endDate; ?>';  // Ví dụ: "2025-03-31"

  $('#daterange').daterangepicker({
    autoUpdateInput: true,
    startDate: moment(phpStart, 'YYYY-MM-DD'),
    endDate:   moment(phpEnd,   'YYYY-MM-DD'),
    locale: {
      format: 'DD/MM/YYYY',
      separator: ' - ',
      applyLabel: 'OK',
      cancelLabel: 'Hủy',
      daysOfWeek:  ['CN','T2','T3','T4','T5','T6','T7'],
      monthNames:  [
        'Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5',
        'Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10',
        'Tháng 11','Tháng 12'
      ],
      firstDay: 1
    }
  },
  function(start, end) {
    // Callback khi người dùng bấm OK
    $('#daterange').val(
      start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY')
    );
    $('#start_date').val(start.format('YYYY-MM-DD'));
    $('#end_date').val(end.format('YYYY-MM-DD'));
      // Tự động submit form 
    $('#daterange').closest('form').submit();
});

  // Đoạn thêm nút "Hôm nay" vào popup:
  $('#daterange').on('show.daterangepicker', function(ev, picker) {
    // Cho vào setTimeout để chắc chắn phần tử .drp-buttons đã tồn tại
    setTimeout(function(){
      // Tránh thêm trùng lặp nhiều lần:
      if (!picker.container.find('.btn-today').length) {
        // Thêm nút "Hôm nay" vào trước nút OK/Hủy
        picker.container.find('.drp-buttons').prepend(`
          <button type="button" class="btn btn-sm btn-primary btn-today">Hôm nay</button>
        `);

        // Gán sự kiện click cho nút
        picker.container.find('.btn-today').on('click', function(){
          var now = moment();
          picker.setStartDate(now);
          picker.setEndDate(now);
          // Nhảy lịch tới tháng hiện tại
        picker.leftCalendar.month = now.clone();
        picker.rightCalendar.month = now.clone().add(1, 'month');
        picker.updateCalendars();

        });
      }
    }, 50);
  });
});
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      // 1) Kích hoạt nút
      document.querySelectorAll('.tab-btn')
        .forEach(b => b.classList.remove('active'));
      this.classList.add('active');

      // 2) Show content đúng ID, ẩn các content khác
      const target = this.getAttribute('data-target');
      document.querySelectorAll('.tab-content')
        .forEach(tc => tc.classList.remove('active'));
      document.getElementById(target)
        .classList.add('active');
    });
  });
</script>

</body>
</html>
 
