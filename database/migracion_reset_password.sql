CREATE TABLE IF NOT EXISTS tokens_recuperacion (
    id          SERIAL PRIMARY KEY,
    id_usuario  INT         NOT NULL REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    token       VARCHAR(64) UNIQUE NOT NULL,
    expira_en   TIMESTAMP   DEFAULT (NOW() + INTERVAL '1 hour'),
    usado       BOOLEAN     DEFAULT FALSE
);
