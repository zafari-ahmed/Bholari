<?php

$user = recovery_user();
$blocks = recovery_accessible_blocks();
$staffId = recovery_is_staff() ? (int)$user['staff_id'] : null;
$stats = recovery_dashboard_stats($blocks, $staffId);
$recentBookings = array_slice(recovery_fetch_bookings($blocks), 0, 10);
$config = recovery_config();

ob_start();
?>
<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="panel panel-yellow recovery-stat-card">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3"><i class="fa fa-clock-o fa-3x"></i></div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo number_format((int)$stats['remaining_count']); ?></div>
                        <div>Booking with Remaining Amount</div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                Bookings with balance · PKR <?php echo number_format((float)$stats['remaining_amount_total']); ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="panel panel-red recovery-stat-card">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3"><i class="fa fa-warning fa-3x"></i></div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo number_format((int)$stats['due_count']); ?></div>
                        <div>Bookings With Due Amount</div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                Overdue · PKR <?php echo number_format((float)$stats['due_amount_total']); ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="panel panel-primary recovery-stat-card">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3"><i class="fa fa-bookmark fa-3x"></i></div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo number_format((int)$stats['regular_bookings']); ?></div>
                        <div>Regular Bookings</div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                Remaining − Due
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="panel panel-info recovery-stat-card">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3"><i class="fa fa-book fa-3x"></i></div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo number_format((int)$stats['total_bookings']); ?></div>
                        <div>Assigned Bookings</div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                Booking with Remaining Amount + Full Payment Booking
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="panel panel-green recovery-stat-card">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3"><i class="fa fa-phone fa-3x"></i></div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo number_format((int)$stats['calls_today']); ?></div>
                        <div>Calls Today</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if (recovery_is_manager()): ?>
    <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="panel panel-default recovery-stat-card">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3"><i class="fa fa-check fa-3x"></i></div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo number_format((int)$stats['pending_approvals']); ?></div>
                        <div>Pending Approvals</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if (recovery_is_manager()): ?>
<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading"><b>Staff Calls Today</b></div>
            <div class="panel-body">
                <table class="table table-striped">
                    <thead><tr><th>Staff</th><th>Calls</th></tr></thead>
                    <tbody>
                    <?php foreach (recovery_staff_call_stats_today() as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(trim($row['first_name'] . ' ' . $row['last_name'])); ?></td>
                            <td><?php echo (int)$row['call_count']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading"><b>Recent Team Chat</b></div>
            <div class="panel-body recovery-chat-box" style="height:220px;">
                <?php foreach (array_slice(recovery_fetch_team_messages(8), -8) as $msg): ?>
                    <div class="recovery-chat-message <?php echo htmlspecialchars($msg['status']); ?>">
                        <div class="recovery-chat-meta"><?php echo htmlspecialchars($msg['sender_name']); ?> · <?php echo date('d M, Y H:i', strtotime($msg['created_at'])); ?></div>
                        <div><?php echo nl2br(htmlspecialchars($msg['display_message'])); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="panel-footer"><a href="<?php echo recovery_base_url('index.php?page=chat'); ?>" class="btn btn-primary btn-sm">Open Team Chat</a></div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="panel panel-default">
    <div class="panel-heading"><b>Recent Bookings</b></div>
    <div class="panel-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th><th>Customer</th><th>Plot</th><th>Due</th><th>Last Call</th><th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recentBookings as $row): ?>
                <tr>
                    <td><?php echo (int)$row['booking_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['plot_label']); ?></td>
                    <td><?php echo $row['due_amount'] > 0 ? 'Rs. ' . number_format($row['due_amount']) : '-'; ?></td>
                    <td><?php echo $row['last_call_at'] ? date('d M, Y', strtotime($row['last_call_at'])) : '-'; ?></td>
                    <td><a class="btn btn-xs btn-primary" href="<?php echo recovery_base_url('index.php?page=booking&id=' . (int)$row['booking_id']); ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
recovery_render_layout($content, 'Dashboard');
