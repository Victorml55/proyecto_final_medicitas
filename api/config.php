<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function cargarEnv(): void {
    $path = dirname(__DIR__) . '/.env';
    if (!file_exists($path)) return;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val);
    }
}
cargarEnv();

function conectarDB(): PDO {
    $host = $_ENV['DB_HOST'] ?? 'postgres';
    $port = $_ENV['DB_PORT'] ?? '5432';
    $name = $_ENV['DB_NAME'] ?? 'medicitas';
    $user = $_ENV['DB_USER'] ?? 'admin';
    $pass = $_ENV['DB_PASSWORD'] ?? 'admin123';
    $dsn = "pgsql:host=$host;port=$port;dbname=$name";
    return new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}

function responder(int $codigo, mixed $datos): void {
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function leerBody(): array {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}