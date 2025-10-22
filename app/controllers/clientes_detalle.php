<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/clientes_model.php';

header('Content-Type: application/json');

$model = new ClientesModel($pdo);

$id = $_GET['id'] ?? null;

if (empty($id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de cliente no especificado']);
    exit;
}

try {
    $cliente = $model->obtenerClientePorId($id);
    
    if (!$cliente) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $cliente
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener el cliente: ' . $e->getMessage()
    ]);
}
