<?php

$envPath = dirname(__DIR__, 2) . '/.env';
if (file_exists($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val);
    }
}

define('MAIL_HOST',       $_ENV['MAIL_HOST']       ?? 'smtp.gmail.com');
define('MAIL_PORT',       (int)($_ENV['MAIL_PORT'] ?? 587));
define('MAIL_ENCRYPTION', $_ENV['MAIL_ENCRYPTION'] ?? 'tls');
define('MAIL_USERNAME',   $_ENV['MAIL_USERNAME']   ?? '');
define('MAIL_PASSWORD',   $_ENV['MAIL_PASSWORD']   ?? '');
define('MAIL_FROM_EMAIL', $_ENV['MAIL_FROM_EMAIL'] ?? '');
define('MAIL_FROM_NAME',  $_ENV['MAIL_FROM_NAME']  ?? 'MediCitas');
define('MAIL_DEBUG',      0);
