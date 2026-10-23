<?php $statusMeta = isset($statusMeta) ? $statusMeta : CallingFollowUpTracker::statusMeta(); ?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Call History</h1>
        <?php
            foreach(Yii::app()->user->getFlashes() as $key => $message) {
                echo '<div class="alert alert-info alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$message.'</div>';
            }
        ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <span id="callingSummaryTitle">Today — <?php echo date('d M, Y'); ?></span>
            </div>
            <div class="panel-body" id="callingSummaryBody" style="padding-top:10px;">
                <div class="row" id="callingSummaryCounters"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">Call History Calendar</div>
            <div class="panel-body">
                <div class="calling-legend" style="margin-bottom:12px;">
                    <?php foreach ($statusMeta as $meta): ?>
                        <span class="label" style="background-color:<?php echo $meta['color']; ?>;color:<?php echo $meta['textColor']; ?>;border:1px solid <?php echo isset($meta['borderColor']) ? $meta['borderColor'] : $meta['color']; ?>;"><?php echo CHtml::encode($meta['short']); ?></span>
                    <?php endforeach; ?>
                </div>
                <div id="callHistoryCalendar"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12" id="callHistoryDetail">
        <div class="panel panel-default">
            <div class="panel-heading">View Details</div>
            <div class="panel-body">Click a date or calendar event to view details.</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12" id="callHistoryTimeline"></div>
</div>
<link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/dist/fullcalendar/main.min.css" rel="stylesheet">
<style type="text/css">
    #callHistoryCalendar .fc-event {
        font-size: 12px;
        cursor: pointer;
        padding: 2px 4px;
        white-space: normal;
    }
    #callHistoryCalendar .fc-toolbar-title {
        font-size: 20px;
    }
    #callHistoryCalendar .fc-button {
        text-transform: capitalize;
    }
    .calling-legend .label {
        margin-right: 4px;
        margin-bottom: 4px;
        font-weight: normal;
        display: inline-block;
    }
    .calling-summary-card {
        cursor: pointer;
        margin-bottom: 10px;
        border-radius: 3px;
        padding: 10px 12px;
        min-height: 74px;
    }
    .calling-summary-card .count {
        font-size: 24px;
        font-weight: bold;
        line-height: 1.1;
    }
    .calling-summary-card .label-text {
        font-size: 12px;
    }
    ul.call-timeline {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }
    ul.call-timeline li {
        border-left: 4px solid #777;
        padding: 8px 0 12px 12px;
        margin-bottom: 8px;
    }
</style>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/dist/fullcalendar/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('callHistoryCalendar');
    var detailUrl = '<?php echo Yii::app()->baseUrl; ?>/callings/day';
    var summaryUrl = '<?php echo Yii::app()->baseUrl; ?>/callings/summary';
    var timelineUrl = '<?php echo Yii::app()->baseUrl; ?>/callings/timeline';
    var today = '<?php echo date('Y-m-d'); ?>';
    var selectedDate = today;

    function formatTitleDate(dateStr) {
        var d = new Date(dateStr + 'T00:00:00');
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var prefix = (dateStr === today) ? 'Today — ' : '';
        return prefix + d.getDate() + ' ' + months[d.getMonth()] + ', ' + d.getFullYear();
    }

    function renderSummary(data) {
        var box = document.getElementById('callingSummaryCounters');
        var title = document.getElementById('callingSummaryTitle');
        if (title) {
            title.textContent = formatTitleDate(data.date);
        }
        if (!box) {
            return;
        }
        var html = '';
        (data.items || []).forEach(function(item) {
            html += '<div class="col-lg-3 col-md-4 col-sm-6">';
            html += '<div class="calling-summary-card" data-type="' + item.type + '" style="background-color:' + item.color + ';color:' + item.textColor + ';">';
            html += '<div class="count">' + item.count + '</div>';
            html += '<div class="label-text">' + item.label + '</div>';
            html += '</div></div>';
        });
        box.innerHTML = html;
        $(box).find('.calling-summary-card').on('click', function() {
            loadCallDetails(selectedDate, $(this).data('type'));
        });
    }

    function initDetailTables() {
        if (!$.fn.DataTable) {
            return;
        }
        $('.call-history-table').each(function() {
            if ($.fn.DataTable.isDataTable(this)) {
                return;
            }
            $(this).DataTable({
                dom: '<"top"l i f>rt<"bottom"p><"clear">',
                iDisplayLength: 50,
                responsive: true,
                aaSorting: [],
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
            });
        });
    }

    function loadSummary(date) {
        $.getJSON(summaryUrl, { date: date }, function(data) {
            renderSummary(data);
        });
    }

    function loadCallDetails(date, type) {
        var box = document.getElementById('callHistoryDetail');
        if (!box) {
            return;
        }
        selectedDate = date;
        loadSummary(date);
        box.innerHTML = '<div class="panel panel-default"><div class="panel-body">Loading...</div></div>';
        $.get(detailUrl, { date: date, type: type || 'all' }, function(html) {
            box.innerHTML = html;
            initDetailTables();
        }).fail(function() {
            box.innerHTML = '<div class="panel panel-default"><div class="panel-body">Unable to load details.</div></div>';
        });
    }

    function loadTimeline(bookingId) {
        var box = document.getElementById('callHistoryTimeline');
        if (!box) {
            return;
        }
        box.innerHTML = '<div class="panel panel-default"><div class="panel-body">Loading timeline...</div></div>';
        $.get(timelineUrl, { booking_id: bookingId }, function(html) {
            box.innerHTML = html;
            if ($.fn.datepicker) {
                $(box).find('.calender').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true
                });
            }
            if (box.scrollIntoView) {
                box.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    $(document).on('click', '.call-timeline-link', function() {
        loadTimeline($(this).data('booking-id'));
    });

    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Today',
                month: 'Month',
                week: 'Week',
                day: 'Day'
            },
            height: 'auto',
            navLinks: true,
            events: '<?php echo Yii::app()->baseUrl; ?>/callings/events',
            eventDisplay: 'block',
            displayEventTime: false,
            dateClick: function(info) {
                loadCallDetails(info.dateStr, 'all');
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                var type = (info.event.extendedProps && info.event.extendedProps.type) ? info.event.extendedProps.type : 'all';
                var date = info.event.startStr;
                loadCallDetails(date, type);
            }
        });
        calendar.render();
    }

    loadCallDetails(today, 'all');

    var urlBookingId = <?php echo intval(Yii::app()->request->getParam('booking_id')); ?>;
    if (urlBookingId) {
        loadTimeline(urlBookingId);
    }
});
</script>
