<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">
    
    <!-- Date picker -->
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/dist/css/bootstrap-datepicker.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/vendor/morrisjs/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">


    <link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/dist/css/select2.min.css" rel="stylesheet" />

    
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/vendor/jquery/jquery.min.js"></script>
    
    <link rel="icon" href="<?php echo Yii::app()->baseUrl?>/images/favicon.ico" type="image/x-icon">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
        .aLink{
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo Yii::app()->baseUrl?>/dashboard"><?php echo Yii::app()->name; ?></a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <?php /*?>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <a href="#">
                                <div>
                                    <strong>John Smith</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>John Smith</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>John Smith</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>Read All Messages</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-messages -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-tasks fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-tasks">
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 1</strong>
                                        <span class="pull-right text-muted">40% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                                            <span class="sr-only">40% Complete (success)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 2</strong>
                                        <span class="pull-right text-muted">20% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                                            <span class="sr-only">20% Complete</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 3</strong>
                                        <span class="pull-right text-muted">60% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                                            <span class="sr-only">60% Complete (warning)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 4</strong>
                                        <span class="pull-right text-muted">80% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                            <span class="sr-only">80% Complete (danger)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>See All Tasks</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-tasks -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-alerts">
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-comment fa-fw"></i> New Comment
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                                    <span class="pull-right text-muted small">12 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-envelope fa-fw"></i> Message Sent
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-tasks fa-fw"></i> New Task
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-upload fa-fw"></i> Server Rebooted
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>See All Alerts</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-alerts -->
                </li>
                <?php */?>
                <!-- /.dropdown -->
                <?php $userModel = Yii::app()->session->get('userModel');?>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"> 
                        <?php echo ucwords($userModel['first_name'].' '.$userModel['last_name'])?> <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <!-- <li><a href="<?php //echo Yii::app()->baseUrl.'/user/profile'?>"><i class="fa fa-user fa-fw"></i> User Profile</a></li>-->
                        <li><a href="<?php echo Yii::app()->baseUrl.'/user/changepassword'?>"><i class="fa fa-gear fa-fw"></i> Change Password</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo Yii::app()->params['AppUrl'].'/site/logout'?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->
            <?php 

                $blocks = Plots::model()->findAll(array(
                    'select'=>'t.block_number',
                    'distinct'=>true,
                )); 

                $transactions = CustomerPlotTransactions::model()->findAll(array(
                    'select'=>'t.transaction_number',
                    'distinct'=>true,
                ));


            ?>
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <!-- <input type="text" id="searchPlotText" class="form-control" placeholder="Search..."> -->
                                <!-- <select name="searchPlotText" id="searchPlotText" class="form-control select2_main"> -->
                                    <!-- <option value="">Block# / Trans #</option> -->
                                    <?php /*foreach($blocks as $block):?>
                                        <option value="<?php echo $block->block_number?>"><?php echo $block->block_number?></option>
                                    <?php endforeach;*/?>
                                    <!-- <option disabled="disabled">Transaction #</option> -->
                                    <?php /*foreach($transactions as $transaction):?>
                                        <option value="<?php echo $transaction->transaction_number?>"><?php echo ($this->startsWith($transaction->transaction_number, '#'))?$transaction->transaction_number:'#'.$transaction->transaction_number?></option>
                                    <?php endforeach;*/?>
                                <!-- </select> -->
                                <select id="searchPlotText" class="form-control select2_main"></select>
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="searchPlot" style="padding: 4px;left: -16px;">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>
                            <!-- /input-group -->
                        </li>
                        <li>
                            <a href="<?php echo Yii::app()->baseUrl.'/dashboard'?>"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 3 || $userModel['user_type']['id'] == 4 || $userModel['user_type']['id'] == 5 || $userModel['user_type']['id'] == 6){?>
                        <li>
                            <a href="#"><i class="fa fa-book fa-fw"></i> Bookings<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/booking'?>">Manage Bookings</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/callings'?>">Call History</a>
                                </li>
                                <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 3 || $userModel['user_type']['id'] == 5){?>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/booking/add'?>">New Booking</a>
                                </li>
                                
                                <?php }?>
                                <!-- <li>
                                    <a href="<?php //echo Yii::app()->baseUrl.'/booking/letters'?>">Booking Reminder Letters</a>
                                </li> -->
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <?php } ?>
                        <!--<li>-->
                        <!--    <a href="<?php //echo Yii::app()->baseUrl.'/plot?status=0'?>"><i class="fa fa-dashboard fa-fw"></i> Available Bookings</a>-->
                        <!--</li>-->
                        <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                        <li>
                            <a href="#"><i class="fa fa-table fa-fw"></i> Plots<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/plot'?>">Manage Plots</a>
                                </li>
                                <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?> 
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/plot/add'?>">Add Plots</a>
                                </li>
                                <?php } ?>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                        <li>
                            <a href="#"><i class="fa fa-table fa-fw"></i> Plot Size<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/sizes'?>">Manage Plot Size</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/sizes/add'?>">Add Plot Size</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <?php } ?>
                        <?php } ?>
                        <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                        <li>
                            <a href="#"><i class="fa fa-user fa-fw"></i> Users<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/user'?>">Manage Users</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/user/add'?>">Add User</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <?php } ?>
                        <?php if($userModel['id'] == 1 || $userModel['id'] == 29 || $userModel['id'] == 30){?>
                        <li>
                            <a href="#"><i class="fa fa-user fa-fw"></i> Dealers<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <?php if($userModel['user_type']['id'] == 1 || $userModel['id'] == 29){?>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/agent'?>">Manage Dealers</a>
                                </li>
                                <?php } ?>
                                <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/agent/add'?>">Add Dealers</a>
                                </li>
                                <?php } ?>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <?php } ?>
                        
                        <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5 || $userModel['user_type']['id'] == 4){?>
                        <li>
                            <a href="#"><i class="fa fa-money fa-fw"></i> Expenses<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/expenses/import'?>">Upload Expense</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/expenses'?>">Manage Expenses</a>
                                </li>
                                <!--<li>
                                    <a href="<?php //echo Yii::app()->baseUrl.'/expenses?development=1'?>">Manage Development Expenses</a>
                                </li>-->
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/expenses/add'?>">Add Expense</a>
                                </li>
                                <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/expenses/report'?>">Expense Report</a>
                                </li>
                                <?php } if($userModel['user_type']['id'] == 1){?>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/expenses/summary'?>">Expense Summary</a>
                                </li>
                                <?php } ?>
                                <!-- <li>
                                    <a href="<?php //echo Yii::app()->baseUrl.'/paymentmode/add'?>">Add Payment Mode Setting</a>
                                </li> -->
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <?php }?>
                        
                        <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                        <li>
                            <a href="#"><i class="fa fa-cogs fa-fw"></i> Payment Schedules<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/paymentschedule'?>">Manage Payment Schedules</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/paymentschedule/add'?>">Add Payment Schedule</a>
                                </li>
                            </ul>
                        </li>
                        <?php }?>
                        
                        <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5 || $userModel['user_type']['id'] == 4){?>
                        
                        <li>
                            <a href="#"><i class="fa fa-dashboard fa-fw"></i> Reports<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/expenses/accounts'?>">Accounts Report</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/report/cancelled'?>">Cancelled Plots</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/report'?>">Transaction Report</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/report/report3'?>">Dealer Report</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/report/dealerreport'?>">Dealer Comm. Report</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/report/callingreport'?>">Calling Report</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/report/blockreport'?>">Block Report</a>
                                </li>
                               <?php if($userModel['id'] == 1 || $userModel['id'] == 29 || $userModel['id'] == 25){?>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/report/commissionreport'?>">Comm. Report</a>
                                </li>
                                <?php }?>
                                
                                <?php if($userModel['id'] == 1 || $userModel['id'] == 29 || $userModel['id'] == 25){?>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/report/monthlyinstallmentreport'?>">Monthly Inst. Report</a>
                                </li>
                                <?php }?>
                            </ul>
                        </li>
                        <?php }?>
                        
                        
                        <?php if($userModel['user_type']['id'] == 6 || $userModel['user_type']['id'] == 7){?>
                        
                        <li>
                            <a href="#"><i class="fa fa-dashboard fa-fw"></i> Reports<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/report/report3'?>">Dealer Report</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/report/callingreport'?>">Calling Report</a>
                                </li>
                                
                            </ul>
                        </li>
                        <?php }?>
                        <?php /*?>
                        <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5 || $userModel['user_type']['id'] == 4){?>
                            <li>
                                <a href="#"><i class="fa fa-user fa-fw"></i> Dealers<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="<?php echo Yii::app()->baseUrl.'/agent'?>">Manage Dealers</a>
                                    </li>
                                    <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                                    <li>
                                        <a href="<?php echo Yii::app()->baseUrl.'/agent/add'?>">Add Dealers</a>
                                    </li>
                                    <?php } ?>
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            
                            <li>
                                <a href="#"><i class="fa fa-user fa-fw"></i> Development Charges<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="<?php echo Yii::app()->baseUrl.'/charges'?>">Manage Charges</a>
                                    </li>
                                    <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                                    <li>
                                        <a href="<?php echo Yii::app()->baseUrl.'/charges/add'?>">Add Charges</a>
                                    </li>
                                    <?php } ?>
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            <?php } ?>
                                <?php if($userModel['user_type']['id'] != 3){?>
                                <li>
                                    <a href="#"><i class="fa fa-dashboard fa-fw"></i> Report<span class="fa arrow"></span></a>
                                    <ul class="nav nav-second-level">
                                        <li>
                                            <a href="<?php echo Yii::app()->baseUrl.'/report'?>">Report</a>
                                        </li>
                                        <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                                        <li>
                                            <a href="<?php echo Yii::app()->baseUrl.'/booking/reportalltransaction'?>">Transactions Report</a>
                                        </li>

                                        <li>
                                            <a href="<?php echo Yii::app()->baseUrl.'/booking/reportalldevtransaction'?>">Extra Transactions Report</a>
                                        </li>
                                        
                                        <!-- <li>
                                            <a href="<?php echo Yii::app()->baseUrl.'/report/report2'?>">Account Report</a>
                                        </li> -->
                                        <?php }?>
                                        <?php if($userModel['user_type']['id'] == 1){?>
                                        <!-- <li>
                                            <a href="<?php //echo Yii::app()->baseUrl.'/report/report3'?>">Dealer Report</a>
                                        </li> -->

                                        <li>
                                            <a href="<?php echo Yii::app()->baseUrl.'/report/cancelled'?>">Cancelled Plots</a>
                                        </li>
                                        
                                        <li>
                                            <a href="<?php echo Yii::app()->baseUrl.'/report/transfered'?>">Transfered Plots</a>
                                        </li>

                                        <!-- <li>
                                            <a href="<?php ///echo Yii::app()->baseUrl.'/report/report4'?>">Datewise Report</a>
                                        </li> -->

                                        <!-- <li>
                                            <a href="<?php //echo Yii::app()->baseUrl.'/report/reportdev'?>">Dev/Pen Report</a>
                                        </li> -->
                                    <?php }?>
                                    <?php if($userModel['user_type']['id'] == 5){?>
                                        <!-- <li>
                                            <a href="<?php //echo Yii::app()->baseUrl.'/report/reportdev'?>">Dev/Pen Report</a>
                                        </li> -->
                                    <?php }?>

                                    </ul>
                                    <!-- /.nav-second-level -->
                                </li>
                                <?php } ?>

                                <?php if($userModel['user_type']['id'] != 3){?>
                                <li>
                                    <a href="#"><i class="fa fa-dashboard fa-fw"></i> Accounts<span class="fa arrow"></span></a>
                                    <ul class="nav nav-second-level">
                                        <li>
                                            <a href="<?php echo Yii::app()->baseUrl.'/expenses/accounts'?>">Account Reports</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo Yii::app()->baseUrl.'/expenses/accountreportcsv?start=2021-01-01&end='.date('Y-m-d')?>">Account Report CSV Download<br/>01 Jan, 2021 -- <?php echo date('d M, Y')?></a>
                                        </li>
                                    </ul>
                                    <!-- /.nav-second-level -->
                                </li>
                                <?php } ?>
                        <li>
                            <a href="#"><i class="fa fa-user fa-fw"></i> Customer<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/customer'?>">Manage Customer</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <?php if($userModel['user_type']['id'] == 1){?>
                        <li>
                            <a href="<?php echo Yii::app()->baseUrl.'/message/upload'?>"><i class="fa fa-envelope fa-fw"></i> Bulk Messages</a>
                        </li>
                        <li>
                            <a href="<?php echo Yii::app()->baseUrl.'/message/allmessages'?>"><i class="fa fa-envelope fa-fw"></i> Reminder Messages</a>
                        </li>
                        <?php }?>
                        <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 4 || $userModel['user_type']['id'] == 5){?>
                        <li>
                            <a href="<?php echo Yii::app()->baseUrl.'/message/add'?>"><i class="fa fa-envelope fa-fw"></i> Messages</a>
                        </li>
                        <?php }?>

                        <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                        <li>
                            <a href="#"><i class="fa fa-cogs fa-fw"></i> Payment Schedules<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/paymentschedule'?>">Manage Payment Schedules</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/paymentschedule/add'?>">Add Payment Schedule</a>
                                </li>
                            </ul>
                        </li>
                        <?php }?>

                        <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                        <li>
                            <a href="#"><i class="fa fa-cogs fa-fw"></i> Paymode Mode Setting<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/paymentmode'?>">Manage Payment Mode Setting</a>
                                </li>
                                <!-- <li>
                                    <a href="<?php //echo Yii::app()->baseUrl.'/paymentmode/add'?>">Add Payment Mode Setting</a>
                                </li> -->
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <?php }?>



                        <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                        <li>
                            <a href="#"><i class="fa fa-table fa-fw"></i> Extra Plan<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/extra'?>">Manage Extra Plan</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/extra/add'?>">Add Extra Plan</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <?php } ?>


                        <?php if($userModel['user_type']['id'] == 1){?>
                        <li>
                            <a href="#"><i class="fa fa-table fa-fw"></i> Phases<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/phase'?>">Manage Phases</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/phase/add'?>">Add Phase</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <?php } ?>


                        <?php if($userModel['user_type']['id'] == 1){?>
                        <li>
                            <a href="<?php echo Yii::app()->baseUrl.'/letter/development'?>"><i class="fa fa-table fa-fw"></i> Development Letter</a>
                            
                            <!-- /.nav-second-level -->
                        </li>
                        <?php } ?>


                        <?php //if($userModel['user_type']['id'] == 1){?>
                        <li>
                            <a href="<?php echo Yii::app()->baseUrl.'/warningletter'?>"><i class="fa fa-table fa-fw"></i> Warning Letters</a>
                            
                            <!-- /.nav-second-level -->
                        </li>
                        <?php //} ?>

                        <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5 || $userModel['user_type']['id'] == 3){?>
                        <li>
                            <a href="#"><i class="fa fa-money fa-fw"></i> Expenses<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/expenses'?>">Manage Expenses</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/expenses?development=1'?>">Manage Development Expenses</a>
                                </li>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/expenses/add'?>">Add Expense</a>
                                </li>
                                <?php if($userModel['user_type']['id'] == 1 || $userModel['user_type']['id'] == 5){?>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/expenses/report'?>">Expense Report</a>
                                </li>
                                <?php } if($userModel['user_type']['id'] == 1){?>
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/expenses/summary'?>">Expense Summary</a>
                                </li>
                                <?php } ?>
                                <!-- <li>
                                    <a href="<?php //echo Yii::app()->baseUrl.'/paymentmode/add'?>">Add Payment Mode Setting</a>
                                </li> -->
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <?php }?>

                        <?php //if($userModel['user_type']['id'] == 1){?>
                        <li>
                            <a href="#"><i class="fa fa-money fa-fw"></i> Petty Cash<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Yii::app()->baseUrl.'/pettycash/add'?>">Add PettyCash Expense</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <?php //}?>
                        <?php */?>
                        
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="alertMsgBox" style="display: none"></div>
            <?php echo $content?>
        </div>
        <!-- /#page-wrapper -->
<!-- Modal -->
<div class="modal fade" id="searchList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="    width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Search Result for block # <span class="blockText"></span></h4>
            </div>
            <div class="modal-body">
                <div class="">
                    <table class="table table-striped table-bordered table-hover" id="dataTabless">
                        <thead>
                            <tr>
                                <!-- <th>#</th> -->
                                <th>Block #</th>
                                <th>Plot #</th>
                                <th>Size</th>
                                <th>Category</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="searchListData">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Save</button> -->
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->



<!-- Modal -->
<div class="modal fade" id="searchList-transaction" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="    width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Search Result for Transaction # <span class="blockText"></span></h4>
            </div>
            <div class="modal-body">
                <div class="">
                    <table class="table table-striped table-bordered table-hover" id="dataTabless-transaction">
                        <thead>
                            <tr>
                                <!-- <th>#</th> -->
                                <th>Trans. #</th>
                                <th>Block / Plot</th>
                                <th>Amount</th>
                                <th>Trans. Type</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="searchListData-transaction">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Save</button> -->
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    

    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/vendor/raphael/raphael.min.js"></script>
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/vendor/morrisjs/morris.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/dist/js/sb-admin-2.js"></script>

    <!-- DataTables JavaScript -->
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/vendor/datatables-responsive/dataTables.responsive.js"></script>
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        var baseUrl = '<?php echo Yii::app()->baseUrl?>';
        var controller = '<?php echo Yii::app()->controller->id?>';
        var action = '<?php echo Yii::app()->controller->action->id;?>';


        $('#dataTables').DataTable({
            iDisplayLength : 50,
            responsive: true,
            aaSorting: [],
            aoColumnDefs: [
                { "bSearchable": false, "aTargets": [ 0 ] }
            ],
            lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
            order: [[ 0, "desc" ]]
        });

        $('#sizeTable').DataTable({
            iDisplayLength : 50,
            responsive: true,
            aaSorting: [],
        });

        $('.select2').select2();
        // $('.select2_main').select2({
        //   ajax: {
        //     url: baseUrl+'/dashboard/fetchall',
        //     dataType: 'json',
        //     data: function (params) {
        //       var query = {
        //         search: params.term,
        //         type: 'public'
        //       }

        //       // Query parameters will be ?search=[term]&type=public
        //       return query;
        //     }
        //     // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
        //   }
        // });

        $(".select2_main").select2({
          ajax: {
            url: baseUrl+'/dashboard/fetchall',
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return {
                q: params.term, // search term
                page: params.page
              };
            },
            processResults: function (data) {
                return {
                  results: data
                };
            },
            cache: true
          },
          placeholder: 'Block# / Trans #',
          minimumInputLength: 2,
        });


        var getUrlParameter = function getUrlParameter(sParam) {
            var sPageURL = window.location.search.substring(1),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;
            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                }
            }
            return false;
        };

        if(controller=='booking' && action == 'index'){
            var isInitialized = false;
            var flag_status = getUrlParameter('flag_status');
            var payment = getUrlParameter('payment');
            var documentFlag = getUrlParameter('documentFlag');
            var searchType = 'regNo';
            var bookingDatatable =$('#dataTablesServer').DataTable({
                "drawCallback": function( settings ) {
                    if(settings.iDraw == 1){
                        $('<div class="pull-right">&nbsp;&nbsp;Search Column: &nbsp;<select class="form-control" style="height:30px!important" id="searchType"><option value="plot">Plot #</option><option value="regNo" selected>File #</option><option value="name">Name</option><option value="cnic">CNIC</option><option value="number">Mobile Number</option></select></div>').appendTo(".dataTables_filter");
                    }
                    
                },
                iDisplayLength : 50,
                responsive: true,
                aaSorting: [],
                aoColumnDefs: [
                    { "bSearchable": false, "targets": [ 0 ] },
                    { "orderable": false, "targets": [ 3,4,5,6 ]  }
                ],
                lengthMenu: [ [50, 100, 200, -1], [50, 100, 200, "All"] ],
                order: [[ 0, "desc" ]],
                //ajax: baseUrl+'/player/fetchAll',
                ajax: {
                    url: baseUrl+'/booking/fetchAll',
                    data: function (d) {
                        d.flag_status = flag_status;
                        d.payment = payment;
                        d.documentFlag = documentFlag;
                        d.searchType = $('#searchType').val()??searchType;
                    }
                },
                processing: true,
                serverSide: true
            });

            $('body').on('change', '#searchType', function (e) {
                bookingDatatable.search( '' ).columns().search( '' ).draw();
            });
            
        }

        if(controller=='expenses' && action == 'index'){
            var isInitialized = false;
            var searchType = 'regNo';
            var bookingDatatable =$('#dataTablesExpenseServer').DataTable({
                "drawCallback": function( settings ) {
                    if(settings.iDraw == 1){
                        $('<div class="pull-right">&nbsp;&nbsp;Search Column: &nbsp;<select class="form-control" style="height:30px!important" id="searchType"><option value="desc">Description</option><option value="hoa">H.O.A</option><option value="paid_to">Paid To</option><option value="status">Status</option><option value="ref">Ref No.</option></select></div>').appendTo(".dataTables_filter");
                    }
                    
                },
                iDisplayLength : 50,
                responsive: true,
                aaSorting: [],
                aoColumnDefs: [
                    { "bSearchable": false, "targets": [ 0 ] },
                    { "orderable": false, "targets": [ 3,4,5,6 ]  }
                ],
                lengthMenu: [ [50, 100, 200, -1], [50, 100, 200, "All"] ],
                order: [[ 0, "desc" ]],
                //ajax: baseUrl+'/player/fetchAll',
                ajax: {
                    url: baseUrl+'/expenses/fetchAll',
                    data: function (d) {
                        d.searchType = $('#searchType').val()??searchType;
                    }
                },
                processing: true,
                serverSide: true
            });

            $('body').on('change', '#searchType', function (e) {
                bookingDatatable.search( '' ).columns().search( '' ).draw();
            });
        }
        
        if(controller=='transactions' && action == 'index'){
            $('#dataTables-trans').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 100,
                order: [[0, 'desc']], // hidden sort col
                ajax: {
                    url: '<?php echo Yii::app()->baseUrl; ?>/transactions/fetchall',
                    type: 'GET',
                    data: function(d){
                        d.status = '<?php //echo (int)$status; ?>';
                    }
                },
                columnDefs: [
                    { targets: [0], visible: false, searchable: false }, // hidden #
                    { targets: [11], orderable: false }                  // action not sortable
                ]
            });
        }
        if(controller=='plot' && action == 'index'){
            $('#dataTables-plot').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 100,
                order: [[0, 'desc']], // hidden sort col
                ajax: {
                    url: '<?php echo Yii::app()->baseUrl; ?>/plot/fetchall',
                    type: 'GET',
                    data: function(d){
                        d.status = '<?php //echo (int)$status; ?>';
                    }
                },
                // columnDefs: [
                //     { targets: [0], visible: false, searchable: false }, // hidden #
                //     { targets: [11], orderable: false }                  // action not sortable
                // ]
            });
        }
    </script>
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/dist/js/jquery.mask.min.js"></script>
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/dist/js/custom.js"></script>
    
</body>

</html>
