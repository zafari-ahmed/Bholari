<?php

require_once dirname(__DIR__) . '/bootstrap.php';
recovery_require_manager();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !recovery_verify_csrf(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
    recovery_flash('error', 'Invalid request.');
    recovery_redirect('chat');
}

$messageId = isset($_POST['message_id']) ? (int)$_POST['message_id'] : 0;
if (!$messageId) {
    recovery_flash('error', 'Invalid message.');
    recovery_redirect('chat');
}

if (recovery_reject_message($messageId)) {
    recovery_flash('success', 'Message rejected.');
} else {
    recovery_flash('error', 'Unable to reject message.');
}

$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : recovery_base_url('index.php?page=chat');
header('Location: ' . $redirect);
exit;
