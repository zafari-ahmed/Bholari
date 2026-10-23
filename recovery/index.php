<?php

require_once __DIR__ . '/bootstrap.php';

$page = isset($_GET['page']) ? preg_replace('/[^a-z_]/', '', $_GET['page']) : 'dashboard';
$allowed = array('login', 'dashboard', 'bookings', 'booking', 'staff', 'chat', 'reports');

if (!in_array($page, $allowed, true)) {
    $page = 'dashboard';
}

if ($page !== 'login') {
    recovery_require_login();
}

if (in_array($page, array('staff', 'reports'), true)) {
    recovery_require_manager();
}

$pageFile = __DIR__ . '/pages/' . $page . '.php';
if (!file_exists($pageFile)) {
    http_response_code(404);
    echo 'Page not found';
    exit;
}

require $pageFile;
