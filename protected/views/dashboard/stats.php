<?php $baseUrl = Yii::app()->baseUrl; ?>
<style>
.ps-wrap { padding-bottom: 20px; }
.ps-wrap .page-header {
	margin: 8px 0 8px;
	padding-bottom: 12px;
	border-bottom: 1px solid #e8e8e8;
}
.ps-wrap .page-header small {
	display: block;
	margin-top: 4px;
	color: #888;
	font-size: 13px;
	font-weight: normal;
}
.ps-section {
	margin: 18px 0 8px;
	font-size: 12px;
	font-weight: 700;
	letter-spacing: 0.08em;
	text-transform: uppercase;
	color: #999;
}
.ps-tile {
	background: #fff;
	border: 1px solid #e6e6e6;
	border-radius: 4px;
	margin-bottom: 18px;
	overflow: hidden;
}
.ps-tile-top {
	padding: 18px 18px 14px;
	position: relative;
}
.ps-tile-top:after {
	content: "";
	display: table;
	clear: both;
}
.ps-icon {
	float: left;
	width: 56px;
	height: 56px;
	line-height: 56px;
	text-align: center;
	border-radius: 4px;
	font-size: 24px;
	color: #fff;
}
.ps-info {
	margin-left: 72px;
	padding-top: 2px;
}
.ps-label {
	font-size: 13px;
	color: #777;
	margin-bottom: 4px;
}
.ps-value {
	font-size: 28px;
	font-weight: 700;
	color: #333;
	line-height: 1.15;
	word-break: break-all;
}
.ps-sub {
	margin-top: 12px;
	padding-top: 12px;
	border-top: 1px dashed #eee;
}
.ps-sub-row {
	overflow: hidden;
	font-size: 12px;
	color: #666;
	margin-bottom: 4px;
}
.ps-sub-row:last-child { margin-bottom: 0; }
.ps-sub-row .k { float: left; }
.ps-sub-row .v {
	float: right;
	font-weight: 700;
	color: #333;
}
.ps-tile-foot {
	background: #fafafa;
	border-top: 1px solid #eee;
	padding: 9px 18px;
	font-size: 12px;
}
.ps-tile-foot a {
	color: #555;
	font-weight: 600;
	text-decoration: none;
}
.ps-tile-foot a:hover { color: #222; }
.ps-tile-foot .fa { margin-left: 3px; }

.ps-c1 .ps-icon { background: #5b6b7a; }
.ps-c2 .ps-icon { background: #1a9c8a; }
.ps-c3 .ps-icon { background: #3f5bd9; }
.ps-c4 .ps-icon { background: #1f9d57; }
.ps-c5 .ps-icon { background: #d9891a; }
.ps-c6 .ps-icon { background: #d13b55; }
.ps-c7 .ps-icon { background: #9f1239; }
.ps-c8 .ps-icon { background: #0369a1; }

.ps-c1 { border-top: 3px solid #5b6b7a; }
.ps-c2 { border-top: 3px solid #1a9c8a; }
.ps-c3 { border-top: 3px solid #3f5bd9; }
.ps-c4 { border-top: 3px solid #1f9d57; }
.ps-c5 { border-top: 3px solid #d9891a; }
.ps-c6 { border-top: 3px solid #d13b55; }
.ps-c7 { border-top: 3px solid #9f1239; }
.ps-c8 { border-top: 3px solid #0369a1; }

.ps-clickable { cursor: pointer; transition: box-shadow .15s ease; }
.ps-clickable:hover { box-shadow: 0 2px 10px rgba(0,0,0,.08); }
.ps-block-table { margin-bottom: 0; }
.ps-block-table th, .ps-block-table td { vertical-align: middle !important; }
.ps-block-table td:last-child, .ps-block-table th:last-child { text-align: right; }
</style>

<?php
$blockBreakdown = array(
	'full' => array(
		'title' => 'Full Payment Bookings — by Block',
		'rows' => isset($fullPaymentByBlock) ? $fullPaymentByBlock : array(),
		'total' => isset($fullPaymentCount) ? $fullPaymentCount : 0,
	),
	'remaining' => array(
		'title' => 'Remaining Amount — by Block',
		'rows' => isset($remainingByBlock) ? $remainingByBlock : array(),
		'total' => isset($remainingCount) ? $remainingCount : 0,
	),
	'due' => array(
		'title' => 'Bookings With Due — by Block',
		'rows' => isset($dueByBlock) ? $dueByBlock : array(),
		'total' => isset($dueCount) ? $dueCount : 0,
	),
	'regular' => array(
		'title' => 'Regular Bookings — by Block',
		'rows' => isset($regularByBlock) ? $regularByBlock : array(),
		'total' => isset($regularBookings) ? $regularBookings : 0,
	),
);
?>

<div class="ps-wrap">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				Plot &amp; Booking States
				<small>Inventory and payment overview for the current phase</small>
			</h1>
		</div>
	</div>

	<div class="ps-section">Inventory</div>
	<div class="row">
		<!-- <div class="col-lg-4 col-md-4 col-sm-6">
			<div class="ps-tile ps-c1">
				<div class="ps-tile-top">
					<div class="ps-icon"><i class="fa fa-th"></i></div>
					<div class="ps-info">
						<div class="ps-label">Total Plots</div>
						<div class="ps-value"><?php //echo number_format($totalPlots); ?></div>
					</div>
				</div>
				<div class="ps-tile-foot">
					<a href="<?php //echo $baseUrl.'/plot'; ?>">View all plots <i class="fa fa-angle-right"></i></a>
				</div>
			</div>
		</div> -->

		<!-- <div class="col-lg-4 col-md-4 col-sm-6">
			<div class="ps-tile ps-c2">
				<div class="ps-tile-top">
					<div class="ps-icon"><i class="fa fa-check"></i></div>
					<div class="ps-info">
						<div class="ps-label">Total Available Plots</div>
						<div class="ps-value"><?php //echo number_format($activePlots); ?></div>
					</div>
				</div>
				<div class="ps-tile-foot">
					<a href="<?php //echo $baseUrl.'/plot?status=0'; ?>">View available only <i class="fa fa-angle-right"></i></a>
				</div>
			</div>
		</div> -->

		<div class="col-lg-4 col-md-4 col-sm-6">
			<div class="ps-tile ps-c3">
				<div class="ps-tile-top">
					<div class="ps-icon"><i class="fa fa-book"></i></div>
					<div class="ps-info">
						<div class="ps-label">Total Bookings</div>
						<div class="ps-value"><?php echo number_format($totalBookings); ?></div>
					</div>
					<div class="ps-sub">
						<div class="ps-sub-row">
							<span class="k">&nbsp;</span>
							<span class="v">&nbsp;</span>
						</div>
					</div>
				</div>
				<div class="ps-tile-foot">
					<a href="<?php echo $baseUrl.'/booking'; ?>">View all bookings <i class="fa fa-angle-right"></i></a>
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-4 col-sm-6">
			<div class="ps-tile ps-c7">
				<div class="ps-tile-top">
					<div class="ps-icon"><i class="fa fa-ban"></i></div>
					<div class="ps-info">
						<div class="ps-label">Cancelled Plots</div>
						<div class="ps-value"><?php echo number_format($cancelledBookings); ?></div>
					</div>
					<div class="ps-sub">
						<div class="ps-sub-row">
							<span class="k">Total refund</span>
							<span class="v">PKR <?php echo number_format($cancelledRefundTotal); ?></span>
						</div>
					</div>
				</div>
				<div class="ps-tile-foot">
					<a href="<?php echo $baseUrl.'/report/cancelled'; ?>">View cancelled <i class="fa fa-angle-right"></i></a>
				</div>
			</div>
		</div>
	</div>

	<div class="ps-section">Payment Status</div>
	<div class="row">
		<div class="col-lg-3 col-md-3 col-sm-6">
			<div class="ps-tile ps-c4 ps-clickable" data-block-type="full" title="Click to see block-wise count">
				<div class="ps-tile-top">
					<div class="ps-icon"><i class="fa fa-check-circle"></i></div>
					<div class="ps-info">
						<div class="ps-label">Full Payment Bookings</div>
						<div class="ps-value"><?php echo number_format($fullPaymentCount); ?></div>
					</div>
					<div class="ps-sub">
						<div class="ps-sub-row">
							<span class="k">Total plot amount</span>
							<span class="v">PKR <?php echo number_format($fullPaymentPlotAmount); ?></span>
						</div>
						<div class="ps-sub-row">
							<span class="k">Total paid</span>
							<span class="v">PKR <?php echo number_format($fullPaymentPaidAmount); ?></span>
						</div>
					</div>
				</div>
				<div class="ps-tile-foot">
					<a href="javascript:void(0)" class="ps-block-trigger" data-block-type="full">View by block <i class="fa fa-angle-right"></i></a>
				</div>
			</div>
		</div>

		<div class="col-lg-3 col-md-3 col-sm-6">
			<div class="ps-tile ps-c5 ps-clickable" data-block-type="remaining" title="Click to see block-wise count">
				<div class="ps-tile-top">
					<div class="ps-icon"><i class="fa fa-clock-o"></i></div>
					<div class="ps-info">
						<div class="ps-label">Remaining Amount</div>
						<div class="ps-value"><?php echo number_format($remainingCount); ?></div>
					</div>
					<div class="ps-sub">
						<div class="ps-sub-row">
							<span class="k">Bookings with balance</span>
							<span class="v"><?php echo number_format($remainingCount); ?></span>
						</div>
						<div class="ps-sub-row">
							<span class="k">Remaining total</span>
							<span class="v">PKR <?php echo number_format($remainingAmountTotal); ?></span>
						</div>
					</div>
				</div>
				<div class="ps-tile-foot">
					<a href="javascript:void(0)" class="ps-block-trigger" data-block-type="remaining">View by block <i class="fa fa-angle-right"></i></a>
				</div>
			</div>
		</div>

		<div class="col-lg-3 col-md-3 col-sm-6">
			<div class="ps-tile ps-c6 ps-clickable" data-block-type="due" title="Click to see block-wise count">
				<div class="ps-tile-top">
					<div class="ps-icon"><i class="fa fa-warning"></i></div>
					<div class="ps-info">
						<div class="ps-label">Bookings With Due</div>
						<div class="ps-value"><?php echo number_format($dueCount); ?></div>
					</div>
					<div class="ps-sub">
						<div class="ps-sub-row">
							<span class="k">Overdue bookings</span>
							<span class="v"><?php echo number_format($dueCount); ?></span>
						</div>	
						<div class="ps-sub-row">
							<span class="k">Due amount</span>
							<span class="v">PKR <?php echo number_format($dueAmountTotal); ?></span>
						</div>
					</div>
				</div>
				<div class="ps-tile-foot">
					<a href="javascript:void(0)" class="ps-block-trigger" data-block-type="due">View by block <i class="fa fa-angle-right"></i></a>
				</div>
			</div>
		</div>

		<div class="col-lg-3 col-md-3 col-sm-6">
			<div class="ps-tile ps-c8 ps-clickable" data-block-type="regular" title="Click to see block-wise count">
				<div class="ps-tile-top">
					<div class="ps-icon"><i class="fa fa-bookmark"></i></div>
					<div class="ps-info">
						<div class="ps-label">Regular Bookings</div>
						<div class="ps-value"><?php echo number_format($regularBookings); ?></div>
					</div>
					<div class="ps-sub">
						<div class="ps-sub-row">
							<span class="k">Bookings with balance</span>
							<span class="v"><?php echo number_format($remainingCount); ?></span>
						</div>	
						<div class="ps-sub-row">
							<span class="k">Bookings with Due amount</span>
							<span class="v"><?php echo number_format($dueCount); ?></span>
						</div>
					</div>
				</div>
				<div class="ps-tile-foot">
					<a href="javascript:void(0)" class="ps-block-trigger" data-block-type="regular">View by block <i class="fa fa-angle-right"></i></a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="psBlockModal" tabindex="-1" role="dialog" aria-labelledby="psBlockModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="psBlockModalLabel">By Block</h4>
			</div>
			<div class="modal-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered ps-block-table">
						<thead>
							<tr>
								<th>#</th>
								<th>Block</th>
								<th>Bookings</th>
							</tr>
						</thead>
						<tbody id="psBlockModalBody"></tbody>
						<tfoot>
							<tr>
								<th colspan="2">Total</th>
								<th id="psBlockModalTotal">0</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
(function ($) {
	var blockData = <?php echo CJSON::encode($blockBreakdown); ?>;

	function openBlockPopup(type) {
		var info = blockData[type] || { title: 'By Block', rows: {}, total: 0 };
		var rows = info.rows || {};
		var html = '';
		var i = 0;
		var sum = 0;

		$.each(rows, function (block, count) {
			i++;
			sum += parseInt(count, 10) || 0;
			html += '<tr><td>' + i + '</td><td>' + $('<div/>').text(block).html() + '</td><td>' + count + '</td></tr>';
		});

		if (!html) {
			html = '<tr><td colspan="3" class="text-center">No bookings</td></tr>';
		}

		$('#psBlockModalLabel').text(info.title || 'By Block');
		$('#psBlockModalBody').html(html);
		$('#psBlockModalTotal').text(sum || info.total || 0);
		$('#psBlockModal').modal('show');
	}

	$(document).on('click', '.ps-clickable', function (e) {
		if ($(e.target).closest('a').length && !$(e.target).closest('.ps-block-trigger').length) {
			return;
		}
		e.preventDefault();
		openBlockPopup($(this).data('block-type'));
	});

	$(document).on('click', '.ps-block-trigger', function (e) {
		e.preventDefault();
		e.stopPropagation();
		openBlockPopup($(this).data('block-type'));
	});
})(jQuery);
</script>
