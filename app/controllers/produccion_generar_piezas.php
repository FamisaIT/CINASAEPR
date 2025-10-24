<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../models/produccion_model.php';
require_once __DIR__ . '/../models/calidad_model.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        throw new Exception('Datos inválidos');
    }

    $produccion_id = (int)($data['produccion_id'] ?? 0);
    $cantidad_piezas = (int)($data['cantidad_piezas'] ?? 0);

    if ($produccion_id <= 0 || $cantidad_piezas <= 0) {
        throw new Exception('Parámetros inválidos');
    }

    $prodModel = new ProductionModel($pdo);
    $calidadModel = new CalidadModel($pdo);

    // Obtener datos de la producción
    $produccion = $prodModel->obtenerProduccionPorId($produccion_id);

    if (!$produccion) {
        throw new Exception('Producción no encontrada');
    }

    // Crear piezas individuales
    $piezas_creadas = $calidadModel->crearPiezasProducidas($produccion_id, $cantidad_piezas);

    // Actualizar estado de calidad en la tabla de producción
    $prodModel->actualizarEstadoCalidad($produccion_id);

    echo json_encode([
        'exito' => true,
        'mensaje' => "Se han creado {$cantidad_piezas} piezas correctamente",
        'piezas' => $piezas_creadas,
        'cantidad_creadas' => count($piezas_creadas)
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}
?>
