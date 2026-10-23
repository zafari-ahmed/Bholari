<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Upload Expense</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Upload Expense
            </div>
            <div class="panel-body">
                <div class="row">
                    <form role="form" method="POST" action="<?php echo Yii::app()->baseUrl?>/expenses/expensedata" enctype="multipart/form-data">
                        <!-- <div class="col-lg-12"> -->
                            <div class="form-group col-lg-6" >
                                <label>CSV File</label>
                                <input type="file" class="form-control" name="expense" id="expense" placeholder="Upload CSV" required="">
                                <!-- <p class="help-block">Example block-level help text here.</p> -->
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