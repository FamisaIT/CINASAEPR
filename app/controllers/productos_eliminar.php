<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/productos_model.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$model = new ProductosModel($pdo);
$id = $_POST['id'] ?? null;

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
    
    $model->eliminarProducto($id);
    
    echo json_encode([
        'success' => true,
        'message' => 'Producto marcado como descontinuado exitosamente'
    ]);
    
} catch (PDOException $e) {
    error_log("Error SQL al eliminar producto: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos al eliminar el producto',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error al eliminar producto: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar producto: ' . $e->getMessage()
    ]);
}
