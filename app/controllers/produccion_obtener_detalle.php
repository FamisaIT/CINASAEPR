<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/produccion_model.php';

header('Content-Type: application/json');

try {
    $model = new ProductionModel($pdo);

    // Obtener ID del pedido
    $pedidoId = intval($_GET['pedido_id'] ?? 0);

    if (empty($pedidoId)) {
        throw new Exception('ID de pedido inválido');
    }

    // Obtener información del pedido
    $sqlPedido = "SELECT p.*, c.razon_social as cliente_nombre, c.correo, c.telefono, c.contacto_principal
                  FROM pedidos p
                  LEFT JOIN clientes c ON p.cliente_id = c.id
                  WHERE p.id = :id";

    $stmt = $pdo->prepare($sqlPedido);
    $stmt->bindValue(':id', $pedidoId, PDO::PARAM_INT);
    $stmt->execute();
    $pedido = $stmt->fetch();

    if (!$pedido) {
        throw new Exception('Pedido no encontrado');
    }

    // Obtener items de producción del pedido
    $items = $model->obtenerProduccionPorPedido($pedidoId);

    // Agregar historial a cada item
    foreach ($items as &$item) {
        $item['historial'] = $model->obtenerHistorialProduccion($item['id']);
    }

    echo json_encode([
        'success' => true,
        'pedido' => $pedido,
        'items' => $items
    ]);

} catch (Exception $e) {
    error_log("Error al obtener detalle de producción: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
