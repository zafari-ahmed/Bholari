<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Bookings</h1>
        <?php
            foreach(Yii::app()->user->getFlashes() as $key => $message) {
                echo '<div class="alert alert-info alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$message.'</div>';
            }
        ?>
    </div>
    <!-- /.col-lg-12 -->
</div>
<?php $userModel = Yii::app()->session->get('userModel'); ?>
<?php 
    $params = $_GET;
    array_shift($params);
    $params = http_build_query($params);
?>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                All Bookings<br/>
                <span class="hide" style="margin-top: -5px;"><a href="<?php echo Yii::app()->baseUrl?>/booking"><span class="label label-info btn-sm">All</span></a></span>
                <?php foreach($paymentSchedules as $ps): $active = (@$_GET['payment']==$ps->id)?'success':'info'?>
                    <span style="margin-top: -5px;"><a href="<?php echo Yii::app()->baseUrl?>/booking?payment=<?php echo $ps->id?>"><span class="label label-<?php echo $active?> btn-sm"><?php echo $ps->name?></span></a></span>
                <?php endforeach;?>
                <?php /*?>
                <br/>
                <span class="hide" style="margin-top: -5px;"><a href="<?php echo Yii::app()->baseUrl?>/booking?flag_status=1"><span class="label label-<?php echo (@$_GET['flag_status']==1)?'success':'warning'?> btn-sm">File Completed</span></a></span>

                <span class="hide" style="margin-top: -5px;"><a href="<?php echo Yii::app()->baseUrl?>/booking?flag_status=2"><span class="label label-<?php echo (@$_GET['flag_status']==2)?'success':'warning'?> btn-sm">Received By Customer</span></a></span>

                <br/>
                <span class="hide" style="margin-top: -5px;"><a href="<?php echo Yii::app()->baseUrl?>/booking/bookingDocument?documentFlag=1"><span class="label label-<?php echo (@$_GET['documentFlag']==1)?'success':'warning'?> btn-sm">Completed Documents</span></a></span>
                <?php */?>
                <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                <span class="pull-right" style="margin-top: -25px;"><a href="<?php echo Yii::app()->baseUrl?>/booking/reportall" target="_blank"><button type="button" class="btn btn-success btn-sm">Report</button></a></span>
                <?php }?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTablesServer">
                    <thead>
                        <tr>
                            <th>Plot #</th>
                            <th>Reg. No.</th>
                            <th>Name</th>
                            <th>CNIC</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Action</th>
                            <th>Payments</th>
                            <th>Discount</th>
                            <th>Dealer</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>