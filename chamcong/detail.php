<?php
// detail.php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'db.php';
require __DIR__ . '/../auth.php';


// Các hàm hỗ trợ
function toHourMinute($ymdHis) {
    if (!$ymdHis) return '';
    $parts = explode(' ', $ymdHis);
    if (count($parts) < 2) return '';
    return substr($parts[1], 0, 5); // "HH:ii"
}

function ymdToDmy($ymd) {
    if (!$ymd) return '';
    $arr = explode('-', $ymd);
    if (count($arr) < 3) return $ymd;
    // arr[0] = YYYY, arr[1] = mm, arr[2] = dd
    return $arr[2].'/'.$arr[1].'/'.$arr[0]; // dd/mm/yyyy
}

// 1) Lấy biến filter từ GET
$filterUID  = !empty($_GET['filter_user_id']) ? (int)$_GET['filter_user_id'] : 0;
$startDate  = !empty($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate    = !empty($_GET['end_date'])   ? $_GET['end_date']   : '';
$rowsPerPage= isset($_GET['rows_per_page']) ? (int)$_GET['rows_per_page'] : 10;
if ($rowsPerPage <= 0) $rowsPerPage = 10;
$page       = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Nếu chưa có start/end => mặc định từ đầu tháng đến cuối tháng
if (empty($startDate) || empty($endDate)) {
    $startDate = date('Y-m-01');
    $endDate   = date('Y-m-t');
}
// Đổi sang dạng dd/mm/yyyy để in sẵn ra input
$startDateDmy = ymdToDmy($startDate);
$endDateDmy   = ymdToDmy($endDate);

// 2) Lấy danh sách user (để <select> lọc nhân viên)
$stmtUsers = $conn->query("SELECT * FROM users");
$allUsers = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// 3) Tính tổng số ngày (để phân trang)
$sqlCount = "
SELECT COUNT(*) AS total_days
FROM (
    SELECT DATE(a.check_in) AS work_date, a.user_id
    FROM attendance a
";
$cond = [];
if ($filterUID > 0) {
    $cond[] = " a.user_id = :filterUID ";
}
if (!empty($startDate)) {
    $cond[] = " DATE(a.check_in) >= :startD ";
}
if (!empty($endDate)) {
    $cond[] = " DATE(a.check_in) <= :endD ";
}
if (!empty($cond)) {
    $sqlCount .= " WHERE ".implode(" AND ", $cond);
}
$sqlCount .= " GROUP BY a.user_id, DATE(a.check_in)
) as sub";

// Chuẩn bị + bind
$stmtCount = $conn->prepare($sqlCount);
if ($filterUID > 0) {
    $stmtCount->bindValue(':filterUID', $filterUID, PDO::PARAM_INT);
}
if (!empty($startDate)) {
    $stmtCount->bindValue(':startD', $startDate, PDO::PARAM_STR);
}
if (!empty($endDate)) {
    $stmtCount->bindValue(':endD', $endDate, PDO::PARAM_STR);
}
$stmtCount->execute();
$rowC = $stmtCount->fetch(PDO::FETCH_ASSOC);
$total_days = (int)($rowC['total_days'] ?? 0);
$total_pages = $total_days > 0 ? ceil($total_days / $rowsPerPage) : 1;
// ================================
// A) TRUY VẤN KHÔNG LIMIT ĐỂ TÍNH TỔNG
// ================================
$sqlAll = "
SELECT
  u.id AS user_id,
  u.username,
  DATE(a.check_in) AS work_date,
  MIN(a.check_in) AS earliest_in,
  MAX(a.check_out) AS latest_out
FROM attendance a
JOIN users u ON a.user_id = u.id
";

// Mảng điều kiện
$condAll = [];
if ($filterUID > 0) {
    $condAll[] = " a.user_id = :filterUID ";
}
if (!empty($startDate)) {
    $condAll[] = " DATE(a.check_in) >= :startD ";
}
if (!empty($endDate)) {
    $condAll[] = " DATE(a.check_in) <= :endD ";
}
if (!empty($condAll)) {
    $sqlAll .= " WHERE " . implode(" AND ", $condAll);
}
$sqlAll .= "
GROUP BY u.id, DATE(a.check_in)
ORDER BY work_date DESC, u.id ASC
";

// Chuẩn bị + bind
$stmtAll = $conn->prepare($sqlAll);
if ($filterUID > 0) {
    $stmtAll->bindValue(':filterUID', $filterUID, PDO::PARAM_INT);
}
if (!empty($startDate)) {
    $stmtAll->bindValue(':startD', $startDate, PDO::PARAM_STR);
}
if (!empty($endDate)) {
    $stmtAll->bindValue(':endD', $endDate, PDO::PARAM_STR);
}
$stmtAll->execute();

$allRows = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

// Tính sumHours, sumSalary (không limit)
$sumHours = 0;
$sumSalary = 0;
foreach ($allRows as $r) {
    // Tính dailyHours
    $earliestTS = strtotime($r['earliest_in']);
    $latestTS   = strtotime($r['latest_out']);
    $dailyHours = 0;
    if ($earliestTS && $latestTS && $latestTS > $earliestTS) {
        $diffSec    = $latestTS - $earliestTS;
        $dailyHours = round($diffSec / 3600, 2);
    }
    $sumHours += $dailyHours;

    // Tính lương (dùng hàm getUserSalaryPerDay bạn đã có)
    $daySalary = getUserSalaryPerDay($conn, $r['user_id'], $r['earliest_in'], $r['latest_out']);
    $sumSalary += $daySalary;
}

// ================================
// HẾT PHẦN TÍNH TỔNG
// ================================

// 4) Lấy dữ liệu chấm công (group by user_id + date)
$start = ($page - 1) * $rowsPerPage;
$sqlGroup = "
SELECT
  u.id AS user_id,
  u.username,
  DATE(a.check_in) AS work_date,
  MIN(a.check_in) AS earliest_in,
  MAX(a.check_out) AS latest_out,
  SUBSTRING_INDEX(GROUP_CONCAT(a.id ORDER BY a.check_in ASC SEPARATOR ','), ',', 1) AS earliest_id,
  SUBSTRING_INDEX(GROUP_CONCAT(a.id ORDER BY a.check_out ASC SEPARATOR ','), ',', -1) AS latest_id
FROM attendance a
JOIN users u ON a.user_id = u.id
";
$cond = [];
if ($filterUID > 0) {
    $cond[] = " a.user_id = :filterUID ";
}
if (!empty($startDate)) {
    $cond[] = " DATE(a.check_in) >= :startD ";
}
if (!empty($endDate)) {
    $cond[] = " DATE(a.check_in) <= :endD ";
}
if (!empty($cond)) {
    $sqlGroup .= " WHERE ".implode(" AND ", $cond);
}
$sqlGroup .= "
GROUP BY u.id, DATE(a.check_in)
ORDER BY work_date DESC, u.id ASC
LIMIT :start, :rpp
";

// Chuẩn bị + bind
$stmtGroup = $conn->prepare($sqlGroup);
if ($filterUID > 0) {
    $stmtGroup->bindValue(':filterUID', $filterUID, PDO::PARAM_INT);
}
if (!empty($startDate)) {
    $stmtGroup->bindValue(':startD', $startDate, PDO::PARAM_STR);
}
if (!empty($endDate)) {
    $stmtGroup->bindValue(':endD', $endDate, PDO::PARAM_STR);
}
$stmtGroup->bindValue(':start', $start, PDO::PARAM_INT);
$stmtGroup->bindValue(':rpp', $rowsPerPage, PDO::PARAM_INT);
$stmtGroup->execute();

$groupedAtt = $stmtGroup->fetchAll(PDO::FETCH_ASSOC);

// 5) Tính “Tổng giờ” + “Tổng lương” (demo)
// $sumHours = 0;
// $sumSalary = 0;

// 5a) Bạn cần 1 hàm lấy lương user => ví dụ:
function getUserSalaryPerDay($conn, $uid, $earliest_in, $latest_out) {
    // DEMO: Tính lương 1 ngày cho user $uid
    // Lấy info user
    $stmtU = $conn->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
    $stmtU->execute([$uid]);
    $u = $stmtU->fetch(PDO::FETCH_ASSOC);
    if (!$u) return 0;

    // Tính số giờ
    $earliestTS = strtotime($earliest_in);
    $latestTS   = strtotime($latest_out);
    if (!$earliestTS || !$latestTS || $latestTS <= $earliestTS) {
        return 0;
    }
    $diffSec = $latestTS - $earliestTS;
    $hours   = $diffSec / 3600.0;

    // Tính theo employee_type
    if ($u['employee_type'] === 'chinh_thuc') {
        // Giả sử: (base_salary * (hours / required_hours))
        $base = (float) $u['base_salary'];
        $reqH = (float) $u['required_hours'];
        if ($reqH > 0) {
            return ($base * ($hours / $reqH));
        } else {
            return 0;
        }
    } else {
        // 'thoi_vu' => hourly_rate * hours
        $rate = (float) $u['hourly_rate'];
        return $rate * $hours;
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Detail Nhân Viên & Lương</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="admin.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- daterangepicker CSS -->
  <link rel="stylesheet" href="assets/daterangepicker.min.css">
  <link rel="stylesheet" href="/logoday.css">

  <!-- jQuery + moment + daterangepicker JS -->
  <script src="assets/jquery.min.js"></script>
  <script src="assets/moment.min.js"></script>
  <script src="assets/daterangepicker.min.js"></script>
  <style>
    .tro-ve-trang-admin > a {
        
       display: inline-block; 
       background-color: #d1a029; 
       color: #fff; 
       padding: 8px 12px; 
       border-radius: 5px;
       text-decoration: none;
     
    }
    .tro-ve-trang-admin {
      text-align: right; 
      margin: 1rem 0;
      display: flex;
      justify-content: flex-end;

    }
  @media only screen and (max-width: 1024px) {
    /* Co lại form lọc */
    form.filter-row {
      display: flex !important;
      flex-wrap: nowrap !important;
      align-items: center !important;
      gap: 8px !important;
      margin: 0 10px 1rem !important;
      overflow-x: auto !important;
      font-size: 0.9rem !important;
    }
    form.filter-row label {
      flex: 0 0 auto !important;
      margin-left: 0 !important;
      white-space: nowrap !important;
    }
    form.filter-row select,
    form.filter-row input#daterange {
      flex: 1 1 auto !important;
      min-width: 80px !important;
      width: auto !important;
      text-align: left !important;
    }

    /* Ép table và tbody thành block, giãn full width */
    table,
    table tbody {
      display: block !important;
      width: 100% !important;
    }
    table {
      overflow-x: auto !important;
      white-space: nowrap !important;
    }
    table, th, td {
      font-size: 14px !important;
    }
    table input {
      width: 70% !important;
    }
  
  .day-ne tbody {
    display: block;
    width: 100%;
  }
   /* Chỉ chọn đúng bảng trong detail.php, tăng specificity 
     => đánh bại admin.css / logoday.css */
     .container > table > tbody {
    display: block !important;
    width: 100%   !important;
  }
  /* (Vẫn giữ table scroll) */
  .container > table {
    display: block        !important;
    width:   100%         !important;
    overflow-x: auto      !important;
    white-space: nowrap   !important;
  }
/* 1) giữ table là block và có scroll */
table.day-ne {
    display: block !important;
    width: 100%   !important;
    overflow-x: auto !important;
    white-space: nowrap !important;
  }

  /* 2) cho thead & tbody đều là block, để các <tr> có space để giãn */
  table.day-ne thead,
  table.day-ne tbody {
    display: block !important;
    width: 100%   !important;
  }

  /* 3) ép mỗi <tr> thành bảng con width 100% */
  table.day-ne thead tr,
  table.day-ne tbody tr {
    display: table !important;
    width: 100%   !important;
    table-layout: fixed !important;
  }

  /* 4) chia đều 5 cột, mỗi cột 20% */
  table.day-ne th,
  table.day-ne td {
    width: 20% !important;
  }
  
}
@media only screen and (max-width: 768px) {
    /* 1) Form lọc xếp dọc và sát trái */
    form.filter-row {
      display: flex !important;
      flex-direction: column !important;
      align-items: flex-start !important;
      gap: 12px !important;
      margin: 0 !important;
      padding: 0 10px !important;
      font-size: 0.9rem !important;
    }
    form.filter-row label,
    form.filter-row select,
    form.filter-row input#daterange {
      text-align: left !important;
      margin: 0 !important;
    }
   
    /* 2) Wrapper table bật scroll ngang, không ép width cột */
    table.day-ne {
      display: block          !important;
      width: 100%             !important;
      overflow-x: auto        !important;
      white-space: nowrap     !important;
    }

    /* 3) Trả lại layout bình thường cho thead/tbody/tr/cell */
    table.day-ne thead,
    table.day-ne tbody {
      display: table-header-group !important;
      width: auto               !important;
    }
    table.day-ne tr {
      display: table-row !important;
      width: auto       !important;
    }
    table.day-ne th,
    table.day-ne td {
      display: table-cell !important;
      white-space: nowrap  !important;
      width: 150px          !important;
      padding: 8px 12px    !important;
      line-height: 1.5     !important;
      white-space: nowrap  !important;
    }
      /* Kéo lại table về separate, để border-spacing có tác dụng */
      table.day-ne {
      border-collapse: separate !important;
      border-spacing: 0 8px    !important; /* 8px khoảng cách giữa các hàng */
    }

    
  
  }
</style>


</head>


<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/logoday.php'; ?>



<div class="container">
<div  class="tro-ve-trang-admin">
  <a href="admin.php">
    Trở về Trang Admin
  </a>
</div>
  <h1>Chi Tiết Nhân Viên & Lương</h1>

  <!-- Form lọc -->
  <form method="GET" action="" class="filter-row" style="margin-bottom:1rem;">
    <label>Chọn nhân viên:</label>
    <select name="filter_user_id" onchange="this.form.submit()">
      <option value="">-- Tất cả --</option>
      <?php foreach ($allUsers as $u): ?>
        <option value="<?php echo $u['id']; ?>"
          <?php if (!empty($_GET['filter_user_id']) && $_GET['filter_user_id'] == $u['id']) echo 'selected'; ?>>
          <?php echo htmlspecialchars($u['username']); ?>
        </option>
      <?php endforeach; ?>
    </select>

    <!-- Khoảng ngày -->
    <label style="margin-left:10px;">Khoảng ngày:</label>
    <input type="text" id="daterange" 
           style="width:200px; text-align:center;" 
           value="<?php echo $startDateDmy.' - '.$endDateDmy; ?>"
           readonly />

    <input type="hidden" name="start_date" id="start_date" value="<?php echo $startDate; ?>">
    <input type="hidden" name="end_date"   id="end_date"   value="<?php echo $endDate; ?>">

    <!-- Số hàng/trang -->
    <label style="margin-left:10px;">Số hàng/trang:</label>
    <select name="rows_per_page" onchange="this.form.submit()">
      <option value="10" <?php if($rowsPerPage==10) echo 'selected';?>>10</option>
      <option value="20" <?php if($rowsPerPage==20) echo 'selected';?>>20</option>
      <option value="30" <?php if($rowsPerPage==30) echo 'selected';?>>30</option>
    </select>
  </form>

  <!-- Bảng chấm công -->
  <table class="day-ne" border="1" cellpadding="5" cellspacing="0">
    <tr>
      <th>Nhân viên</th>
      <th>Ngày chấm công</th>
      <th>Giờ Check-In</th>
      <th>Giờ Check-Out</th>
      <th>Thời gian làm</th>
      <!-- Bỏ cột user_id, bỏ cột Hành động -->
    </tr>

    <?php if (count($groupedAtt) === 0): ?>
      <tr>
        <td colspan="5" style="color:red; font-weight:bold;">
          Không có dữ liệu chấm công
        </td>
      </tr>
    <?php else: ?>
      <?php foreach ($groupedAtt as $g):
        // Tính dailyHours
        $earliestTS = strtotime($g['earliest_in']);
        $latestTS   = strtotime($g['latest_out']);
        $dailyHours = 0;
        if ($earliestTS && $latestTS && $latestTS > $earliestTS) {
            $diffSec    = $latestTS - $earliestTS;
            $dailyHours = round($diffSec / 3600, 2);
        }
        // Cộng dồn
        // $sumHours += $dailyHours;

        // Tính lương 1 ngày (tùy logic)
        $daySalary = getUserSalaryPerDay($conn, $g['user_id'], $g['earliest_in'], $g['latest_out']);
        // $sumSalary += $daySalary;

        $earliestHM = toHourMinute($g['earliest_in']);
        $latestHM   = toHourMinute($g['latest_out']);
      ?>
        <tr>
          <td><?php echo htmlspecialchars($g['username']); ?></td>
          <td><?php echo ymdToDmy($g['work_date']); ?></td>
          <td><?php echo $earliestHM; ?></td>
          <td><?php echo $latestHM; ?></td>
          <td><?php echo $dailyHours; ?> giờ</td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>

  <!-- Hiển thị pagination -->
  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="?filter_user_id=<?php echo $filterUID; ?>&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>&rows_per_page=<?php echo $rowsPerPage; ?>&page=<?php echo ($page-1); ?>">«</a>
    <?php endif; ?>
    
    <?php for ($i=1; $i<=$total_pages; $i++): ?>
      <a href="?filter_user_id=<?php echo $filterUID; ?>&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>&rows_per_page=<?php echo $rowsPerPage; ?>&page=<?php echo $i; ?>"
         class="<?php echo ($i==$page)?'active':''; ?>">
         <?php echo $i; ?>
      </a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
      <a href="?filter_user_id=<?php echo $filterUID; ?>&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>&rows_per_page=<?php echo $rowsPerPage; ?>&page=<?php echo ($page+1); ?>">»</a>
    <?php endif; ?>
  </div>

  <!-- Bảng Tổng giờ, Tổng lương, Thưởng, Tổng cộng lương -->
  <?php 
    // Chuẩn bị biến hiển thị
    $sumHoursDisplay = round($sumHours, 2);
    $sumSalaryDisplay = number_format($sumSalary, 0, ',', '.');
?>
  <div style="max-width: 400px; margin-top: 30px;">
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
      <tr>
        <th style="text-align:right;">Tổng giờ làm:</th>
        <td id="totalHours"><?php echo $sumHoursDisplay; ?></td>
      </tr>
      <tr>
        <th style="text-align:right;">Tổng lương:</th>
        <td id="totalSalary"><?php echo $sumSalaryDisplay; ?></td>
      </tr>
    <tr>
    <th style="text-align:right;">Thưởng:</th>
    <td>
        <!-- Đổi type="number" => type="text" -->
        <input style="border: 0px solid #ccc;" type="text" id="bonusInput" value="0" style="width:100%;">
    </td>
    </tr>
    <tr>
    <th style="text-align:right; background-color:#c82333; ">Tổng cộng lương:</th>
    <td>
        <!-- Đổi type="number" => type="text" -->
        <input style="border: 0px solid #ccc; color: #c82333; font-weight:800;" type="text" id="finalInput" value="0" style="width:100%;">
    </td>
    </tr>

    </table>
  </div>

</div> <!-- end container -->

<!-- SCRIPT DATERANGEPICKER & Tự động submit khi nhấn OK -->
<script>
$(document).ready(function(){
  var phpStart = '<?php echo $startDate; ?>'; 
  var phpEnd   = '<?php echo $endDate; ?>';

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
      monthNames:  ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'],
      firstDay: 1
    }
  }, 
  function(start, end) {
    // Callback khi nhấn OK
    $('#daterange').val(
      start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY')
    );
    $('#start_date').val(start.format('YYYY-MM-DD'));
    $('#end_date').val(end.format('YYYY-MM-DD'));

    // Submit form
    $('#daterange').closest('form').submit();
  });

  // Thêm nút "Hôm nay" (nếu cần)
  $('#daterange').on('show.daterangepicker', function(ev, picker) {
    setTimeout(function(){
      if (!picker.container.find('.btn-today').length) {
        picker.container.find('.drp-buttons').prepend(`
          <button type="button" class="btn btn-sm btn-primary btn-today">Hôm nay</button>
        `);
        picker.container.find('.btn-today').on('click', function(){
          var now = moment();
          picker.setStartDate(now);
          picker.setEndDate(now);
          // Nhảy luôn về tháng hiện tại
          picker.leftCalendar.month = now.clone();
          picker.rightCalendar.month= now.clone().add(1,'month');
          picker.updateCalendars();
        });
      }
    }, 50);
  });
});
</script>

<!-- SCRIPT Thưởng/Tổng cộng -->
<script>
function formatMoney(value) {
  // chuyển 3040520 => "3.040.520"
  return value
    .toString()
    .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function unformatMoney(str) {
  // bỏ hết ký tự không phải digit => "3040520"
  return str.replace(/[^\d]/g, "");
}

document.addEventListener('DOMContentLoaded', function(){
  // 1) Lấy totalSalary (dưới dạng text "3.040.520", v.v.)
  var tsText = document.getElementById('totalSalary').textContent;
  // gỡ bỏ dấu chấm => "3040520"
  tsText = unformatMoney(tsText);
  // parseFloat => 3040520
  var totalSalary = parseFloat(tsText) || 0;

  // 2) Lấy 2 ô input
  var bonusEl = document.getElementById('bonusInput');
  var finalEl = document.getElementById('finalInput');

  // 3) Khởi tạo Final = formatMoney(totalSalary)
  finalEl.value = formatMoney(totalSalary);

  // 4) Sự kiện khi người dùng gõ vào ô "Thưởng"
  bonusEl.addEventListener('input', function(){
    // Lấy giá trị hiện tại => bỏ dấu chấm => parse số
    var bonusRaw = unformatMoney(bonusEl.value);
    var bonusVal = parseFloat(bonusRaw) || 0;

    var total = totalSalary + bonusVal; 
    // Hiển thị "Tổng cộng lương" có dấu chấm
    finalEl.value = formatMoney(total);
  });

  // 5) Sự kiện khi người dùng gõ vào ô "Tổng cộng lương"
  finalEl.addEventListener('input', function(){
    var finalRaw = unformatMoney(finalEl.value);
    var finalVal = parseFloat(finalRaw) || 0;

    // Tính thưởng = finalVal - totalSalary
    var bonusVal = finalVal - totalSalary;
    // Format hiển thị
    bonusEl.value = formatMoney(bonusVal);
  });
});
</script>


</body>
</html>
