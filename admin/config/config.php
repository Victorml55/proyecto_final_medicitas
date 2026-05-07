<?php

$_envPath = dirname(__DIR__, 2) . '/.env';
if (file_exists($_envPath)) {
    foreach (file($_envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_line) {
        if (str_starts_with(trim($_line), '#')) continue;
        [$_k, $_v] = explode('=', $_line, 2);
        $_ENV[trim($_k)] = trim($_v);
    }
}

define('DBDRIVER',   'pgsql');
define('DBHOST',     $_ENV['DB_HOST']     ?? 'postgres');
define('DBPORT',     $_ENV['DB_PORT']     ?? '5432');
define('DBNAME',     $_ENV['DB_NAME']     ?? 'medicitas');
define('DBUSER',     $_ENV['DB_USER']     ?? 'admin');
define('DBPASSWORD', $_ENV['DB_PASSWORD'] ?? 'admin123');
