<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Cancelled Booking</h1>
        <?php
            foreach(Yii::app()->user->getFlashes() as $key => $message) {
                echo '<div class="alert alert-info alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$message.'</div>';
            }
        ?>
    </div>
    <!-- /.col-lg-12 -->
</div>
<?php $userModel = Yii::app()->session->get('userModel'); ?>
<!-- /.row -->
<div class="row">

    <?php if(@$cancelled){?>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Filter
            </div>
            <div class="panel-body">
                <div class="row">
                    <form role="form" method="GET" action="<?php echo Yii::app()->baseUrl?>/report/cancelled">
                        <!-- <div class="col-lg-12"> -->
                            <div class="form-group col-lg-2" >
                                <label>Start Date</label>
                                <input class="form-control calenderr" name="start_date" value="<?php echo @$_GET['start_date'] ?? date('Y-m-d')?>" autocomplete="off" required>
                            </div>
                            <div class="form-group col-lg-2">
                                <label>End Date</label>
                                <input class="form-control calenderr" name="end_date" value="<?php echo @$_GET['end_date'] ?? date('Y-m-d')?>" autocomplete="off"  required>
                            </div>

                            <div class="form-group col-lg-12">
                                <button type="submit" name="submitBtn" value="report" class="btn btn-primary">Generate Report</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.col-lg-6 (nested) -->
                </div>
                <!-- /.row (nested) -->
            </div>
            <!-- /.panel-body -->
        </div>
        </div>
        <?php
        $params = [];
        
        if (isset($_GET['start_date']) && $_GET['start_date'] != '') {
            $params['start_date'] = $_GET['start_date'];
        }
        
        if (isset($_GET['end_date']) && $_GET['end_date'] != '') {
            $params['end_date'] = $_GET['end_date'];
        }
        
        $csvUrl = Yii::app()->baseUrl . '/report/cancelledcsv';
        
        if (!empty($params)) {
            $csvUrl .= '?' . http_build_query($params);
        }
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                Cancelled Booking Report
                <span class="pull-right">
                    <a href="javascript:void(0)" id="report2btn"><span class="label label-success">Print</span></a>&nbsp;&nbsp;
                    <a href="<?php echo $csvUrl; ?>"><span class="label label-success">Excel Report</span></a>&nbsp;&nbsp;
                </span>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body" id="tableBody">
                <table width="100%" class="table table-striped table-bordered table-hover" id="report2table" style="font-size: 12px">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer Name</th>
                            <th>Customer Number</th>
                            <th>Dealer</th>
                            <th>Block</th>
                            <th>Plot</th>
                            <th>Paid Amount</th>
                            <th>Deduct Amount</th>
                            <th>Cancelled Date</th>
                            <th>Reason</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php $ta = $pa = 0; if(@$cancelled){ foreach($cancelled as $data): ?>
                            <tr>
                                <td><a href="<?php echo Yii::app()->baseUrl?>/booking/viewbooking/<?php echo $data->booking->id?>"><?php echo $this->getBookingRegNo($data->booking->id)?></a></td>
                                <td><?php echo @$data->booking->customer->name?></td>
                                <td><?php echo @$data->booking->customer->mobile?></td>
                                <td><?php echo @$data->booking->agent->name?></td>
                                <td><?php echo @$data->booking->plot->block_number?></td>
                                <td><?php echo @$data->booking->plot->plot_type.' - '.$data->booking->plot->plot_number?></td>
                                <td><?php echo 'Rs. '.number_format(@$data->booking->customerPlotTransactionSum);?></td>
                                
                                <td><?php echo 'Rs. '.@number_format(@$data->amount)?></td>
                                <?php
                                $ta = $ta + @$data->amount;
                                $pa = $pa + @$data->booking->customerPlotTransactionSum;
                                //$paid = @$data->booking->customerPlotTransactionSum;
                                //$return = $paid - @$data->amt;
                                //$deduct = $paid - $return;

                                ?>
                                <td><?php echo date('d M,Y',strtotime(@$data->createdOn))?></td>
                                <td><?php echo @$data->reason?></td>
                                <!-- <td><?php //echo ($data->account_id==1)?('Rs. '.number_format($data->amount)):0?></td>
                                <td><?php //echo ($data->account_id==3)?('Rs. '.number_format($data->amount)):0?></td> -->
                                <!-- <td><?php //echo ($data->status==0)?'<span class="label label-danger">Pending</span>':'<span class="label label-success">Paid</span>'?></td> -->
                            </tr>
                       <?php endforeach;}?>
                    </tbody>
                    <tr style="text-align: center;font-weight: bold;">
                        <td colspan="6">Total</td>
                        <td style="width: 7%;"><?php echo 'Rs. '.number_format(@$pa)?></td>
                        <td style="width: 7%;"><?php echo 'Rs. '.number_format(@$ta)?></td>
                        <td colspan="2"></td>
                    </tr>
                </table>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <?php }?>
    <!-- /.col-lg-12 -->
</div>

<script type="text/javascript">
    $(document).on('click', '#report2btn', function (e) {
       var divToPrint=document.getElementById("tableBody");
       newWin= window.open("");
       var heading = '<div><h3>Gulshan-e-Bholari<span style="margin-left:25%">Gulshan-e-Bholari</span><span style="float:right">Cancelled Bookings</span></h3></div>';
       newWin.document.write('<style>table, th, td {border: 1px solid black;}</style>'+heading+divToPrint.outerHTML);
       newWin.print();
       newWin.close();
    });

</script>