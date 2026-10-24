<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Activity Logs</h1>
        <p class="help-block">Activity is recorded for every user (super admin and all other roles). Only super admin can open this page or download the log file.</p>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                All user activity
                <?php
                    $exportQuery = http_build_query(array_filter($filters, function ($v) {
                        return $v !== '' && $v !== null;
                    }));
                    $exportUrl = Yii::app()->baseUrl.'/activitylog/export'.($exportQuery ? '?'.$exportQuery : '');
                ?>
                <a href="<?php echo CHtml::encode($exportUrl)?>" class="btn btn-success btn-xs pull-right">Download CSV</a>
            </div>
            <div class="panel-body">
                <form method="GET" action="<?php echo Yii::app()->baseUrl?>/activitylog" class="row" style="margin-bottom:15px;">
                    <div class="form-group col-lg-3">
                        <label>User</label>
                        <select name="user_id" class="form-control">
                            <option value="">All users (all roles)</option>
                            <?php foreach($users as $u): ?>
                                <option value="<?php echo (int)$u->id?>" <?php echo ((string)$filters['user_id'] === (string)$u->id) ? 'selected' : ''?>>
                                    <?php echo CHtml::encode(trim($u->first_name.' '.$u->last_name).' ('.$u->username.')'.($u->userType ? ' — '.$u->userType->name : ''))?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-2">
                        <label>From</label>
                        <input type="date" name="date_from" class="form-control" value="<?php echo CHtml::encode($filters['date_from'])?>">
                    </div>
                    <div class="form-group col-lg-2">
                        <label>To</label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo CHtml::encode($filters['date_to'])?>">
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Search</label>
                        <input type="text" name="q" class="form-control" placeholder="Description, URL, IP..." value="<?php echo CHtml::encode($filters['q'])?>">
                    </div>
                    <div class="form-group col-lg-2">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="<?php echo Yii::app()->baseUrl?>/activitylog" class="btn btn-default">Reset</a>
                        </div>
                    </div>
                </form>
                <table width="100%" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>IP</th>
                            <th>Action</th>
                            <th>Description</th>
                            <!-- <th>URL</th> -->
                            <!-- <th>Method</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php $logsList = $logs->getData(); ?>
                        <?php if($logsList){ foreach($logsList as $log): ?>
                        <tr>
                            <td><?php echo date('d M, Y h:i A', strtotime($log->created_on))?></td>
                            <td><?php echo CHtml::encode($log->user_name)?></td>
                            <td><?php echo CHtml::encode(($log->user && $log->user->userType) ? $log->user->userType->name : '-')?></td>
                            <td><?php echo CHtml::encode($log->ip_address)?></td>
                            <td><?php echo CHtml::encode(ucfirst($log->controller).' / '.ucfirst($log->action))?></td>
                            <td><?php echo CHtml::encode($log->description)?></td>
                            <!-- <td style="max-width:240px;word-break:break-all;"><?php echo CHtml::encode($log->url)?></td> -->
                            <!-- <td><?php echo CHtml::encode($log->method)?></td> -->
                        </tr>
                        <?php endforeach;} else { ?>
                        <tr>
                            <td colspan="8">No activity found.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php $this->widget('CLinkPager', array(
                    'pages' => $logs->getPagination(),
                    'header' => '',
                    'htmlOptions' => array('class' => 'pagination'),
                )); ?>
            </div>
        </div>
    </div>
</div>
