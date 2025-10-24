-- Migración Alternativa: Agregar campos de Calidad a tabla de Producción
-- Use esta versión si migracion_calidad.sql da error por columnas duplicadas

-- Opción 1: Agregue campos UNO POR UNO (comentar líneas si ya existen)
-- Si recibe error "Duplicate column name", simplemente comente esa línea y continúe

-- ALTER TABLE produccion ADD COLUMN prod_liberada DECIMAL(10, 2) DEFAULT 0;
-- ALTER TABLE produccion ADD COLUMN prod_rechazada DECIMAL(10, 2) DEFAULT 0;
-- ALTER TABLE produccion ADD COLUMN estado_calidad ENUM('sin_inspeccionar', 'inspeccionando', 'parcialmente_inspeccionada', 'completamente_inspeccionada', 'con_rechazos') DEFAULT 'sin_inspeccionar';
-- ALTER TABLE produccion ADD COLUMN qty_por_inspeccionar DECIMAL(10, 2) DEFAULT 0;

-- Opción 2: Verificar que las columnas existan (ejecutar una por una)
-- Si la columna ya existe, simplemente saltará al siguiente comando

SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME='produccion' AND COLUMN_NAME='prod_liberada';

-- Si la consulta anterior NO retorna resultados, ejecute:
-- ALTER TABLE produccion ADD COLUMN prod_liberada DECIMAL(10, 2) DEFAULT 0 COMMENT 'Cantidad de piezas liberadas por calidad';

-- Si la consulta anterior NO retorna resultados, ejecute:
-- ALTER TABLE produccion ADD COLUMN prod_rechazada DECIMAL(10, 2) DEFAULT 0 COMMENT 'Cantidad de piezas rechazadas por calidad';

-- Si la consulta anterior NO retorna resultados, ejecute:
-- ALTER TABLE produccion ADD COLUMN estado_calidad ENUM('sin_inspeccionar', 'inspeccionando', 'parcialmente_inspeccionada', 'completamente_inspeccionada', 'con_rechazos') DEFAULT 'sin_inspeccionar' COMMENT 'Estado de la inspección de calidad';

-- Si la consulta anterior NO retorna resultados, ejecute:
-- ALTER TABLE produccion ADD COLUMN qty_por_inspeccionar DECIMAL(10, 2) DEFAULT 0 COMMENT 'Cantidad pendiente de inspección o reinspección';

-- Opción 3: Agregar índices (estos pueden duplicarse sin problema)
ALTER TABLE produccion ADD INDEX IF NOT EXISTS idx_estado_calidad (estado_calidad);
ALTER TABLE produccion ADD INDEX IF NOT EXISTS idx_prod_liberada (prod_liberada);

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
    (SELECT COUNT(*) FROM piezas_producidas WHERE produccion_id = p.id AND estatus IN ('por_inspeccionar', 'pendiente_reinspeccion')) as piezas_pendientes
FROM produccion p;
