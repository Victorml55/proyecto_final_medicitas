<?php

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['loggedIn' => false]);
    exit;
}

function cargarEnv(): void {
    $path = dirname(__DIR__) . '/.env';
    if (!file_exists($path)) return;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val);
    }
}
cargarEnv();

$id_paciente = null;
try {
    $host = $_ENV['DB_HOST'] ?? 'postgres';
    $port = $_ENV['DB_PORT'] ?? '5432';
    $name = $_ENV['DB_NAME'] ?? 'medicitas';
    $user = $_ENV['DB_USER'] ?? 'admin';
    $pass = $_ENV['DB_PASSWORD'] ?? 'admin123';
    $db   = new PDO("pgsql:host=$host;port=$port;dbname=$name", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $stmt = $db->prepare('SELECT id_paciente FROM pacientes WHERE id_usuario = ? LIMIT 1');
    $stmt->execute([$_SESSION['id_usuario']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_paciente = $row['id_paciente'] ?? null;
} catch (Exception $e) {}

echo json_encode([
    'loggedIn'    => true,
    'id_usuario'  => $_SESSION['id_usuario'],
    'nombre'      => $_SESSION['nombre'] ?? null,
    'id_paciente' => $id_paciente,
]);
