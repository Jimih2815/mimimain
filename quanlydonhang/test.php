<?php
// Bật hiển thị lỗi để debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Debug Thông tin:</h2>";

// Hiển thị giá trị __DIR__
echo "<strong>Current __DIR__:</strong> " . __DIR__ . "<br>";

// Xác định đường dẫn cần include
// Giả sử file bạn cần include là manual_orders.php nằm trong thư mục Views, nằm 1 cấp trên so với file test.php
$relativePath = '/../Views/manual_orders.php';
$targetPath = __DIR__ . $relativePath;

echo "<strong>Relative path cần include:</strong> " . htmlspecialchars($relativePath) . "<br>";
echo "<strong>Computed target path:</strong> " . htmlspecialchars($targetPath) . "<br>";

// Sử dụng realpath để lấy đường dẫn tuyệt đối
$realTarget = realpath($targetPath);
if ($realTarget === false) {
    echo "<strong>realpath:</strong> Không tìm thấy file tại đường dẫn: " . htmlspecialchars($targetPath) . "<br>";
} else {
    echo "<strong>realpath:</strong> " . $realTarget . "<br>";
}

// Kiểm tra sự tồn tại của file
if (file_exists($targetPath)) {
    echo "<strong>File tồn tại:</strong> " . htmlspecialchars($targetPath) . "<br>";
} else {
    echo "<strong>File không tồn tại:</strong> " . htmlspecialchars($targetPath) . "<br>";
}

// In ra giá trị open_basedir nếu có
$openBasedir = ini_get('open_basedir');
echo "<strong>open_basedir:</strong> " . ($openBasedir ? $openBasedir : "Không có open_basedir") . "<br>";

// Nếu file tồn tại, thử include nó
if (file_exists($targetPath)) {
    echo "<br>Đang include file...<br>";
    include $targetPath;
} else {
    echo "<br>Không thể include file vì không tồn tại.<br>";
}
?>
