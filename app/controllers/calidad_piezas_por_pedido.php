<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../models/calidad_model.php';

try {
    $model = new CalidadModel($pdo);

    // Obtener parámetro del pedido
    $numero_pedido = $_GET['numero_pedido'] ?? null;

    if (!$numero_pedido) {
        throw new Exception('Número de pedido no especificado');
    }

    // Obtener filtros opcionales
    $filtros = [
        'numero_pedido' => $numero_pedido,
        'buscar' => $_GET['buscar'] ?? '',
        'item_code' => $_GET['item_code'] ?? '',
        'supervisor' => $_GET['supervisor'] ?? ''
    ];

    $pagina = (int)($_GET['pagina'] ?? 1);
    $limite = (int)($_GET['limite'] ?? 20);
    $offset = ($pagina - 1) * $limite;

    // Obtener piezas del pedido con estado pendiente
    $sql = "SELECT
                pp.id,
                pp.folio_pieza,
                pp.numero_pedido,
                pp.item_code,
                pp.descripcion,
                pp.supervisor_produccion,
                pp.fecha_produccion,
                pp.estatus,
                c.id as cliente_id,
                c.razon_social as cliente_razon_social
            FROM piezas_producidas pp
            LEFT JOIN pedidos p ON pp.pedido_id = p.id
            LEFT JOIN clientes c ON p.cliente_id = c.id
            WHERE pp.numero_pedido = :numero_pedido
            AND pp.estatus IN ('por_inspeccionar', 'pendiente_reinspeccion')";

    $params = [':numero_pedido' => $numero_pedido];

    // Agregar filtros adicionales
    if (!empty($filtros['buscar'])) {
        $sql .= " AND (pp.folio_pieza LIKE :buscar OR pp.item_code LIKE :buscar OR pp.descripcion LIKE :buscar)";
        $params[':buscar'] = '%' . $filtros['buscar'] . '%';
    }

    if (!empty($filtros['item_code'])) {
        $sql .= " AND pp.item_code = :item_code";
        $params[':item_code'] = $filtros['item_code'];
    }

    if (!empty($filtros['supervisor'])) {
        $sql .= " AND pp.supervisor_produccion = :supervisor";
        $params[':supervisor'] = $filtros['supervisor'];
    }

    $sql .= " ORDER BY pp.folio_pieza DESC";

    // Contar total
    $sql_count = "SELECT COUNT(*) as total FROM piezas_producidas pp
                  WHERE pp.numero_pedido = :numero_pedido
                  AND pp.estatus IN ('por_inspeccionar', 'pendiente_reinspeccion')";

    if (!empty($filtros['buscar'])) {
        $sql_count .= " AND (pp.folio_pieza LIKE :buscar OR pp.item_code LIKE :buscar OR pp.descripcion LIKE :buscar)";
    }
    if (!empty($filtros['item_code'])) {
        $sql_count .= " AND pp.item_code = :item_code";
    }
    if (!empty($filtros['supervisor'])) {
        $sql_count .= " AND pp.supervisor_produccion = :supervisor";
    }

    $stmt_count = $pdo->prepare($sql_count);
    foreach ($params as $key => $value) {
        $stmt_count->bindValue($key, $value);
    }
    $stmt_count->execute();
    $total = $stmt_count->fetch()['total'];

    // Agregar LIMIT y OFFSET
    $sql .= " LIMIT :limite OFFSET :offset";
    $params[':limite'] = $limite;
    $params[':offset'] = $offset;

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        if ($key === ':limite' || $key === ':offset') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key, $value);
        }
    }
    $stmt->execute();
    $piezas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener información general del pedido
    $sql_pedido = "SELECT DISTINCT pp.numero_pedido, c.id as cliente_id, c.razon_social as cliente
                   FROM piezas_producidas pp
                   LEFT JOIN pedidos p ON pp.pedido_id = p.id
                   LEFT JOIN clientes c ON p.cliente_id = c.id
                   WHERE pp.numero_pedido = :numero_pedido LIMIT 1";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->bindValue(':numero_pedido', $numero_pedido);
    $stmt_pedido->execute();
    $pedido_info = $stmt_pedido->fetch(PDO::FETCH_ASSOC);

    // Obtener supervisores para filtro
    $supervisores = $model->obtenerSupervisores();

    // Obtener items del pedido
    $sql_items = "SELECT DISTINCT item_code FROM piezas_producidas
                  WHERE numero_pedido = :numero_pedido ORDER BY item_code";
    $stmt_items = $pdo->prepare($sql_items);
    $stmt_items->bindValue(':numero_pedido', $numero_pedido);
    $stmt_items->execute();
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'exito' => true,
        'pedido' => $pedido_info,
        'piezas' => $piezas,
        'total' => $total,
        'pagina' => $pagina,
        'limite' => $limite,
        'paginas_totales' => ceil($total / $limite),
        'filtros' => [
            'supervisores' => $supervisores,
            'items' => $items
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}
?>
