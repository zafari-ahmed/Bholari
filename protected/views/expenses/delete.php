<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Expenses</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Update Expense
            </div>
            <div class="panel-body">
                <div class="row">
                    <form role="form" method="POST" action="<?php echo Yii::app()->baseUrl?>/expenses/update">
                        <input type="hidden" name="id" value="<?php echo @$expense->id?>">
                        <input type="hidden" name="expense_type" value="<?php echo @$expense->expense_type?>">
                        <!-- <div class="col-lg-12"> -->
                            <div class="form-group col-lg-6" >
                                <label>Expense Type</label>
                                <?php  $type = @$expense->expense_type;?>
                                <select name="expense_type" id="expense_type" class="form-control" required disabled>
                                    <option value="">Please select expense type</option>
                                    <option value="1" <?php echo ($type==1)?'selected':''?>>Office Expense</option>
                                    <option value="2" <?php echo ($type==2)?'selected':''?>>Site</option>
                                    <option value="3" <?php echo ($type==3)?'selected':''?>>Agent Commission - Cash</option>
                                    <option value="4" <?php echo ($type==4)?'selected':''?>>Agent Commission - Installment</option>
                                    <option value="5" <?php echo ($type==5)?'selected':''?>>Marketing</option>
                                    <option value="6" <?php echo ($type==6)?'selected':''?>>Donation</option>
                                    <option value="7" <?php echo ($type==7)?'selected':''?>>Assets</option>
                                    <option value="8" <?php echo ($type==8)?'selected':''?>>Loan</option>
                                    <option value="9" <?php echo ($type==9)?'selected':''?>>Petty Cash Load</option>
                                    <option value="10" <?php echo ($type==10)?'selected':''?>>Zakat</option>
                                </select>
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>
                            <div class="form-group col-lg-4">
                                <label>Amount</label>
                                <input readonly class="form-control" placeholder="Amount"  value="<?php echo @$expense->amount?>">
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>

                            <div class="form-group col-lg-12">
                                <label>Expense Description</label>
                                <textarea readonly class="form-control" rows="3" placeholder="Description"><?php echo @$expense->description?></textarea>
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
                            </div>

                            <div class="form-group col-lg-12">
                                <label>Reason</label>
                                <textarea required class="form-control" rows="3" name="reason" placeholder="Reason">Not Approved</textarea>
                            </div>

                            <input name="status" type="hidden" value="<?php echo @$status?>" >

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
            