<?php
declare(strict_types=1);

// 1) Autoload Composer (để load phpdotenv)
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// 2) Load .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// 3) Kiểm tra biến môi trường bắt buộc
foreach (['DB_HOST','DB_NAME','DB_USER','DB_PASS'] as $v) {
    if (empty($_ENV[$v])) {
        throw new RuntimeException("Missing env var: {$v}");
    }
}

// 4) Lấy config DB
$host    = $_ENV['DB_HOST'];
$dbname  = $_ENV['DB_NAME'];
$user    = $_ENV['DB_USER'];
$pass    = $_ENV['DB_PASS'];
// đảm bảo bạn có đặt trong .env: DB_CHARSET=utf8mb4
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
// nếu cần custom port: DB_PORT=3306
$port    = $_ENV['DB_PORT']    ?? '3306';

// 5) Xây DSN
$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

// 6) Tuỳ chọn PDO
$options = [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,
];

// 7) Kết nối
try {
    $db = new \PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Lỗi charset “lỗi” thường do $charset không hợp lệ
    die('DB Connection failed: ' . $e->getMessage());
}

// 8) Trả về PDO để bạn include/require vào chỗ cần
return $db;
