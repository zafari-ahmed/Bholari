<?php

$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$bookingId || !recovery_can_access_booking($bookingId)) {
    recovery_flash('error', 'Booking not found or access denied.');
    recovery_redirect('bookings');
}

$config = recovery_config();
$booking = recovery_fetch_booking($bookingId);
$dues = recovery_calculate_dues($bookingId);
$transactions = recovery_fetch_transactions($bookingId);
$callHistory = recovery_fetch_call_history($bookingId);
$messages = recovery_fetch_booking_messages($bookingId);

ob_start();
?>
<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading"><b>Booking #<?php echo (int)$booking['id']; ?></b></div>
            <div class="panel-body">
                <p><strong>Customer:</strong> <?php echo htmlspecialchars($booking['customer_name']); ?></p>
                <p><strong>Mobile:</strong> <?php echo htmlspecialchars($booking['mobile']); ?></p>
                <p><strong>Plot:</strong> <?php echo htmlspecialchars($booking['plot_label']); ?></p>
                <p><strong>Booking Date:</strong> <?php echo date('d M, Y', strtotime($booking['createdOn'])); ?></p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading"><b>Due Summary</b></div>
            <div class="panel-body">
                <?php if ($dues['status'] === 'error'): ?>
                    <p class="text-danger"><?php echo htmlspecialchars($dues['message']); ?></p>
                <?php else: ?>
                    <p><strong>Monthly Amount:</strong> Rs. <?php echo number_format($dues['monthly_amount']); ?></p>
                    <p><strong>Total Paid:</strong> Rs. <?php echo number_format($dues['total_paid']); ?></p>
                    <p><strong>Booking Total:</strong> Rs. <?php echo number_format($dues['booking_total']); ?></p>
                    <p><strong>Due Amount:</strong> Rs. <?php echo number_format($dues['due_amount']); ?></p>
                    <p><strong>Due Months:</strong> <?php echo htmlspecialchars($dues['due_months']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($dues['message']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><b>Recent Transactions</b></div>
    <div class="panel-body">
        <table class="table table-striped table-bordered">
            <thead><tr><th>Type</th><th>Amount</th><th>Date</th><th>Txn #</th><th>Comment</th></tr></thead>
            <tbody>
            <?php foreach ($transactions as $txn): ?>
                <tr>
                    <td><?php echo htmlspecialchars($txn['txn_type']); ?></td>
                    <td>Rs. <?php echo number_format($txn['amount']); ?></td>
                    <td><?php echo date('d M, Y', strtotime($txn['createdOn'])); ?></td>
                    <td><?php echo htmlspecialchars($txn['transaction_number']); ?></td>
                    <td><?php echo htmlspecialchars($txn['comment']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><b>Log Call</b></div>
    <div class="panel-body">
        <form method="post" action="<?php echo recovery_base_url('api/save_call.php'); ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?php echo recovery_csrf_token(); ?>">
            <input type="hidden" name="booking_id" value="<?php echo (int)$bookingId; ?>">
            <div class="row">
                <div class="form-group col-md-3">
                    <label>Call Status</label>
                    <select name="call_status" class="form-control" required>
                        <option value="">Select</option>
                        <?php foreach ($config['call_status'] as $id => $label): ?>
                            <option value="<?php echo (int)$id; ?>"><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Booking Status</label>
                    <select name="booking_status" class="form-control" required>
                        <option value="">Select</option>
                        <?php foreach ($config['booking_status'] as $id => $label): ?>
                            <option value="<?php echo (int)$id; ?>"><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Next Reminder</label>
                    <input type="text" name="next_reminder" class="form-control calender" required value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
                </div>
                <div class="form-group col-md-3">
                    <label>Audio File</label>
                    <input type="file" name="callFile" class="form-control">
                </div>
                <div class="form-group col-md-12">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control" rows="3" required></textarea>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-success">Save Call</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><b>Call History</b></div>
    <div class="panel-body">
        <table class="table table-striped table-bordered">
            <thead><tr><th>Date</th><th>Called By</th><th>Call Status</th><th>Booking Status</th><th>Remarks</th><th>Next Reminder</th><th>Audio</th></tr></thead>
            <tbody>
            <?php foreach ($callHistory as $call): ?>
                <?php
                $csc = isset($config['call_status_color'][$call['call_status']]) ? $config['call_status_color'][$call['call_status']] : '#999';
                $bsc = isset($config['booking_status_color'][$call['booking_status']]) ? $config['booking_status_color'][$call['booking_status']] : '#999';
                ?>
                <tr>
                    <td><?php echo date('d M, Y H:i', strtotime($call['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars(recovery_call_caller_name($call)); ?></td>
                    <td style="color:#fff;background:<?php echo $csc; ?>;"><?php echo htmlspecialchars($config['call_status'][$call['call_status']]); ?></td>
                    <td style="color:#fff;background:<?php echo $bsc; ?>;"><?php echo htmlspecialchars($config['booking_status'][$call['booking_status']]); ?></td>
                    <td><?php echo htmlspecialchars($call['remarks']); ?></td>
                    <td><?php echo date('d M, Y', strtotime($call['next_reminder'])); ?></td>
                    <td><?php if (!empty($call['call_audio_file'])): ?><a target="_blank" href="<?php echo recovery_asset_url('uploads/booking_audio/' . $call['call_audio_file']); ?>">Audio</a><?php endif; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><b>Recovery Notes / Team Messages</b></div>
    <div class="panel-body">
        <form method="post" action="<?php echo recovery_base_url('api/post_message.php'); ?>" style="margin-bottom:15px;">
            <input type="hidden" name="csrf" value="<?php echo recovery_csrf_token(); ?>">
            <input type="hidden" name="booking_id" value="<?php echo (int)$bookingId; ?>">
            <div class="form-group">
                <label>Post note about this booking</label>
                <textarea name="message" class="form-control" rows="3" required placeholder="I called this booking, customer needs payment schedule..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post Note</button>
        </form>

        <?php foreach ($messages as $msg): ?>
            <div class="recovery-chat-message <?php echo htmlspecialchars($msg['status']); ?>">
                <div class="recovery-chat-meta">
                    <?php echo htmlspecialchars($msg['sender_name']); ?> ·
                    <?php echo date('d M, Y H:i', strtotime($msg['created_at'])); ?> ·
                    <?php echo htmlspecialchars(ucfirst($msg['status'])); ?>
                </div>
                <div><?php echo nl2br(htmlspecialchars($msg['display_message'])); ?></div>
                <?php if (recovery_is_manager() && $msg['status'] === 'pending'): ?>
                    <form method="post" action="<?php echo recovery_base_url('api/approve_message.php'); ?>" style="margin-top:8px;">
                        <input type="hidden" name="csrf" value="<?php echo recovery_csrf_token(); ?>">
                        <input type="hidden" name="message_id" value="<?php echo (int)$msg['id']; ?>">
                        <textarea name="approved_message" class="form-control" rows="2"><?php echo htmlspecialchars($msg['message']); ?></textarea>
                        <button type="submit" class="btn btn-success btn-xs" style="margin-top:5px;">Approve</button>
                    </form>
                    <form method="post" action="<?php echo recovery_base_url('api/reject_message.php'); ?>" style="display:inline;">
                        <input type="hidden" name="csrf" value="<?php echo recovery_csrf_token(); ?>">
                        <input type="hidden" name="message_id" value="<?php echo (int)$msg['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-xs" style="margin-top:5px;">Reject</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
recovery_render_layout($content, 'Booking #' . $bookingId);
