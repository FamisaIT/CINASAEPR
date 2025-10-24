<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../models/calidad_model.php';

try {
    $model = new CalidadModel($pdo);

    // Obtener pedidos con piezas pendientes de inspección
    $sql = "SELECT DISTINCT
                pp.numero_pedido,
                pp.pedido_id,
                COUNT(pp.id) as cantidad_piezas_pendientes,
                c.id as cliente_id,
                c.razon_social
            FROM piezas_producidas pp
            LEFT JOIN pedidos p ON pp.pedido_id = p.id
            LEFT JOIN clientes c ON p.cliente_id = c.id
            WHERE pp.estatus IN ('por_inspeccionar', 'pendiente_reinspeccion')
            GROUP BY pp.numero_pedido, pp.pedido_id, c.id, c.razon_social
            ORDER BY pp.numero_pedido DESC";

    $stmt = $pdo->query($sql);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'exito' => true,
        'pedidos' => $pedidos,
        'total' => count($pedidos)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}
?>
