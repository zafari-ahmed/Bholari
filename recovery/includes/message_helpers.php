<?php

function recovery_fetch_team_messages($limit = 100)
{
    $pdo = recovery_db();
    $stmt = $pdo->prepare(
        'SELECT m.*, c.name AS customer_name, p.block_number, p.plot_number, p.plot_type
         FROM recovery_team_messages m
         LEFT JOIN customer_plots cp ON cp.id = m.booking_id
         LEFT JOIN customers c ON c.id = cp.customer_id
         LEFT JOIN plots p ON p.id = cp.plot_id
         ORDER BY m.created_at DESC
         LIMIT ' . (int)$limit
    );
    $stmt->execute();
    $rows = array_reverse($stmt->fetchAll());
    foreach ($rows as &$row) {
        $row['sender_name'] = recovery_message_sender_name($row);
        $row['display_message'] = recovery_message_display_text($row);
    }
    unset($row);
    return $rows;
}

function recovery_fetch_booking_messages($bookingId)
{
    $pdo = recovery_db();
    $stmt = $pdo->prepare(
        'SELECT m.* FROM recovery_team_messages m
         WHERE m.booking_id = :booking_id
         ORDER BY m.created_at DESC'
    );
    $stmt->execute(array(':booking_id' => $bookingId));
    $rows = $stmt->fetchAll();
    foreach ($rows as &$row) {
        $row['sender_name'] = recovery_message_sender_name($row);
        $row['display_message'] = recovery_message_display_text($row);
    }
    unset($row);
    return $rows;
}

function recovery_fetch_approved_booking_messages($bookingId)
{
    $pdo = recovery_db();
    $stmt = $pdo->prepare(
        "SELECT m.* FROM recovery_team_messages m
         WHERE m.booking_id = :booking_id AND m.status = 'approved'
         ORDER BY m.approved_at DESC, m.created_at DESC"
    );
    $stmt->execute(array(':booking_id' => $bookingId));
    $rows = $stmt->fetchAll();
    foreach ($rows as &$row) {
        $row['display_message'] = recovery_message_display_text($row);
    }
    unset($row);
    return $rows;
}

function recovery_message_sender_name($row)
{
    if ($row['sender_type'] === 'manager') {
        $pdo = recovery_db();
        $stmt = $pdo->prepare('SELECT first_name, last_name FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(array(':id' => $row['sender_id']));
        $user = $stmt->fetch();
        return $user ? trim($user['first_name'] . ' ' . $user['last_name']) . ' (Manager)' : 'Manager';
    }

    $pdo = recovery_db();
    $stmt = $pdo->prepare('SELECT first_name, last_name FROM recovery_staff WHERE id = :id LIMIT 1');
    $stmt->execute(array(':id' => $row['sender_id']));
    $staff = $stmt->fetch();
    return $staff ? trim($staff['first_name'] . ' ' . $staff['last_name']) . ' (Staff)' : 'Staff';
}

function recovery_message_display_text($row)
{
    if (!empty($row['yii_edited_message'])) {
        return $row['yii_edited_message'];
    }
    if (!empty($row['approved_message'])) {
        return $row['approved_message'];
    }
    return $row['message'];
}

function recovery_post_message($message, $bookingId = null)
{
    $user = recovery_user();
    $pdo = recovery_db();
    $status = ($bookingId && $user['type'] === 'staff') ? 'pending' : 'approved';
    $approvedMessage = ($status === 'approved') ? $message : null;
    $approvedBy = ($status === 'approved' && $user['type'] === 'manager') ? (int)$user['user_id'] : null;
    $approvedAt = ($status === 'approved') ? date('Y-m-d H:i:s') : null;

    $stmt = $pdo->prepare(
        'INSERT INTO recovery_team_messages
         (sender_type, sender_id, booking_id, message, approved_message, status, approved_by_manager_id, approved_at, created_at)
         VALUES (:sender_type, :sender_id, :booking_id, :message, :approved_message, :status, :approved_by, :approved_at, :created_at)'
    );
    $stmt->execute(array(
        ':sender_type' => $user['type'],
        ':sender_id' => $user['type'] === 'manager' ? (int)$user['user_id'] : (int)$user['staff_id'],
        ':booking_id' => $bookingId ? (int)$bookingId : null,
        ':message' => $message,
        ':approved_message' => $approvedMessage,
        ':status' => $status,
        ':approved_by' => $approvedBy,
        ':approved_at' => $approvedAt,
        ':created_at' => date('Y-m-d H:i:s'),
    ));
    return (int)$pdo->lastInsertId();
}

function recovery_approve_message($messageId, $approvedText)
{
    $user = recovery_user();
    $pdo = recovery_db();
    $stmt = $pdo->prepare(
        "UPDATE recovery_team_messages
         SET status = 'approved', approved_message = :approved_message,
             approved_by_manager_id = :manager_id, approved_at = :approved_at
         WHERE id = :id AND status = 'pending'"
    );
    $stmt->execute(array(
        ':approved_message' => $approvedText,
        ':manager_id' => (int)$user['user_id'],
        ':approved_at' => date('Y-m-d H:i:s'),
        ':id' => (int)$messageId,
    ));
    return $stmt->rowCount() > 0;
}

function recovery_reject_message($messageId)
{
    $pdo = recovery_db();
    $stmt = $pdo->prepare(
        "UPDATE recovery_team_messages SET status = 'rejected' WHERE id = :id AND status = 'pending'"
    );
    $stmt->execute(array(':id' => (int)$messageId));
    return $stmt->rowCount() > 0;
}
