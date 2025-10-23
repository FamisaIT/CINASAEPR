<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/pedidos_model.php';

header('Content-Type: application/json');

$model = new PedidosModel($pdo);

$id = $_GET['id'] ?? null;

if (empty($id)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID de pedido no especificado'
    ]);
    exit;
}

try {
    $pedido = $model->obtenerPedidoPorId($id);

    if (!$pedido) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Pedido no encontrado'
        ]);
        exit;
    }

    // Obtener items del pedido
    $items = $model->obtenerItemsPedido($id);

    echo json_encode([
        'success' => true,
        'data' => $pedido,
        'items' => $items
    ]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener pedido: ' . $e->getMessage()
    ]);
}
