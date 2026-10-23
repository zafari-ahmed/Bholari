<?php
    $statusBadge = function ($status) use ($statusMeta) {
        if (!isset($statusMeta[$status])) {
            return CHtml::encode($status);
        }
        $meta = $statusMeta[$status];
        $border = isset($meta['borderColor']) ? $meta['borderColor'] : $meta['color'];
        return '<span class="label" style="background-color:'.$meta['color'].';color:'.$meta['textColor'].';border:1px solid '.$border.';">'.CHtml::encode($meta['label']).'</span>';
    };
    $formatDate = function ($value) {
        if (!$value) {
            return '—';
        }
        return date('d M, Y', strtotime($value));
    };
?>
<div class="panel panel-default">
    <div class="panel-heading">
        Customer Timeline
        <?php if ($booking): ?>
            <small><?php echo CHtml::encode($booking['customer_name']); ?> — <?php echo CHtml::encode($booking['plot_label']); ?></small>
            <span class="pull-right">
                <a href="<?php echo Yii::app()->baseUrl?>/booking/viewbooking/<?php echo intval($booking['id']); ?>">Open Booking</a>
            </span>
        <?php endif; ?>
    </div>
    <div class="panel-body">
        <?php if (!$booking): ?>
            <p>Booking not found.</p>
        <?php else: ?>
        <?php
            $callStatus = isset($callStatus) ? $callStatus : Yii::app()->params['callStatus'];
            $bookingStatus = isset($bookingStatus) ? $bookingStatus : Yii::app()->params['bookingStatus'];
        ?>
        <div class="row">
            <form role="form" method="POST" action="<?php echo Yii::app()->baseUrl?>/bookingcall/save" enctype="multipart/form-data">
                <input type="hidden" name="booking_id" value="<?php echo intval($booking['id']); ?>">
                <input type="hidden" name="return_to" value="callings">
                <div class="form-group col-lg-3" style="padding-right: 0px;">
                    <label>Call Status</label>
                    <select name="call_status" id="timeline_call_status" class="form-control">
                        <option value="">Please Select Call Status</option>
                        <?php foreach($callStatus as $callS=>$callST):?>
                            <option value="<?php echo $callS?>"><?php echo $callST?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="form-group col-lg-3" style="padding-right: 0px;">
                    <label>Booking Status</label>
                    <select name="booking_status" id="timeline_booking_status" class="form-control">
                        <option value="">Please Select Booking Status</option>
                        <?php foreach($bookingStatus as $bookS=>$bookST):?>
                            <option value="<?php echo $bookS?>"><?php echo $bookST?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="form-group col-lg-3">
                    <label>Call Audio File</label>
                    <input type="file" class="form-control" name="callFile" id="timeline_callFile" placeholder="Upload Audio File">
                </div>
                <div class="form-group col-lg-3">
                    <label>Next Reminder</label>
                    <input class="form-control calender" name="next_reminder" placeholder="Next Reminder" autocomplete="off" required value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
                </div>
                <div class="form-group col-lg-12">
                    <label>Remark</label>
                    <textarea class="form-control" rows="3" name="remarks" placeholder="Remarks" required></textarea>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
        <hr/>
        <?php if ($neverCalled): ?>
            <p><?php echo $statusBadge(CallingFollowUpTracker::NEVER_CALLED); ?> This booking has no calling records yet.</p>
        <?php elseif (!$entries): ?>
            <p>No timeline entries.</p>
        <?php else: ?>
            <ul class="call-timeline">
                <?php foreach ($entries as $entry): ?>
                    <?php
                        $item = $entry['item'];
                        $meta = isset($statusMeta[$entry['status']]) ? $statusMeta[$entry['status']] : array('color' => '#777');
                    ?>
                    <li style="border-left-color:<?php echo $meta['color']; ?>">
                        <div><b><?php echo $formatDate($entry['date']); ?></b> — <?php echo CHtml::encode($entry['title']); ?></div>
                        <div><?php echo $statusBadge($entry['status']); ?></div>
                        <?php if ($item['due_followup_date'] && $entry['status'] !== CallingFollowUpTracker::FIRST_CALL): ?>
                            <div>Original Due Date: <?php echo $formatDate($item['due_followup_date']); ?></div>
                        <?php endif; ?>
                        <?php if ($item['actual_followup_date'] && $entry['status'] !== CallingFollowUpTracker::FIRST_CALL): ?>
                            <div>Actual Date: <?php echo $formatDate($item['actual_followup_date']); ?></div>
                        <?php endif; ?>
                        <?php if ($entry['status'] === CallingFollowUpTracker::FIRST_CALL && $item['next_reminder']): ?>
                            <div>Next Follow-up: <?php echo $formatDate($item['next_reminder']); ?></div>
                        <?php elseif (!empty($item['next_reminder']) && $item['actual_followup_date']): ?>
                            <div>Next Follow-up: <?php echo $formatDate($item['next_reminder']); ?></div>
                        <?php endif; ?>
                        <?php if ($item['called_by']): ?>
                            <div>Called By: <?php echo CHtml::encode($item['called_by']); ?></div>
                        <?php endif; ?>
                        <?php if ($item['remarks']): ?>
                            <div>Notes: <?php echo CHtml::encode($item['remarks']); ?></div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
