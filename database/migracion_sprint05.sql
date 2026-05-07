-- ============================================================
-- Sprint 05 – Migración: tablas permisos y rol_permisos
-- ============================================================

-- Tabla de permisos individuales del sistema
CREATE TABLE IF NOT EXISTS permisos (
    id_permiso      SERIAL PRIMARY KEY,
    nombre_permiso  VARCHAR(100) NOT NULL UNIQUE,
    descripcion     TEXT,
    modulo          VARCHAR(50),
    activo          BOOLEAN DEFAULT TRUE
);

ALTER TABLE permisos OWNER TO admin;

-- Tabla pivote que asigna permisos a roles
CREATE TABLE IF NOT EXISTS rol_permisos (
    id_rol_permiso   SERIAL PRIMARY KEY,
    id_rol           INTEGER NOT NULL REFERENCES roles(id_rol)      ON DELETE CASCADE,
    id_permiso       INTEGER NOT NULL REFERENCES permisos(id_permiso) ON DELETE CASCADE,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_rol, id_permiso)
);

ALTER TABLE rol_permisos OWNER TO admin;

-- Datos de ejemplo
INSERT INTO permisos (nombre_permiso, descripcion, modulo) VALUES
    ('usuarios.ver',       'Ver listado de usuarios',         'Usuarios'),
    ('usuarios.crear',     'Crear nuevos usuarios',           'Usuarios'),
    ('usuarios.editar',    'Editar usuarios existentes',      'Usuarios'),
    ('usuarios.eliminar',  'Eliminar usuarios',               'Usuarios'),
    ('medicos.ver',        'Ver listado de médicos',          'Médicos'),
    ('medicos.crear',      'Crear nuevos médicos',            'Médicos'),
    ('medicos.editar',     'Editar médicos existentes',       'Médicos'),
    ('medicos.eliminar',   'Eliminar médicos',                'Médicos'),
    ('citas.ver',          'Ver listado de citas',            'Citas'),
    ('citas.crear',        'Agendar nuevas citas',            'Citas'),
    ('citas.editar',       'Modificar citas existentes',      'Citas'),
    ('citas.cancelar',     'Cancelar citas',                  'Citas'),
    ('reportes.ver',       'Ver reportes del sistema',        'Reportes'),
    ('config.ver',         'Ver configuración del sistema',   'Configuración'),
    ('config.editar',      'Editar configuración del sistema','Configuración')
ON CONFLICT (nombre_permiso) DO NOTHING;
