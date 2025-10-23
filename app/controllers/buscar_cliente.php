<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/clientes_model.php';

header('Content-Type: application/json');

$model = new ClientesModel($pdo);

$query = $_GET['q'] ?? '';
$id = $_GET['id'] ?? null;

if (!empty($id)) {
    // Búsqueda por ID
    try {
        $cliente = $model->obtenerClientePorId($id);

        if (!$cliente) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $cliente
        ]);
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al buscar cliente: ' . $e->getMessage()
        ]);
    }
} else if (!empty($query)) {
    // Búsqueda por texto (autocompletado)
    try {
        $sql = "SELECT id, razon_social, rfc, contacto_principal, telefono, correo, direccion
                FROM clientes
                WHERE estatus = 'activo' AND (razon_social LIKE ? OR rfc LIKE ? OR contacto_principal LIKE ?)
                LIMIT 10";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            '%' . $query . '%',
            '%' . $query . '%',
            '%' . $query . '%'
        ]);

        $clientes = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'data' => $clientes
        ]);
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al buscar clientes: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Parámetros requeridos'
    ]);
}
