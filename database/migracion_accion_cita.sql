-- Migración: token de acción para confirmación de citas por médico vía correo

-- Token único para que el médico confirme/rechace/reprograme desde el correo
ALTER TABLE citas ADD COLUMN IF NOT EXISTS token_accion VARCHAR(64) UNIQUE;

-- Estado "Rechazada" (por el médico)
INSERT INTO estados_cita (nombre_estado, descripcion, color)
SELECT 'Rechazada', 'Cita rechazada por el médico', '#dc2626'
WHERE NOT EXISTS (SELECT 1 FROM estados_cita WHERE nombre_estado = 'Rechazada');
