<?php

function recovery_asset_url($path)
{
    return '../' . ltrim($path, '/');
}

function recovery_render_layout($content, $pageTitle = 'Recovery')
{
    $config = recovery_config();
    $user = recovery_user();
    $flash = recovery_get_flash();
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle . ' - ' . $config['app_name']); ?></title>
    <link href="<?php echo recovery_base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo recovery_base_url('assets/vendor/metisMenu/metisMenu.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo recovery_base_url('assets/vendor/datatables-plugins/dataTables.bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?php echo recovery_base_url('assets/dist/css/bootstrap-datepicker.css'); ?>" rel="stylesheet">
    <link href="<?php echo recovery_base_url('assets/dist/css/sb-admin-2.css'); ?>" rel="stylesheet">
    <link href="<?php echo recovery_base_url('assets/vendor/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo recovery_base_url('assets/recovery.css'); ?>" rel="stylesheet">
    <script src="<?php echo recovery_base_url('assets/vendor/jquery/jquery.min.js'); ?>"></script>
</head>
<body>
<div id="wrapper">
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo recovery_base_url('index.php?page=dashboard'); ?>"><?php echo htmlspecialchars($config['app_name']); ?></a>
        </div>
        <ul class="nav navbar-top-links navbar-right">
            <?php if ($user): ?>
            <li><span class="navbar-text"><?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['type']); ?>)</span></li>
            <li><a href="<?php echo recovery_base_url('api/logout.php'); ?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
            <?php endif; ?>
        </ul>
        <?php if ($user): ?>
        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                        <a href="<?php echo recovery_base_url('index.php?page=dashboard'); ?>"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                    </li>
                    <li class="<?php echo $currentPage === 'bookings' ? 'active' : ''; ?>">
                        <a href="<?php echo recovery_base_url('index.php?page=bookings'); ?>"><i class="fa fa-book fa-fw"></i> Bookings</a>
                    </li>
                    <li class="<?php echo $currentPage === 'chat' ? 'active' : ''; ?>">
                        <a href="<?php echo recovery_base_url('index.php?page=chat'); ?>"><i class="fa fa-comments fa-fw"></i> Team Chat</a>
                    </li>
                    <?php if (recovery_is_manager()): ?>
                    <li class="<?php echo $currentPage === 'staff' ? 'active' : ''; ?>">
                        <a href="<?php echo recovery_base_url('index.php?page=staff'); ?>"><i class="fa fa-users fa-fw"></i> Staff</a>
                    </li>
                    <li class="<?php echo $currentPage === 'reports' ? 'active' : ''; ?>">
                        <a href="<?php echo recovery_base_url('index.php?page=reports'); ?>"><i class="fa fa-bar-chart fa-fw"></i> Reports</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </nav>

    <div id="page-wrapper" style="<?php echo $user ? '' : 'margin-left:0;'; ?>">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo htmlspecialchars($pageTitle); ?></h1>
            </div>
        </div>
        <?php if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
        <?php endif; ?>
        <?php echo $content; ?>
    </div>
</div>

<script src="<?php echo recovery_asset_url('assets/vendor/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo recovery_asset_url('assets/vendor/metisMenu/metisMenu.min.js'); ?>"></script>
<script src="<?php echo recovery_asset_url('assets/vendor/datatables/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo recovery_asset_url('assets/vendor/datatables-plugins/dataTables.bootstrap.min.js'); ?>"></script>
<script src="<?php echo recovery_asset_url('assets/dist/js/bootstrap-datepicker.min.js'); ?>"></script>
<script src="<?php echo recovery_asset_url('assets/dist/js/sb-admin-2.js'); ?>"></script>
<script>var recoveryBaseUrl = '<?php echo recovery_base_url(); ?>';</script>
<script src="<?php echo recovery_base_url('assets/recovery.js'); ?>"></script>
</body>
</html>
    <?php
}

function recovery_render_login_layout($content, $pageTitle = 'Login')
{
    $config = recovery_config();
    $flash = recovery_get_flash();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle . ' - ' . $config['app_name']); ?></title>
    <link href="<?php echo recovery_asset_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo recovery_asset_url('assets/dist/css/sb-admin-2.css'); ?>" rel="stylesheet">
    <link href="<?php echo recovery_asset_url('assets/vendor/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo recovery_base_url('assets/recovery.css'); ?>" rel="stylesheet">
</head>
<body>
<?php if ($flash): ?>
<div class="container" style="margin-top:20px;">
    <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?>">
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
</div>
<?php endif; ?>
<?php echo $content; ?>
<script src="<?php echo recovery_asset_url('assets/vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo recovery_asset_url('assets/vendor/bootstrap/js/bootstrap.min.js'); ?>"></script>
</body>
</html>
    <?php
}
