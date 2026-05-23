<?php

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit;
}

$data     = json_decode(file_get_contents('php://input'), true) ?? [];
$email    = trim($data['email']    ?? $_POST['email']    ?? '');
$password = trim($data['password'] ?? $_POST['password'] ?? '');

if (!$email || !$password) {
    echo json_encode(['success' => false, 'error' => 'Ingresa tu correo y contraseña.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'El correo no tiene un formato válido.']);
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

try {
    $host = $_ENV['DB_HOST']     ?? 'postgres';
    $port = $_ENV['DB_PORT']     ?? '5432';
    $name = $_ENV['DB_NAME']     ?? 'medicitas';
    $user = $_ENV['DB_USER']     ?? 'admin';
    $pass = $_ENV['DB_PASSWORD'] ?? 'admin123';

    $db = new PDO(
        "pgsql:host=$host;port=$port;dbname=$name",
        $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );

    $stmt = $db->prepare(
        'SELECT id_usuario, nombre, email, password_hash
         FROM usuarios
         WHERE email = ? AND activo = true
         LIMIT 1'
    );
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre']     = $usuario['nombre'];
        $_SESSION['email']      = $usuario['email'];

        // Detectar si el usuario es médico
        $stmtMedico = $db->prepare('SELECT id_medico FROM medicos WHERE id_usuario = ? LIMIT 1');
        $stmtMedico->execute([$usuario['id_usuario']]);
        $medico = $stmtMedico->fetch();

        $rol = 'paciente';
        if ($medico) {
            $rol = 'medico';
            $_SESSION['id_medico'] = $medico['id_medico'];
        }
        $_SESSION['rol'] = $rol;

        echo json_encode(['success' => true, 'nombre' => $usuario['nombre'], 'rol' => $rol]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Correo o contraseña incorrectos.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al conectar con la base de datos.']);
}
