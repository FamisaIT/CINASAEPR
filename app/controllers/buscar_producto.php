<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/productos_model.php';

header('Content-Type: application/json');

$model = new ProductosModel($pdo);

$id = $_GET['id'] ?? null;

if (empty($id)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID de producto no especificado'
    ]);
    exit;
}

try {
    $producto = $model->obtenerProductoPorId($id);

    if (!$producto) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Producto no encontrado'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => $producto
    ]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al buscar producto: ' . $e->getMessage()
    ]);
}
