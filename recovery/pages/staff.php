<?php

$pdo = recovery_db();
$managerId = (int)recovery_user()['user_id'];
$allBlocks = recovery_all_blocks();
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editStaff = null;
$editBlocks = array();

if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM recovery_staff WHERE id = :id LIMIT 1');
    $stmt->execute(array(':id' => $editId));
    $editStaff = $stmt->fetch();
    if ($editStaff) {
        $editBlocks = recovery_staff_blocks($editId);
    }
}

$staffRows = $pdo->query(
    'SELECT rs.*, GROUP_CONCAT(rsb.block_number ORDER BY rsb.block_number SEPARATOR ", ") AS blocks
     FROM recovery_staff rs
     LEFT JOIN recovery_staff_blocks rsb ON rsb.staff_id = rs.id
     GROUP BY rs.id
     ORDER BY rs.id DESC'
)->fetchAll();

ob_start();
?>
<div class="row">
    <div class="col-lg-5">
        <div class="panel panel-default">
            <div class="panel-heading"><b><?php echo $editStaff ? 'Edit Staff' : 'Create Staff'; ?></b></div>
            <div class="panel-body">
                <form method="post" action="<?php echo recovery_base_url('api/staff_save.php'); ?>">
                    <input type="hidden" name="csrf" value="<?php echo recovery_csrf_token(); ?>">
                    <?php if ($editStaff): ?>
                        <input type="hidden" name="staff_id" value="<?php echo (int)$editStaff['id']; ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required value="<?php echo $editStaff ? htmlspecialchars($editStaff['username']) : ''; ?>" <?php echo $editStaff ? 'readonly' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control" required value="<?php echo $editStaff ? htmlspecialchars($editStaff['first_name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control" required value="<?php echo $editStaff ? htmlspecialchars($editStaff['last_name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label><?php echo $editStaff ? 'New Password (optional)' : 'Password'; ?></label>
                        <input type="password" name="password" class="form-control" <?php echo $editStaff ? '' : 'required'; ?>>
                    </div>
                    <div class="form-group">
                        <label>Assigned Blocks</label>
                        <select name="blocks[]" class="form-control" multiple required size="8">
                            <?php foreach ($allBlocks as $block): ?>
                                <option value="<?php echo htmlspecialchars($block); ?>" <?php echo in_array($block, $editBlocks, true) ? 'selected' : ''; ?>><?php echo htmlspecialchars($block); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success"><?php echo $editStaff ? 'Update Staff' : 'Create Staff'; ?></button>
                    <?php if ($editStaff): ?>
                        <a href="<?php echo recovery_base_url('index.php?page=staff'); ?>" class="btn btn-default">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="panel panel-default">
            <div class="panel-heading"><b>Recovery Staff</b></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered recovery-datatable">
                    <thead><tr><th>ID</th><th>Name</th><th>Username</th><th>Blocks</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($staffRows as $row): ?>
                        <tr>
                            <td><?php echo (int)$row['id']; ?></td>
                            <td><?php echo htmlspecialchars(trim($row['first_name'] . ' ' . $row['last_name'])); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['blocks']); ?></td>
                            <td><?php echo (int)$row['status'] === 1 ? 'Active' : 'Inactive'; ?></td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="<?php echo recovery_base_url('index.php?page=staff&edit=' . (int)$row['id']); ?>">Edit</a>
                                <form method="post" action="<?php echo recovery_base_url('api/staff_toggle.php'); ?>" style="display:inline;">
                                    <input type="hidden" name="csrf" value="<?php echo recovery_csrf_token(); ?>">
                                    <input type="hidden" name="staff_id" value="<?php echo (int)$row['id']; ?>">
                                    <button type="submit" class="btn btn-xs btn-warning"><?php echo (int)$row['status'] === 1 ? 'Deactivate' : 'Activate'; ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
recovery_render_layout($content, 'Staff Management');
