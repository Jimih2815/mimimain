<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$laravelAdmin = false;
if (function_exists('session')) {
    try {
        $laravelAdmin = session('is_admin') === true;
    } catch (\Throwable $e) {
        $laravelAdmin = false;
    }
}

$legacyAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if ($laravelAdmin && !$legacyAdmin) {
    $_SESSION['admin_logged_in'] = true;
    $legacyAdmin = true;
}

if (!$laravelAdmin && !$legacyAdmin) {
    header('Location: /admin/login');
    exit;
}
