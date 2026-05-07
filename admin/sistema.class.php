<?php

require_once(__DIR__ . '/config/config.php');

class Sistema {

    public $db;

    function conectar() {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $dsn      = DBDRIVER . ':host=' . DBHOST . ';port=' . DBPORT . ';dbname=' . DBNAME;
            $this->db = new PDO($dsn, DBUSER, DBPASSWORD, $options);
        } catch (PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }

    function alerta($tipo, $mensaje) {
        $alerta            = [];
        $alerta['tipo']    = $tipo;   
        $alerta['mensaje'] = $mensaje;
        include(__DIR__ . '/views/alerta.php');
    }
}