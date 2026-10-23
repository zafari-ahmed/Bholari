<?php

require_once dirname(__DIR__) . '/bootstrap.php';
recovery_require_manager();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !recovery_verify_csrf(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
    recovery_flash('error', 'Invalid request.');
    recovery_redirect('staff');
}

$staffId = isset($_POST['staff_id']) ? (int)$_POST['staff_id'] : 0;
if ($staffId) {
    $pdo = recovery_db();
    $pdo->prepare(
        'UPDATE recovery_staff SET status = IF(status = 1, 0, 1) WHERE id = :id'
    )->execute(array(':id' => $staffId));
    recovery_flash('success', 'Staff status updated.');
}

recovery_redirect('staff');
