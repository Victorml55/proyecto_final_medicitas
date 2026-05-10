<?php

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

echo json_encode([
    'loggedIn' => isset($_SESSION['id_usuario']),
    'nombre'   => $_SESSION['nombre'] ?? null,
]);
