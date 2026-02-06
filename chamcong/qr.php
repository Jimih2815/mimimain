<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'db.php';

// (A) Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Lấy user_id
$user_id = $_SESSION['user_id'];

// (A1) Xem user có ignore_location = 1 không
$stmtUser = $conn->prepare("SELECT ignore_location FROM users WHERE id = :uid");
$stmtUser->execute(['uid' => $user_id]);
$userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);
$ignoreLocation = (int)$userRow['ignore_location'];  // 0 hoặc 1

// (B) Nếu ignore_location = 0 => Hiển thị form GPS lấy lat/lng
//     Nếu =1 => bỏ qua GPS
if ($ignoreLocation === 0) {
    // Kiểm tra có POST lat,lng chưa?
    if (!isset($_POST['lat']) || !isset($_POST['lng'])) {
        // CHƯA => in HTML + JS geolocation
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>MiMi-Chấm công</title>
            <link rel="stylesheet" href="qr.css">
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
            
        </head>
        <body>
            <h3>Đang xác định vị trí của bạn...</h3>
            <p>Vui lòng cho phép truy cập vị trí (GPS) để chấm công.</p>
            <script>
            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition(
                    function success(pos) {
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        // Gửi lat,lng lên server (POST)
                        const frm = document.createElement('form');
                        frm.method = 'POST';
                        frm.action = ''; // reload chính trang này

                        frm.innerHTML = `
                            <input type="hidden" name="lat" value="${lat}">
                            <input type="hidden" name="lng" value="${lng}">
                        `;
                        document.body.appendChild(frm);
                        frm.submit();
                    },
                    function error(err) {
                        alert("Bạn đã từ chối hoặc không lấy được vị trí.\nKhông thể check-in/out!");
                        window.location.href = 'dashboard.php';
                    },
                    { enableHighAccuracy:true, timeout:15000, maximumAge:0 }
                );
            } else {
                alert("Trình duyệt không hỗ trợ geolocation!");
                window.location.href = 'dashboard.php';
            }
            </script>
        </body>
        </html>
        <?php
        exit; 
    }

    // ĐÃ CÓ lat,lng => Kiểm tra bán kính 
    function distanceInKm($lat1,$lng1,$lat2,$lng2){
        $R=6371;
        $dLat=deg2rad($lat2-$lat1);
        $dLng=deg2rad($lng2-$lng1);
        $a=sin($dLat/2)*sin($dLat/2)+cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLng/2)*sin($dLng/2);
        $c=2*atan2(sqrt($a),sqrt(1-$a));
        return $R*$c;
    }

    // toạ độ công ty, định vị, vị trí
    $officeLat = 21.028320;
    $officeLng = 105.742490;
    $radiusKm  = 1;

    $userLat = floatval($_POST['lat']);
    $userLng = floatval($_POST['lng']);

    $dist = distanceInKm($officeLat,$officeLng,$userLat,$userLng);
    if($dist>$radiusKm){
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>
        <title>Lỗi Chấm Công</title>
        <link rel='stylesheet' href='qr.css'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        </head><body>
        <div class='error-message'>
        <h2>Không thể chấm công!</h2>
        <p>Bạn đang không ở văn phòng!</p>
        <a href='dashboard.php' class='back-btn'>Quay lại Trang Chính</a>
        </div></body></html>";
        exit;
    }
}else{
    // ignore_location=1 => bỏ qua GPS
    $userLat=0;
    $userLng=0;
}

// (C) Kiểm tra hôm nay đã checkin chưa?
$dateToday = date('Y-m-d');
$clientIp  = $_SERVER['REMOTE_ADDR'];

$stmt = $conn->prepare("
    SELECT * 
    FROM attendance
    WHERE user_id=:uid
      AND DATE(check_in)=:today
      AND check_out IS NULL
    LIMIT 1
");
$stmt->execute([
    'uid'=>$user_id,
    'today'=>$dateToday
]);
$attendance = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$attendance){
    // (C1) CHƯA CHECK-IN => checkin
    $checkInTime = date('Y-m-d H:i:s');
    $stmtInsert = $conn->prepare("
        INSERT INTO attendance (user_id, check_in, ip_in, lat_in, lng_in)
        VALUES (:uid, :cin, :ipIn, :latIn, :lngIn)
    ");
    $stmtInsert->execute([
        'uid'=>$user_id,
        'cin'=>$checkInTime,
        'ipIn'=>$clientIp,
        'latIn'=>$userLat,
        'lngIn'=>$userLng
    ]);
    $_SESSION['msg']="Check-in thành công lúc $checkInTime";
    header("Location: dashboard.php");
    exit;
}else{
    // (C2) ĐÃ CHECK-IN => checkout
    $checkOutTime = date('Y-m-d H:i:s');
    $stmtUpdate = $conn->prepare("
        UPDATE attendance
        SET check_out=:cout,
            ip_out=:ipOut,
            lat_out=:latOut,
            lng_out=:lngOut
        WHERE id=:aid
    ");
    $stmtUpdate->execute([
        'cout'=>$checkOutTime,
        'ipOut'=>$clientIp,
        'latOut'=>$userLat,
        'lngOut'=>$userLng,
        'aid'=>$attendance['id']
    ]);
    $_SESSION['msg']="Check-out thành công lúc $checkOutTime";
    header("Location: dashboard.php");
    exit;
}
