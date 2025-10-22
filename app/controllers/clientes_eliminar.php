<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/clientes_model.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$model = new ClientesModel($pdo);

$id = $_POST['id'] ?? null;

if (empty($id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de cliente no especificado']);
    exit;
}

$cliente = $model->obtenerClientePorId($id);
if (!$cliente) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
    exit;
}

try {
    $model->eliminarCliente($id);
    
    echo json_encode([
        'success' => true,
        'message' => 'Cliente bloqueado exitosamente'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al bloquear el cliente: ' . $e->getMessage()
    ]);
}
