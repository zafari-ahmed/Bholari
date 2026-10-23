<?php

require_once dirname(__DIR__) . '/bootstrap.php';
recovery_require_login();

header('Content-Type: application/json');
$messages = recovery_fetch_team_messages(100);
echo json_encode(array('success' => true, 'messages' => $messages));
