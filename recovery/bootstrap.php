<?php

if (session_status() === PHP_SESSION_NONE) {
    session_name('recovery_session');
    session_start();
}

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/booking_helpers.php';
require_once __DIR__ . '/includes/call_helpers.php';
require_once __DIR__ . '/includes/message_helpers.php';
require_once __DIR__ . '/includes/layout.php';
