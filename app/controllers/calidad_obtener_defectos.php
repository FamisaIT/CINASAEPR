<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../models/calidad_model.php';

try {
    $model = new CalidadModel($pdo);

    $defectos = $model->obtenerDefectos();

    echo json_encode([
        'exito' => true,
        'defectos' => $defectos
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}
?>
