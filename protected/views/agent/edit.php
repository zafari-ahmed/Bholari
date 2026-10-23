<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Dealers</h1>
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
                Edit Dealer
                <?php if(@$agent->parent_id){?>
                <span class="pull-right" style="margin-top: -5px;"><a href="<?php echo Yii::app()->baseUrl?>/agent/agentreport/<?php echo $agent->id?>" target="_blank"><button type="button" class="btn btn-success btn-sm">CSV Report</button></a></span>
                <?php }?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <form role="form" method="POST" action="<?php echo Yii::app()->baseUrl?>/agent/update">
                        <!-- <div class="col-lg-12"> -->
                            <div class="form-group col-lg-3" style="padding-right: 0px;">
                                <label>Parent Dealer</label>                                
                                <select name="parent_id" id="parent_id" class="form-control">
                                    <option value="">Please select Parent Dealer</option>
                                    <?php foreach($parents as $parent):?>
                                        <option value="<?php echo $parent->id?>" <?php echo ($agent->parent_id==$parent->id)?'selected':''?>><?php echo $parent->name?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>

                            <div class="form-group col-lg-6" >
                                <label>Full Name</label>
                                <input class="form-control" name="name" id="name" placeholder="Full Name" required="" value="<?php echo $agent->name?>">
                                <input name="id" value="<?php echo $agent->id?>" type="hidden">
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>

                            <div class="form-group col-lg-4">
                                <label>Number</label>
                                <input class="form-control" name="number" id="number" placeholder="Number" required="" value="<?php echo $agent->number?>">
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>

                            <div class="form-group col-lg-4">
                                <label>Percentage</label>
                                <input class="form-control" name="percentage" id="percentage" placeholder="Percentage" required="" type="text" value="<?php echo $agent->percentage?>">
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>

                            <div class="form-group col-lg-4">
                                <label>Percentage Value</label>
                                <input class="form-control" name="percentage_value" id="percentage" placeholder="Percentage" type="text" value="<?php echo $agent->percentage_value?>">
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>

                            <div class="col-md-12" id="agentPlotBox">
                                <h2>Plot Reserve Detail
                                    <span class="pull-right btn-lg"><a href="javascript:void(0)" id="report2btn"><span class="label label-success">Print</span></a></span>
                                </h2>
                                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Plot #</th>
                                        <th>Agent Commission</th>
                                        <!-- <th>Agent Commission Paid</th>
                                        <th>Agent Balance</th> -->
                                        <th>Plot Total / Paid</th>
                                        <th>Plot Attributes</th>
                                        <!-- <th>Status</th> -->
                                        <!--<th>Dues Desciption</th>-->
                                        <!--<th>Total Dues</th>-->
                                        <!--<th>Installments<br/> Paid Monthly/Yearly</th>-->
                                        <th>Status</th>
                                        <th class="btnn">Action</th>
                                        
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $tComission = 0; $tpComission = 0; $tbComission = 0; if($agent->agentPlotsActive){ foreach($agent->agentPlotsActive as $res):
                                    $det = [];
                                    $booking = null;
                                    if(@$res->plot && @$res->plot->customerPlots){
                                        $booking = @$res->plot->customerPlots[0];
                                        $det = $this->getPlotLedgerDetail(@$booking->id);    
                                    }
                                    
                                    // Same as booking view Total ($tpp) and Paid Total
                                    $plotTotalAmount = 0;
                                    $paidTotal = 0;
                                    if ($booking && @$booking->plot) {
                                        $plot = $booking->plot;
                                        $plotTotalAmount = floatval($plot->total) - floatval($plot->discount);
                                        if ((int)$plot->is_corner === 1) {
                                            $plotTotalAmount += floatval($this->Percentage($plot->total, $plot->is_corner_amount, 0));
                                        }
                                        if ((int)$plot->is_road_facing === 1) {
                                            $plotTotalAmount += floatval($this->Percentage($plot->total, $plot->is_road_facing_amount, 0));
                                        }
                                        if ((int)$plot->is_park_facing === 1) {
                                            $plotTotalAmount += floatval($this->Percentage($plot->total, $plot->is_park_facing_amount, 0));
                                        }
                                        if ((int)$plot->is_west_open === 1) {
                                            $plotTotalAmount += floatval($this->Percentage($plot->total, $plot->is_west_open_amount, 0));
                                        }
                                        $paidTotal = floatval(@$booking->customerPlotTransactionSum)
                                                   + floatval(@$booking->customerPlotExtraTransactionSum);
                                    } elseif (@$res->plot) {
                                        $plotBaseTotal = $this->finalViewBookingTotal($res->plot->id);
                                        $plotTotalAmount = $plotBaseTotal ? floatval($plotBaseTotal['final_total']) : 0;
                                    }

                                    $complete = ($booking && number_format($plotTotalAmount) == number_format($paidTotal))?1:0;
                                    $trasferred = 0;
                                    if(@$booking->plot->customerPlotTransfers){
                                        $trasferred = 1;
                                    }

                                    // Same as booking view: commission on Booking + Allocation + Confirmation
                                    $agentComTotal = 0;
                                    $agentCommission = 0;
                                    $agentCommissionPaid = 0;
                                    if ($booking) {
                                        $agentComTotal = floatval(@$booking->customerPlotTransactionSumBooking)
                                                       + floatval(@$booking->customerPlotTransactionSumAllocation)
                                                       + floatval(@$booking->customerPlotTransactionSumConfirmation);
                                        $agentCommission = floatval($this->Percentage($agentComTotal, @$booking->agent_percentage, 0));
                                        $agentCommissionPaid = floatval(@$booking->agent_commision_sum);
                                    }
                                    $agentCommissionBalance = $agentCommission - $agentCommissionPaid;
                                    ?>
                                    <tr>
                                        <td><?php echo $res->id?></td>
                                        <?php if($booking){?>
                                        
                                        <td><a href="<?php echo Yii::app()->baseUrl?>/booking/viewbooking/<?php echo @$booking->id?>"><?php echo '*'.@$res->plot->block_number.'-'.$res->plot->plot_type.'-'.$res->plot->plot_number?>*</a><br/><?php echo @$booking->customer->name?></td>
                                        <?php } else {?>
                                            <td><?php echo '*'.@$res->plot->block_number.'-'.@$res->plot->plot_type.'-'.@$res->plot->plot_number.'*'?></td>    
                                        <?php }?>
                                        
                                        <td><?php echo 'PKR '.number_format($agentCommission)?></td>
                                        <?php $tComission = @$tComission + $agentCommission?>
                                        <!-- <td><?php //echo 'PKR '.number_format($agentCommissionPaid)?></td>
                                        <?php $tpComission = $tpComission + $agentCommissionPaid?>
                                        <td><?php //echo 'PKR '.number_format($agentCommissionBalance)?></td>
                                        <?php $tbComission = $tbComission + $agentCommissionBalance?> -->
                                        <td><?php echo 'Rs. '.number_format($plotTotalAmount).' / Rs. '.number_format($paidTotal);?></td>
                                        <td>
                                            <table class="table table-responsive" style="FONT-SIZE: 12PX;">
                                                <tbody>
                                                    <tr>
                                                        <td class="tbBold">Corner</td>
                                                        <td><?php echo (@$res->plot->is_corner==1)?'<span class="label label-success" style="text-decoration: none;">YES</span>':'<span class="label label-danger" style="text-decoration: none;">No</span>'?></td>
                                                        
                                                    </tr>
                                                    <tr>
                                                        <td class="tbBold">Park Facing</td>
                                                        <td><?php echo (@$res->plot->is_park_facing==1)?'<span class="label label-success" style="text-decoration: none;">YES</span>':'<span class="label label-danger" style="text-decoration: none;">No</span>'?></td>
                                                        
                                                    </tr>
                                                    <tr>
                                                        <td class="tbBold">West Open</td>
                                                        <td><?php echo (@$res->plot->is_west_open==1)?'<span class="label label-success" style="text-decoration: none;">YES</span>':'<span class="label label-danger" style="text-decoration: none;">No</span>'?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <?php /*?>
                                        <td><?php echo @$det['html'];?></td>
                                        <td><b><?php echo 'PKR '.number_format(@$det['amount'],2,'.',',');?></b></td>
                                        <td><?php echo $this->getPlotLedgerDetailSingle(@$res->plot->customerPlots[0]->id,'monthly',false,false)?><hr/>
                                        <?php echo $this->getPlotLedgerDetailSingle(@$res->plot->customerPlots[0]->id,'yearly',false,false)?>
                                        </td>
                                        <?php */?>
                                        <td style="display: none;"><?php echo (@$res->plot->customerPlots)?'<span class="label label-danger" style="text-decoration: none;">Booked</span>':'<span class="label label-success" style="text-decoration: none;">Available</span>'?></td>
                                        
                                        
                                        <?php if(@$res->plot->customerPlots){?>
                                        <?php if(@$res->plot->customerPlots[0]->blocked != 1 && $res->plot->customerPlots[0]->blocked != 2){?> 
                                            <?php if(empty(@$res->plot->customerPlots[0]->customerPlotCancelled)){?>
                                            <td><?php echo (@$res->plot->customerPlots[0]->status==1)?(($complete==0)?'<span class="aLink label label-primary">Booked '.(($trasferred==1)?'(Transferred)':'').'</span>':'<span class="aLink label label-success">Completed</span>'):'<span class="aLink label label-danger">Temporary Booked</span>'?>
                                                
                                                <?php if(@$res->plot->customerPlots[0]->customerPlotTransactionSum >= $this->discountedPlotCostOfLand(@$res->plot->customerPlots[0]->plot->id)) {?>
                                            <span class="label label-warning" style="text-decoration: none;">Cost of Land Paid</span>
                                            <?php }?>

                                            <?php if(@$res->plot->customerPlots[0]->customerPlotTransactionSum >= $this->discountedPlotCostOfLandAndExtra(@$res->plot->customerPlots[0]->plot->id)) {?>
                                            <br/><span class="label label-success" style="text-decoration: none;">Plot Total Paid</span>
                                            <?php }?>
                                            </td>
                                            <?php } else{ ?>
                                                <td><span class="aLink label label-danger">Cancelled</span></td>
                                            <?php } ?>
                                        <?php } else {?>
                                            <td><span class="aLink label label-danger">Blocked</span></td>
                                            <?php if(@$res->plot->customerPlots[0]->is_open==0){?>
                                                <td><span class="aLink label label-danger">Blocked</span></td>
                                            <?php } else {?>
                                                <td><span class="aLink label label-success">Open</span></td>
                                            <?php } ?>
                                        <?php } } else{ ?>
                                            <td><span class="aLink label label-success">Available</span></td>
                                        <?php } ?>

                                        <td class="btnn"><a href="<?php echo Yii::app()->baseUrl?>/agent/deleteagentplot/<?php echo @$res->id?>"><span class="aLink label label-danger">Delete</span></a></td>
                                        
                                    </tr>
                                    
                                <?php endforeach; }?>
                            </tbody>
                            <tfoot style="font-weight: bold;">
                                <td colspan="2">Total</td>
                                <td><?php echo 'PKR '.number_format(@$tComission)?></td>
                                <td><?php echo 'PKR '.number_format(@$tpComission)?></td>
                                <td><?php echo 'PKR '.number_format(@$tbComission)?></td>
                                <td colspan="8"></td>
                                
                            </tfoot>
                        </table>
                                <div class="form-group col-lg-4">
                                    <label>Block #</label>
                                    <select id="block_number" class="form-control">
                                        <option value="">Select block #</option>
                                        <?php foreach($blocks as $block):?>
                                            <option value="<?php echo $block->block_number?>" <?php (@$currentPlot['block_number'] == $block->block_number)?'selected':''?>><?php echo $block->block_number?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>

                                <div class="form-group col-lg-4" >
                                    <label>Plot type</label>
                                    <select id="plot_type" class="form-control">
                                        <option value="">Select plot #</option>
                                    </select>
                                </div>

                                <div class="form-group col-lg-4" >
                                    <label>Plot #</label>
                                    <select name="plot_number[]" id="plot_number" class="form-control">
                                        <option value="">Select plot #</option>
                                    </select>
                                </div>
                            </div>
                            

                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-success">Update</button>
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
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<script type="text/javascript">
    $(document).on('click', '#report2btn', function (e) {
       var divToPrint=document.getElementById("dataTables");
       newWin= window.open("");
       var heading = '<div><h3>GULSHAN-E-BHOLARI<span style="margin-left:25%">GULSHAN-E-BHOLARI</span></h3></div>';
       newWin.document.write('<style>table, th, td {border: 1px solid black;}.btnn{display: none;}</style>'+heading+divToPrint.outerHTML);
       newWin.print();
       newWin.close();
    });

</script>
            