<?php $bookingDues = $this->calculateBookingDues($booking->id);?>
<?php $result = [];$netTotalCheck = $this->plotDiscount($booking->plot->id,false) - intval($booking->customerPlotTransactionSum)?>
<div class="col-lg-12 infoBox" style="margin-top: 5%!important;">
    <div class="" style="margin-top:-5%">
        <table class="table table-hover dataTable no-footer duesTable" border=1 id="duesTable" style="FONT-SIZE: 11PX;margin-bottom: 0px;"> 
            <thead>
                <!-- <tr>
                    <th style="background-color: lightgrey!important;line-height: 15px!important;">Payment Mode</th>
                    <th style="background-color: lightgrey!important;line-height: 15px!important;">Due Date</th>
                    <th style="background-color: lightgrey!important;line-height: 15px!important;">Total Amount</th>
                    <th style="background-color: lightgrey!important;line-height: 15px!important;">Received Amount</th>
                    <th style="background-color: lightgrey!important;line-height: 15px!important;">Balance Amount</th>
                </tr> -->
            </thead>
            <tbody>
<?php $paymentmodes = PaymentSchedulePaymentModes::model()->findAll('payment_schedule_id = :id AND plot_type = :type',array(':id'=>$booking->paymentSchedule->id,':type'=>strtolower($booking->plot->plot_type)));
if($booking->customerpaymentSchedule){
    $modesCustom = CustomPaymentSchedulePaymentModes::model()->findAll($booking->id); 
    $modesC = [];
    array_map(function($item) use(&$modesC){
        $modesC[$item->mode] = $item;
    }, $modesCustom);
    $asd = [];
    foreach($paymentmodes as $ind => $cpm){
        $x = new stdClass();
        $x->id = $cpm->id;
        $x->mode = $cpm->mode;
        $x->amount = $modesC[$cpm->mode]->amount;    
        $asd[] = $x;  
    }   

    $paymentmodes = $asd;
}

$paymentmodesOnly = [];
foreach($paymentmodes as $modes){
    if(strtolower(@$modes->mode) == 'monthly'){
        $paymentmodesOnly[] = $modes;    
    }

    if(strtolower(@$modes->mode) == 'yearly'){
        $paymentmodesOnly[] = $modes;    
    }
     
}
$paymentmodes = $paymentmodesOnly;

?>
                    <?php $allocationTotal = 0;$allocationSum = 0;$total = 0; $balance = 0; $received = 0;
                    foreach($paymentmodes as $modes): $rowBalance = 0;
                    $cpt = CustomerPlotTransactions::model()->findAll('plot_id = :id AND plot_payment_mode_id = :modeid',array(':id'=>$booking->id,':modeid'=>$modes->id)); 
                    ?>
                    <tr style="font-size: 12px;">
                        <!--Mode-->
                        <td style="line-height: 15px!important;">
                            <?php echo $this->getModesName(@$modes->mode)?>                              
                        </td>
<?php 
$var_sum = CustomerPlotTransactions::model()->findBySql('select sum(`amount`) as `total` from customer_plot_transactions WHERE status = 1 AND plot_id = :id AND plot_payment_mode_id = :modeid', array(':id'=>$booking->id,':modeid'=>$modes->id));
if($modes->mode=='allocation'){
    $allocationTotal = $modes->amount;
    $allocationSum = $var_sum->total;
}
?> 
<?php $checkValue = false;?>
<?php if($modes->mode=='monthly'){
    if($modes->amount){
        $divideVal = (($var_sum->total/number_format(($modes->amount/36),'2','.','')));
        $floorVal = floor($divideVal);
        $fractionVal = $divideVal - $floorVal;
        $checkValue =  floor(($var_sum->total/number_format(($modes->amount/36),'2','.','')));
        $isDetailShowMonthly = 1;    
    }
    
} ?>  
<?php if($modes->mode=='yearly'){
    if($modes->amount){
        //$checkValue =  floor(($var_sum->total/number_format(($modes->amount/6),'2','.','')));
        $divideVal = (($var_sum->total/number_format(($modes->amount/6),'2','.','')));
        $floorVal = floor($divideVal);
        $fractionVal = $divideVal - $floorVal;
        $checkValue =  floor(($var_sum->total/number_format(($modes->amount/6),'2','.','')));
        $isDetailShowYearly = 1;
    }
} ?>                        
                        <!--Mode Next Column-->
                        <?php if(strtolower(@$modes->mode) == 'monthly'){?>
                            <?php if($netTotalCheck > 0){?>
                                <td class="hide"><?php echo '<p>'.$checkValue.' out of 36 monthly installments,</p>';
                                    if($checkValue!=0){
                                        //$checkValue = $checkValue-1;
                                        //echo '<p><b>Paid Upto: '.date('M, Y',strtotime(date("Y-m-d", strtotime($booking->monthly_start_date)) . "+$checkValue months")).'</p></b>';
                                    }//echo ( strtolower(@$modes->mode) == 'monthly')? '36 / '.$checkValue .' = '.(36-$checkValue) : '' ?></td>
                            <?php } else{?>
                                <td class="hide"><?php //echo ( strtolower(@$modes->mode) == 'monthly')? '36 ' : '' ?></td>
                            <?php }?>        
                            
                        <?php } else if(strtolower(@$modes->mode) == 'yearly'){?>
                            <?php if($netTotalCheck > 0){?>
                                <td class="hide"><?php echo '<p>'.$checkValue.' out of 6 yearly installments,</p>';
                                        if($checkValue!=0){
                                        //$checkValuee = ($checkValue==1)?6:($checkValue*6)-1;
                                        //echo '<p><b>Paid Upto: '.date('M, Y',strtotime(date("Y-m-d", strtotime($booking->monthly_start_date)) . "+$checkValuee months")).'</p></b>';
                                    };//echo ( strtolower(@$modes->mode) == 'yearly')? '6 / '.($checkValue) .' = '.(6-$checkValue) : '' ?></td>
                            <?php } else{?>
                                <td class="hide"><?php //echo ( strtolower(@$modes->mode) == 'yearly')? '6 ' : '' ?></td>
                            <?php }?>
                        <?php } else{?>
                            <td class="hide"><?php //echo $modes->mode?></td>
                        <?php } ?>

                        <?php if($modes->mode=='monthly'){?>
                        <td>
                            <?php if($var_sum->total >= $modes->amount){?>
                                <table class="table table-hover dataTable no-footer duesTable" border="2" id="duesTable" style="FONT-SIZE: 09PX;margin-bottom: 0px;">
                                  <tbody>
                                    <tr>
                                      <td colspan="2" style="text-align: center;line-height: 15px!important;">
                                        <?php echo '<p><b>36</b> out of <b>36</b> monthly installments,';
                                            $checkValuePaid = $checkValue-1;
                                            echo '&nbsp;&nbsp;<br/><b>Paid Upto: '.date('M, Y',strtotime(date("Y-m-d", strtotime($booking->monthly_start_date)) . "+36 months")).'</p></b>';
                                        ?>
                                    </td>
                                    </tr>
                                  </tbody>
                                </table>
                            <?php $isDetailShowMonthly = 0;}?>

                            <?php if($checkValue != 36 && @$isDetailShowMonthly == 1){?>
                            <table class="table table-hover dataTable no-footer duesTable" border=2 id="duesTable" style="FONT-SIZE: 09PX;margin-bottom: 0px;">
                                <tr class="hide">
                                    <td>Start Month</td>
                                    <td><b><?php echo date('M, o',strtotime($booking->monthly_start_date));?></b></td>
                                </tr>
                                
                                <?php if($netTotalCheck >= 0){?>
                                    <?php $origPaidM = $checkValue;?>
                                    <td colspan="2" style="text-align: center;line-height: 15px!important;"><?php echo '<p><b>'.$checkValue.'</b> out of <b>36</b> monthly installments,';
                                        if($checkValue!=0){
                                            $checkValuePaid = $checkValue-1;
                                            echo '&nbsp;&nbsp;<br/><b>Paid Upto: '.date('M, Y',strtotime(date("Y-m-d", strtotime($booking->monthly_start_date)) . "+$checkValuePaid months")).'</p></b>';
                                        }}?>
                                    </td>

                                <?php $prev = 0;if($fractionVal!=0){?>
                                <tr>
                                    <td>Previous Balance(<?php echo date('M, Y',strtotime(date("Y-m-d", strtotime($booking->monthly_start_date)) . "+$checkValue months"))?>)</td>
                                    <td><b><?php echo $prev = number_format((1-$fractionVal) * number_format(($modes->amount/36),'2','.',''),'2','.','')?></b></td>
                                </tr>
                                <?php }?>
                                <tr>
                                    <td>Due Months</td>
                                    <?php $checkValueMonthly = $checkValue-1;//($fractionVal!=0)?$checkValue+1:$checkValue;?>
                                    <?php $upM = $this->getDateDiff(date('d M, Y'),date('d M, Y',strtotime(date("Y-m-d", strtotime($booking->monthly_start_date)) . "+$checkValueMonthly months")));?>
                                    <?php if(($upM+$origPaidM) >= 36){?>
                                        <td><b><?php echo $upM = 36-$origPaidM ?> Month(s)</b></td>
                                    <?php } else{?>
                                        <td><b><?php echo $upM ?> Month(s)</b></td>
                                    <?php }?>
                                </tr>
                                <?php $result['monthlyDue'] = $upM?>
                                <tr>
                                    <?php //$ccc = //$this->getDateDiff(date('d M, Y'),date('d M, Y',strtotime(date("Y-m-d", strtotime($booking->monthly_start_date)) . "+$checkValueMonthly months")));?>

                                    <?php if(($upM+$origPaidM) >= 36){
                                        $ccc = 36-$origPaidM;
                                    } else{
                                        $ccc = $upM;
                                    }?>

                                    <?php $ccc = ($prev > 0)?$ccc-1:$ccc?>
                                    <?php $cccC = ($ccc < 0)?0:$ccc?>
                                    <td style="line-height: 5px!important;"><p>Next Due Monthly Installment</p><p><span>(<?php echo number_format(($modes->amount/36),'2','.','')." x $cccC  "?>)</span></p></td>
                                    <td><b><?php echo number_format($cccC * number_format(($modes->amount/36),'2','.',''),'2','.',',')?></b></td>
                                    <?php $result['monthly'] = ($cccC * number_format(($modes->amount/36),'2','.',''))?>
                                </tr>
                                
                                <?php if($fractionVal!=0){?>
                                    <tr>
                                    <td>Total Monthly Installment Due Amount</td>
                                    <?php $dueTotal = ((1-$fractionVal) * number_format(($modes->amount/36),'2','.','')) + ($ccc * number_format(($modes->amount/36),'2','.',''));?>
                                    <?php if($ccc < 0){?>
                                        <td><b>0</b></td>
                                        <?php $result['monthly'] = 0?>
                                    <?php } else {?>
                                        <td><b><?php echo number_format($dueTotal,'2','.',',')?></b></td>
                                        <?php $result['monthly'] = $dueTotal?>
                                    <?php }?>
                                    
                                </tr>
                                <?php }?>
                                
                                <tr>
                                    <td>Next Monthly Installment Due Date</td>
                                    <?php $ce = 0;//$checkValue+$ccc; ?>
                                    <td><b><?php echo date('10 M, Y');//$this->actionGetModeDueDate(@$booking, @$booking->monthly_start_date,strtolower(@$modes->mode),round($ce))?></b></td>
                                </tr>
                            </table>
                            <?php } ?>
                        </td>
                        <!--Due Amount-->
                        <?php } elseif($modes->mode=='yearly'){?>
                            <td>

                                <?php if($var_sum->total >= $modes->amount){?>
                                <table class="table table-hover dataTable no-footer duesTable" border="2" id="duesTable" style="FONT-SIZE: 09PX;margin-bottom: 0px;">
                                  <tbody>
                                    <tr>
                                        <td colspan="2" style="text-align: center;line-height: 15px!important;"><?php echo '<p><b>6</b> out of <b>6</b> half yearly installments,';
                                                $checkValuePaid = $checkValue-1;
                                                echo '&nbsp;&nbsp;<br/><b>Paid Upto: '.date('M, Y',strtotime(date("Y-m-d", strtotime($booking->monthly_start_date)) . "+36 months")).'</p></b>';
                                            ?>
                                        </td>
                                    </tr>
                                  </tbody>
                                </table>
                            <?php $isDetailShowYearly = 0;}?>


                            <?php if($checkValue != 6 && @$isDetailShowYearly == 1){?>
                                <table class="table table-hover dataTable no-footer duesTable" border=2 id="duesTable" style="FONT-SIZE: 09PX;margin-bottom: 0px;">
                                <tr class="hide">
                                    <td>Start Month</td>
                                    <td><b><?php echo date('M, o',strtotime($booking->monthly_start_date));?></b></td>
                                </tr>
                                <?php if($netTotalCheck >= 0){?>
                                <td colspan="2" style="text-align: center;line-height: 15px!important;">
                                    <?php $origPaidHY = $checkValue;?>
                                    <?php echo '<p><b>'.$checkValue.'</b> out of <b>6</b> half yearly installments,';
                                        if($checkValue!=0){
                                            $checkValuee = ($checkValue==1)?6:($checkValue*6);
                                            echo '&nbsp;&nbsp;<br/><b>Paid Upto: '.date('M, Y',strtotime(date("Y-m-d", strtotime(date('Y-m-d',strtotime(date("Y-m-d", strtotime($booking->monthly_start_date)) . "-1 months")))) . "+$checkValuee months")).'</p></b>';
                                        } else{
                                            $checkValuee = ($checkValue==1)?6:($checkValue*6);
                                            echo '&nbsp;&nbsp;<br/><b>Paid Upto: '.date('M, Y',strtotime(date("Y-m-d", strtotime(date('Y-m-d',strtotime(date("Y-m-d", strtotime($booking->monthly_start_date)))))) . "+$checkValuee months")).'</p></b>';
                                        }
                                } ?>
                                </td>
                                <?php $prev = 0; if($fractionVal!=0){?>
                                <tr>
                                    <?php if($checkValue==0){?>
                                        <?php $checkValuee = (($checkValue==0)?6:$checkValue*6); ?>
                                    <?php } else {?>
                                    <?php $checkValuee = ($checkValue==1)?12:($checkValue*6);?>
                                    <?php }?>
                                    <?php $checkValueess = $checkValuee+6; ?>
                                    <td>Previous Balance(<?php echo date('M, Y',strtotime(date("Y-m-d", strtotime(date('Y-m-d',strtotime(date("Y-m-d", strtotime($booking->monthly_start_date)) . "-1 months")))) . "+$checkValueess months"))?>)</td>



                                    <?php //$checkValuee = ($checkValue==1)?12:($checkValue*6);?>
                                    <!-- <td>Previos Balance(<?php //echo date('M, Y',strtotime(date("Y-m-d", strtotime($booking->monthly_start_date)) . "+$checkValuee months"))?>)</td> -->
                                    <td><b><?php echo $prev = number_format((1-$fractionVal) * number_format(($modes->amount/6),'2','.',''),'2','.','')?></b></td>
                                </tr>
                                <?php }?>
                                <tr>
                                    <td>Due Half Yearly</td>
                                    <?php $checkValuee = ($checkValue==1 && $checkValue==0)?1:($checkValue*6)-1;?>
                                    <?php $upHY = floor($this->getDateDiff(date('d M, Y'),date('d M, Y',strtotime(date("Y-m-d", strtotime($booking->monthly_start_date)) . "+$checkValuee months")))/6);?>
                                    <?php if(($upHY+$origPaidHY) >=6){?>
                                        <td><b><?php echo $upHY = 6-$origPaidHY ?> Half Yearly(s)</b></td>
                                    <?php } else{?>
                                        <td><b><?php echo $upHY ?> Half Yearly(s)</b></td>
                                    <?php }?>
                                </tr>
                                <?php $result['yearlyDue'] = $upHY?>
                                <tr>
                                    <?php //$ccc = $this->getDateDiff(date('d M, o'),$this->actionGetModeDueDate(@$booking, @$booking->monthly_start_date,'bookingMonthly',round($checkValue)));?>
                                    <?php $checkValuee = ($checkValue==1 && $checkValue==0)?1:($checkValue*6)-1;?>
                                    <?php $ccc = $upHY;?>

                                    <?php if(($upHY+$origPaidHY) >=6){
                                            $ccc = 6-$origPaidHY;
                                        } else{
                                            $ccc = $upHY;
                                        }
                                    ?>

                                    <?php $ccc = ($prev > 0)?$ccc-1:$ccc?>
                                    <?php $cccC = ($ccc < 0)?0:$ccc?>

                                    <td style="line-height: 5px!important;"><p>Next Due Half Yearly</p><p><span>(<?php echo number_format(($modes->amount/6),'2','.','')." x $cccC  "?>)</span></p></td>
                                    <td><b><?php echo $tyearly = number_format($cccC * number_format(($modes->amount/6),'2','.',''),'2','.',',')?></b></td>
                                </tr>
                                <?php $result['yearly'] = ($cccC * number_format(($modes->amount/6),'2','.',''))?>
                                <?php if($fractionVal!=0){?>
                                <tr>
                                    <td>Total Half Yearly Due Amount</td>
                                    <?php
                                    //if($fractionVal!=0){
                                        //$ccc = $ccc-1;
                                        //$cccC = ($ccc < 0)?0:$ccc;
                                    //} else{
                                        //$cccC = $ccc;
                                    //}
                                    ?>
                                    <?php $dueTotal = ((1-$fractionVal) * number_format(($modes->amount/6),'2','.','')) + ($cccC * number_format(($modes->amount/6),'2','.',''));?>
                                    <?php if($ccc < 0){?>
                                        <td><b>0</b></td>
                                        <?php $result['yearly'] = 0?>
                                    <?php } else {?>
                                        <td><b><?php echo number_format($dueTotal,'2','.',',')?></b></td>
                                        <?php $result['yearly'] = $dueTotal?>
                                    <?php }?>
                                </tr>
                                <?php }?>
                                
                                <tr>
                                    <td>Next Half Yearly Due Date</td>
                                    <?php $ce = (($checkValue==1 || $checkValue==0)?6:$checkValue*6); ?>
                                    <td><b><?php echo $this->actionGetModeDueDate(@$booking, date('Y-m-d',strtotime(date("Y-m-d", strtotime($booking->monthly_start_date))."-1 months")),strtolower(@$modes->mode),round($ce))?></b></td>
                                </tr>
                            </table>
                                <?php }?>
                            
                        </td>
                        <?php } else{ ?>
                        <td><?php echo $this->actionGetModeDueDate(@$booking, @$booking->createdOn,strtolower(@$modes->mode),round($checkValue))?></td>
                        <?php }?>
                        <td><?php echo 'Rs. '.number_format(@$modes->amount) ?>
                            
                            
                        </td>
                        <?php $total = $total + @$modes->amount;?>

                        <!--Rec Amount-->
                        <td><?php echo (@$var_sum->total)?'Rs. '.@number_format(@$var_sum->total):'-';?></td>
                        <?php $received = $received + ((@$var_sum->total)?$var_sum->total:0); ?>
                        <?php $rowBalance = @$modes->amount - @$var_sum->total;?>
                        
                        <!--Balance Amount-->
                         <td><?php echo (@$var_sum->total )?'Rs. '.number_format(@$rowBalance):'Rs. '.number_format($modes->amount)?></td>
                        <?php $balance = $balance + @$rowBalance;?>
                    </tr> 
                <?php endforeach; ?>
                

                
            </tbody>
        </table>
    </div>
</div>
<?php echo json_encode($result);?>