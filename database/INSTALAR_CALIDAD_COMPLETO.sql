-- ================================================================
-- INSTALACIÓN COMPLETA DEL MÓDULO DE CALIDAD - CINASA
-- Ejecutar TODO en phpMyAdmin en la BD: clientes_db
-- ================================================================

-- 1. TABLA DE DEFECTOS
CREATE TABLE IF NOT EXISTS defectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABLA DE PIEZAS PRODUCIDAS
CREATE TABLE IF NOT EXISTS piezas_producidas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    folio_pieza VARCHAR(50) UNIQUE NOT NULL,
    produccion_id BIGINT UNSIGNED NOT NULL,
    pedido_id BIGINT UNSIGNED NOT NULL,
    producto_id INT NOT NULL,
    numero_pedido VARCHAR(50) NOT NULL,
    item_code VARCHAR(100) NOT NULL,
    descripcion VARCHAR(500),
    supervisor_produccion VARCHAR(100),
    fecha_produccion DATE NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estatus ENUM('por_inspeccionar', 'liberada', 'rechazada', 'pendiente_reinspeccion') DEFAULT 'por_inspeccionar',
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_piezas_produccion FOREIGN KEY (produccion_id) REFERENCES produccion(id) ON DELETE CASCADE,
    CONSTRAINT fk_piezas_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_piezas_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_folio_pieza (folio_pieza),
    INDEX idx_produccion_id (produccion_id),
    INDEX idx_pedido_id (pedido_id),
    INDEX idx_estatus (estatus),
    INDEX idx_fecha_produccion (fecha_produccion),
    INDEX idx_item_code (item_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABLA DE INSPECCIONES
CREATE TABLE IF NOT EXISTS calidad_inspecciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    folio_pieza VARCHAR(50) NOT NULL,
    produccion_id BIGINT UNSIGNED NOT NULL,
    pedido_id BIGINT UNSIGNED NOT NULL,
    producto_id INT NOT NULL,
    numero_pedido VARCHAR(50) NOT NULL,
    item_code VARCHAR(100) NOT NULL,
    supervisor_produccion VARCHAR(100),
    inspector_calidad VARCHAR(100) NOT NULL,
    cantidad_inspeccionada DECIMAL(10, 2) NOT NULL,
    cantidad_aceptada DECIMAL(10, 2) NOT NULL DEFAULT 0,
    cantidad_rechazada DECIMAL(10, 2) NOT NULL DEFAULT 0,
    defectos JSON,
    observaciones TEXT,
    estatus ENUM('completa', 'pendiente') DEFAULT 'completa',
    fecha_inspeccion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_insp_produccion FOREIGN KEY (produccion_id) REFERENCES produccion(id) ON DELETE CASCADE,
    CONSTRAINT fk_insp_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_insp_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_folio_pieza (folio_pieza),
    INDEX idx_produccion_id (produccion_id),
    INDEX idx_fecha_inspeccion (fecha_inspeccion),
    INDEX idx_estatus (estatus),
    INDEX idx_item_code (item_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TABLA DE DEFECTOS POR INSPECCIÓN
CREATE TABLE IF NOT EXISTS inspeccion_defectos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inspeccion_id BIGINT UNSIGNED NOT NULL,
    defecto_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_insp_def_inspeccion FOREIGN KEY (inspeccion_id) REFERENCES calidad_inspecciones(id) ON DELETE CASCADE,
    CONSTRAINT fk_insp_def_defecto FOREIGN KEY (defecto_id) REFERENCES defectos(id) ON DELETE RESTRICT,
    INDEX idx_inspeccion_id (inspeccion_id),
    INDEX idx_defecto_id (defecto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. TABLA DE RESUMEN DE CALIDAD
CREATE TABLE IF NOT EXISTS calidad_resumen (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    produccion_id BIGINT UNSIGNED NOT NULL UNIQUE,
    total_piezas_producidas DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total_piezas_inspeccionadas DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total_piezas_aceptadas DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total_piezas_rechazadas DECIMAL(10, 2) NOT NULL DEFAULT 0,
    porcentaje_aceptacion DECIMAL(5, 2) NOT NULL DEFAULT 0,
    piezas_pendientes_inspeccion DECIMAL(10, 2) NOT NULL DEFAULT 0,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_resumen_produccion FOREIGN KEY (produccion_id) REFERENCES produccion(id) ON DELETE CASCADE,
    INDEX idx_produccion_id (produccion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. AGREGAR COLUMNAS A PRODUCCIÓN
ALTER TABLE produccion ADD COLUMN prod_liberada DECIMAL(10, 2) DEFAULT 0 COMMENT 'Cantidad de piezas liberadas por calidad';
ALTER TABLE produccion ADD COLUMN prod_rechazada DECIMAL(10, 2) DEFAULT 0 COMMENT 'Cantidad de piezas rechazadas por calidad';
ALTER TABLE produccion ADD COLUMN estado_calidad ENUM('sin_inspeccionar', 'inspeccionando', 'parcialmente_inspeccionada', 'completamente_inspeccionada', 'con_rechazos') DEFAULT 'sin_inspeccionar' COMMENT 'Estado de la inspección de calidad';
ALTER TABLE produccion ADD COLUMN qty_por_inspeccionar DECIMAL(10, 2) DEFAULT 0 COMMENT 'Cantidad pendiente de inspección o reinspección';

-- 7. AGREGAR ÍNDICES
ALTER TABLE produccion ADD INDEX idx_estado_calidad (estado_calidad);
ALTER TABLE produccion ADD INDEX idx_prod_liberada (prod_liberada);

-- 8. INSERTAR DEFECTOS INICIALES
INSERT INTO defectos (codigo, nombre, descripcion, estado) VALUES
('DEF001', 'Dimensión Fuera de Tolerancia', 'Pieza con medidas fuera de especificación técnica', 'activo'),
('DEF002', 'Deformación', 'Pieza deformada, torcida o doblada', 'activo'),
('DEF003', 'Rayadura Visible', 'Superficie con rayaduras o marcas visibles', 'activo'),
('DEF004', 'Rugosidad Inaceptable', 'Acabado superficial áspero o pobre', 'activo'),
('DEF005', 'Agujero Faltante', 'Falta algún agujero, orificio o perforación', 'activo'),
('DEF006', 'Burr o Rebaba', 'Presencia de rebabas metálicas sin remover', 'activo'),
('DEF007', 'Color Inadecuado', 'Color diferente al especificado o inconsistente', 'activo'),
('DEF008', 'Corrosión', 'Signos de oxidación, herrumbre o corrosión superficial', 'activo'),
('DEF009', 'Soldadura Defectuosa', 'Soldadura deficiente, incompleta o débil', 'activo'),
('DEF010', 'Grieta o Fractura', 'Grieta, rotura o fractura visible en la pieza', 'activo'),
('DEF011', 'Pintura Descascarada', 'Pintura descascarada, desprendida o desigual', 'activo'),
('DEF012', 'Ensamble Incorrecto', 'Componentes ensamblados incorrectamente o faltantes', 'activo'),
('DEF013', 'Entalle o Muesca', 'Entalle, muesca o corte no especificado', 'activo'),
('DEF014', 'Doblamiento Parcial', 'Doblamiento o torsión parcial de la pieza', 'activo'),
('DEF015', 'Material Incorrecto', 'Utilizó material diferente al especificado', 'activo'),
('DEF016', 'Porosidad', 'Burbujas, huecos o porosidad en el material', 'activo'),
('DEF017', 'Alineación Defectuosa', 'Alineación incorrecta de componentes', 'activo'),
('DEF018', 'Acabado de Borde', 'Bordes afilados, sin lijar o mal acabados', 'activo'),
('DEF019', 'Impureza o Mancha', 'Impurezas, manchas o contaminación superficial', 'activo'),
('DEF020', 'Otros Defectos', 'Defectos no clasificados en las categorías anteriores', 'activo');

-- ================================================================
-- ✅ INSTALACIÓN COMPLETADA
-- Próximos pasos:
-- 1. Acceder a: http://localhost/CINASA-main/calidad.php
-- 2. Registrar una producción para crear piezas
-- 3. Inspeccionar piezas en el módulo de Calidad
-- ================================================================
