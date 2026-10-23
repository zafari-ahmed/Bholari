<?php $dues = $this->calculateBookingDues($booking->id);
$bookingLastTransaction = $booking->customerPlotTransactionslastCalling();

$formattedTransactions = $booking->getFormattedLatestTransaction(); 


?>
<div class="row">
    <div class="col-lg-12">
            <table width="100%" class="table table-striped table-bordered table-hover">
                <thead style="color: #3c763d;background-color: #dff0d8;    text-transform: UPPERCASE;font-weight: bold;">
                    <th>Monthly Amount</th>
                    <th>Elapsed Months</th>
                    <th>Months Paid</th>
                    <th>Due Months</th>
                    <th>Total Paid</th>
                    <th>Remaining Balance</th>
                    <th>Total Due</th>
                </thead>
                <tbody>
                    <tr>
                        <td><b><?php echo 'PKR '.number_format(@$dues['monthly_amount'],2,'.',',');?></b></td>
                        <td><?php echo (int)@$dues['elapsed_months'];?></td>
                        <td><?php echo @$dues['months_paid'];?></td>
                        <td><b><?php echo @$dues['due_months'];?></b></td>
                        <td><?php echo 'PKR '.number_format(@$dues['total_paid'],2,'.',',');?></td>
                        <td><?php echo 'PKR '.number_format(@$dues['remaining_balance'],2,'.',',');?></td>
                        <td><b><?php echo 'PKR '.number_format(@$dues['due_amount'],2,'.',',');?></b></td>
                    </tr>
                </tbody>
            </table>
            
            <table width="100%" class="table table-striped table-bordered table-hover">
                <thead style="color: #3c763d;background-color: #dff0d8; text-transform: UPPERCASE;font-weight: bold;">
                    <th>#</th>
                    <th>Transaction Number</th>
                    <th>Payment Modes</th>
                    <th>Amount (PKR)</th>
                    <th>Created On</th>
                    <!--<th>Type</th>-->
                    <th>Status</th>
                </thead>
                <tbody>
                    <?php if ($formattedTransactions['has_transactions']): ?>
                        <?php 
                        $counter = 1;
                        foreach ($formattedTransactions['groups'] as $group): 
                        ?>
                            <!-- Group Header -->
                            <tr style="background-color: #fcf8e3; border-bottom: 2px solid #8a6d3b;">
                                <td colspan="7" style="font-weight: bold; color: #8a6d3b; padding: 10px;">
                                    <i class="fa fa-folder-open"></i> 
                                    Transaction Group: <strong style="font-size: 16px;"><?php echo $group['transaction_number']; ?></strong> | 
                                    Payment Modes: <strong><?php echo $group['payment_modes_string']; ?></strong> | 
                                    Total: <strong style="color: #3c763d;">PKR <?php echo number_format($group['total_amount'], 2, '.', ','); ?></strong> | 
                                    Date: <strong><?php echo date('d-M-Y', strtotime($group['latest_date'])); ?></strong>
                                </td>
                            </tr>
                            
                            <!-- Individual Transactions -->
                            <?php foreach ($group['transactions'] as $txn): ?>
                            <tr>
                                <td><?php echo $counter++; ?></td>
                                <td><strong><?php echo $txn['transaction_number']; ?></strong></td>
                                <td>
                                    <span class="label label-primary" style="font-size: 11px;">
                                        <?php echo htmlspecialchars($txn['payment_mode']); ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <b><?php echo number_format($txn['amount'], 2, '.', ','); ?></b>
                                </td>
                                <td><?php echo date('d-M-Y', strtotime($txn['createdOn'])); ?></td>
                                <!--<td>-->
                                <!--    <span class="label <?php echo $txn['type_class']; ?>">-->
                                <!--        <?php echo $txn['type']; ?>-->
                                <!--    </span>-->
                                <!--</td>-->
                                <td>
                                    <span class="label <?php echo $txn['status_class']; ?>">
                                        <?php echo $txn['status']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <!-- Group Summary -->
                            <tr style="background-color: #dff0d8; font-weight: bold;">
                                <td colspan="3" style="text-align: right; text-transform: uppercase; font-size: 13px;">
                                    GROUP SUMMARY
                                </td>
                                <td style="text-align: right; color: #3c763d; font-size: 14px;">
                                    <strong>PKR <?php echo number_format($group['total_amount'], 2, '.', ','); ?></strong>
                                </td>
                                <td colspan="3">
                                    <span class="label label-info">
                                        <?php echo $group['main_count']; ?> Main | 
                                        <?php echo $group['extra_count']; ?> Extra
                                    </span>
                                    <span class="label label-warning" style="margin-left: 5px;">
                                        Modes: <?php echo $group['payment_modes_string']; ?>
                                    </span>
                                </td>
                            </tr>
                            
                        <?php endforeach; ?>
                        
                        <!-- Grand Total (only if multiple groups) -->
                        <?php if (count($formattedTransactions['groups']) > 1): ?>
                        <tr style="background-color: #3c763d; font-weight: bold; font-size: 14px; color: white;">
                            <td colspan="3" style="text-align: right; text-transform: uppercase;">
                                GRAND TOTAL
                            </td>
                            <td style="text-align: right;">
                                <strong>PKR <?php echo number_format($formattedTransactions['grand_total'], 2, '.', ','); ?></strong>
                            </td>
                            <td colspan="3"></td>
                        </tr>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #999; padding: 30px;">
                                <em>No transactions found</em>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <h1 class="page-header">Transactions</h1>
        <?php
            foreach(Yii::app()->user->getFlashes() as $key => $message) {
                echo '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$message.'</div>';
            }
        ?>
    </div>
    <!-- /.col-lg-12 -->
</div>
<input type="hidden" id="extraLastTrans" value="<?php echo sprintf('%04d', @$extraLastTrans->transaction_number+1)?>">
<input type="hidden" id="LastTrans" value="<?php echo sprintf('%04d',@$lastTrans->transaction_number+1)?>">
<?php $userModel = Yii::app()->session->get('userModel');?>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Plot Transaction
                <?php if(in_array($userModel['id'],[1,25,29,30,35,42])){?>
                <span class="pull-right">
                    <a href="<?php echo Yii::app()->baseUrl?>/booking/addoldtransaction/<?php echo $booking->id?>"><span class="label label-success">Add Old Transaction</span></a>
                </span>
                <?php } ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <form id="transactionForm" name="transactionForm" role="form" method="POST" action="<?php echo Yii::app()->baseUrl?>/booking/savetransaction">
                        <input class="form-control" type="hidden" name="customer_id" value="<?php echo $booking->customer->id?>">
                        <input class="form-control" type="hidden" name="plot_id"  id="plot_id" value="<?php echo $booking->id?>">
                        <?php /*?>
                        <!-- <div class="col-lg-12"> -->
                            <div class="form-group col-lg-4" >
                                <label>Plot Payment Mode</label>
                                <select name="size_id" id="size_id" class="form-control" required>
                                    <option value="">Please select plot payment mode</option>
                                    <?php foreach(@$paymentmodes as $mode):?>
                                        <option value="<?php echo $mode->id?>"><?php echo $mode->mode?></option>
                                    <?php endforeach;?>
                                </select>
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>
                            <div class="form-group col-lg-4">
                                <label>Amount</label>
                                <input class="form-control" disabled="" name="amount" id="amount" placeholder="Amount">
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>
                            <div class="form-group col-lg-4" style="padding-right: 0px;">
                                <label>Discount</label>
                                <input class="form-control" name="discount" id="discount" placeholder="Discount">
                                
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>
                        <!-- </div> -->
                        <?php */?>
                        <div class="col-lg-12  form-group">
                            <!-- <label>Full Payment Discount (%)</label>
                            <label class="radio-inline">
                                <input type="number" class="form-control" name="discount" placeholder="Discount Percent" autocomplete="off">
                            </label> -->
                            <span class="pull-right"><a href="javascript:void(0)"><button type="button" class="btn btn-success btn-sm" id="addNewPayment">Add Another</button></a></span>
                        </div>
                        <div class="col-lg-12 form-group" id="paymentModeBox">
                            <div class="modeSBoxOrig">
                                <div class="col-lg-4" style="padding-left: 0px;">
                                    <label>Plot Payment Mode</label>
                                    <select name="mode[]" id="mode" class="form-control modesS" required>
                                        <option value="">Please select plot payment mode</option>
                                        <?php foreach(@$paymentmodes as $mode):?>
                                            <option value="<?php echo $mode['id']?>" rel="<?php echo $mode['amount']?>"><?php echo $mode['mode']?></option>
                                        <?php endforeach;?>
                                        <option value="development">Development</option>
                                        <option value="penalty">Penalty</option>
                                        <option value="transfer_fee">Transfer Fee</option>
                                        <option value="lease_charges">Lease Charges</option>
                                        <option value="others">Others</option>
                                        <option value="road_facing">Road Facing</option>
                                        <option value="west_open">West Open</option>
                                        <option value="corner">Corner</option>
                                        <option value="extra_land">Extra Land</option>
                                        <option value="park_facing">Park Facing</option>
                                        <?php /*if( $booking->customerPlotPlanTransactionsDevlopment || $userModel['user_type']['id'] == 1 ){?>
                                            <option value="development">Development</option>
                                        <?php }*/ ?>
                                        <!-- <option value="full_payment">Full Payment</option> -->
                                    </select>
                                    <!-- <p class="help-block">Example block-level help text here.</p> -->
                                </div>
                                <div class="form-group col-lg-3">
                                    <label>Amount</label>
                                    <input type="number" class="form-control numbersOnly" name="amount[]" placeholder="Amount" autocomplete="off" min="1">
                                </div>
                                <div class="form-group col-lg-3">
                                    <label>Transaction Number <?php //echo $lastTrans->transaction_number?></label>
                                    <?php if($userModel['id'] == 29 || $userModel['id'] == 1){//if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5 ){?>
                                    <input type="number" class="form-control" name="transaction[]" placeholder="Transaction Number" readonly autocomplete="off" value="<?php echo sprintf('%04d',@$lastTrans->transaction_number+1)?>">
                                    <?php } else{ ?>
                                    <input type="number" class="form-control" name="transaction[]" placeholder="Transaction Number" readonly autocomplete="off" value="<?php echo sprintf('%04d',@$lastTrans->transaction_number+1)?>">
                                    <?php } ?>
                                </div>

                                <div class="form-group col-lg-3">
                                <label>Transaction Type</label>
                                <select name="transaction_type[]" id="transaction_type" class="form-control" required>
                                    <option value="">Please select transaction type</option>
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="online">Online</option>
                                    <option value="PayOrder">PayOrder</option>
                                    <option value="DebitVoucher">DebitVoucher</option>
                                </select>
                                </div>
                            
                                <div class="form-group col-lg-3" style="padding-left: 0px;">
                                    <label>Bank</label>
                                    <!-- <input class="form-control" id="bank" name="bank" placeholder="Bank" autocomplete="off"> -->
                                    <select name="bank[]" id="bank" class="form-control">
                                        <option value="">Please select Bank</option>
                                        <option value="Other">Other</option>
                                        <option value="Habib Bank Limited">Habib Bank Limited</option>
                                        <option value="Allied Bank Limited">Allied Bank Limited</option>
                                        <option value="Bank Al Habib Limited">Bank Al Habib Limited</option>
                                        <option value="Habib Metropolitan Bank">Habib Metropolitan Bank</option>
                                        <option value="Dubai Bank Islami">Dubai Bank Islami</option>
                                        <option value="Bank Islami">Bank Islami</option>
                                        <option value="Standard Chartered Bank">Standard Chartered Bank</option>
                                        <option value="Bank Of Punjab">Bank Of Punjab</option>
                                        <option value="Meezan Bank Limited">Meezan Bank Limited</option>
                                        <option value="Soneri Bank">Soneri Bank</option>
                                        <option value="National Bank of Pakistan">National Bank of Pakistan</option>
                                        <option value="United Bank Limited">United Bank Limited</option>
                                        <option value="Askari Bank">Askari Bank</option>
                                        <option value="Faysal Bank">Faysal Bank</option>
                                        <option value="Silk Bank">Silk Bank</option>
                                        <option value="Bank Alfalah">Bank Alfalah</option>
                                        <option value="City Bank">City Bank</option>
                                        <option value="JS Bank">JS Bank</option>
                                        <option value="Muslim Commercial Bank">Muslim Commercial Bank</option>
                                        <option value="Sindh Bank">Sindh Bank</option>
                                        <option value="Bank of khyber">Bank of khyber</option>
                                        <option value="Bank of Azad & Jamu Kashmir">Bank of Azad & Jamu Kashmir</option>
                                        <option value="Samba Bank">Samba Bank</option>
                                        <option value="Summit Bank">Summit Bank</option>
                                        <option value="Bank Makramah Limited (BML)">Bank Makramah Limited (BML)</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-3" style="padding-right: 0px;">
                                    <label>Bank Branch</label>
                                    <input class="form-control" id="branch" name="branch[]" placeholder="Bank Branch" autocomplete="off">
                                </div>
                                <div class="form-group col-lg-3" style="padding-right: 0px;">
                                    <label>Reference Number</label>
                                    <input class="form-control" id="reference_number" name="reference_number[]" placeholder="Reference Number" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="col-md-12">
                                <hr/>
                            </div>
                            <div class="form-group">
                                <label>Comment</label>
                                <textarea class="form-control" rows="3" name="comment" placeholder="Comments"></textarea>
                            </div>
                            <?php if($userModel['id'] != 40 && $userModel['id'] != 43 && $userModel['id'] != 1){?>
                            <div class="form-group col-lg-6" style="padding-left: 0px;">
                                <label>Created Date</label>
                                <input class="form-control calender"  name="createdOn" placeholder="Created Date" autocomplete="off" value="<?php echo date('d-m-Y')//date('Y-m-d')?>">
                            </div>
                            <?php } else {?>
                                <div class="form-group col-lg-6" style="padding-left: 0px;">
                                    <label>Created Date</label>
                                    <input class="form-control"  placeholder="Created Date" disabled="true" value="<?php echo date('d-m-Y')?>">
                                </div>
                                <input   name="createdOn" type="hidden" value="<?php echo date('d-m-Y')?>">
                            <?php } ?>
                            <div class="form-group col-lg-6" style="padding-left: 0px;">
                                <label>Another Number</label>
                                <input class="form-control"  name="another_number" placeholder="Another Number" autocomplete="off">
                            </div>
                            
                            <div class="form-group col-lg-6" style="padding-left: 0px;">
                                <label>User Name</label>
                                <?php $users = Users::model()->findAll('user_type_id IN (1,5) AND id != 25'); ?>
                                <!--<input class="form-control"  name="createdBy" placeholder="User Name" autocomplete="off">-->
                                <select name="createdBy" id="createdBy" class="form-control" disabled>
                                    <option value="">Please select user</option>
                                    <?php if($users){ foreach($users as $u):?>
                                    <option value="<?php echo $u->first_name.' '.$u->last_name?>" <?php echo ($userModel['id']==$u->id)?'selected':''?>><?php echo $u->first_name.' '.$u->last_name?></option>
                                    <?php endforeach; } ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-3">
                                <label>Months</label>
                                <select name="monthlyDate[]" id="monthlyDate" class="form-control select2" multiple>
                                    <option value="Jan">Jan</option>
                                    <option value="Feb">Feb</option>
                                    <option value="Mar">Mar</option>
                                    <option value="Apr">Apr</option>
                                    <option value="May">May</option>
                                    <option value="Jun">Jun</option>
                                    <option value="Jul">Jul</option>
                                    <option value="Aug">Aug</option>
                                    <option value="Sept">Sept</option>
                                    <option value="Oct">Oct</option>
                                    <option value="Nov">Nov</option>
                                    <option value="Dec">Dec</option>
                                </select>
                            </div>
                            <!-- <div class="form-group">
                                <label>Checkboxes</label>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" value="">Checkbox 1
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" value="">Checkbox 2
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" value="">Checkbox 3
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Inline Checkboxes</label>
                                <label class="checkbox-inline">
                                    <input type="checkbox">1
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox">2
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox">3
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Radio Buttons</label>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked>Radio 1
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">Radio 2
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">Radio 3
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Inline Radio Buttons</label>
                                <label class="radio-inline">
                                    <input type="radio" name="optionsRadiosInline" id="optionsRadiosInline1" value="option1" checked>1
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="optionsRadiosInline" id="optionsRadiosInline2" value="option2">2
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="optionsRadiosInline" id="optionsRadiosInline3" value="option3">3
                                </label>
                            </div> -->
                            <input type="hidden" name="lastTransactionId" id="lastTransactionId" value="<?php echo @$booking->customerPlotTransactionslast[0]->id?>">
                            <input type="hidden" name="lastTransactionIdNew" value="<?php echo @$lastTransactionIdNew?>">
                            <input type="hidden" name="lastDevTransactionId" id="lastDevTransactionId" value="<?php echo @$booking->customerPlotPlanTransactionslast[0]->id?>">
                            <div class="col-lg-12" style="padding-left: 0px;">
                                <button type="submit" class="btn btn-success" id="transactionBtn">Submit</button>
                            </div>
                            
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

<div id="paymentModeBoxHidden" class="hide">
    <div class="modeSBox">
        <div class="col-lg-12">
            <hr/>
        </div>
        <div class="col-lg-4" style="padding-left: 0px;">
            <label>Plot Payment Mode</label>
            <select name="mode[]" id="mode" class="form-control modesS" required>
                <option value="">Please select plot payment mode</option>
                <?php foreach(@$paymentmodes as $mode):?>
                    <option value="<?php echo $mode['id']?>" rel="<?php echo $mode['amount']?>"><?php echo $mode['mode']?></option>
                <?php endforeach;?>
                <option value="development">Development</option>
                <option value="penalty">Penalty</option>
                <option value="transfer_fee">Transfer Fee</option>
                <option value="lease_charges">Lease Charges</option>
                <option value="others">Others</option>
                <option value="road_facing">Road Facing</option>
                <option value="west_open">West Open</option>
                <option value="corner">Corner</option>
                <option value="extra_land">Extra Land</option>
                <option value="park_facing">Park Facing</option>
            </select>
            <!-- <p class="help-block">Example block-level help text here.</p> -->
        </div>
        <div class="form-group col-lg-3">
            <label>Amount</label>
            <input type="number" class="form-control numbersOnly" name="amount[]" placeholder="Amount" required min="1">
        </div>
        <div class="form-group col-lg-3">
            <label>Transaction Number</label>
            <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5 ){?>
            <input type="number" class="form-control" name="transaction[]" placeholder="Transaction Number" readonly autocomplete="off" value="<?php echo sprintf('%04d',@$lastTrans->transaction_number+1)?>">
            <?php } else{ ?>
            <input type="number" class="form-control" name="transaction[]" placeholder="Transaction Number" readonly autocomplete="off" value="<?php echo sprintf('%04d',@$lastTrans->transaction_number+1)?>">
            <?php } ?>
        </div>
        <div class="form-group col-lg-2 hide">
            <a href="javascript:void(0)">
                <button type="button" class="btn btn-danger btn-sm deleteRow">Delete Row</button>
            </a>
        </div>
        <div class="form-group col-lg-3">
            <label>Transaction Type</label>
            <select name="transaction_type[]" id="transaction_type" class="form-control" required>
                <option value="">Please select transaction type</option>
                <option value="cash">Cash</option>
                <option value="cheque">Cheque</option>
                <option value="online">Online</option>
                <option value="PayOrder">PayOrder</option>
                <option value="DebitVoucher">DebitVoucher</option>
            </select>
        </div>
        
        <div class="form-group col-lg-3" style="padding-left: 0px;">
            <label>Bank</label>
            <!-- <input class="form-control" id="bank" name="bank" placeholder="Bank" autocomplete="off"> -->
            <select name="bank[]" id="bank" class="form-control">
                <option value="">Please select Bank</option>
                <option value="Other">Other</option>
                <option value="Habib Bank Limited">Habib Bank Limited</option>
                <option value="Allied Bank Limited">Allied Bank Limited</option>
                <option value="Bank Al Habib Limited">Bank Al Habib Limited</option>
                <option value="Habib Metropolitan Bank">Habib Metropolitan Bank</option>
                <option value="Dubai Bank Islami">Dubai Bank Islami</option>
                <option value="Bank Islami">Bank Islami</option>
                <option value="Standard Chartered Bank">Standard Chartered Bank</option>
                <option value="Bank Of Punjab">Bank Of Punjab</option>
                <option value="Meezan Bank Limited">Meezan Bank Limited</option>
                <option value="Soneri Bank">Soneri Bank</option>
                <option value="National Bank of Pakistan">National Bank of Pakistan</option>
                <option value="United Bank Limited">United Bank Limited</option>
                <option value="Askari Bank">Askari Bank</option>
                <option value="Faysal Bank">Faysal Bank</option>
                <option value="Silk Bank">Silk Bank</option>
                <option value="Bank Alfalah">Bank Alfalah</option>
                <option value="City Bank">City Bank</option>
                <option value="JS Bank">JS Bank</option>
                <option value="Muslim Commercial Bank">Muslim Commercial Bank</option>
                <option value="Sindh Bank">Sindh Bank</option>
                <option value="Bank of khyber">Bank of khyber</option>
                <option value="Bank of Azad & Jamu Kashmir">Bank of Azad & Jamu Kashmir</option>
                <option value="Samba Bank">Samba Bank</option>
                <option value="Summit Bank">Summit Bank</option>
                <option value="Bank Makramah Limited (BML)">Bank Makramah Limited (BML)</option>
            </select>
        </div>
        <div class="form-group col-lg-3" style="padding-right: 0px;">
            <label>Bank Branch</label>
            <input class="form-control" id="branch" name="branch[]" placeholder="Bank Branch" autocomplete="off">
        </div>
        <div class="form-group col-lg-3" style="padding-right: 0px;">
            <label>Reference Number</label>
            <input class="form-control" id="reference_number" name="reference_number[]" placeholder="Reference Number" autocomplete="off">
        </div>
    </div>
</div>

<div id="paymentModeDevBoxHidden" class="hide">
    <div class="modeSBox">
        <div class="col-lg-4" style="padding-left: 0px;">
            <label>Plot Payment Mode</label>
            <select name="mode[]" id="mode" class="form-control modesS" required>
                <option value="">Please select plot payment mode</option>
                <option value="development">Development</option>
            </select>
            <!-- <p class="help-block">Example block-level help text here.</p> -->
        </div>
        <div class="form-group col-lg-3">
            <label>Amount</label>
            <input class="form-control numbersOnly" name="amount[]" placeholder="Amount" required>
        </div>
        <div class="form-group col-lg-3">
            <label>Transaction Number</label>
            <?php if($userModel['user_type']['id'] == 1 ){?>
            <input type="number" class="form-control" name="transaction[]" placeholder="Transaction Number" autocomplete="off" value="<?php echo sprintf('%04d',@$lastTrans->transaction_number+1)?>">
            <?php } else{ ?>
            <input type="number" class="form-control" name="transaction[]" placeholder="Transaction Number" readonly autocomplete="off" value="<?php echo sprintf('%04d',@$lastTrans->transaction_number+1)?>">
            <?php } ?>
        </div>
    </div>
</div>

<script>
const form = document.getElementById("transactionForm");
const submitBtn = document.getElementById("transactionBtn");

let isSubmitting = false;

form.addEventListener("submit", function (e) {
    if (isSubmitting) {
        e.preventDefault(); // Prevent duplicate submissions
        return;
    }

    isSubmitting = true;
    submitBtn.disabled = true;
    submitBtn.textContent = "Submitting...";
});

$('#transaction_type').on('change', function () {
    if ($(this).val() === 'online') {
        $('#bank').val('Bank Al Habib Limited').trigger('change'); // trigger change if using Select2
    } else{
        $('#bank').val('').trigger('change'); // trigger change if using Select2
    }
});

document.querySelector('input[type="number"]').addEventListener('wheel', function () {
  this.blur();
});

$(document).on('wheel', 'input[type="number"]', function (e) {
    this.blur();
});
</script>
            