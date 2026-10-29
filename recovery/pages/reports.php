<?php

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$staffStats = recovery_staff_call_stats_today();
$auditRows = recovery_fetch_activity_audit($date);

ob_start();
?>
<div class="panel panel-default">
    <div class="panel-heading"><b>Daily Calls by Staff (Today)</b></div>
    <div class="panel-body">
        <table class="table table-striped table-bordered">
            <thead><tr><th>Staff</th><th>Calls Today</th></tr></thead>
            <tbody>
            <?php foreach ($staffStats as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars(trim($row['first_name'] . ' ' . $row['last_name'])); ?></td>
                    <td><?php echo (int)$row['call_count']; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><b>Recovery Call Activity Audit</b></div>
    <div class="panel-body">
        <form method="get" class="form-inline" style="margin-bottom:15px;">
            <input type="hidden" name="page" value="reports">
            <div class="form-group">
                <label>Date</label>
                <input type="text" name="date" class="form-control calender" value="<?php echo htmlspecialchars($date); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <table class="table table-striped table-bordered recovery-datatable">
            <thead>
                <tr>
                    <th>Date</th><th>Staff/Manager</th><th>Booking</th><th>Customer</th><th>Plot</th><th>Remarks</th><th>Next Reminder</th><th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($auditRows as $row): ?>
                <?php
                $caller = !empty($row['recovery_staff_id'])
                    ? trim($row['staff_first_name'] . ' ' . $row['staff_last_name']) . ' (Staff)'
                    : trim($row['manager_first_name']) . ' (Manager)';
                $bookingUrl = recovery_base_url('index.php?page=booking&id=' . (int)$row['booking_id']);
                ?>
                <tr>
                    <td><?php echo date('d M, Y H:i', strtotime($row['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($caller); ?></td>
                    <td>
                        <a href="<?php echo $bookingUrl; ?>">#<?php echo (int)$row['booking_id']; ?></a>
                    </td>
                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['block_number'] . ' / ' . $row['plot_type'] . ' / ' . $row['plot_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                    <td><?php echo date('d M, Y', strtotime($row['next_reminder'])); ?></td>
                    <td>
                        <a class="btn btn-xs btn-primary" href="<?php echo $bookingUrl; ?>">Open</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
recovery_render_layout($content, 'Reports');
