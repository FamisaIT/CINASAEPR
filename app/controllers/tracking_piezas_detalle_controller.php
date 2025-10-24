<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

try {
    $numero_pedido = $_GET['pedido'] ?? '';

    if (empty($numero_pedido)) {
        throw new Exception('Número de pedido no especificado');
    }

    // Obtener información del pedido
    $sqlPedido = "
        SELECT 
            p.id as pedido_id,
            p.numero_pedido,
            c.razon_social as cliente_nombre,
            c.contacto_principal,
            c.telefono,
            c.correo,
            p.fecha_creacion,
            p.fecha_entrega,
            p.estatus as pedido_estatus,
            p.observaciones,
            COUNT(DISTINCT pp.id) as total_piezas,
            SUM(CASE WHEN pp.estatus = 'por_inspeccionar' THEN 1 ELSE 0 END) as piezas_por_inspeccionar,
            SUM(CASE WHEN pp.estatus = 'liberada' THEN 1 ELSE 0 END) as piezas_liberadas,
            SUM(CASE WHEN pp.estatus = 'rechazada' THEN 1 ELSE 0 END) as piezas_rechazadas,
            SUM(CASE WHEN pp.estatus = 'pendiente_reinspeccion' THEN 1 ELSE 0 END) as piezas_reinspeccion,
            ROUND((SUM(CASE WHEN pp.estatus = 'liberada' THEN 1 ELSE 0 END) / NULLIF(COUNT(DISTINCT pp.id), 0)) * 100, 1) as porcentaje_aprobacion
        FROM pedidos p
        INNER JOIN clientes c ON p.cliente_id = c.id
        LEFT JOIN piezas_producidas pp ON p.id = pp.pedido_id
        WHERE p.numero_pedido = :numero_pedido
        GROUP BY p.id, p.numero_pedido, c.razon_social, c.contacto_principal, c.telefono, c.correo, 
                 p.fecha_creacion, p.fecha_entrega, p.estatus, p.observaciones
    ";

    $stmtPedido = $pdo->prepare($sqlPedido);
    $stmtPedido->bindValue(':numero_pedido', $numero_pedido);
    $stmtPedido->execute();
    $pedido = $stmtPedido->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        throw new Exception('Pedido no encontrado');
    }

    // Obtener todas las piezas del pedido con sus inspecciones
    $sqlPiezas = "
        SELECT 
            pp.id,
            pp.folio_pieza,
            pp.item_code,
            pp.descripcion,
            pp.supervisor_produccion,
            pp.fecha_produccion,
            pp.estatus,
            pp.fecha_actualizacion,
            ci.id as inspeccion_id,
            ci.inspector_calidad,
            ci.fecha_inspeccion,
            ci.cantidad_aceptada,
            ci.cantidad_rechazada,
            ci.observaciones as observaciones_inspeccion,
            (
                SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'codigo', d.codigo,
                        'nombre', d.nombre,
                        'cantidad', id.cantidad
                    )
                )
                FROM inspeccion_defectos id
                JOIN defectos d ON id.defecto_id = d.id
                WHERE id.inspeccion_id = ci.id
            ) as defectos_json
        FROM piezas_producidas pp
        LEFT JOIN calidad_inspecciones ci ON pp.folio_pieza = ci.folio_pieza
        WHERE pp.pedido_id = :pedido_id
        ORDER BY pp.fecha_produccion DESC, pp.folio_pieza DESC
    ";

    $stmtPiezas = $pdo->prepare($sqlPiezas);
    $stmtPiezas->bindValue(':pedido_id', $pedido['pedido_id']);
    $stmtPiezas->execute();
    $piezas = $stmtPiezas->fetchAll(PDO::FETCH_ASSOC);

    // Procesar defectos JSON
    foreach ($piezas as &$pieza) {
        if ($pieza['defectos_json']) {
            $pieza['defectos'] = json_decode($pieza['defectos_json'], true);
        } else {
            $pieza['defectos'] = [];
        }
        unset($pieza['defectos_json']);
    }

    echo json_encode([
        'exito' => true,
        'pedido' => $pedido,
        'piezas' => $piezas
    ]);

} catch (Exception $e) {
    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}
