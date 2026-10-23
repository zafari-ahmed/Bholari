<?php

$blocks = recovery_accessible_blocks();
$filters = array(
    'block' => isset($_GET['block']) ? $_GET['block'] : '',
    'search' => isset($_GET['search']) ? trim($_GET['search']) : '',
    'due_only' => !empty($_GET['due_only']),
);
$bookings = recovery_fetch_bookings($blocks, $filters);
$config = recovery_config();

ob_start();
?>
<div class="panel panel-default">
    <div class="panel-heading"><b>Bookings</b></div>
    <div class="panel-body">
        <form method="get" class="form-inline" style="margin-bottom:15px;">
            <input type="hidden" name="page" value="bookings">
            <div class="form-group">
                <select name="block" class="form-control">
                    <option value="">All Blocks</option>
                    <?php foreach ($blocks as $block): ?>
                        <option value="<?php echo htmlspecialchars($block); ?>" <?php echo $filters['block'] === $block ? 'selected' : ''; ?>><?php echo htmlspecialchars($block); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="text" name="search" class="form-control" placeholder="Search customer/plot/id" value="<?php echo htmlspecialchars($filters['search']); ?>">
            </div>
            <div class="checkbox" style="margin-left:10px;">
                <label><input type="checkbox" name="due_only" value="1" <?php echo $filters['due_only'] ? 'checked' : ''; ?>> Due only</label>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <table class="table table-striped table-bordered recovery-datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Mobile</th>
                    <th>Plot</th>
                    <th>Due Amount</th>
                    <th>Due Months</th>
                    <th>Last Call</th>
                    <th>Next Reminder</th>
                    <th>Call Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($bookings as $row): ?>
                <?php
                $callStatusLabel = isset($config['call_status'][$row['last_call_status']]) ? $config['call_status'][$row['last_call_status']] : '-';
                ?>
                <tr>
                    <td><?php echo (int)$row['booking_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                    <td><?php echo htmlspecialchars($row['plot_label']); ?></td>
                    <td><?php echo $row['due_amount'] > 0 ? 'Rs. ' . number_format($row['due_amount']) : '-'; ?></td>
                    <td><?php echo $row['due_months'] > 0 ? $row['due_months'] : '-'; ?></td>
                    <td><?php echo $row['last_call_at'] ? date('d M, Y H:i', strtotime($row['last_call_at'])) : '-'; ?></td>
                    <td><?php echo $row['next_reminder'] ? date('d M, Y', strtotime($row['next_reminder'])) : '-'; ?></td>
                    <td><?php echo htmlspecialchars($callStatusLabel); ?></td>
                    <td><a class="btn btn-xs btn-primary" href="<?php echo recovery_base_url('index.php?page=booking&id=' . (int)$row['booking_id']); ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
recovery_render_layout($content, 'Bookings');
