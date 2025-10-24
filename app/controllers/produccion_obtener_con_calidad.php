<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../models/produccion_model.php';

try {
    $id = (int)($_GET['id'] ?? 0);

    if ($id <= 0) {
        throw new Exception('Parámetro de producción inválido');
    }

    $model = new ProductionModel($pdo);
    $produccion = $model->obtenerProduccionConCalidad($id);

    if (!$produccion) {
        throw new Exception('Producción no encontrada');
    }

    echo json_encode([
        'exito' => true,
        'produccion' => $produccion
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}
?>
