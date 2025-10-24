-- Migración: Agregar campos de Calidad a tabla de Producción
-- Compatible con MySQL 5.7+

-- Nota: Si alguna columna ya existe, comentar esa línea
-- Agregar columnas a la tabla produccion
ALTER TABLE produccion ADD COLUMN prod_liberada DECIMAL(10, 2) DEFAULT 0 COMMENT 'Cantidad de piezas liberadas por calidad';
ALTER TABLE produccion ADD COLUMN prod_rechazada DECIMAL(10, 2) DEFAULT 0 COMMENT 'Cantidad de piezas rechazadas por calidad';
ALTER TABLE produccion ADD COLUMN estado_calidad ENUM('sin_inspeccionar', 'inspeccionando', 'parcialmente_inspeccionada', 'completamente_inspeccionada', 'con_rechazos') DEFAULT 'sin_inspeccionar' COMMENT 'Estado de la inspección de calidad';
ALTER TABLE produccion ADD COLUMN qty_por_inspeccionar DECIMAL(10, 2) DEFAULT 0 COMMENT 'Cantidad pendiente de inspección o reinspección';

-- Agregar índices
ALTER TABLE produccion ADD INDEX idx_estado_calidad (estado_calidad);
ALTER TABLE produccion ADD INDEX idx_prod_liberada (prod_liberada);

-- Crear vista para resumen de producción con estado de calidad
CREATE OR REPLACE VIEW v_produccion_resumen AS
SELECT
    p.id,
    p.pedido_id,
    p.producto_id,
    p.numero_pedido,
    p.item_code,
    p.descripcion,
    p.qty_solicitada,
    p.prod_total,
    p.prod_liberada,
    p.prod_rechazada,
    p.qty_pendiente,
    p.qty_por_inspeccionar,
    p.estado_calidad,
    p.estatus,
    p.fecha_creacion,
    p.fecha_actualizacion,
    (SELECT COUNT(*) FROM piezas_producidas WHERE produccion_id = p.id) as total_piezas,
    (SELECT COUNT(*) FROM piezas_producidas WHERE produccion_id = p.id AND estatus = 'liberada') as piezas_liberadas,
    (SELECT COUNT(*) FROM piezas_producidas WHERE produccion_id = p.id AND estatus = 'rechazada') as piezas_rechazadas,
    (SELECT COUNT(*) FROM piezas_producidas WHERE produccion_id = p.id AND estatus IN ('por_inspeccionar', 'pendiente_reinspeccion')) as piezas_pendientes_insp,
    (SELECT cr.porcentaje_aceptacion FROM calidad_resumen cr WHERE cr.produccion_id = p.id) as porcentaje_aceptacion
FROM produccion p;
