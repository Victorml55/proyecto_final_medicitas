<?php

if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['id_usuario'])) {
    header('Location: inicio-usuario.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

if (!$email || !$password) {
    header('Location: index.html?error=' . urlencode('Ingresa tu correo y contraseña.'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.html?error=' . urlencode('El correo no tiene un formato válido.'));
    exit;
}

function cargarEnv(): void {
    $path = __DIR__ . '/.env';
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

        header('Location: inicio-usuario.html');
        exit;
    }

    header('Location: index.html?error=' . urlencode('Correo o contraseña incorrectos.'));

} catch (PDOException $e) {
    header('Location: index.html?error=' . urlencode('Error al conectar con la base de datos.'));
}
exit;
