<?php

function recovery_booking_base_sql($blocks)
{
    $sql = 'SELECT cp.id AS booking_id, cp.createdOn, cp.status, cp.blocked,
                   c.name AS customer_name, c.mobile,
                   p.block_number, p.plot_type, p.plot_number, p.total, p.discount,
                   p.is_corner, p.is_corner_amount, p.is_road_facing, p.is_road_facing_amount,
                   p.is_park_facing, p.is_park_facing_amount, p.is_west_open, p.is_west_open_amount,
                   (SELECT MAX(bch.created_at) FROM booking_call_history bch WHERE bch.booking_id = cp.id) AS last_call_at,
                   (SELECT bch.next_reminder FROM booking_call_history bch WHERE bch.booking_id = cp.id ORDER BY bch.id DESC LIMIT 1) AS next_reminder,
                   (SELECT bch.call_status FROM booking_call_history bch WHERE bch.booking_id = cp.id ORDER BY bch.id DESC LIMIT 1) AS last_call_status
            FROM customer_plots cp
            INNER JOIN customers c ON c.id = cp.customer_id
            INNER JOIN plots p ON p.id = cp.plot_id
            WHERE cp.status = 1 AND cp.blocked = 0';
    return $sql;
}

function recovery_fetch_bookings($blocks, $filters = array())
{
    if (empty($blocks)) {
        return array();
    }

    $pdo = recovery_db();
    $sql = recovery_booking_base_sql($blocks);
    $params = array();

    $placeholders = array();
    foreach ($blocks as $i => $block) {
        $key = ':block' . $i;
        $placeholders[] = $key;
        $params[$key] = $block;
    }
    $sql .= ' AND p.block_number IN (' . implode(',', $placeholders) . ')';

    if (!empty($filters['block'])) {
        $sql .= ' AND p.block_number = :filter_block';
        $params[':filter_block'] = $filters['block'];
    }

    if (!empty($filters['search'])) {
        $sql .= ' AND (c.name LIKE :search OR c.mobile LIKE :search OR p.plot_number LIKE :search OR CAST(cp.id AS CHAR) LIKE :search)';
        $params[':search'] = '%' . $filters['search'] . '%';
    }

    $sql .= ' ORDER BY cp.id DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    foreach ($rows as &$row) {
        $dues = recovery_calculate_dues((int)$row['booking_id']);
        $row['due_amount'] = $dues['due_amount'];
        $row['due_months'] = $dues['due_months'];
        $row['total_paid'] = $dues['total_paid'];
        $row['plot_label'] = $row['block_number'] . ' / ' . $row['plot_type'] . ' / ' . $row['plot_number'];
    }
    unset($row);

    if (!empty($filters['due_only'])) {
        $rows = array_values(array_filter($rows, function ($row) {
            return $row['due_amount'] > 0;
        }));
    }

    return $rows;
}

function recovery_fetch_booking($bookingId)
{
    $pdo = recovery_db();
    $stmt = $pdo->prepare(
        'SELECT cp.*, c.name AS customer_name, c.mobile, c.cnic, c.address,
                p.block_number, p.plot_type, p.plot_number, p.total, p.discount,
                p.is_corner, p.is_corner_amount, p.is_road_facing, p.is_road_facing_amount,
                p.is_park_facing, p.is_park_facing_amount, p.is_west_open, p.is_west_open_amount
         FROM customer_plots cp
         INNER JOIN customers c ON c.id = cp.customer_id
         INNER JOIN plots p ON p.id = cp.plot_id
         WHERE cp.id = :id LIMIT 1'
    );
    $stmt->execute(array(':id' => $bookingId));
    $row = $stmt->fetch();
    if ($row) {
        $row['plot_label'] = $row['block_number'] . ' / ' . $row['plot_type'] . ' / ' . $row['plot_number'];
    }
    return $row;
}

function recovery_resolve_booking_total($booking)
{
    $baseTotal = (float)$booking['total'] - (float)$booking['discount'];
    $extraTotal = 0;
    $plotTotal = (float)$booking['total'];

    if ((int)$booking['is_corner'] === 1) {
        $extraTotal += ((float)$booking['is_corner_amount'] / 100) * $plotTotal;
    }
    if ((int)$booking['is_road_facing'] === 1) {
        $extraTotal += ((float)$booking['is_road_facing_amount'] / 100) * $plotTotal;
    }
    if ((int)$booking['is_park_facing'] === 1) {
        $extraTotal += ((float)$booking['is_park_facing_amount'] / 100) * $plotTotal;
    }
    if ((int)$booking['is_west_open'] === 1) {
        $extraTotal += ((float)$booking['is_west_open_amount'] / 100) * $plotTotal;
    }

    return $baseTotal + $extraTotal;
}

function recovery_calculate_dues($bookingId)
{
    $result = array(
        'booking_id' => (int)$bookingId,
        'monthly_amount' => 0,
        'elapsed_months' => 0,
        'months_paid' => 0,
        'due_months' => 0,
        'expected_amount' => 0,
        'total_paid' => 0,
        'booking_total' => 0,
        'remaining_balance' => 0,
        'due_amount' => 0,
        'start_date' => null,
        'status' => 'ok',
        'message' => '',
    );

    $booking = recovery_fetch_booking($bookingId);
    if (!$booking) {
        $result['status'] = 'error';
        $result['message'] = 'Booking not found';
        return $result;
    }

    $pdo = recovery_db();
    $stmt = $pdo->prepare(
        'SELECT amount, createdOn FROM customer_plot_transactions
         WHERE plot_id = :plot_id AND status = 1 ORDER BY createdOn ASC, id ASC'
    );
    $stmt->execute(array(':plot_id' => $bookingId));
    $transactions = $stmt->fetchAll();

    if (empty($transactions)) {
        $result['status'] = 'error';
        $result['message'] = 'No active transactions found';
        return $result;
    }

    $stmtExtra = $pdo->prepare(
        'SELECT amount FROM customer_plot_extra_transactions
         WHERE plot_id = :plot_id AND status = 1 ORDER BY createdOn ASC, id ASC'
    );
    $stmtExtra->execute(array(':plot_id' => $bookingId));
    $transactionsExtra = $stmtExtra->fetchAll();

    $monthlyAmount = (float)$transactions[0]['amount'];
    if ($monthlyAmount <= 0) {
        $result['status'] = 'error';
        $result['message'] = 'First transaction amount is invalid';
        return $result;
    }

    $totalPaid = 0;
    foreach ($transactions as $txn) {
        $totalPaid += (float)$txn['amount'];
    }
    foreach ($transactionsExtra as $txn) {
        $totalPaid += (float)$txn['amount'];
    }

    $startDateRaw = $booking['createdOn'];
    $start = new DateTime(date('Y-m-01', strtotime($startDateRaw)));
    $now = new DateTime(date('Y-m-01'));
    $diff = $start->diff($now);
    $elapsedMonths = ($diff->invert) ? 0 : (($diff->y * 12) + $diff->m + 1);

    $expectedAmount = $elapsedMonths * $monthlyAmount;
    $bookingTotal = recovery_resolve_booking_total($booking);
    $remainingBalance = max(0, $bookingTotal - $totalPaid);
    $rawDue = max(0, $expectedAmount - $totalPaid);
    $dueAmount = min($rawDue, $remainingBalance);
    $dueMonths = ($monthlyAmount > 0) ? round($dueAmount / $monthlyAmount, 2) : 0;

    $result['monthly_amount'] = round($monthlyAmount, 2);
    $result['elapsed_months'] = (int)$elapsedMonths;
    $result['months_paid'] = round($totalPaid / $monthlyAmount, 2);
    $result['due_months'] = $dueMonths;
    $result['expected_amount'] = round($expectedAmount, 2);
    $result['total_paid'] = round($totalPaid, 2);
    $result['booking_total'] = round($bookingTotal, 2);
    $result['remaining_balance'] = round($remainingBalance, 2);
    $result['due_amount'] = round($dueAmount, 2);
    $result['start_date'] = date('Y-m-d', strtotime($startDateRaw));
    $result['message'] = ($dueAmount > 0) ? $dueMonths . ' month(s) due' : 'No dues';

    return $result;
}

function recovery_fetch_transactions($bookingId, $limit = 20)
{
    $pdo = recovery_db();
    $stmt = $pdo->prepare(
        'SELECT "main" AS txn_type, amount, createdOn, transaction_number, comment, plot_payment_mode_id
         FROM customer_plot_transactions WHERE plot_id = :plot_id AND status = 1
         UNION ALL
         SELECT "extra" AS txn_type, amount, createdOn, transaction_number, comment, plot_payment_mode AS plot_payment_mode_id
         FROM customer_plot_extra_transactions WHERE plot_id = :plot_id2 AND status = 1
         ORDER BY createdOn DESC
         LIMIT ' . (int)$limit
    );
    $stmt->execute(array(':plot_id' => $bookingId, ':plot_id2' => $bookingId));
    return $stmt->fetchAll();
}

function recovery_booking_options($blocks)
{
    $bookings = recovery_fetch_bookings($blocks);
    $options = array();
    foreach ($bookings as $row) {
        $options[] = array(
            'id' => (int)$row['booking_id'],
            'label' => '#' . $row['booking_id'] . ' - ' . $row['customer_name'] . ' (' . $row['plot_label'] . ')',
        );
    }
    return $options;
}

function recovery_dashboard_stats($blocks, $staffId = null)
{
    $bookings = recovery_fetch_bookings($blocks);
    $dueCount = 0;
    foreach ($bookings as $row) {
        if ($row['due_amount'] > 0) {
            $dueCount++;
        }
    }

    $pdo = recovery_db();
    $callsToday = 0;
    if ($staffId) {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM booking_call_history
             WHERE recovery_staff_id = :staff_id AND DATE(created_at) = CURDATE()'
        );
        $stmt->execute(array(':staff_id' => $staffId));
        $callsToday = (int)$stmt->fetchColumn();
    } elseif (recovery_is_manager()) {
        $stmt = $pdo->query(
            'SELECT COUNT(*) FROM booking_call_history WHERE DATE(created_at) = CURDATE()'
        );
        $callsToday = (int)$stmt->fetchColumn();
    }

    $pendingApprovals = 0;
    if (recovery_is_manager()) {
        $pendingApprovals = (int)$pdo->query(
            "SELECT COUNT(*) FROM recovery_team_messages WHERE status = 'pending' AND booking_id IS NOT NULL"
        )->fetchColumn();
    }

    return array(
        'total_bookings' => count($bookings),
        'due_count' => $dueCount,
        'calls_today' => $callsToday,
        'pending_approvals' => $pendingApprovals,
    );
}

function recovery_staff_call_stats_today()
{
    $pdo = recovery_db();
    $stmt = $pdo->query(
        'SELECT rs.id, rs.first_name, rs.last_name, COUNT(bch.id) AS call_count
         FROM recovery_staff rs
         LEFT JOIN booking_call_history bch
           ON bch.recovery_staff_id = rs.id AND DATE(bch.created_at) = CURDATE()
         WHERE rs.status = 1
         GROUP BY rs.id
         ORDER BY call_count DESC, rs.first_name ASC'
    );
    return $stmt->fetchAll();
}
