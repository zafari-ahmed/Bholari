<?php

require_once dirname(__DIR__) . '/bootstrap.php';
recovery_require_manager();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !recovery_verify_csrf(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
    recovery_flash('error', 'Invalid request.');
    recovery_redirect('chat');
}

$messageId = isset($_POST['message_id']) ? (int)$_POST['message_id'] : 0;
$approvedText = trim(isset($_POST['approved_message']) ? $_POST['approved_message'] : '');

if (!$messageId || $approvedText === '') {
    recovery_flash('error', 'Approval text is required.');
    recovery_redirect('chat');
}

if (recovery_approve_message($messageId, $approvedText)) {
    recovery_flash('success', 'Message approved.');
} else {
    recovery_flash('error', 'Unable to approve message.');
}

$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : recovery_base_url('index.php?page=chat');
header('Location: ' . $redirect);
exit;
