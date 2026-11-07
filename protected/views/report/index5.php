<?php
// function getInitials($name) {
//     $parts = explode(' ', trim($name));
//     $initials = '';
//     foreach ($parts as $p) {
//         if ($p !== '') {
//             $initials .= strtoupper($p[0]);
//         }
//     }
//     return $initials;
// }

function getInitials($name, $length = 6) {
    $name = trim($name);
    return strtoupper(substr($name, 0, $length));
}

$userModel = Yii::app()->session->get('userModel');
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Block Report</h1>
        <?php
            foreach(Yii::app()->user->getFlashes() as $key => $message) {
                echo '<div class="alert alert-info alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$message.'</div>';
            }
        ?>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Filter
            </div>
            <div class="panel-body">
                <div class="row">
                    <form role="form" method="POST" action="<?php echo Yii::app()->baseUrl?>/report/blocksearch">
                            <div class="form-group col-lg-2">
                                <label>Payment From</label>
                                <input class="form-control calenderr" name="start_date" value="<?php echo isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : ''; ?>" autocomplete="off" placeholder="From">
                            </div>
                            <div class="form-group col-lg-2">
                                <label>Payment To</label>
                                <input class="form-control calenderr" name="end_date" value="<?php echo isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : ''; ?>" autocomplete="off" placeholder="To">
                            </div>
                            <div class="form-group col-lg-2">
                                <label>Block Number</label>
                                <select name="mode" id="mode" class="form-control">
                                    <option value="">All</option>
                                    <?php foreach(@$modes as $mode):?>
                                        <option value="<?php echo $mode['block_number']?>" <?php echo (@$_POST['mode']==$mode['block_number'])?'selected':''?>><?php echo $mode['block_number']?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="form-group col-lg-3">
                                <label>Dealer</label>
                                <select name="agent[]" id="agent" class="form-control select2" required>
                                    <option value="all" <?php echo (!empty($_POST['agent']) && in_array('all', $_POST['agent'])) ? 'selected' : ''; ?>>All</option>
                                    <?php foreach ($agents as $agent): ?>
                                        <option value="<?php echo $agent->id; ?>"
                                            <?php 
                                            // Only select individual agents if 'all' is NOT in the array
                                            if (!empty($_POST['agent']) && is_array($_POST['agent']) && !in_array('all', $_POST['agent'])) {
                                                echo in_array($agent->id, $_POST['agent']) ? 'selected' : '';
                                            }
                                            ?>>
                                            <?php echo $agent->name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group col-lg-12">
                                <button type="submit" name="submit" value="report" class="btn btn-primary">Generate Report</button>
                            </div>
                        <!-- </div> -->
                            
                        </form>
                    </div>
                    <!-- /.col-lg-6 (nested) -->
                </div>
                <!-- /.row (nested) -->
            </div>
            <!-- /.panel-body -->
        </div>
        
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                All Report
                <!--<a href="<?php echo Yii::app()->baseUrl; ?>/report/callingsearch?due=1"><span class="label label-success">Due 1-3 Month</span></a>-->
                <!--<a href="<?php echo Yii::app()->baseUrl; ?>/report/callingsearch?due=3"><span class="label label-success">Due > 3 Month</span></a>-->
                <span class="pull-right"><a href="javascript:void(0)" id="report2btn"><span class="label label-success">Print</span></a></span>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body" id="tableBody">
                <table width="100%" class="table table-striped table-bordered table-hover" id="report2table">
                    <?php
                        $hasDateFilter = !empty($hasDateFilter);
                        $periodPaidMap = isset($periodPaidMap) ? $periodPaidMap : array();
                        $periodLabel = isset($periodLabel) ? $periodLabel : date('M, Y');
                        $txnBoxLabel = $hasDateFilter ? 'Selected Period Txns' : 'Current Month Txns';
                    ?>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>File #</th>
                            <th>Name</th>
                            <th>Contact #</th>
                            <!--<th>SQ.YDS.</th>-->
                            <th>Block</th>
                            <th>Plot</th>
                            <th>Total AMT</th>
                            <th>Paid AMT</th>
                            <th>Rem. AMT</th>
                            <th>Last Trans.</th>
                            <th>Dues</th>
                            <th>Dealer</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $totalBookings = 0;
                            $totalDueBookings = 0;
                            $periodTxnBookings = 0;
                        ?>
                        <?php $dueAmTot = 0;$i=0;if(@$bookings){ foreach($bookings as $ii => $data):
                            $tCost = $this->plotTotal($data->plot->id,false);
                            $plotBaseTotal = $this->finalViewBookingTotal($data->plot->id);
                            $pCost = intval(@$data->customerPlotTransactionSum);
                            $peCost = intval(@$data->customerPlotExtraTransactionSum);
                            $paidTotal = intval(@$data->customerPlotTransactionSum) + intval(@$data->customerPlotExtraTransactionSum);
                            $rem = ($plotBaseTotal['final_total'] - $paidTotal);
                            // Skip fully paid bookings (same as calling report)
                            if($rem <= 0 || $pCost == $tCost){
                                continue;
                            }
                            $totalBookings++;
                            $periodPaid = isset($periodPaidMap[(int)$data->id]) ? (float)$periodPaidMap[(int)$data->id] : 0;
                            $hasPeriodTxn = $periodPaid > 0;
                            if ($hasPeriodTxn) {
                                $periodTxnBookings++;
                            }
                        ?>
                            <?php
                                
                                $paymentSchedule = $this->getPaymentScheduleArray($data->id);
                                $monthlyDues = @$this->getMonthlyDue($data->monthly_start_date, @$paymentSchedule['monthly_installment']['amount']??0);
                                $dues = $this->calculateBookingDues($data->id);
                                
                                if (!empty($dues['due_amount']) && $dues['due_amount'] > 0) {
                                    $totalDueBookings++;
                                }

                                $dat = $data->getLastTransactionDetails();
                            ?>
                            <tr class="<?php echo $hasPeriodTxn ? 'current-month-txn' : ''; ?>">
                                <td><?php echo $i+1;?></td>
                                <td style="width:7%"><?php echo date('d-m-Y',strtotime($data->createdOn))?></td>
                                <td style="width:7%"><a target="_blank" href="<?php echo Yii::app()->baseUrl?>/booking/viewbooking/<?php echo $data->id?>"><?php echo 'GB-'.$data->id?></a></td>
                                <td style="width:10%;text-align:left"><?php echo $data->customer->name?></td>
                                <td style="width:10%"><?php echo $data->customer->mobile?></td>
                                <td><?php echo $data->plot->block_number?></td>
                                <td><?php echo $data->plot->plot_number?></td>
                                <td><?php echo number_format($plotBaseTotal['final_total']) ?></td>
                                <td><?php echo number_format($paidTotal); ?></td>
                                <td><?php echo number_format($rem)?></td>
                                <td style="width:10%">
                                    <?php
                                        if ($dat) {
                                            echo 'Rs. '.number_format($dat['amount']).'<br/>'.date('d M, Y',strtotime($dat['createdOn']));
                                        }
                                    ?>
                                </td>
                                <td style="width:10%">
                                    <?php echo '<b>'.number_format(@$dues['monthly_amount']).' * '.@$dues['due_months'].'</b><br/>'.'Rs. '.number_format(@$dues['due_amount'],2,'.',',');
                                        $dueAmTot = $dueAmTot + $dues['due_amount'];
                                    ?>
                                </td>
                                <td><?php echo getInitials(@$data->agent->name)?></td>
                                <td style="width:25%" colspan=2></td>
                            </tr>
                       <?php $i++; endforeach; }?>
                       <?php 
                        $totalPaidBookings = $totalBookings - $totalDueBookings;

                        $duePercentage = $totalBookings > 0? round(($totalDueBookings / $totalBookings) * 100, 2): 0;
                        $paidPercentage = $totalBookings > 0? round(($totalPaidBookings / $totalBookings) * 100, 2): 0;
                        $periodTxnPercentage = $totalBookings > 0? round(($periodTxnBookings / $totalBookings) * 100, 2): 0;
                       ?>
                       
                    </tbody>
                    <tr>
                        <td colspan="11"><b>Total Dues</b></td>
                        <td colspan="1"><b><?php echo 'Rs. '.number_format(@$dueAmTot)?></b></td>
                        <td colspan="2"></td>
                    </tr>
                </table>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <strong>Total Bookings:</strong>
                                <?php echo $totalBookings; ?>
                            </div>
                        </div>
                    </div>
                
                    <div class="col-lg-3">
                        <div class="panel panel-danger">
                            <div class="panel-body">
                                <strong>Due:</strong>
                                <?php echo $totalDueBookings; ?>
                                (<?php echo $duePercentage; ?>%)
                            </div>
                        </div>
                    </div>
                
                    <div class="col-lg-3">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <strong>Paid:</strong>
                                <?php echo $totalPaidBookings; ?>
                                (<?php echo $paidPercentage; ?>%)
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="panel panel-info" id="currentMonthTxnBox" style="cursor:pointer;" title="Click to highlight rows">
                            <div class="panel-body">
                                <strong><?php echo htmlspecialchars($txnBoxLabel); ?>:</strong>
                                <?php echo $periodTxnBookings; ?>
                                (<?php echo $periodTxnPercentage; ?>%)
                                <?php if ($hasDateFilter): ?>
                                    <br/><small><?php echo htmlspecialchars($periodLabel); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>

<script type="text/javascript">
  /*  $(document).on('click', '#report2btn', function (e) {
        
       var divToPrint=document.getElementById("tableBody");
       newWin= window.open("");
       var heading = '<div><h3>Calling Report<span style="margin-left:25%">Gulshan-e-Bholari</span><span style="float:right">Dated: <?php echo date('M, Y')?></span></h3></div>';
       var che = '';//'<div style="margin-top:100px;"><div style="width:25%;font-family:Arial, Helvetica, sans-serif;text-align:center;margin-right:10px;margin-top:15px;float:left;"><input type="text" style="border:0;border-bottom:solid 1px #000;width:100%;font-family:Arial, Helvetica, sans-serif;outline:0;"><label>Prepared By</label></div></div>';
       var rev = '';//'<div style="margin-left:35%;margin-top:100px;width:35%;"><div style="font-family:Arial, Helvetica, sans-serif;text-align:center;margin-right:10px;margin-top:15px;float:left;"><input type="text" style="border:0;border-bottom:solid 1px #000;width:100%;font-family:Arial, Helvetica, sans-serif;outline:0;"><label>Checked By</label></div></div>';
       var sign = '';//'<div style="margin-left:70%;margin-top:100px;width:50%"><div style="font-family:Arial, Helvetica, sans-serif;text-align:center;margin-right:10px;margin-top:15px;float:left;"><input type="text" style="border:0;border-bottom:solid 1px #000;width:100%;font-family:Arial, Helvetica, sans-serif;outline:0;"><label>Receiver`s Signature</label></div></div>';
       newWin.document.write('<style>table, th, td {border: 1px solid black;}</style>'+heading+divToPrint.outerHTML+che+rev+sign);
       newWin.print();
       newWin.close();
    });
*/
</script>

<style>
    #report2table tr.current-month-txn.highlighted td {
        background-color: #d9edf7 !important;
    }
    #currentMonthTxnBox.active {
        box-shadow: 0 0 0 2px #31708f;
    }
</style>

<script type="text/javascript">
$(document).on('click', '#report2btn', function (e) {

    var divToPrint = document.getElementById("tableBody");
    var newWin = window.open("");

    var heading = '<div><h3>Block Report<span style="margin-left:25%">Gulshan-e-Bholari</span><span style="float:right">Dated: <?php echo (!empty($_POST['start_date']) || !empty($_POST['end_date'])) ? htmlspecialchars(@$_POST['start_date'].' / '.@$_POST['end_date']) : date('M, Y'); ?></span></h3></div>';

    var style = `
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
                font-size:15px
            }
            table, th, td {
                border: 1px solid black;
            }
            th, td {
                text-align: center;
            }
            tr:nth-child(even) {background: #f9f9f9;}
        </style>
    `;

    newWin.document.write(
        style + heading + divToPrint.outerHTML
    );

    newWin.print();
    newWin.close();
});

$(document).on('click', '#currentMonthTxnBox', function () {
    var $rows = $('#report2table tr.current-month-txn');
    var isActive = $(this).toggleClass('active').hasClass('active');
    $rows.toggleClass('highlighted', isActive);

    if (isActive && $rows.length) {
        $('html, body').animate({
            scrollTop: $rows.first().offset().top - 80
        }, 300);
    }
});
</script>
