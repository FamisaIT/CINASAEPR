-- Tabla de Defectos (Catálogo)
CREATE TABLE IF NOT EXISTS defectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de defectos para inspección de calidad';

-- Tabla de Piezas Producidas (con folio único)
CREATE TABLE IF NOT EXISTS piezas_producidas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    folio_pieza VARCHAR(50) UNIQUE NOT NULL COMMENT 'Folio único: PROD-YYYY-MM-DD-XXXXX',
    produccion_id BIGINT UNSIGNED NOT NULL,
    pedido_id BIGINT UNSIGNED NOT NULL,
    producto_id INT NOT NULL,
    numero_pedido VARCHAR(50) NOT NULL,
    item_code VARCHAR(100) NOT NULL,
    descripcion VARCHAR(500),
    supervisor_produccion VARCHAR(100),
    fecha_produccion DATE NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estatus ENUM('por_inspeccionar', 'liberada', 'rechazada', 'pendiente_reinspeccion') DEFAULT 'por_inspeccionar' COMMENT 'Estado de la pieza',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de piezas individuales producidas con folio único para trazabilidad';

-- Tabla de Inspecciones de Calidad
CREATE TABLE IF NOT EXISTS calidad_inspecciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    folio_pieza VARCHAR(50) NOT NULL,
    produccion_id BIGINT UNSIGNED NOT NULL,
    pedido_id BIGINT UNSIGNED NOT NULL,
    producto_id INT NOT NULL,
    numero_pedido VARCHAR(50) NOT NULL,
    item_code VARCHAR(100) NOT NULL,
    supervisor_produccion VARCHAR(100),
    inspector_calidad VARCHAR(100) NOT NULL COMMENT 'Persona que realizó la inspección',
    cantidad_inspeccionada DECIMAL(10, 2) NOT NULL COMMENT 'Cantidad de piezas inspeccionadas',
    cantidad_aceptada DECIMAL(10, 2) NOT NULL DEFAULT 0 COMMENT 'Cantidad aceptada',
    cantidad_rechazada DECIMAL(10, 2) NOT NULL DEFAULT 0 COMMENT 'Cantidad rechazada',
    defectos JSON COMMENT 'Defectos encontrados: {"defecto_id": cantidad, ...}',
    observaciones TEXT COMMENT 'Observaciones adicionales',
    estatus ENUM('completa', 'pendiente') DEFAULT 'completa' COMMENT 'Completa = validada',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Inspecciones de calidad realizadas a las piezas producidas';

-- Tabla de Detalles de Defectos por Inspección
CREATE TABLE IF NOT EXISTS inspeccion_defectos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inspeccion_id BIGINT UNSIGNED NOT NULL,
    defecto_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1 COMMENT 'Cantidad de piezas con este defecto',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_insp_def_inspeccion FOREIGN KEY (inspeccion_id) REFERENCES calidad_inspecciones(id) ON DELETE CASCADE,
    CONSTRAINT fk_insp_def_defecto FOREIGN KEY (defecto_id) REFERENCES defectos(id) ON DELETE RESTRICT,
    INDEX idx_inspeccion_id (inspeccion_id),
    INDEX idx_defecto_id (defecto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detalles de defectos por inspección';

-- Tabla de Resumen de Calidad por Producción
CREATE TABLE IF NOT EXISTS calidad_resumen (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    produccion_id BIGINT UNSIGNED NOT NULL UNIQUE,
    total_piezas_producidas DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total_piezas_inspeccionadas DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total_piezas_aceptadas DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total_piezas_rechazadas DECIMAL(10, 2) NOT NULL DEFAULT 0,
    porcentaje_aceptacion DECIMAL(5, 2) NOT NULL DEFAULT 0 COMMENT 'Porcentaje de aceptación',
    piezas_pendientes_inspeccion DECIMAL(10, 2) NOT NULL DEFAULT 0,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_resumen_produccion FOREIGN KEY (produccion_id) REFERENCES produccion(id) ON DELETE CASCADE,
    INDEX idx_produccion_id (produccion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Resumen de calidad por orden de producción';
