<?php $userModel = Yii::app()->session->get('userModel');?>
<?php $phase_id = $userModel['phase_id'];?>
<div class="row">
<div class="col-lg-12">
    <h1 class="page-header">Dashboard
    <span class="pull-right hide">
        <?php $phases = Phases::model()->findAll();?>
        <div class="form-group">
            <select name="phase_id" id="phase_id_dashboard" class="form-control">
                <?php foreach($phases as $phase){?>
                    <option value="<?php echo $phase->id?>" <?php echo ($phase->id==$userModel['phase_id'])?'selected':''?>><?php echo $phase->phase?></option>
                <?php }?>
            </select>
        </div>
    </span>
    </h1>
</div>
<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<div class="row">
    
    <div class="col-lg-4 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-home fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <!--<div class="huge"><?php //echo Plots::model()->with('customerPlots')->count("t.status = 1 AND customerPlots.blocked = 0 AND customerPlots.status = 1 AND t.phase_id = $phase_id")?></div>-->
                        <div class="huge"><?php echo CustomerPlots::model()->count()?></div>
                        <div>Bookings</div>
                    </div>
                </div>
            </div>
            <a href="<?php echo Yii::app()->baseUrl.'/booking'?>">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
   
    <div class="col-lg-4 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-home fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo Plots::model()->count("status=0 AND t.phase_id = $phase_id")?></div>
                        <div>Available Plots</div>
                    </div>
                </div>
            </div>
            <a href="">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-user fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo Customers::model()->count("t.phase_id = $phase_id")?></div>
                        <div>Customer</div>
                    </div>
                </div>
            </div>
            <a href="">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-home fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo CustomerPlots::model()->count("status != 3 AND status != 0 AND blocked = 1 AND t.phase_id = $phase_id");?></div>
                        <div>Blocked Bookings</div>
                    </div>
                </div>
            </div>
            <a href="">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>


    <div class="col-lg-3 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-home fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo CustomerPlotCancelled::model()->count(array('group'=>'booking_id'));?></div>
                        <div>Cancelled Bookings</div>
                    </div>
                </div>
            </div>
            <a href="">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>


    
    <div class="col-lg-2 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-user fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo Agents::model()->count("t.phase_id = $phase_id")?></div>
                        <div>Dealers</div>
                    </div>
                </div>
            </div>
            <a href="">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="col-lg-6 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-money fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <?php
                        
                        $sql = "SELECT SUM(amount) as amount FROM customer_plot_transactions cpt JOIN customer_plots cp ON cpt.plot_id = cp.id WHERE (cp.status= 1 AND cpt.status = 1 ) AND cpt.phase_id = $phase_id LIMIT 1";
                        
                        //$sql = "SELECT SUM(amount) as amount FROM customer_plot_transactions cpt WHERE cpt.status = 1 AND cpt.phase_id = $phase_id LIMIT 1";
                        $stats = Yii::app()->db->createCommand($sql)->queryAll();


                        $sqlExtra = "SELECT SUM(amount) as amount FROM customer_plot_extra_transactions cpt JOIN customer_plots cp ON cpt.plot_id = cp.id WHERE (cp.status= 1 AND cpt.status = 1 ) AND cpt.phase_id = $phase_id LIMIT 1";
                        //$sqlExtra = "SELECT SUM(amount) as amount FROM customer_plot_extra_transactions cpt WHERE cpt.status = 1 AND cpt.phase_id = $phase_id LIMIT 1";
                        $statsExtra = Yii::app()->db->createCommand($sqlExtra)->queryAll();
                        ?>
                        <div class="huge"><?php echo 'PKR '.number_format(@$stats[0]['amount']+@$statsExtra[0]['amount']);//CustomerPlotTransactions::model()->count("t.phase_id = $phase_id")?></div>
                        <div>Total Transactions</div>
                    </div>
                </div>
            </div>
            <a href="">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-lg-6 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-money fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <?php
                        $sqlCan = "SELECT SUM(amount) as amount FROM customer_plot_transactions cpt WHERE cpt.reason IS NOT NULL AND cpt.status = 0 AND cpt.phase_id = $phase_id LIMIT 1";
                        $statsCan = Yii::app()->db->createCommand($sqlCan)->queryAll();

                        $sqlCanExt = "SELECT SUM(amount) as amount FROM customer_plot_extra_transactions cpt WHERE cpt.status = 0 AND cpt.phase_id = $phase_id LIMIT 1";
                        $statsCanExt = Yii::app()->db->createCommand($sqlCanExt)->queryAll();
                        
                        ?>
                        <div class="huge"><?php echo 'PKR '.number_format(@$statsCan[0]['amount']+@$statsCanExt[0]['amount']);//CustomerPlotTransactions::model()->count("t.phase_id = $phase_id")?></div>
                        <div>Total Cancelled Transactions</div>
                    </div>
                </div>
            </div>
            <a href="">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>


     <div class="col-lg-6 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-money fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">&nbsp;</div>
                        <div>Upcoming Payments (<?php echo date('M-Y',strtotime(date('Y-m-d')."+0 month"))?>)</div>
                    </div>
                </div>
            </div>
            <a href="">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-6 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-home fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <!--<div class="huge"><?php echo Plots::model()->with('customerPlots')->count("t.status = 1 AND customerPlots.blocked = 0 AND customerPlots.status = 1 AND t.phase_id = $phase_id")?></div>-->
                        <div class="huge"><?php echo CustomerPlots::model()->count('status=1')?></div>
                        <div>Sale Summary</div>
                    </div>
                </div>
            </div>
            <a href="">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    
    
    
    <div class="col-lg-12 col-md-12">
        <h1 class="page-header">Expense</h1>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-money fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <?php $expenseTotal = Expenses::model()->findAll(array(
                          'select'=>'SUM(amount) as total_sum',
                          'condition'=>"expense_type != 15 AND status = 1 AND phase_id = $phase_id"));?>
                        <div class="huge" style="font-size: 30px!important"><?php echo 'Rs. '.number_format($expenseTotal[0]->total_sum);?></div>
                        <div>Expense</div>
                    </div>
                </div>
            </div>
            <a href="">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-money fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <?php $expenseTotal = Expenses::model()->findAll(array(
                          'select'=>'SUM(amount) as total_sum',
                          'condition'=>"expense_type != 15 AND status = 1 AND expense_type = 'other_payment' AND phase_id = $phase_id"));?>
                        <div class="huge" style="font-size: 30px!important"><?php echo 'Rs. '.number_format($expenseTotal[0]->total_sum);?></div>
                        <div>Other Payment Expense</div>
                    </div>
                </div>
            </div>
            <a href="">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>