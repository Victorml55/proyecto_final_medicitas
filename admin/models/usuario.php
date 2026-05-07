<?php

class Usuario extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare('SELECT id_usuario, nombre, apellido_paterno, apellido_materno, email, telefono, genero, activo, fecha_registro FROM usuarios ORDER BY apellido_paterno, nombre');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare('SELECT id_usuario, nombre, apellido_paterno, apellido_materno, email, telefono, fecha_nacimiento, genero, activo FROM usuarios WHERE id_usuario = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare('INSERT INTO usuarios (nombre, apellido_paterno, apellido_materno, email, password_hash, telefono, fecha_nacimiento, genero, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            trim($d['nombre']),
            trim($d['apellido_paterno']),
            trim($d['apellido_materno'] ?? '') ?: null,
            trim($d['email']),
            password_hash(trim($d['password']), PASSWORD_BCRYPT),
            trim($d['telefono'] ?? '') ?: null,
            trim($d['fecha_nacimiento'] ?? '') ?: null,
            $d['genero'] ?: null,
            isset($d['activo']) ? 'true' : 'false',
        ]);
    }

    function actualizar(array $d) {
        if (!empty(trim($d['password'] ?? ''))) {
            $stmt = $this->db->prepare('UPDATE usuarios SET nombre=?, apellido_paterno=?, apellido_materno=?, email=?, password_hash=?, telefono=?, fecha_nacimiento=?, genero=?, activo=? WHERE id_usuario=?');
            $stmt->execute([
                trim($d['nombre']),
                trim($d['apellido_paterno']),
                trim($d['apellido_materno'] ?? '') ?: null,
                trim($d['email']),
                password_hash(trim($d['password']), PASSWORD_BCRYPT),
                trim($d['telefono'] ?? '') ?: null,
                trim($d['fecha_nacimiento'] ?? '') ?: null,
                $d['genero'] ?: null,
                isset($d['activo']) ? 'true' : 'false',
                (int)$d['id_usuario'],
            ]);
        } else {
            $stmt = $this->db->prepare('UPDATE usuarios SET nombre=?, apellido_paterno=?, apellido_materno=?, email=?, telefono=?, fecha_nacimiento=?, genero=?, activo=? WHERE id_usuario=?');
            $stmt->execute([
                trim($d['nombre']),
                trim($d['apellido_paterno']),
                trim($d['apellido_materno'] ?? '') ?: null,
                trim($d['email']),
                trim($d['telefono'] ?? '') ?: null,
                trim($d['fecha_nacimiento'] ?? '') ?: null,
                $d['genero'] ?: null,
                isset($d['activo']) ? 'true' : 'false',
                (int)$d['id_usuario'],
            ]);
        }
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM usuarios WHERE id_usuario = ?');
        $stmt->execute([$id]);
    }

    function emailExiste(string $email, int $excludeId = 0): bool {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM usuarios WHERE email = ? AND id_usuario != ?');
        $stmt->execute([$email, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
