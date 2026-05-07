CREATE TABLE IF NOT EXISTS especialidad (
    id_especialidad SERIAL PRIMARY KEY,
    nombre_especialidad VARCHAR(100) NOT NULL,
    descripcion TEXT
);

CREATE TABLE IF NOT EXISTS servicio (
    id_servicio SERIAL PRIMARY KEY,
    nombre_servicio VARCHAR(150) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS medico (
    id_medico SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    cedula VARCHAR(30) NOT NULL UNIQUE,
    correo VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    id_especialidad INTEGER NOT NULL REFERENCES especialidad(id_especialidad)
);

INSERT INTO especialidad (nombre_especialidad, descripcion) VALUES
    ('Cardiología',       'Cuidado integral del corazón con tecnología de punta.'),
    ('Pediatría',         'Atención especializada para el desarrollo y salud de los menores.'),
    ('Neurología',        'Diagnóstico y tratamiento de trastornos del sistema nervioso.'),
    ('Traumatología',     'Especialistas en lesiones óseas, articulares y rehabilitación física.'),
    ('Medicina General',  'Primer punto de contacto para diagnósticos precisos y medicina preventiva.'),
    ('Oftalmología',      'Cuidado completo de la visión, desde exámenes de rutina hasta cirugías.')
ON CONFLICT DO NOTHING;

INSERT INTO servicio (nombre_servicio, descripcion, activo) VALUES
    ('Laboratorio Clínico',      'Análisis de sangre, cultivos y pruebas especializadas con resultados en línea.', TRUE),
    ('Imagenología y Rayos X',   'Tomografía, ultrasonido, mastografía y radiografías de última generación.',       TRUE),
    ('Farmacia 24 Hrs',          'Amplio surtido de medicamentos de patente y especialidad.',                       TRUE),
    ('Urgencias y Traslados',    'Atención médica inmediata las 24 horas, los 365 días del año.',                   TRUE)
ON CONFLICT DO NOTHING;

INSERT INTO medico (nombre, apellido, cedula, correo, telefono, id_especialidad) VALUES
    ('Roberto',   'Sánchez', '1234567', 'r.sanchez@medicitas.com', '4611000001', 1),
    ('Elena',     'Medina',  '2345678', 'e.medina@medicitas.com',  '4611000002', 2),
    ('Alejandro', 'Gómez',   '3456789', 'a.gomez@medicitas.com',   '4611000003', 4)
ON CONFLICT DO NOTHING;
