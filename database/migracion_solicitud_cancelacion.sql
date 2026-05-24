-- Migración: estado "Solicitud de cancelación" para cuando el paciente solicita cancelar una cita confirmada

INSERT INTO estados_cita (nombre_estado, descripcion, color)
SELECT 'Solicitud de cancelación', 'El paciente ha solicitado cancelar la cita confirmada', '#f59e0b'
WHERE NOT EXISTS (SELECT 1 FROM estados_cita WHERE nombre_estado = 'Solicitud de cancelación');
