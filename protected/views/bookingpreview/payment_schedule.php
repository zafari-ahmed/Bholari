<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Payment Schedule - A4</title>
<style>
  @page { size: A4; margin: 12mm 14mm 16mm 14mm; }
  @media print { .page { box-shadow: none; } }
  body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; color:#000; }
  .page {
    width: 210mm; min-height: 297mm; margin: 0 auto;
    /*border: 1px solid #000; background:#fff; box-sizing: border-box;*/
  }
  .inner { padding: 50mm 16mm 16mm 16mm; }

  .row { display:flex; justify-content: space-between; align-items:flex-end; }
  .title { font-weight:700; letter-spacing:.3px; font-size:18px; text-transform:uppercase; }
  .subtitle { font-style: italic; font-size:14px; }

  /* Top stripe line under headings */
  /*.divline { height:1px; background:#000; }*/

  /* Mode table */
  .mode-grid {
    margin-top: 2mm;
    border: 1px solid #000;
    display: grid;
    grid-template-columns: 88mm 1fr;
    margin-bottom: 5px;
  }
  .mode-left, .mode-right { display:flex; align-items:center; justify-content:center; height:10mm; font-size:13px; }
  .mode-left { border-right:1px solid #000; font-weight:600; }
  .mode-right { }

  .schedule {
    border:1px solid #000; border-top:none;
    display:grid; grid-template-columns: 54mm 34mm 1fr;
  }
  .cell {
    min-height: 10mm; display:flex; align-items:center; justify-content:center;
    border-right:1px solid #000; border-top:1px solid #000; font-size:12px;
  }
  .cell:nth-child(3n) { border-right:none; justify-content:flex-start; padding-left:8mm; }
  .cell.label { font-weight:600; }
  .cell.subtle { font-style: italic; font-weight: 600; }
  .cell.amount { justify-content:center; padding-right:8mm; font-size: 15px;font-weight: bold;}

  /* Cost of plot row */
  .costrow {
    /*border:1px solid #000; border-top:none; display:grid; grid-template-columns: 1fr 50mm;*/
    margin-top: 2mm;
    border: 1px solid #000;
    display: grid;
    grid-template-columns: 88mm 1fr;
  }
  .costrow .left { padding:3mm 6mm; font-size:13px; text-align: center;font-weight: bold;}
  .costrow .right { border-left:1px solid #000; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; }

  /* Notes & extra charges */
  .two-col {
    display:grid; grid-template-columns: 1fr 1fr; column-gap: 14mm; margin-top: 10mm;
  }
  .box-title { font-weight:700; text-transform:uppercase; font-size:12.5px; text-decoration: underline; margin-bottom:3mm; }
  .note { font-size:12.5px; line-height: 1.55; }
  .charges { font-size:12.5px; font-weight:bold;}
  .charges .item { display:grid; grid-template-columns: 1fr 16mm; padding:1.5mm 0; }
  .charges .item .pct { text-align:right; }

  /* Bottom details */
  .details {
    margin-top: 0mm;
    /*border-top: 1px solid #000;*/
    padding-top: 5mm;
  }
  .grid4 {
    display:grid; grid-template-columns: 1fr 1fr; 
    column-gap: 24mm; 
    /*row-gap: 6mm;*/
    font-size:13px;
  }
  .line-field {
    display:grid; grid-template-columns: 20mm 1fr;
    column-gap: 6mm; align-items:end;
  }
  .line-field .lab { white-space:nowrap; font-size:12.5px; }
  .line-field .val {
    border-bottom:1px solid #000; min-height:7mm; display:flex; align-items:flex-end; padding-bottom:1mm; padding-left:3mm;
    font-weight:600;
  }
  .line-field .val.thin { font-weight:bold }

  .bottom-grid {
    display:grid; grid-template-columns: 1fr 1fr; column-gap: 18mm; margin-top: 7mm;
  }
  .money {
    font-size:14px; display:grid; grid-template-columns: 20mm 8mm 1fr; 
    /*row-gap:5mm; */
    align-items:end;
    padding: 17px;
  }
  .money .under {
    border-bottom:1px solid #000; min-height:7mm; display:flex; align-items:flex-end; padding-bottom:1mm; padding-left:3mm; justify-content:flex-start;font-weight:bold;
  }
  .accept {
    border:1px solid #000; 
    height:30mm; display:flex; 
    align-items:end; justify-content:center; 
    padding-bottom:3mm;
    font-size:12px;
  }
  .signature-line {
    margin-top: 16mm; display:flex; justify-content:center; align-items:center;
  }
  .signature-line .line { width: 60mm; border-top:1px solid #000; }
  .signature-line .txt { font-size:12.5px; margin-top:2mm; text-align:center; }
  
  .accept-wrapper {
    text-align: center;
    display: inline-block;
  }
  .accept-label {
    display: block;
    font-size: 12px;
    margin-bottom: 4px; /* space above the box */
  }
  .accept {
    border: 1px solid #000;
    width: 250px;  /* adjust as needed */
    height: 90px;  /* adjust as needed */
    margin: 0 auto;
  }
  .signature-wrapper {
    text-align: center;
    display: inline-block;
    margin-top: 20px;
  }
  .signature {
    border-top: 1px solid #000;
    width: 180px; /* adjust */
    margin: 0 auto;
    height: 0;
  }
  .signature-label {
    display: block;
    font-size: 12px;
    margin-top: 4px; /* space below the line */
  }
</style>
</head>
<body>
<div class="page">
  <div class="inner">

    <div class="row">
      <div class="title">PAYMENT SCHEDULE</div>
      <div class="subtitle"><b><?php echo strtoupper(@$booking->plot->category->name)?></b></div>
    </div>
    <div class="divline"></div>

    <!-- Mode of Payment header -->
    <div class="mode-grid">
      <div class="mode-left">MODE OF PAYMENT</div>
      <div class="mode-right"><b><?php echo (@$booking->plot->size->size)?></b></div>
    </div>

    <!-- Schedule table -->
    <!--<div class="schedule">-->
      <!-- BOOKING -->
    <!--  <div class="cell label">BOOKING</div>-->
    <!--  <div class="cell subtle">1st Payment</div>-->
    <!--  <div class="cell amount">8,000</div>-->

      <!-- YEARLY -->
    <!--  <div class="cell"></div>-->
    <!--  <div class="cell subtle">YEARLY</div>-->
    <!--  <div class="cell amount">15,000 × 4 &nbsp;&nbsp;&nbsp;&nbsp; 60,000</div>-->

      <!-- M.INSTALLMENT -->
    <!--  <div class="cell label">M.INSTALLMENT</div>-->
    <!--  <div class="cell subtle">Monthly</div>-->
    <!--  <div class="cell amount">8,000 × 55 &nbsp;&nbsp;&nbsp;&nbsp; 440,000</div>-->

      <!-- 2ND LAST -->
    <!--  <div class="cell label">2ND LAST</div>-->
    <!--  <div class="cell subtle">2ND LAST</div>-->
    <!--  <div class="cell amount">16,000</div>-->

      <!-- LAST PAYMENT -->
    <!--  <div class="cell label">LAST PAYMENT</div>-->
    <!--  <div class="cell subtle">Last Payment</div>-->
    <!--  <div class="cell amount">16,000</div>-->
    <!--</div>-->
    
    <!-- Schedule table -->
    <div class="schedule">
        <?php $cop=0;if(@$booking->payment_schedule_json){?>
            <?php 
                // Decode JSON to PHP array
                $data = json_decode($booking->payment_schedule_json, true);
                $cop = $data['cop'];
                foreach (@$data['rows'] as $row): ?>
              <div class="cell label">
                <?= ($row['heading1'] !== 'Empty Box') ? htmlspecialchars($row['heading1']) : '' ?>
              </div>
              <div class="cell subtle">
                <?= ($row['heading2'] !== 'Empty Box') ? htmlspecialchars($row['heading2']) : '' ?>
              </div>
              <div class="cell amount"><?= htmlspecialchars($row['value']) ?></div>
            <?php endforeach; ?>
        <?php } ?>
    </div>

    <!-- Cost of Plot -->
    <div class="costrow">
      <div class="left">COST OF PLOT</div>
      <div class="right"><?php echo number_format($cop)?></div>
    </div>




<?php $sEx = 0;$tpEx = $booking->plot->total;$tpp = $booking->plot->total-$booking->plot->discount;?>
    <!-- Notes and Extra Charges -->
    <div class="two-col">
      <div>
        <div class="box-title">Important Notes:</div>
        <div class="note">
          * Development Charges, Documentation Charges, Lease,
          &nbsp;&nbsp;Connection Charges of Gas/Electricity, Transformer, etc
          &nbsp;&nbsp;will be charged extra as and when demanded.
        </div>
      </div>
      <div>
        <div class="box-title">Extra Charges</div>
        <div class="charges">
          <!--<div class="item"><div>Main Boulevard</div><div class="pct">10%</div></div>-->
          <!--<div class="item"><div>Park Facing</div><div class="pct">10%</div></div>-->
          <!--<div class="item"><div>Corner</div><div class="pct">10%</div></div>-->
          <!--<div class="item"><div>West Open</div><div class="pct">10%</div></div>-->
          
          <?php if($booking->plot->is_corner == 1){?>
                <?php $tpp += $this->Percentage($booking->plot->total,$booking->plot->is_corner_amount,false)?>
                <?php $tpEx += $this->Percentage($booking->plot->total,$booking->plot->is_corner_amount,false)?>
                <div class="item"><div>Corner</div><div class="pct"><?php echo @$booking->plot->is_corner_amount?>%</div></div>
            <?php } ?>
            <?php if($booking->plot->is_road_facing == 1){?>
                <?php $tpp += $this->Percentage($booking->plot->total,$booking->plot->is_road_facing_amount,false)?>
                <?php $tpEx += $this->Percentage($booking->plot->total,$booking->plot->is_road_facing_amount,false)?>
                <div class="item"><div>Road Facing</div><div class="pct"><?php echo @$booking->plot->is_road_facing_amount?>%</div></div>
            <?php } ?>
            <?php if($booking->plot->is_park_facing == 1){?>
                <?php $tpp += $this->Percentage($booking->plot->total,$booking->plot->is_park_facing_amount,false)?>
                <?php $tpEx += $this->Percentage($booking->plot->total,$booking->plot->is_park_facing_amount,false)?>
                <div class="item"><div>Park Facing</div><div class="pct"><?php echo @$booking->plot->is_park_facing_amount?>%</div></div>
            <?php } ?>
            <?php if($booking->plot->is_west_open == 1){?>
                <?php $tpp += $this->Percentage($booking->plot->total,$booking->plot->is_west_open_amount,false)?>
                <?php $tpEx += $this->Percentage($booking->plot->total,$booking->plot->is_west_open_amount,false)?>
                <div class="item"><div>West Open</div><div class="pct"><?php echo @$booking->plot->is_west_open_amount?>%</div></div>
            <?php } ?>
        </div>
        <div class="item" style="position:absolute;width:17%;display:flex">
            <div>Discount</div>
            <div class="pct" style="position:absolute;padding-left:90%"><?php echo number_format(@$booking->plot->discount)?></div></div>
      </div>
    </div>

    <!-- Lower details -->
    <div class="details">
      <div class="grid5">
        <div class="line-field"><div class="lab">Name:</div><div class="val"><?php echo @$booking->customer->name?></div></div>
        </div>
    <div class="grid4">
        <div class="line-field"><div class="lab">Plot No.</div><div class="val thin"><?php echo @$booking->plot->plot_number?></div></div>
        <div class="line-field"><div class="lab">Block No.</div><div class="val thin"><?php echo @$booking->plot->block_number?></div></div>

        <div class="line-field"><div class="lab">Category:</div><div class="val thin"><?php echo @$booking->plot->category->name?></div></div>
        <div class="line-field"><div class="lab">Size:</div><div class="val thin"><?php echo @$booking->plot->size->size?></div></div>
      </div>



      <div class="bottom-grid">
        <div class="money">
          <div>Cost:</div><div>Rs:</div><div class="under"><?php echo 'Rs. '.number_format($tpEx)?></div>
          <div>Discount:</div><div>Rs:</div><div class="under"><?php echo 'Rs. '.number_format($booking->plot->discount)?></div>
          <div><b>Net Cost:</b></div><div>Rs:</div><div class="under"><?php echo 'Rs. '.number_format($tpp)?></div>
        </div>
        <!--<span>Read, Understood & Accepted</span>-->
        <!--<div class="accept"></div>-->
        <!--<span>Signature</span>-->
        <div class="accept-wrapper">
  <span class="accept-label">Read, Understood &amp; Accepted</span>
  <div class="accept"></div>
  <span class="signature-label">Signature</span>
</div>

      <!--<div class="signature-line">-->
      <!--  <div>-->
      <!--    <div class="line"></div>-->
      <!--    <div class="txt">Signature</div>-->
      <!--  </div>-->
      <!--</div>-->
    </div>

  </div>
</div>
</body>
</html>
