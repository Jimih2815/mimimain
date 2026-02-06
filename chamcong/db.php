<?php

declare(strict_types=1);

if (!class_exists(\Dotenv\Dotenv::class)) {
    require __DIR__ . '/../vendor/autoload.php';
}

if (class_exists(\Dotenv\Dotenv::class)) {
    \Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();
}

$host = $_ENV['DB_CHAMCONG_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_CHAMCONG_PORT'] ?? '3306';
$db = $_ENV['DB_CHAMCONG_DATABASE'] ?? 'zutfakaz_mimi_chamcong';
$user = $_ENV['DB_CHAMCONG_USERNAME'] ?? 'root';
$pass = $_ENV['DB_CHAMCONG_PASSWORD'] ?? '';
$charset = $_ENV['DB_CHAMCONG_CHARSET'] ?? 'utf8mb4';

try {
    $conn = new PDO(
        "mysql:host={$host};port={$port};dbname={$db};charset={$charset}",
        $user,
        $pass
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Khong the ket noi CSDL cham cong: ' . $e->getMessage());
}

