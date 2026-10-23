<?php

$messages = recovery_fetch_team_messages(200);
$bookingOptions = recovery_booking_options(recovery_accessible_blocks());

ob_start();
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <b>Team Chat</b>
        <button id="recovery-chat-refresh" class="btn btn-default btn-xs pull-right">Refresh</button>
    </div>
    <div class="panel-body">
        <div class="recovery-chat-box">
            <?php foreach ($messages as $msg): ?>
                <div class="recovery-chat-message <?php echo htmlspecialchars($msg['status']); ?>">
                    <div class="recovery-chat-meta">
                        <?php echo htmlspecialchars($msg['sender_name']); ?> ·
                        <?php echo date('d M, Y H:i', strtotime($msg['created_at'])); ?>
                        <?php if ($msg['booking_id']): ?>
                            · Booking #<?php echo (int)$msg['booking_id']; ?>
                            <?php if (!empty($msg['customer_name'])): ?>
                                (<?php echo htmlspecialchars($msg['customer_name']); ?> - <?php echo htmlspecialchars($msg['block_number'] . '/' . $msg['plot_type'] . '/' . $msg['plot_number']); ?>)
                            <?php endif; ?>
                        <?php endif; ?>
                        · <?php echo htmlspecialchars(ucfirst($msg['status'])); ?>
                    </div>
                    <div><?php echo nl2br(htmlspecialchars($msg['display_message'])); ?></div>

                    <?php if (recovery_is_manager() && $msg['status'] === 'pending' && $msg['booking_id']): ?>
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

        <form method="post" action="<?php echo recovery_base_url('api/post_message.php'); ?>">
            <input type="hidden" name="csrf" value="<?php echo recovery_csrf_token(); ?>">
            <div class="form-group">
                <label>Link to booking (optional)</label>
                <select name="booking_id" class="form-control">
                    <option value="">General team message</option>
                    <?php foreach ($bookingOptions as $opt): ?>
                        <option value="<?php echo (int)$opt['id']; ?>"><?php echo htmlspecialchars($opt['label']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea name="message" class="form-control" rows="3" required placeholder="I called booking #123, customer needs payment schedule to continue..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
recovery_render_layout($content, 'Team Chat');
