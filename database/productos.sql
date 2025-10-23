-- Tabla de Productos/Piezas (Catálogo)
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Información básica del producto
    material_code VARCHAR(100) NULL COMMENT 'Código de material/pieza',
    descripcion TEXT NULL COMMENT 'Descripción del material',
    unidad_medida VARCHAR(20) NULL COMMENT 'Unidad de medida (EA, KG, PZ, etc)',
    
    -- Información de origen y clasificación
    pais_origen VARCHAR(50) NULL COMMENT 'País de origen',
    hts_code VARCHAR(100) NULL COMMENT 'Código HTS',
    hts_descripcion TEXT NULL COMMENT 'Descripción del código HTS',
    
    -- Sistema de calidad y categoría
    sistema_calidad VARCHAR(50) NULL COMMENT 'Sistema de calidad objetivo',
    categoria VARCHAR(100) NULL COMMENT 'Categoría del producto',
    tipo_parte VARCHAR(50) NULL COMMENT 'Tipo de parte (Standard, Custom, etc)',
    
    -- Información técnica del dibujo
    drawing_number VARCHAR(100) NULL COMMENT 'Número de dibujo',
    drawing_version VARCHAR(20) NULL COMMENT 'Versión del dibujo',
    drawing_sheet VARCHAR(20) NULL COMMENT 'Hoja del dibujo',
    ecm_number VARCHAR(50) NULL COMMENT 'Número ECM',
    material_revision VARCHAR(20) NULL COMMENT 'Revisión del material',
    change_number VARCHAR(50) NULL COMMENT 'Número de cambio',
    
    -- Información de componentes
    nivel_componente VARCHAR(20) NULL COMMENT 'Nivel del componente',
    componente_linea VARCHAR(100) NULL COMMENT 'Componente línea',
    ref_documento VARCHAR(255) NULL COMMENT 'Documento de referencia',
    
    -- Especificaciones técnicas
    peso DECIMAL(10,3) NULL COMMENT 'Peso del producto',
    unidad_peso VARCHAR(10) NULL COMMENT 'Unidad de peso (KG, LB, etc)',
    material VARCHAR(100) NULL COMMENT 'Material de fabricación',
    acabado VARCHAR(100) NULL COMMENT 'Acabado superficial',
    
    -- Notas y observaciones
    notas TEXT NULL COMMENT 'Notas adicionales',
    especificaciones TEXT NULL COMMENT 'Especificaciones técnicas adicionales',
    
    -- Control y auditoría
    estatus ENUM('activo', 'inactivo', 'descontinuado') DEFAULT 'activo',
    fecha_alta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    usuario_creacion VARCHAR(100) NULL,
    
    INDEX idx_material_code (material_code),
    INDEX idx_drawing_number (drawing_number),
    INDEX idx_estatus (estatus),
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
