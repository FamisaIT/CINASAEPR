-- Tabla de Pedidos/Órdenes de Trabajo
CREATE TABLE IF NOT EXISTS pedidos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_pedido VARCHAR(50) NOT NULL UNIQUE COMMENT 'Número o folio del pedido',
    cliente_id BIGINT UNSIGNED NOT NULL COMMENT 'ID del cliente',
    facturacion TEXT NOT NULL COMMENT 'Dirección de facturación (Bill To)',
    entrega TEXT NOT NULL COMMENT 'Dirección de entrega (Ship To)',
    contacto VARCHAR(150) COMMENT 'Contacto del cliente',
    telefono VARCHAR(30) COMMENT 'Teléfono de contacto',
    correo VARCHAR(150) COMMENT 'Correo de contacto',
    estatus ENUM('creada','en_produccion','completada','cancelada') DEFAULT 'creada' COMMENT 'Estado del pedido',
    observaciones TEXT COMMENT 'Notas o observaciones del pedido',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega DATETIME COMMENT 'Fecha estimada de entrega',
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    usuario_creacion VARCHAR(100),
    usuario_actualizacion VARCHAR(100),

    CONSTRAINT fk_pedidos_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    INDEX idx_numero_pedido (numero_pedido),
    INDEX idx_cliente_id (cliente_id),
    INDEX idx_estatus (estatus),
    INDEX idx_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de pedidos u órdenes de trabajo';

-- Tabla de items/líneas de pedidos
CREATE TABLE IF NOT EXISTS pedidos_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL COMMENT 'ID del pedido',
    line INT UNSIGNED NOT NULL COMMENT 'Número de línea (autoincrementable)',
    producto_id INT NOT NULL COMMENT 'ID del producto',
    descripcion TEXT NOT NULL COMMENT 'Descripción del producto',
    cantidad DECIMAL(12,3) NOT NULL COMMENT 'Cantidad solicitada',
    unidad_medida VARCHAR(20) COMMENT 'Unidad de medida',
    precio_unitario DECIMAL(15,2) DEFAULT 0 COMMENT 'Precio unitario',
    subtotal DECIMAL(15,2) DEFAULT 0 COMMENT 'Subtotal (cantidad * precio)',
    notas TEXT COMMENT 'Notas específicas de la línea',
    estatus ENUM('pendiente','en_produccion','completada','cancelada') DEFAULT 'pendiente',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_pedidos_items_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_pedidos_items_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
    UNIQUE KEY unique_pedido_line (pedido_id, line),
    INDEX idx_pedido_id (pedido_id),
    INDEX idx_producto_id (producto_id),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de líneas/items de pedidos';
