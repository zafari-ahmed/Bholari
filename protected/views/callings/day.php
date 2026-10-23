<?php
    $statusMeta = isset($statusMeta) ? $statusMeta : CallingFollowUpTracker::statusMeta();
    $formatDate = function ($value) {
        if (!$value) {
            return '—';
        }
        return date('d M, Y', strtotime($value));
    };
    $statusBadge = function ($status) use ($statusMeta) {
        if (!isset($statusMeta[$status])) {
            return CHtml::encode($status);
        }
        $meta = $statusMeta[$status];
        $border = isset($meta['borderColor']) ? $meta['borderColor'] : $meta['color'];
        return '<span class="label" style="background-color:'.$meta['color'].';color:'.$meta['textColor'].';border:1px solid '.$border.';">'.CHtml::encode($meta['label']).'</span>';
    };
?>
<?php if (!$groups): ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            View Details
            <small><?php echo date('d M, Y', strtotime($date)); ?></small>
        </div>
        <div class="panel-body">No calling or follow-up items for this date.</div>
    </div>
<?php endif; ?>
<?php foreach ($groups as $groupType => $records): ?>
    <?php
        $meta = isset($statusMeta[$groupType]) ? $statusMeta[$groupType] : array('label' => $groupType, 'color' => '#777', 'textColor' => '#fff');
        $isNever = ($groupType === CallingFollowUpTracker::NEVER_CALLED);
    ?>
    <div class="panel panel-default">
        <div class="panel-heading" style="border-left:4px solid <?php echo $meta['color']; ?>">
            <?php echo CHtml::encode($meta['label']); ?>
            <small><?php echo date('d M, Y', strtotime($date)); ?></small>
            <span class="pull-right"><?php echo count($records); ?></span>
        </div>
        <div class="panel-body">
            <table width="100%" class="table table-striped table-bordered table-hover call-history-table">
                <thead>
                    <tr>
                        <th>Plot #</th>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>Sequence</th>
                        <th>Due Date</th>
                        <th>Actual Date</th>
                        <th>Status</th>
                        <?php if (!$isNever): ?>
                            <th>Called By</th>
                            <th>Call Status</th>
                            <th>Remarks</th>
                            <th>Next Follow-up</th>
                        <?php else: ?>
                            <th>Booked On</th>
                        <?php endif; ?>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($records) { foreach ($records as $row): ?>
                        <?php
                            $displayStatus = isset($row['calendar_status']) ? $row['calendar_status'] : $row['followup_status'];
                            $csc = ($row['call_status'] && isset($callStatusColor[$row['call_status']])) ? $callStatusColor[$row['call_status']] : '';
                        ?>
                        <tr>
                            <td>
                                <a href="<?php echo Yii::app()->baseUrl?>/booking/viewbooking/<?php echo $row['booking_id']?>">
                                    <?php echo CHtml::encode($row['plot_label']); ?>
                                </a>
                            </td>
                            <td><?php echo CHtml::encode($row['customer_name']); ?></td>
                            <td><?php echo CHtml::encode($row['mobile']); ?></td>
                            <td><?php echo CHtml::encode($row['followup_label']); ?></td>
                            <td><?php echo $formatDate($row['due_followup_date']); ?></td>
                            <td><?php echo $formatDate($row['actual_followup_date']); ?></td>
                            <td><?php echo $statusBadge($displayStatus); ?></td>
                            <?php if (!$isNever): ?>
                                <td><?php echo CHtml::encode($row['called_by']); ?></td>
                                <td>
                                    <?php if ($row['call_status'] && isset($callStatus[$row['call_status']])): ?>
                                        <span style="color:#fff;background-color:<?php echo $csc; ?>;padding:2px 6px;"><?php echo CHtml::encode($callStatus[$row['call_status']]); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo CHtml::encode($row['remarks']); ?></td>
                                <td><?php echo $formatDate($row['next_reminder']); ?></td>
                            <?php else: ?>
                                <td><?php echo $formatDate($row['booked_on']); ?></td>
                            <?php endif; ?>
                            <td>
                                <a href="javascript:void(0)" class="btn btn-xs btn-default call-timeline-link" data-booking-id="<?php echo intval($row['booking_id']); ?>">Timeline</a>
                            </td>
                        </tr>
                    <?php endforeach; } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endforeach; ?>
