<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/produccion_model.php';

header('Content-Type: application/json');

try {
    $model = new ProductionModel($pdo);

    $id = $_GET['id'] ?? null;

    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'ID no especificado']);
        exit;
    }

    // Obtener registro de producción
    $produccion = $model->obtenerProduccionPorId($id);
    if (!$produccion) {
        echo json_encode(['success' => false, 'message' => 'Registro de producción no encontrado']);
        exit;
    }

    // Obtener histórico
    $historial = $model->obtenerHistorialProduccion($id);

    echo json_encode([
        'success' => true,
        'produccion' => $produccion,
        'historial' => $historial
    ]);

} catch (Exception $e) {
    error_log("Error al obtener histórico: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener histórico: ' . $e->getMessage()
    ]);
}
?>
