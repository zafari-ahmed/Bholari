<?php

require_once dirname(__DIR__) . '/bootstrap.php';

recovery_logout();
recovery_redirect('login');
