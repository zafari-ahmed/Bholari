<?php

return array(
    'app_name' => 'Gulshan-e-Bholari Recovery',
    'recovery_manager_type_id' => 8,
    'default_phase_id' => 1,
    'upload_dir' => dirname(__DIR__) . '/uploads/booking_audio/',
    'call_status' => require dirname(__DIR__) . '/protected/config/call_status.php',
    'booking_status' => require dirname(__DIR__) . '/protected/config/booking_status.php',
    'call_status_color' => require dirname(__DIR__) . '/protected/config/call_status_color.php',
    'booking_status_color' => require dirname(__DIR__) . '/protected/config/booking_status_color.php',
);
