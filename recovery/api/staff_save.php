<?php

require_once dirname(__DIR__) . '/bootstrap.php';
recovery_require_manager();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !recovery_verify_csrf(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
    recovery_flash('error', 'Invalid request.');
    recovery_redirect('staff');
}

$pdo = recovery_db();
$managerId = (int)recovery_user()['user_id'];
$staffId = isset($_POST['staff_id']) ? (int)$_POST['staff_id'] : 0;
$username = trim(isset($_POST['username']) ? $_POST['username'] : '');
$firstName = trim(isset($_POST['first_name']) ? $_POST['first_name'] : '');
$lastName = trim(isset($_POST['last_name']) ? $_POST['last_name'] : '');
$password = isset($_POST['password']) ? $_POST['password'] : '';
$blocks = isset($_POST['blocks']) ? (array)$_POST['blocks'] : array();
$config = recovery_config();

if ($username === '' || $firstName === '' || $lastName === '' || empty($blocks)) {
    recovery_flash('error', 'Please fill all required fields and assign at least one block.');
    recovery_redirect('staff');
}

try {
    $pdo->beginTransaction();

    if ($staffId) {
        $stmt = $pdo->prepare(
            'UPDATE recovery_staff SET first_name = :first_name, last_name = :last_name WHERE id = :id'
        );
        $stmt->execute(array(
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':id' => $staffId,
        ));
        if ($password !== '') {
            $stmt = $pdo->prepare('UPDATE recovery_staff SET password = :password WHERE id = :id');
            $stmt->execute(array(':password' => md5($password), ':id' => $staffId));
        }
    } else {
        if ($password === '') {
            throw new Exception('Password is required for new staff.');
        }
        $stmt = $pdo->prepare(
            'INSERT INTO recovery_staff (username, password, first_name, last_name, status, created_by, created_at)
             VALUES (:username, :password, :first_name, :last_name, 1, :created_by, :created_at)'
        );
        $stmt->execute(array(
            ':username' => $username,
            ':password' => md5($password),
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':created_by' => $managerId,
            ':created_at' => date('Y-m-d H:i:s'),
        ));
        $staffId = (int)$pdo->lastInsertId();
    }

    $pdo->prepare('DELETE FROM recovery_staff_blocks WHERE staff_id = :staff_id')
        ->execute(array(':staff_id' => $staffId));

    $insertBlock = $pdo->prepare(
        'INSERT INTO recovery_staff_blocks (staff_id, block_number, phase_id) VALUES (:staff_id, :block_number, :phase_id)'
    );
    foreach ($blocks as $block) {
        $insertBlock->execute(array(
            ':staff_id' => $staffId,
            ':block_number' => trim($block),
            ':phase_id' => $config['default_phase_id'],
        ));
    }

    $pdo->commit();
    recovery_flash('success', 'Staff saved successfully.');
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    recovery_flash('error', $e->getMessage());
}

recovery_redirect('staff');
