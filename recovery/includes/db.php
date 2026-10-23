<?php

function recovery_db()
{
    static $pdo = null;
    if ($pdo === null) {
        $dbConfig = require dirname(__DIR__) . '/../protected/config/database.php';
        $pdo = new PDO(
            $dbConfig['connectionString'],
            $dbConfig['username'],
            $dbConfig['password'],
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            )
        );
    }
    return $pdo;
}
