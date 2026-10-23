<?php $userModel = Yii::app()->session->get('userModel');?>
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
                All Dealers
                <span class="pull-right"><a href="javascript:void(0)" id="report2btn"><span class="label label-success">Print</span></a></span>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body" id="tableBody">
                <table width="100%" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <!--<th>Parent Name</th>-->
                            <!--<th>In-Active Bookings</th>-->
                            <th>Active Bookings</th>
                            <th>Percentage(%)</th>
                            <!--<th class="dnone">Agent Plots</th>-->
                            <th class="dnone">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                       <?php if($users){ $iab = 0;$ab = 0;$ap=0;foreach($users as $user):?>
                            <tr>
                                <td><?php echo $user->name?></td>
                                <!--<td><?php //echo @$user->agentParent->name?></td>-->
                                <!--<td><?php //echo @$user->agentReservesCount - @$user->agentPlots?></td>-->
                                <?php $iab = $iab + (@$user->agentReservesCount - @$user->agentPlots)?>
                                <td><?php echo $user->agentPlots?></td>
                                <?php $ab = $ab + @$user->agentPlots?>
                                <!--<td class="dnone"><?php //echo @$user->agentReservesCount?></td>-->
                                <?php $ap = $ap + @$user->agentReservesCount?>
                                <td><?php echo $user->percentage_value?></td>
                                <td class="dnone">
                                    <?php echo ($user->status==0)?'<span class="label label-danger">In active</span>':'<span class="label label-success">Active</span>'?>&nbsp;
                                    <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                                    <a href="<?php echo Yii::app()->baseUrl?>/agent/edit/<?php echo $user->id?>"><span class="aLink label label-info">Edit</span></a>&nbsp;<a href="<?php echo Yii::app()->baseUrl?>/agent/delete/<?php echo $user->id?>"><span class="aLinkDanger label label-<?php echo ($user->status==0)?'success':'danger'?>"><?php echo ($user->status==0)?'Activate':'Delete'?></span></a>
                                <?php } ?>
                                
                                </td>
                            </tr>
                       <?php endforeach;}?>
                       <!--<tr>-->
                       <!--    <td colspan="2">Total</td>-->
                       <!--    <td colspan=""><?php //echo @$iab?></td>-->
                       <!--    <td colspan=""><?php //echo @$ab?></td>-->
                       <!--    <td></td>-->
                       <!--    <td><?php //echo @$ap?></td>-->
                       <!--    <td></td>-->
                       <!--</tr>-->
                    </tbody>
                </table>
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
       var heading = '<div><h3>Gulshan-e-Bholari<span style="margin-left:25%">Gulshan-e-Bholari</span><span style="float:right">All Dealers</h3></div>';
       newWin.document.write('<style>table, th, td {border: 1px solid black;} .dnone{display:none}</style>'+heading+divToPrint.outerHTML);
       newWin.print();
       newWin.close();
    });

</script>