<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/produccion_model.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    $model = new ProductionModel($pdo);

    $id = $_POST['id'] ?? null;

    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID de producción requerido'
        ]);
        exit;
    }

    // Recalcular prod_total basado en el histórico actual
    $model->recalcularProduccionTotal($id);

    // Obtener el registro actualizado
    $registroActualizado = $model->obtenerProduccionPorId($id);

    echo json_encode([
        'success' => true,
        'message' => 'Producción recalculada correctamente',
        'data' => $registroActualizado
    ]);

} catch (Exception $e) {
    error_log("Error al recalcular producción: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al recalcular producción: ' . $e->getMessage()
    ]);
}
?>
