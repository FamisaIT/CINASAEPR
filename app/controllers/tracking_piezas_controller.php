<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

try {
    // Obtener parámetros
    $buscar = $_GET['buscar'] ?? '';
    $estatus = $_GET['estatus'] ?? '';
    $fecha = $_GET['fecha'] ?? '';
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 10;
    $offset = ($pagina - 1) * $limite;

    // Query base
    $sql = "
        SELECT 
            p.id as pedido_id,
            p.numero_pedido,
            c.razon_social as cliente_nombre,
            p.fecha_entrega,
            p.estatus as pedido_estatus,
            COUNT(DISTINCT pp.id) as total_piezas,
            SUM(CASE WHEN pp.estatus = 'por_inspeccionar' THEN 1 ELSE 0 END) as piezas_por_inspeccionar,
            SUM(CASE WHEN pp.estatus = 'liberada' THEN 1 ELSE 0 END) as piezas_liberadas,
            SUM(CASE WHEN pp.estatus = 'rechazada' THEN 1 ELSE 0 END) as piezas_rechazadas,
            SUM(CASE WHEN pp.estatus = 'pendiente_reinspeccion' THEN 1 ELSE 0 END) as piezas_reinspeccion,
            ROUND((SUM(CASE WHEN pp.estatus = 'liberada' THEN 1 ELSE 0 END) / COUNT(DISTINCT pp.id)) * 100, 1) as porcentaje_aprobacion
        FROM pedidos p
        INNER JOIN clientes c ON p.cliente_id = c.id
        INNER JOIN piezas_producidas pp ON p.id = pp.pedido_id
        WHERE 1=1
    ";

    $params = [];

    // Filtros
    if (!empty($buscar)) {
        $sql .= " AND (p.numero_pedido LIKE :buscar 
                  OR c.razon_social LIKE :buscar 
                  OR pp.folio_pieza LIKE :buscar
                  OR pp.item_code LIKE :buscar)";
        $params[':buscar'] = "%$buscar%";
    }

    if (!empty($estatus)) {
        $sql .= " AND pp.estatus = :estatus";
        $params[':estatus'] = $estatus;
    }

    if (!empty($fecha)) {
        $sql .= " AND DATE(pp.fecha_produccion) = :fecha";
        $params[':fecha'] = $fecha;
    }

    // Agrupar por pedido
    $sql .= " GROUP BY p.id, p.numero_pedido, c.razon_social, p.fecha_entrega, p.estatus";
    $sql .= " ORDER BY p.fecha_creacion DESC";

    // Contar total
    $sqlCount = "SELECT COUNT(*) as total FROM (" . $sql . ") as subquery";
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute($params);
    $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

    // Obtener resultados paginados
    $sql .= " LIMIT :limite OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Para cada pedido, obtener las piezas
    foreach ($pedidos as &$pedido) {
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
                ci.inspector_calidad,
                ci.fecha_inspeccion,
                ci.cantidad_rechazada,
                ci.observaciones,
                (
                    SELECT GROUP_CONCAT(CONCAT(d.codigo, ': ', d.nombre) SEPARATOR ', ')
                    FROM inspeccion_defectos id
                    JOIN defectos d ON id.defecto_id = d.id
                    WHERE id.inspeccion_id = ci.id
                ) as defectos
            FROM piezas_producidas pp
            LEFT JOIN calidad_inspecciones ci ON pp.folio_pieza = ci.folio_pieza
            WHERE pp.pedido_id = :pedido_id
        ";

        // Aplicar filtro de estatus a las piezas también
        if (!empty($estatus)) {
            $sqlPiezas .= " AND pp.estatus = :estatus";
        }

        $sqlPiezas .= " ORDER BY pp.fecha_produccion DESC, pp.folio_pieza DESC";

        $stmtPiezas = $pdo->prepare($sqlPiezas);
        $stmtPiezas->bindValue(':pedido_id', $pedido['pedido_id']);
        if (!empty($estatus)) {
            $stmtPiezas->bindValue(':estatus', $estatus);
        }
        $stmtPiezas->execute();
        $pedido['piezas'] = $stmtPiezas->fetchAll(PDO::FETCH_ASSOC);
    }

    $paginasTotales = ceil($total / $limite);

    echo json_encode([
        'exito' => true,
        'pedidos' => $pedidos,
        'total' => (int)$total,
        'pagina' => $pagina,
        'paginas_totales' => $paginasTotales,
        'limite' => $limite
    ]);

} catch (Exception $e) {
    echo json_encode([
        'exito' => false,
        'error' => 'Error al cargar pedidos: ' . $e->getMessage()
    ]);
}
