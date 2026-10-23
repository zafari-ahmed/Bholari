<?php

require_once dirname(__DIR__) . '/bootstrap.php';
recovery_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !recovery_verify_csrf(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
    recovery_flash('error', 'Invalid request.');
    recovery_redirect('bookings');
}

$bookingId = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
if (!$bookingId || !recovery_can_access_booking($bookingId)) {
    recovery_flash('error', 'Booking access denied.');
    recovery_redirect('bookings');
}

try {
    $data = array(
        'booking_id' => $bookingId,
        'call_status' => (int)$_POST['call_status'],
        'booking_status' => (int)$_POST['booking_status'],
        'remarks' => trim($_POST['remarks']),
        'next_reminder' => trim($_POST['next_reminder']),
    );
    $data['call_audio_file'] = recovery_handle_audio_upload('callFile');
    recovery_save_call($data);
    recovery_flash('success', 'Call logged successfully.');
} catch (Exception $e) {
    recovery_flash('error', $e->getMessage());
}

recovery_redirect('booking', array('id' => $bookingId));
