<?php

if (recovery_is_logged_in()) {
    recovery_redirect('dashboard');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!recovery_verify_csrf(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        recovery_flash('error', 'Invalid request.');
        recovery_redirect('login');
    }

    $loginType = isset($_POST['login_type']) ? $_POST['login_type'] : 'manager';
    $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $ok = false;
    if ($loginType === 'manager') {
        $ok = recovery_login_manager($username, $password);
    } else {
        $ok = recovery_login_staff($username, $password);
    }

    if ($ok) {
        recovery_redirect('dashboard');
    }
    recovery_flash('error', 'Invalid credentials or access denied.');
    recovery_redirect('login');
}

ob_start();
?>
<div class="container">
    <div class="row">
        <div class="col-md-5 col-md-offset-4">
            <div class="login-panel panel panel-default" style="margin-top:80px;">
                <div class="panel-heading">
                    <h3 class="panel-title">Recovery Sign In</h3>
                </div>
                <div class="panel-body">
                    <form method="post" action="<?php echo recovery_base_url('index.php?page=login'); ?>">
                        <input type="hidden" name="csrf" value="<?php echo recovery_csrf_token(); ?>">
                        <div class="form-group">
                            <label>Login As</label>
                            <select name="login_type" class="form-control">
                                <option value="manager">Recovery Manager</option>
                                <option value="staff">Recovery Staff</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Username / Email</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
recovery_render_login_layout($content, 'Login');
