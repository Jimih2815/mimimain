<?php
// bootstrap.php
// 1) Load Composer autoload
require __DIR__ . '/vendor/autoload.php';

// 2) Khởi tạo và load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// (Tuỳ chọn) Kiểm tra đã load chưa
// var_dump($_ENV['DB_HOST'], $_ENV['DB_USER']);
