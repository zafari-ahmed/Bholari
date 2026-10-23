<?php

function recovery_fetch_call_history($bookingId)
{
    $pdo = recovery_db();
    $stmt = $pdo->prepare(
        'SELECT bch.*,
                u.first_name AS user_first_name,
                rs.first_name AS staff_first_name, rs.last_name AS staff_last_name
         FROM booking_call_history bch
         LEFT JOIN users u ON u.id = bch.user_id
         LEFT JOIN recovery_staff rs ON rs.id = bch.recovery_staff_id
         WHERE bch.booking_id = :booking_id
         ORDER BY bch.id DESC'
    );
    $stmt->execute(array(':booking_id' => $bookingId));
    return $stmt->fetchAll();
}

function recovery_save_call($data)
{
    $pdo = recovery_db();
    $user = recovery_user();
    $config = recovery_config();

    $stmt = $pdo->prepare(
        'INSERT INTO booking_call_history
         (booking_id, created_at, user_id, recovery_staff_id, source, call_details, remarks,
          booking_status, call_status, next_reminder, call_audio_file)
         VALUES
         (:booking_id, :created_at, :user_id, :recovery_staff_id, :source, :call_details, :remarks,
          :booking_status, :call_status, :next_reminder, :call_audio_file)'
    );

    $userId = null;
    $staffId = null;
    if ($user['type'] === 'manager') {
        $userId = (int)$user['user_id'];
    } else {
        $staffId = (int)$user['staff_id'];
    }

    $stmt->execute(array(
        ':booking_id' => (int)$data['booking_id'],
        ':created_at' => date('Y-m-d H:i:s'),
        ':user_id' => $userId,
        ':recovery_staff_id' => $staffId,
        ':source' => 'recovery',
        ':call_details' => $data['remarks'],
        ':remarks' => $data['remarks'],
        ':booking_status' => (int)$data['booking_status'],
        ':call_status' => (int)$data['call_status'],
        ':next_reminder' => $data['next_reminder'],
        ':call_audio_file' => isset($data['call_audio_file']) ? $data['call_audio_file'] : '',
    ));

    return (int)$pdo->lastInsertId();
}

function recovery_call_caller_name($row)
{
    if (!empty($row['recovery_staff_id'])) {
        return trim($row['staff_first_name'] . ' ' . $row['staff_last_name']) . ' (Recovery Staff)';
    }
    if (!empty($row['user_first_name'])) {
        return $row['user_first_name'] . ' (Manager)';
    }
    return 'System';
}

function recovery_handle_audio_upload($fieldName)
{
    if (empty($_FILES[$fieldName]['name'])) {
        return '';
    }

    $config = recovery_config();
    $allowed = array('mp3', 'wav', 'm4a', 'ogg', 'webm');
    $ext = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        throw new Exception('Invalid audio file type.');
    }
    if ($_FILES[$fieldName]['size'] > 10 * 1024 * 1024) {
        throw new Exception('Audio file is too large.');
    }

    if (!is_dir($config['upload_dir'])) {
        mkdir($config['upload_dir'], 0775, true);
    }

    $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $_FILES[$fieldName]['name']);
    if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], $config['upload_dir'] . $fileName)) {
        throw new Exception('Failed to upload audio file.');
    }
    return $fileName;
}

function recovery_fetch_activity_audit($date = null)
{
    $pdo = recovery_db();
    $params = array();
    $sql = 'SELECT bch.*, c.name AS customer_name, p.block_number, p.plot_number, p.plot_type,
                   rs.first_name AS staff_first_name, rs.last_name AS staff_last_name,
                   u.first_name AS manager_first_name
            FROM booking_call_history bch
            INNER JOIN customer_plots cp ON cp.id = bch.booking_id
            INNER JOIN customers c ON c.id = cp.customer_id
            INNER JOIN plots p ON p.id = cp.plot_id
            LEFT JOIN recovery_staff rs ON rs.id = bch.recovery_staff_id
            LEFT JOIN users u ON u.id = bch.user_id
            WHERE bch.source = "recovery"';
    if ($date) {
        $sql .= ' AND DATE(bch.created_at) = :date';
        $params[':date'] = $date;
    }
    $sql .= ' ORDER BY bch.created_at DESC LIMIT 500';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
