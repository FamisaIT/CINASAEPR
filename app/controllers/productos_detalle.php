<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/productos_model.php';

header('Content-Type: application/json');

$model = new ProductosModel($pdo);
$id = $_GET['id'] ?? null;

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'ID de producto no especificado']);
    exit;
}

try {
    $producto = $model->obtenerProductoPorId($id);
    
    if (!$producto) {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $producto
    ]);
    
} catch (PDOException $e) {
    error_log("Error SQL al obtener producto: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos al obtener el producto',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error al obtener producto: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener producto: ' . $e->getMessage()
    ]);
}
