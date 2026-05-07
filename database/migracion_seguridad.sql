-- ============================================================
-- migracion_seguridad.sql
-- Datos iniciales: roles, permisos y usuario admin de prueba
-- ============================================================

-- Roles base
INSERT INTO roles (nombre_rol, descripcion) VALUES
    ('Administrador', 'Acceso total al sistema'),
    ('Recepcionista',  'Gestión de citas y pacientes'),
    ('Médico',         'Acceso a recetas y citas propias')
ON CONFLICT (nombre_rol) DO NOTHING;

-- Permisos por módulo (convenio: modulo.accion)
INSERT INTO permisos (nombre_permiso) VALUES
    -- Usuarios
    ('usuarios.ver'), ('usuarios.crear'), ('usuarios.editar'), ('usuarios.borrar'),
    -- Roles
    ('roles.ver'), ('roles.crear'), ('roles.editar'), ('roles.borrar'),
    -- Permisos
    ('permisos.ver'), ('permisos.crear'), ('permisos.editar'), ('permisos.borrar'),
    -- Médicos
    ('medicos.ver'), ('medicos.crear'), ('medicos.editar'), ('medicos.borrar'),
    -- Pacientes
    ('pacientes.ver'), ('pacientes.crear'), ('pacientes.editar'), ('pacientes.borrar'),
    -- Especialidades
    ('especialidades.ver'), ('especialidades.crear'), ('especialidades.editar'), ('especialidades.borrar'),
    -- Consultorios
    ('consultorios.ver'), ('consultorios.crear'), ('consultorios.editar'), ('consultorios.borrar'),
    -- Pagos
    ('pagos.ver'), ('pagos.crear'), ('pagos.editar'), ('pagos.borrar'),
    -- Recetas
    ('recetas.ver'), ('recetas.crear'), ('recetas.editar'), ('recetas.borrar'),
    -- Horarios
    ('horarios.ver'), ('horarios.crear'), ('horarios.editar'), ('horarios.borrar'),
    -- Configuración
    ('configuracion.ver'), ('configuracion.editar')
ON CONFLICT (nombre_permiso) DO NOTHING;

-- Asignar TODOS los permisos al rol Administrador
INSERT INTO rol_permisos (id_rol, id_permiso)
SELECT r.id_rol, p.id_permiso
FROM   roles r, permisos p
WHERE  r.nombre_rol = 'Administrador'
ON CONFLICT DO NOTHING;

-- Permisos del Recepcionista
INSERT INTO rol_permisos (id_rol, id_permiso)
SELECT r.id_rol, p.id_permiso
FROM   roles r
JOIN   permisos p ON p.nombre_permiso IN (
    'pacientes.ver','pacientes.crear','pacientes.editar',
    'medicos.ver',
    'especialidades.ver',
    'consultorios.ver',
    'pagos.ver','pagos.crear','pagos.editar'
)
WHERE  r.nombre_rol = 'Recepcionista'
ON CONFLICT DO NOTHING;

-- Permisos del Médico
INSERT INTO rol_permisos (id_rol, id_permiso)
SELECT r.id_rol, p.id_permiso
FROM   roles r
JOIN   permisos p ON p.nombre_permiso IN (
    'pacientes.ver',
    'recetas.ver','recetas.crear','recetas.editar',
    'horarios.ver'
)
WHERE  r.nombre_rol = 'Médico'
ON CONFLICT DO NOTHING;

-- ── Usuario administrador inicial ────────────────────────────────────
-- Contraseña: Admin1234  (cámbiala después de la primera vez)
-- password_hash generado con: password_hash('Admin1234', PASSWORD_BCRYPT)
INSERT INTO usuarios (nombre, apellido_paterno, email, password_hash, activo)
VALUES (
    'Administrador',
    'Sistema',
    'admin@medicitas.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- Admin1234
    true
)
ON CONFLICT (email) DO NOTHING;

-- Asignar rol Administrador al usuario creado
INSERT INTO usuario_roles (id_usuario, id_rol)
SELECT u.id_usuario, r.id_rol
FROM   usuarios u, roles r
WHERE  u.email = 'admin@medicitas.com'
  AND  r.nombre_rol = 'Administrador'
ON CONFLICT DO NOTHING;
