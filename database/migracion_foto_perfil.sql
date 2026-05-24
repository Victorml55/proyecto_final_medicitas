-- Agrega columna de foto de perfil a la tabla usuarios
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS foto_perfil VARCHAR(255);