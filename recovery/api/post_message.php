<?php

require_once dirname(__DIR__) . '/bootstrap.php';
recovery_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !recovery_verify_csrf(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
    recovery_flash('error', 'Invalid request.');
    recovery_redirect('chat');
}

$message = trim(isset($_POST['message']) ? $_POST['message'] : '');
$bookingId = !empty($_POST['booking_id']) ? (int)$_POST['booking_id'] : null;

if ($message === '') {
    recovery_flash('error', 'Message is required.');
    recovery_redirect('chat');
}

if ($bookingId && !recovery_can_access_booking($bookingId)) {
    recovery_flash('error', 'Booking access denied.');
    recovery_redirect('chat');
}

recovery_post_message($message, $bookingId);
recovery_flash('success', 'Message posted.');

if ($bookingId) {
    recovery_redirect('booking', array('id' => $bookingId));
}
recovery_redirect('chat');
