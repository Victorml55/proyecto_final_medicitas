-- ============================================================
-- migracion_catalogo_farmacia.sql
-- Catálogo de medicamentos y productos disponibles en farmacia
-- ============================================================

CREATE TABLE IF NOT EXISTS catalogo_farmacia (
    id_catalogo   SERIAL PRIMARY KEY,
    nombre        VARCHAR(200) NOT NULL,
    categoria     VARCHAR(100) NOT NULL,
    presentacion  VARCHAR(100),
    precio        NUMERIC(10,2),
    disponible    BOOLEAN NOT NULL DEFAULT TRUE
);

INSERT INTO catalogo_farmacia (nombre, categoria, presentacion, precio, disponible) VALUES
    -- Analgésicos y antiinflamatorios
    ('Paracetamol 500mg',          'Analgésicos',          'Caja 20 tabletas',      45.00,  TRUE),
    ('Ibuprofeno 400mg',           'Analgésicos',          'Caja 20 tabletas',      55.00,  TRUE),
    ('Naproxeno 250mg',            'Analgésicos',          'Caja 10 tabletas',      62.00,  TRUE),
    ('Ketorolaco 10mg',            'Analgésicos',          'Caja 10 tabletas',      78.00,  TRUE),
    ('Diclofenaco 100mg',          'Analgésicos',          'Caja 10 supositorios',  90.00,  TRUE),

    -- Antibióticos
    ('Amoxicilina 500mg',          'Antibióticos',         'Caja 15 cápsulas',     120.00,  TRUE),
    ('Azitromicina 500mg',         'Antibióticos',         'Caja 3 tabletas',      145.00,  TRUE),
    ('Ciprofloxacino 500mg',       'Antibióticos',         'Caja 10 tabletas',     135.00,  TRUE),
    ('Metronidazol 500mg',         'Antibióticos',         'Caja 20 tabletas',      95.00,  TRUE),

    -- Antihipertensivos
    ('Losartán 50mg',              'Antihipertensivos',    'Caja 30 tabletas',      85.00,  TRUE),
    ('Enalapril 10mg',             'Antihipertensivos',    'Caja 30 tabletas',      70.00,  TRUE),
    ('Amlodipino 5mg',             'Antihipertensivos',    'Caja 30 tabletas',      75.00,  TRUE),
    ('Metoprolol 100mg',           'Antihipertensivos',    'Caja 20 tabletas',      80.00,  FALSE),

    -- Gastrointestinales
    ('Omeprazol 20mg',             'Gastrointestinal',     'Caja 14 cápsulas',      65.00,  TRUE),
    ('Ranitidina 150mg',           'Gastrointestinal',     'Caja 20 tabletas',      55.00,  TRUE),
    ('Metoclopramida 10mg',        'Gastrointestinal',     'Caja 20 tabletas',      48.00,  TRUE),
    ('Loperamida 2mg',             'Gastrointestinal',     'Caja 12 cápsulas',      52.00,  TRUE),

    -- Vitaminas y suplementos
    ('Complejo B',                 'Vitaminas',            'Frasco 30 tabletas',    90.00,  TRUE),
    ('Vitamina C 500mg',           'Vitaminas',            'Frasco 30 tabletas',    75.00,  TRUE),
    ('Calcio + Vitamina D3',       'Vitaminas',            'Frasco 60 tabletas',   110.00,  TRUE),
    ('Hierro 300mg',               'Vitaminas',            'Frasco 30 tabletas',    85.00,  TRUE),

    -- Material de curación
    ('Gasas estériles 10x10cm',    'Material de curación', 'Paquete 10 piezas',     35.00,  TRUE),
    ('Vendas elásticas 5cm',       'Material de curación', 'Pieza',                 28.00,  TRUE),
    ('Micropore 1"',               'Material de curación', 'Rollo',                 22.00,  TRUE),
    ('Alcohol isopropílico 500ml', 'Material de curación', 'Frasco',                45.00,  TRUE),
    ('Agua oxigenada 500ml',       'Material de curación', 'Frasco',                30.00,  TRUE)
ON CONFLICT DO NOTHING;
