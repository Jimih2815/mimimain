<?php
declare(strict_types=1);

// 1) Bật hiển thị lỗi
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// 2) Load Composer autoload (để Dotenv, PhpSpreadsheet…)
require __DIR__ . '/../vendor/autoload.php';

// 3) Load bootstrap (Dotenv, config chung…)
require __DIR__ . '/../bootstrap.php';

// 4) Kiểm tra xác thực
require $_SERVER['DOCUMENT_ROOT'] . '/auth.php';
// NẾU LỖI THỬ CMT DÒNG TRÊN BỎ CMT DÒNG DƯỚI
// require __DIR__ . '/../../auth.php'; 

// 5) Kết nối DB
$db = require __DIR__ . '/../config/db.php';


// 7) Khởi tạo Controller và dispatch action
//    (dùng fully-qualified name vì không dùng PSR‑4 autoload)
$action = $_GET['action'] ?? 'home';
$allowed = [
    'home', 'uploadForm', 'processUpload', 'saveHome',
    'costCalcForm', 'processCostCalc', 'addChildAjax',
    'deleteChildAjax', 'saveMonthlyData', 'viewMonthlyHistory',
    'viewMonthlyData','manualOrders', 'createOrder', 'ajaxSearchSku',
    'manualOrders','createOrder','ajaxSearchSku',
    'deleteOrder','updateOrder', 'saveSkuAjax', 'deleteSkuAjax'
];

// Khởi tạo controller
$ctrl = new \App\Controllers\SkuController($db);

// Gọi method tương ứng
if (in_array($action, $allowed, true)) {
    $ctrl->{$action}();
} else {
    http_response_code(400);
    echo "Unknown action.";
}
