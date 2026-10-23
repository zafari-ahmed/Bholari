<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Expenses</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<?php
$phaseId = Yii::app()->session->get('userModel')['phase_id'];
?>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Add Commission Expense
            </div>
            <div class="panel-body">
                <div class="row">
                    <form role="form" method="POST" action="<?php echo Yii::app()->baseUrl?>/expenses/savedealer">
                        <!-- <div class="col-lg-12"> -->
                            <input type="hidden" name="bookingInfo" value='<?php echo json_encode($bookingInfo)?>' />
                            <div class="form-group col-lg-4" >
                                <label>Head of A/c</label>
                                <select name="expense_type" id="expense_type" class="form-control" required >
                                    <option value="3">Agent Commission</option>
                                </select>
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>
                            <div class="form-group col-lg-3" >
                                <label>Payment Mode</label>
                                <select name="payment_mode" id="payment_mode" class="form-control" required>
                                    <option value="">Please select payment mode</option>
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="payorder">PayOrder</option>
                                    <!-- <option value="debit voucher">Debit Voucher</option>
                                    <option value="adjustment">Adjustment</option> -->
                                </select>
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>

                            <div class="form-group col-lg-3" style="padding-left: 0px;">
                                    <label>Bank</label>
                                    <!-- <input class="form-control" id="bank" name="bank" placeholder="Bank" autocomplete="off"> -->
                                    <select name="bank" id="bank" class="form-control">
                                        <option value="">Please select Bank</option>
                                        <option value="Other">Other</option>
                                        <option value="Habib Bank Limited">Habib Bank Limited</option>
                                        <option value="Alied Bank Limited">Alied Bank Limited</option>
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
                                        
                                    </select>
                                </div>
                            
                            <div class="form-group col-lg-3">
                                <label>Amount</label>
                                <input class="form-control" type="number" name="amount" id="amount" placeholder="Amount" required="" value="<?php echo @$totalCom?>">
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>
                            <div class="form-group col-lg-4">
                                <label>Reference Number</label>
                                <input class="form-control" type="number" name="number" id="amount" placeholder="Reference Number">
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>
                            <div class="form-group col-lg-3">
                                <label>Paid To</label>
                                <input list="browsers" class="form-control" type="text" name="paid_to" id="paid_to" placeholder="Paid To" value="<?php echo @$agentSelected->name?>">
                            </div>
                            <div class="form-group col-lg-4">
                                <label>CNIC/NTN</label>
                                <input class="form-control cnic" type="text" name="cnic" id="cnic" placeholder="CNIC/NTN" value="<?php echo @$agentSelected->number?>">
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>
                            <div class="form-group col-lg-4" >
                                <label>Expense Date</label>
                                <input class="form-control calenderr" name="createdOn" autocomplete="off" required value="<?php echo date('Y-m-d')?>">
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>
                            
                            <div class="form-group col-lg-12">
                                <label>Expense Particulars</label>
                                <textarea class="form-control" rows="3" name="description" placeholder="Expense Particulars" required><?php echo nl2br(@$msg);?></textarea>
                                <p class="help-block">* Add <code>&lt;br/&gt;</code> for multiple numbers.</p>
                            </div>

                            


                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-success">Submit</button>
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
            