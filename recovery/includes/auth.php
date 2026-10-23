<?php

function recovery_config()
{
    static $config = null;
    if ($config === null) {
        $config = require dirname(__DIR__) . '/config.php';
    }
    return $config;
}

function recovery_user()
{
    return isset($_SESSION['recovery_user']) ? $_SESSION['recovery_user'] : null;
}

function recovery_is_logged_in()
{
    return recovery_user() !== null;
}

function recovery_is_manager()
{
    $user = recovery_user();
    return $user && $user['type'] === 'manager';
}

function recovery_is_staff()
{
    $user = recovery_user();
    return $user && $user['type'] === 'staff';
}

function recovery_require_login()
{
    if (!recovery_is_logged_in()) {
        recovery_redirect('login');
    }
}

function recovery_require_manager()
{
    recovery_require_login();
    if (!recovery_is_manager()) {
        recovery_flash('error', 'Access denied.');
        recovery_redirect('dashboard');
    }
}

function recovery_csrf_token()
{
    if (empty($_SESSION['recovery_csrf'])) {
        $_SESSION['recovery_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['recovery_csrf'];
}

function recovery_verify_csrf($token)
{
    return isset($_SESSION['recovery_csrf']) && hash_equals($_SESSION['recovery_csrf'], (string)$token);
}

function recovery_flash($type, $message)
{
    $_SESSION['recovery_flash'] = array('type' => $type, 'message' => $message);
}

function recovery_get_flash()
{
    if (empty($_SESSION['recovery_flash'])) {
        return null;
    }
    $flash = $_SESSION['recovery_flash'];
    unset($_SESSION['recovery_flash']);
    return $flash;
}

function recovery_base_url($path = '')
{
    $script = dirname($_SERVER['SCRIPT_NAME']);
    $base = rtrim(str_replace('\\', '/', $script), '/');
    if ($path === '') {
        return $base . '/';
    }
    return $base . '/' . ltrim($path, '/');
}

function recovery_redirect($page, $params = array())
{
    $query = array('page' => $page);
    $query = array_merge($query, $params);
    header('Location: ' . recovery_base_url('index.php?' . http_build_query($query)));
    exit;
}

function recovery_login_manager($email, $password)
{
    $config = recovery_config();
    $pdo = recovery_db();
    $stmt = $pdo->prepare(
        'SELECT u.* FROM users u
         WHERE (u.email_address = :login OR u.username = :login)
           AND u.password = :password
           AND u.status = 1
           AND u.user_type_id = :type_id
         LIMIT 1'
    );
    $stmt->execute(array(
        ':login' => $email,
        ':password' => md5($password),
        ':type_id' => $config['recovery_manager_type_id'],
    ));
    $user = $stmt->fetch();
    if (!$user) {
        return false;
    }

    $_SESSION['recovery_user'] = array(
        'type' => 'manager',
        'user_id' => (int)$user['id'],
        'name' => trim($user['first_name'] . ' ' . $user['last_name']),
        'username' => $user['username'],
        'phase_id' => $config['default_phase_id'],
    );
    return true;
}

function recovery_login_staff($username, $password)
{
    $pdo = recovery_db();
    $stmt = $pdo->prepare(
        'SELECT * FROM recovery_staff
         WHERE username = :username AND password = :password AND status = 1
         LIMIT 1'
    );
    $stmt->execute(array(
        ':username' => $username,
        ':password' => md5($password),
    ));
    $staff = $stmt->fetch();
    if (!$staff) {
        return false;
    }

    $blocks = recovery_staff_blocks((int)$staff['id']);
    $_SESSION['recovery_user'] = array(
        'type' => 'staff',
        'staff_id' => (int)$staff['id'],
        'name' => trim($staff['first_name'] . ' ' . $staff['last_name']),
        'username' => $staff['username'],
        'blocks' => $blocks,
        'phase_id' => recovery_config()['default_phase_id'],
    );
    return true;
}

function recovery_logout()
{
    unset($_SESSION['recovery_user']);
}

function recovery_staff_blocks($staffId)
{
    $pdo = recovery_db();
    $stmt = $pdo->prepare('SELECT block_number FROM recovery_staff_blocks WHERE staff_id = :id ORDER BY block_number');
    $stmt->execute(array(':id' => $staffId));
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function recovery_accessible_blocks()
{
    $user = recovery_user();
    if (!$user) {
        return array();
    }
    if ($user['type'] === 'manager') {
        return recovery_all_blocks();
    }
    return isset($user['blocks']) ? $user['blocks'] : array();
}

function recovery_all_blocks()
{
    $config = recovery_config();
    $pdo = recovery_db();
    $stmt = $pdo->prepare(
        'SELECT DISTINCT block_number FROM plots WHERE phase_id = :phase ORDER BY block_number'
    );
    $stmt->execute(array(':phase' => $config['default_phase_id']));
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function recovery_can_access_booking($bookingId)
{
    $pdo = recovery_db();
    $stmt = $pdo->prepare(
        'SELECT p.block_number FROM customer_plots cp
         INNER JOIN plots p ON p.id = cp.plot_id
         WHERE cp.id = :id LIMIT 1'
    );
    $stmt->execute(array(':id' => $bookingId));
    $row = $stmt->fetch();
    if (!$row) {
        return false;
    }
    if (recovery_is_manager()) {
        return true;
    }
    return in_array($row['block_number'], recovery_accessible_blocks(), true);
}
