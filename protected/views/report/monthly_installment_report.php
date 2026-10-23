<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Dealer Report</h1>
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
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Filter
            </div>
            <div class="panel-body">
                <div class="row">
                    <form role="form" method="POST" action="<?php echo Yii::app()->baseUrl?>/report/monthlyinstallsearch">
                        <!-- <div class="col-lg-12"> -->
                            <!--<div class="form-group col-lg-2" >-->
                            <!--    <label>Start Date</label>-->
                            <!--    <input class="form-control calenderr" name="start_date" value="<?php echo @$_POST['start_date'] ?? date('Y-m-d')?>" autocomplete="off" required>-->
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            <!--</div>-->
                            <!--<div class="form-group col-lg-2">-->
                            <!--    <label>End Date</label>-->
                            <!--    <input class="form-control calenderr" name="end_date" value="<?php echo @$_POST['end_date'] ?? date('Y-m-d')?>" autocomplete="off"  required>-->
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            <!--</div>-->

                            <div class="form-group col-lg-2">
                                <label>Till Monthly Installment</label>
                                <select name="mode" id="mode" class="form-control">
                                    <option value="1" <?php echo (@$_POST['mode']==1)?'selected':''?>>Yes</option>
                                    <option value="0" <?php echo (@$_POST['mode']==0)?'selected':''?>>No</option>
                                </select>
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>
                            <div class="form-group col-lg-3">
                                <label>Dealer</label>
                                <select name="agent" id="agent" class="form-control select2" required>
                                    <option value="0">All</option>
                                    <?php foreach(@$agents as $agent):?>
                                        <option value="<?php echo $agent->id?>" <?php echo (@$_POST['agent']==$agent->id)?'selected':''?>><?php echo $agent->name?></option>
                                    <?php endforeach;?>
                                    <!-- <option value="full_payment">Full Payment</option> -->
                                </select>
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>

                            <!--<div class="form-group col-lg-2">-->
                            <!--    <label>Cheque #</label>-->
                            <!--    <input class="form-control" name="cheque" value="<?php //echo @$_POST['cheque']?>" autocomplete="off" > -->
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            <!--</div>-->

                            


                            <!--<div class="form-group col-lg-2">-->
                            <!--    <button type="submit" class="btn btn-success" style="margin-top: 9%;" name="submitBtn" value="1">Submit</button>-->
                            <!--    <button type="submit" class="btn btn-success" style="margin-top: 9%;" name="submitBtn" value="2">Booking Only</button>-->
                            <!--</div>-->
                            <div class="form-group col-lg-12">
                                <!--<button type="submit" class="btn btn-success">Submit</button>-->
                                <button type="submit" name="submitBtn" value="report" class="btn btn-primary">Generate Report</button>
                                <?php if($userModel['id'] == 39){?>
                                    <button type="submit" name="submit" value="csv" class="btn btn-success">Download CSV</button>
                                <?php } ?>
                                <!--<button type="submit" name="submitBtn" value="booking" class="btn btn-info">Booking Only</button>-->
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
                <span class="pull-right"><a href="javascript:void(0)" id="report2btn"><span class="label label-success">Print</span></a></span>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body" id="tableBody">
                <table width="100%" class="table table-striped table-bordered table-hover" id="report2table">
                    <thead>
                        <tr>
                            <th style="width: 5%">Sr #</th>
                            <th style="width: 5%">File #</th>
                            <th style="width: 15%">Plot #</th>
                            <th style="width: 30%">Client Name</th>
                            <th style="width: 30%">Payments<br/><small>Total/Paid</small></th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php $bookingids = [];$total = 0;$commission = 0;if(@$model){ foreach($model as $ind=>$datattttt):$data= CustomerPlotTransactions::model()->with('plot')->find('t.plot_id = :plot_id', array(':plot_id' => $datattttt['customer_booking_id'])); if(!empty($data->plot_id)){ $bookingids[] = @$data->plot_id;?>
                            <?php 
                                $netTotal = intval(@$data->plot->customerPlotTransactionSum);
                                $paid = $this->plotTotal($data->plot->plot->id);
                                
                                if(number_format($netTotal) != $paid){
                            ?>
                            <tr>
                                <td><?php echo $ind+1?></a></td>
                                <td><?php echo @$data->plot->id?></a></td>
                                <td><a href="<?php echo Yii::app()->baseUrl?>/booking/viewbooking/<?php echo @$data->plot->id?>"><?php echo @$data->plot->plot->block_number.' / '.@$data->plot->plot->plot_number?></a></td>
                                <td><?php echo @$data->plot->customer->name?></td>
                                <td><?php echo 'Rs. '.$paid.' / Rs. '.number_format($netTotal);?></td>
                                
                                
                            </tr>
                       <?php }} endforeach;}?>
                       
                    </tbody>
                </table>
                <?php $uniqueBookingids = array_unique($bookingids);?>
                
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>

<script type="text/javascript">
    $(document).on('click', '#report2btn', function (e) {
        
       var divToPrint=document.getElementById("tableBody");
       newWin= window.open("");
       var heading = '<div><h3>Gulshan-e-Bholari<span style="margin-left:25%">Gulshan-e-Bholari</span><span style="float:right">Dated: <?php echo @$_POST['start_date']?> / <?php echo @$_POST['end_date']?></span></h3></div>';
       var che = '<div style="margin-top:100px;"><div style="width:25%;font-family:Arial, Helvetica, sans-serif;text-align:center;margin-right:10px;margin-top:15px;float:left;"><input type="text" style="border:0;border-bottom:solid 1px #000;width:100%;font-family:Arial, Helvetica, sans-serif;outline:0;"><label>Prepared By</label></div></div>';
       var rev = '<div style="margin-left:35%;margin-top:100px;width:35%;"><div style="font-family:Arial, Helvetica, sans-serif;text-align:center;margin-right:10px;margin-top:15px;float:left;"><input type="text" style="border:0;border-bottom:solid 1px #000;width:100%;font-family:Arial, Helvetica, sans-serif;outline:0;"><label>Checked By</label></div></div>';
       var sign = '<div style="margin-left:70%;margin-top:100px;width:50%"><div style="font-family:Arial, Helvetica, sans-serif;text-align:center;margin-right:10px;margin-top:15px;float:left;"><input type="text" style="border:0;border-bottom:solid 1px #000;width:100%;font-family:Arial, Helvetica, sans-serif;outline:0;"><label>Receiver`s Signature</label></div></div>';
       newWin.document.write('<style>table, th, td {border: 1px solid black;}</style>'+heading+divToPrint.outerHTML+che+rev+sign);
       newWin.print();
       newWin.close();
    });

</script>