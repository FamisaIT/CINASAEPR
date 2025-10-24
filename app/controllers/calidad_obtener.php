<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../models/calidad_model.php';

try {
    $folio_pieza = $_GET['folio'] ?? null;

    if (!$folio_pieza) {
        throw new Exception('Folio de pieza requerido');
    }

    $model = new CalidadModel($pdo);

    // Obtener pieza
    $pieza = $model->obtenerPiezaPorFolio($folio_pieza);

    if (!$pieza) {
        throw new Exception('Pieza no encontrada');
    }

    // Obtener inspecciones previas
    $inspecciones = $model->obtenerInspeccionesPieza($folio_pieza);

    echo json_encode([
        'exito' => true,
        'pieza' => $pieza,
        'inspecciones' => $inspecciones
    ]);

} catch (Exception $e) {
    http_response_code(404);
    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}
?>
