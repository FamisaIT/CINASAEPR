-- Tabla de Tracking de Producción
CREATE TABLE IF NOT EXISTS produccion (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    producto_id INT NOT NULL,
    numero_pedido VARCHAR(50) NOT NULL,
    item_code VARCHAR(100) NOT NULL COMMENT 'Código del producto (material_code)',
    descripcion VARCHAR(500),
    qty_solicitada DECIMAL(10, 2) NOT NULL COMMENT 'Cantidad total solicitada del pedido',
    prod_hoy DECIMAL(10, 2) DEFAULT 0 COMMENT 'Cantidad producida hoy (ingresada manualmente)',
    prod_total DECIMAL(10, 2) DEFAULT 0 COMMENT 'Total producido a la fecha (suma acumulada)',
    qty_pendiente DECIMAL(10, 2) GENERATED ALWAYS AS (qty_solicitada - prod_total) STORED COMMENT 'Cantidad pendiente (calculado)',
    unidad_medida VARCHAR(10),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    estatus ENUM('en_produccion', 'completada', 'cancelada') DEFAULT 'en_produccion',
    CONSTRAINT fk_produccion_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_produccion_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_pedido_id (pedido_id),
    INDEX idx_producto_id (producto_id),
    INDEX idx_numero_pedido (numero_pedido),
    INDEX idx_estatus (estatus),
    UNIQUE KEY unique_pedido_producto (pedido_id, producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de tracking de producción diaria';

-- Tabla de Histórico de Producción Diaria
CREATE TABLE IF NOT EXISTS produccion_historial (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    produccion_id BIGINT UNSIGNED NOT NULL,
    pedido_id BIGINT UNSIGNED NOT NULL,
    producto_id INT NOT NULL,
    numero_pedido VARCHAR(50) NOT NULL,
    item_code VARCHAR(100) NOT NULL,
    cantidad_producida DECIMAL(10, 2) NOT NULL COMMENT 'Cantidad producida en ese día',
    fecha_produccion DATE NOT NULL,
    supervisor VARCHAR(100),
    observaciones TEXT,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_hist_produccion FOREIGN KEY (produccion_id) REFERENCES produccion(id) ON DELETE CASCADE,
    CONSTRAINT fk_hist_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_hist_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_produccion_id (produccion_id),
    INDEX idx_fecha_produccion (fecha_produccion),
    INDEX idx_pedido_id (pedido_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico diario de producción';
