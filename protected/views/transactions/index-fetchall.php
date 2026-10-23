<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo ($status==0)?'Cancelled':'All'?> Transactions</h1>
        <?php
            foreach(Yii::app()->user->getFlashes() as $key => $message) {
                echo '<div class="alert alert-info alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$message.'</div>';
            }
        ?>
    </div>
    <!-- /.col-lg-12 -->
</div>
<?php $userModel = Yii::app()->session->get('userModel');?>
<!-- /.row -->
<div class="row">
    <!-- <div class="col-lg-12"> -->
        
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo ($status==0)?'Cancelled':'All'?> Transactions
                <span class="pull-right">
                    <a href="<?php echo Yii::app()->baseUrl.'/booking/reportalltransaction/status/'.$status?>"><span class="label label-success">CSV Download</span></a>
                        &nbsp;
                    <a href="javascript:void(0)" id="report2btn"><span class="label label-success">Print</span></a>
                </span>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body" id="tableBody">
                <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-trans">
                    <thead>
                        <tr>
                            <th class="hide">#</th>
                            <th>Plot #</th>
                            <th>Client Name</th>
                            <th>Transaction #</th>
                            <th>Transaction Type</th>
                            <th>Reference #</th>
                            <th>Payment Mode</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                            <th>Created On</th>
                            <th>Created By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
<!-- </div> -->

<script type="text/javascript">
    $(document).on('click', '#report2btn', function (e) {
       var divToPrint=document.getElementById("tableBody");
       newWin= window.open("");
       var heading = '<div><h3>Kainat City<span style="margin-left:25%">Kainat City</span><span style="float:right">Dated: <?php echo @$_POST['start_date']?> / <?php echo @$_POST['end_date']?></span></h3></div>';
       newWin.document.write('<style>table, th, td {border: 1px solid black;}</style>'+heading+divToPrint.outerHTML);
       newWin.print();
       newWin.close();
    });

</script>