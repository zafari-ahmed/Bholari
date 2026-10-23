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

.ps-c1 { border-top: 3px solid #5b6b7a; }
.ps-c2 { border-top: 3px solid #1a9c8a; }
.ps-c3 { border-top: 3px solid #3f5bd9; }
.ps-c4 { border-top: 3px solid #1f9d57; }
.ps-c5 { border-top: 3px solid #d9891a; }
.ps-c6 { border-top: 3px solid #d13b55; }
</style>

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
		<div class="col-lg-4 col-md-4 col-sm-6">
			<div class="ps-tile ps-c1">
				<div class="ps-tile-top">
					<div class="ps-icon"><i class="fa fa-th"></i></div>
					<div class="ps-info">
						<div class="ps-label">Total Plots</div>
						<div class="ps-value"><?php echo number_format($totalPlots); ?></div>
					</div>
				</div>
				<div class="ps-tile-foot">
					<a href="<?php echo $baseUrl.'/plot'; ?>">View all plots <i class="fa fa-angle-right"></i></a>
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-4 col-sm-6">
			<div class="ps-tile ps-c2">
				<div class="ps-tile-top">
					<div class="ps-icon"><i class="fa fa-check"></i></div>
					<div class="ps-info">
						<div class="ps-label">Total Available Plots</div>
						<div class="ps-value"><?php echo number_format($activePlots); ?></div>
					</div>
				</div>
				<div class="ps-tile-foot">
					<a href="<?php echo $baseUrl.'/plot?status=0'; ?>">View available only <i class="fa fa-angle-right"></i></a>
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-4 col-sm-6">
			<div class="ps-tile ps-c3">
				<div class="ps-tile-top">
					<div class="ps-icon"><i class="fa fa-book"></i></div>
					<div class="ps-info">
						<div class="ps-label">Total Bookings</div>
						<div class="ps-value"><?php echo number_format($totalBookings); ?></div>
					</div>
				</div>
				<div class="ps-tile-foot">
					<a href="<?php echo $baseUrl.'/booking'; ?>">View all bookings <i class="fa fa-angle-right"></i></a>
				</div>
			</div>
		</div>
	</div>

	<div class="ps-section">Payment Status</div>
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-6">
			<div class="ps-tile ps-c4">
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
					<a href="<?php echo $baseUrl.'/booking'; ?>">View bookings <i class="fa fa-angle-right"></i></a>
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-4 col-sm-6">
			<div class="ps-tile ps-c5">
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
					<a href="<?php echo $baseUrl.'/booking'; ?>">View bookings <i class="fa fa-angle-right"></i></a>
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-4 col-sm-6">
			<div class="ps-tile ps-c6">
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
					<a href="<?php echo $baseUrl.'/booking'; ?><?php //echo $baseUrl.'/booking/getledgerdetail'; ?>">View dues <i class="fa fa-angle-right"></i></a>
				</div>
			</div>
		</div>
	</div>
</div>
