<?php
// public/webhook/facebook.php
declare(strict_types=1);

// 1) Xác thực webhook (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $mode      = $_GET['hub_mode']        ?? '';
    $token     = $_GET['hub_verify_token']?? '';
    $challenge = $_GET['hub_challenge']   ?? '';

    // Đúng verify token, trả về hub.challenge
    if ($mode === 'subscribe' && $token === 'my_verify_token_123') {
        echo $challenge;
        exit;
    }
    // Sai token
    http_response_code(403);
    exit;
}

// 2) Xử lý POST (tin nhắn, postback…)
// (phần này bạn sẽ bổ sung sau)
http_response_code(200);
echo 'OK';
