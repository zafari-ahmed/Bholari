<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Customer Ledger - Print</title>
  <style>
    /* -------- A4 PRINT SETUP -------- */
    /*@page { size: A4; margin: 10mm; }*/
    /*@page { size: A4; }*/
    body { margin-top: 50px; font-family: Calibri, Arial, sans-serif; color: #000; }
    .page {
      width: 190mm; /* A4 width (210) - 2*10mm margin */
      margin: 0 auto;
    }
    td.label.bold{
        font-size:15px;
    }


    /* Default: applies to page 2 and onward */
@page {
    size: A4;
    margin-top: 35mm;   /* extra top margin from page 2 */
}

/* First page only */
@page :first {
    margin-top: 10mm;  /* normal top margin on first page */
}
    /* -------- TYPOGRAPHY -------- */
    .title {
      text-align: center;
      font-size: 18px;
      font-weight: 700;
      letter-spacing: .5px;
      margin: 6mm 0 4mm;
    }
    .small { font-size: 12px; }
    .bold { font-weight: 700; }
    .muted { color: #222; }

    /* -------- TOP SUMMARY GRID -------- */
    .top-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8mm;
      margin-bottom: 4mm;
    }
    .info-table {
      width: 120%;
      border-collapse: collapse;
      font-size: 12px;
    }
    .info-table td {
      /*padding: 2.2mm 2mm;*/
      /*vertical-align: top;*/
    }
    .label {
      width: 28mm;
      white-space: nowrap;
      color: #000;
    }
    .value {
      font-weight: 700;
      font-size: 14px;
    }
    .value.normal {
      font-weight: 400;
      font-size: 12px;
    }
    .right-table .label { width: 30mm; }
    .right-table .value { text-align: left; }

    /* -------- STATUS ROW -------- */
    .status-row {
      display: flex;
      justify-content: flex-end;
      gap: 6mm;
      font-size: 12px;
      margin: 2mm 0 3mm;
    }
    .status-pill {
      padding: 1mm 3mm;
      border: 1px solid #000;
      border-radius: 2mm;
      font-weight: 700;
    }

    /* -------- LEDGER TABLE -------- */
    table.ledger {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
      font-size: 12px;
    }

    /* Column widths roughly matching Excel proportions */
    table.ledger col.date   { width: 18%; }
    table.ledger col.part   { width: 54%; } /* merged B-F feel */
    table.ledger col.dr     { width: 10%; }
    table.ledger col.cr     { width: 10%; }
    table.ledger col.bal    { width: 8%; }

    .ledger thead th {
      padding: 2mm 1.5mm;
      /*text-align: center;*/
      font-weight: 700;
      border-top: 1px solid #000;     /* thin */
      border-bottom: 3px double #000; /* double like Excel */
    }
    .ledger tbody td {
      padding: 2mm 1.5mm;
      border-bottom: 1px solid #000;
      vertical-align: top;
    }
    .ledger td.date { text-align: left; white-space: nowrap; }
    .ledger td.part { text-align: left; }
    .ledger td.num  { text-align: right; white-space: nowrap; }

    /* Lighter blank spacer rows if needed */
    .ledger tbody tr.spacer td { border-bottom: none; padding: 1.5mm 1.5mm; }

    /* Grand total row */
    .grand-total td {
      font-weight: 700;
      border-top: 2px solid #000;
      border-bottom: 2px solid #000;
      padding-top: 2.5mm;
      padding-bottom: 2.5mm;
    }
    .grand-label {
      text-align: right;
      padding-right: 3mm !important;
    }

    /* Optional: keep table header visible on each printed page */
    thead { display: table-header-group; }
    tfoot { display: table-row-group; }

    /* Optional: prevent row breaking awkwardly */
    tr { page-break-inside: avoid; }
    
  </style>
</head>
<body>
  <div class="page" style="margin-top:50px">

    <!--<div class="title">-->
    <!--    <div class="header">-->
    <!--        <div style="overflow:hidden;">-->
    <!--            <div style="    width: 50%;">-->
    <!--                <h3>SHINE START BUILDER & DEVELOPER</h3>-->
    <!--            </div>-->
    <!--            <div style="width: 30%;float: left;text-align: center;position: relative;left: 65%;">-->
    <!--                <h3>GULSHAN-E-BHOLARI HOUSING SCHEME</h3>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    <div class="title">
        <div class="header">
            <img src="<?php echo Yii::app()->baseUrl?>/images/GB1-B-resized.png" style="    position: absolute;z-index: 999;width: 80%;margin-left: -40%;margin-top: 35%;opacity: 0.1;">
            <div style="overflow:hidden;" class="hide">
                <div style="    width: 10%;float: left;position: relative;left: -20%;">
                    <img src="<?php echo Yii::app()->baseUrl?>/images/SS-B-resized.png" style="max-width: 120%;margin-top: 1px;margin-left: 200%;">
                </div>
                <div style="width: 30%;float: left;text-align: center;position: relative;left: 65%;">
                    <img src="<?php echo Yii::app()->baseUrl?>/images/GB-B-resized.png" style="    max-width: 50%;margin-top: 5px;margin-left: -115px;">
                </div>
            </div>
        </div>
    </div>
    
    <div class="top-grid">
      <!-- LEFT: Customer Info -->
      <p style="    position: absolute;top: 2%;left: 10%;"><b>SHINE START BUILDER & DEVELOPER</b></p>
      <table class="info-table left-table" style="display:table-row-group;line-height:20px;border: 1px solid;border-radius: 6px;padding: 10px;">
        <tr>
          <td style="font-weight: bold;width: 120px;">FILE NO:</td>
          <td class="value"><?php echo $this->getBookingRegNo(@$booking->id)?></td>
        </tr>
        <tr>
          <td style="font-weight: bold;width: 110px;">NAME:</td>
          <td class="value normal"><?php echo @$booking->customer->name?></td>
        </tr>
        <tr>
          <td style="font-weight: bold;width: 110px;">FATHER/HUSBAND:</td>
          <td class="value normal"><?php echo @$booking->customer->father_husband_name?></td>
        </tr>
        <tr>
          <td style="font-weight: bold;width: 110px;">CNIC #.:</td>
          <td class="value normal"><?php echo @$booking->customer->cnic?></td>
        </tr>
        <tr>
          <td style="font-weight: bold;width: 110px;">MOBILE #.:</td>
          <td class="value normal"><?php echo @$booking->customer->mobile?></td>
        </tr>
        <tr>
          <td style="font-weight: bold;width: 110px;">NOMINEE:</td>
          <td class="value normal"><?php echo @$booking->customer->nominee_name?> / <?php echo @$booking->customer->nominee_relation?></td>
        </tr>
        <tr>
          <td style="font-weight: bold;width: 110px;">ADDRESS:</td>
          <td class="value normal"><p style="    line-height: normal;"><?php echo @$booking->customer->address?></p></td>
        </tr>
        
        <tr>
          <td style="font-weight: bold;width: 110px;">BOOKED BY:</td>
          <td class="value normal" s><?php echo @$booking->agent->name?></td>
        </tr>
      </table>
    <?php $bookingDues = $this->calculateBookingDues($booking->id);?>
    <?php $cop=0;if(@$booking->payment_schedule_json){?>
    <?php 
        // Decode JSON to PHP array
        $data = json_decode($booking->payment_schedule_json, true);
        $cop = $data['cop'];
    ?>
    <?php }?>
    <?php $tpp = $booking->plot->total-$booking->plot->discount;?>
    <?php if($booking->plot->is_road_facing == 1 || $booking->plot->is_park_facing == 1 || $booking->plot->is_corner == 1 || $booking->plot->is_west_open == 1){?>
        <?php if($booking->plot->is_corner == 1){?>
            <?php $tpp += $this->Percentage($booking->plot->total,$booking->plot->is_corner_amount,false)?>
        <?php } ?>

        <?php if($booking->plot->is_road_facing == 1){?>
            <?php $tpp += $this->Percentage($booking->plot->total,$booking->plot->is_road_facing_amount,false)?>
        <?php } ?>
        
        <?php if($booking->plot->is_park_facing == 1){?>
            <?php $tpp += $this->Percentage($booking->plot->total,$booking->plot->is_park_facing_amount,false)?>
        <?php } ?>
        <?php if($booking->plot->is_west_open == 1){?>
            <?php $tpp += $this->Percentage($booking->plot->total,$booking->plot->is_west_open_amount,false)?>
        <?php } ?>
    <?php }?>
      <!-- RIGHT: Plot / Cost Info -->
      <p style="    position: absolute;top: 2%;right: 7%;"><b>GULSHAN-E-BHOLARI HOUSING SCHEME</b></p>
      <table class="info-table right-table" style="display:table-row-group;line-height:20px;margin-left:37px;border: 1px solid;border-radius: 6px;padding: 10px;width:90%">
        <tr>
          <td style="font-weight: bold;width: 110px;width: 70%;">PLOT NO.:</td>
          <td class="value normal"><b><?php echo @$booking->plot->block_number.' - '.@$booking->plot->plot_number?></b></td>
        </tr>
        <tr>
          <td style="font-weight: bold;width: 110px;width: 65%;">PLOT SIZE:</td>
          <td class="value normal"><?php echo @$booking->plot->size->size?></td>
        </tr>
        <?php
            $extras = [];
            
            if ($booking->plot->is_corner == 1) {
                $extras[] = 'Corner';
            }
            if ($booking->plot->is_road_facing == 1) {
                $extras[] = 'Road Facing';
            }
            if ($booking->plot->is_park_facing == 1) {
                $extras[] = 'Park Facing';
            }
            if ($booking->plot->is_west_open == 1) {
                $extras[] = 'West Open';
            }
        ?>
        <tr>
          <td style="font-weight: bold;width: 110px;width: 65%;">EXTRA:</td>
          <td class="value normal" colspan="2" style="font-size:9px"><b><?php echo implode(', ', $extras); ?></b></td>
        </tr>
        <tr>
          <td style="font-weight: bold;width: 110px;width: 65%;">TOTAL COST:</td>
          <td class="value normal" colspan="2"><b><?php echo number_format(@$tpp)?> PKR</b></td>
        </tr>
        <tr>
          <td style="font-weight: bold;width: 110px;width: 65%;">DISCOUNT:</td>
          <td class="value normal" colspan="2"><b><?php echo number_format(@$booking->plot->discount)?> PKR</b></td>
        </tr>
        <tr>
          <td style="font-weight: bold;width: 110px;width: 65%;">PAID AMOUNT:</td>
          <td class="value normal" colspan="2"><b><?php echo number_format($booking->customerPlotTransactionSum + $booking->customerPlotExtraTransactionSum)?> PKR</b></td>
        </tr>
        <tr>
          <td style="font-weight: bold;width: 110px;">DUE MONTH:</td>
          <td class="value normal" colspan="2"><b><?php echo @$bookingDues['due_months']?> Month(s)</b></td>
        </tr>
        <tr>
          <td style="font-weight: bold;width: 110px;">DUE AMOUNT:</td>
          <td class="value normal" colspan="2"><b><?php echo number_format(@$bookingDues['due_amount'])?> PKR</b></td>
        </tr>
        <tr>
          <td style="font-weight: bold;width: 110px;">BALANCE AMOUNT:</td>
          <td class="value normal" colspan="2"><b><?php echo number_format($tpp-@$booking->customerPlotTransactionSum - @$booking->plot->discount + $booking->customerPlotExtraTransactionSum)?> PKR</b></td>
        </tr>
        
        <tr>
          <td style="font-weight: bold;width: 110px;">STATUS:</td>
          <td class="value normal" colspan="2"><b>BOOKED</b></td>
        </tr>
        <!--<tr>-->
        <!--  <td class="label bold">BOOKED BY:</td>-->
        <!--  <td class="value normal" colspan="2">{{bookedBy}}</td>-->
        <!--</tr>-->
      </table>
    </div>

    <!--<div class="status-row">-->
    <!--  <div class="small bold">STATUS</div>-->
    <!--  <div class="status-pill">Booked</div>-->
    <!--</div>-->

    <table class="ledger table-bordered">
      <thead>
        <tr>
            <th>Mode</th>
            <th>Trans #</th>
            <th>Trans Type</th>
            <th>Amount</th>
            <!--<th>Bank/Branch</th>-->
            <th>Ref #</th>
            <th>Date</th>
        </tr>
      </thead>

      <tbody>
          <?php
                            $allTransactions = [];
                            
                            /* keep source info */
                            if (!empty($booking->customerPlotTransactions)) {
                                foreach ($booking->customerPlotTransactions as $t) {
                                    $t->_source = 'normal';
                                    $allTransactions[] = $t;
                                }
                            }
                            
                            if (!empty($booking->customerPlotExtraTransactionCustomLogic)) {
                                foreach ($booking->customerPlotExtraTransactionCustomLogic as $t) {
                                    $t->_source = 'extra';
                                    $allTransactions[] = $t;
                                }
                            }
                            
                            /* sort globally */
                            usort($allTransactions, function ($a, $b) {
                                return strnatcmp(
                                    ltrim($a->transaction_number, '#0'),
                                    ltrim($b->transaction_number, '#0')
                                );
                            });
                        ?>
                        
                        <?php
                            $agentComArray = ['booking','confirmation','allocation'];
                            $agentComTotal = 0;
                            $cpttTotal = 0;
                            
                            foreach ($allTransactions as $txn):
                            ?>
                            <tr>
                                <td style="width: 20%;">
                                    <?php
                                    if ($txn->_source === 'normal') {
                                        //echo ucfirst(@$txn->plotPaymentMode->mode.''.(($txn->monthlyDate!='')?' ('.$txn->monthlyDate.')':''));
                                        echo ucfirst(@$txn->plotPaymentMode->mode);
                                    } else {
                                        echo ucfirst(@$txn->plot_payment_mode);
                                    }
                                    ?>
                            
                                    <?php if ($txn->_source === 'normal' && $txn->plotPaymentMode->mode=='monthly'){ ?>
                                        <br><span style="font-size:10px;">
                                            <?php echo $this->getPlotLedgerDetailSingle(@$booking->id,'monthly',false,$txn->id)?>
                                        </span>
                                    <?php } ?>
                            
                                    <?php if ($txn->_source === 'normal' && $txn->plotPaymentMode->mode=='yearly'){ ?>
                                        <br><span style="font-size:10px;">
                                            <?php echo $this->getPlotLedgerDetailSingle(@$booking->id,'yearly',false,$txn->id)?>
                                        </span>
                                    <?php } ?>
                                </td>
                            
                                <td><?php echo ($this->startsWith($txn->transaction_number, '#')) ? $txn->transaction_number : '#'.ltrim($txn->transaction_number,0) ?></td>
                                <td><?php echo ucfirst($txn->transaction_type) ?></td>
                                <td><b><?php echo 'Rs. '.number_format($txn->amount) ?></b></td>
                            
                                <?php
                                if ($txn->_source === 'normal' && in_array(strtolower($txn->plotPaymentMode->mode), $agentComArray)) {
                                    $agentComTotal += $txn->amount;
                                }
                            
                                if ($txn->_source === 'extra') {
                                    $cpttTotal += $txn->amount;
                                }
                                ?>
                            
                                <!--<td><?php //echo ($txn->bank!='') ? $txn->bank.' - '.$txn->branch : '-' ?></td>-->
                                <td><?php echo $txn->reference_number ?></td>
                                <td><?php echo date('d M,Y',strtotime($txn->createdOn)) ?></td>
                            </tr>
                        <?php endforeach; ?>
        <!-- Example opening row (like your sheet has POST / BOOKING...) -->
        <?php /*?>
        <tr>
          <td class="date">01.02.2025</td>
          <td class="part">
            <span class="bold">POST</span> &nbsp; BOOK/ALLO/CONF/COMP.INST/
            <div class="muted small">PRE LAUNCHING SCHEDULE</div>
          </td>
          <td class="num">0</td>
          <td class="num">0</td>
          <td class="num">{{openingBalance}}</td>
        </tr>

        <!-- Example transaction rows -->
        <tr>
          <td class="date">01.02.2025</td>
          <td class="part">REC &nbsp; | &nbsp; RCT-1818 &nbsp; | &nbsp; BOOKING</td>
          <td class="num">0</td>
          <td class="num">30,000</td>
          <td class="num">{{bal1}}</td>
        </tr>

        <tr>
          <td class="date">24-05-2025</td>
          <td class="part">REC &nbsp; | &nbsp; RCT-5085 &nbsp; | &nbsp; BOOKING</td>
          <td class="num">0</td>
          <td class="num">30,000</td>
          <td class="num">{{bal2}}</td>
        </tr>

        <tr>
          <td class="date">23-07-2025</td>
          <td class="part">REC &nbsp; | &nbsp; RCT-7082 &nbsp; | &nbsp; BOOKING</td>
          <td class="num">0</td>
          <td class="num">20,000</td>
          <td class="num">{{bal3}}</td>
        </tr>

        <tr>
          <td class="date">06-12-2025</td>
          <td class="part">REC &nbsp; | &nbsp; RCT-15479 &nbsp; | &nbsp; BOOKING/ALLOCATION</td>
          <td class="num">0</td>
          <td class="num">85,000</td>
          <td class="num">{{bal4}}</td>
        </tr>
        <tr>
          <td class="date">06-12-2025</td>
          <td class="part">REC &nbsp; | &nbsp; RCT-15479 &nbsp; | &nbsp; BOOKING/ALLOCATION</td>
          <td class="num">0</td>
          <td class="num">85,000</td>
          <td class="num">{{bal4}}</td>
        </tr>
        <tr>
          <td class="date">06-12-2025</td>
          <td class="part">REC &nbsp; | &nbsp; RCT-15479 &nbsp; | &nbsp; BOOKING/ALLOCATION</td>
          <td class="num">0</td>
          <td class="num">85,000</td>
          <td class="num">{{bal4}}</td>
        </tr>
        <tr>
          <td class="date">06-12-2025</td>
          <td class="part">REC &nbsp; | &nbsp; RCT-15479 &nbsp; | &nbsp; BOOKING/ALLOCATION</td>
          <td class="num">0</td>
          <td class="num">85,000</td>
          <td class="num">{{bal4}}</td>
        </tr>
        <tr>
          <td class="date">06-12-2025</td>
          <td class="part">REC &nbsp; | &nbsp; RCT-15479 &nbsp; | &nbsp; BOOKING/ALLOCATION</td>
          <td class="num">0</td>
          <td class="num">85,000</td>
          <td class="num">{{bal4}}</td>
        </tr>
        <tr>
          <td class="date">06-12-2025</td>
          <td class="part">REC &nbsp; | &nbsp; RCT-15479 &nbsp; | &nbsp; BOOKING/ALLOCATION</td>
          <td class="num">0</td>
          <td class="num">85,000</td>
          <td class="num">{{bal4}}</td>
        </tr>

        <!-- Add more rows here dynamically -->
        <!--
        <tr>
          <td class="date">{{date}}</td>
          <td class="part">{{particulars}}</td>
          <td class="num">{{dr}}</td>
          <td class="num">{{cr}}</td>
          <td class="num">{{balance}}</td>
        </tr>
        -->

        <!-- Grand Total -->
        <tr class="grand-total">
          <td colspan="2" class="grand-label">GRAND TOTAL:</td>
          <td class="num">{{totalDr}}</td>
          <td class="num">{{totalCr}}</td>
          <td class="num">{{grandBalance}}</td>
        </tr><?php */?>
      </tbody>
    </table>

  </div>
<script type="text/javascript">
    window.print();
</script>
</body>
</html>