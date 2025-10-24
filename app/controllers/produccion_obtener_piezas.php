<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../models/produccion_model.php';
require_once __DIR__ . '/../models/calidad_model.php';

try {
    $produccion_id = (int)($_GET['produccion_id'] ?? 0);

    if ($produccion_id <= 0) {
        throw new Exception('Parámetro de producción inválido');
    }

    $prodModel = new ProductionModel($pdo);
    $calidadModel = new CalidadModel($pdo);

    // Obtener información de producción
    $produccion = $prodModel->obtenerProduccionPorId($produccion_id);

    if (!$produccion) {
        throw new Exception('Producción no encontrada');
    }

    // Obtener piezas
    $piezas = $calidadModel->obtenerPiezasProducidas([
        'produccion_id' => $produccion_id
    ]);

    // Obtener resumen de calidad
    $resumen = $calidadModel->obtenerResumenCalidad($produccion_id);

    echo json_encode([
        'exito' => true,
        'produccion' => $produccion,
        'numero_pedido' => $produccion['numero_pedido'],
        'piezas' => $piezas,
        'resumen' => $resumen
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}
?>
