<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../models/calidad_model.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        throw new Exception('Datos inválidos');
    }

    $model = new CalidadModel($pdo);

    // Validar datos requeridos
    $campos_requeridos = ['folio_pieza', 'cantidad_inspeccionada', 'cantidad_aceptada', 'cantidad_rechazada', 'inspector_calidad'];
    foreach ($campos_requeridos as $campo) {
        if (empty($data[$campo]) && $data[$campo] !== '0' && $data[$campo] !== 0) {
            throw new Exception("El campo '{$campo}' es requerido");
        }
    }

    // Preparar datos para inserción
    $datos_inspeccion = [
        'folio_pieza' => trim($data['folio_pieza']),
        'cantidad_inspeccionada' => (float)$data['cantidad_inspeccionada'],
        'cantidad_aceptada' => (float)$data['cantidad_aceptada'],
        'cantidad_rechazada' => (float)$data['cantidad_rechazada'],
        'inspector_calidad' => trim($data['inspector_calidad']),
        'observaciones' => $data['observaciones'] ?? null,
        'defectos' => $data['defectos'] ?? []
    ];

    // Registrar inspección
    $inspeccion_id = $model->registrarInspeccion($datos_inspeccion);

    echo json_encode([
        'exito' => true,
        'mensaje' => 'Inspección registrada correctamente',
        'inspeccion_id' => $inspeccion_id
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}
?>
