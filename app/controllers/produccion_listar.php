<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/produccion_model.php';

header('Content-Type: application/json');

try {
    $model = new ProductionModel($pdo);

    // Obtener parámetros de búsqueda y paginación
    $buscar = $_GET['buscar'] ?? '';
    $estatus = $_GET['estatus'] ?? '';
    $orden = $_GET['orden'] ?? 'numero_pedido';
    $direccion = $_GET['direccion'] ?? 'ASC';
    $pagina = max(1, intval($_GET['pagina'] ?? 1));
    $limite = 20;
    $offset = ($pagina - 1) * $limite;

    // Preparar filtros
    $filtros = [];
    if (!empty($buscar)) {
        $filtros['buscar'] = $buscar;
    }
    // Si no hay estatus especificado, usar "en_produccion" por defecto
    if (!empty($estatus)) {
        $filtros['estatus'] = $estatus;
    } else {
        $filtros['estatus'] = 'en_produccion';
    }

    // Filtros de fecha de entrega
    if (!empty($_GET['fecha_desde'])) {
        $filtros['fecha_entrega_desde'] = $_GET['fecha_desde'];
    }
    if (!empty($_GET['fecha_hasta'])) {
        $filtros['fecha_entrega_hasta'] = $_GET['fecha_hasta'];
    }

    // Obtener datos
    $registros = $model->listarProduccion($filtros, $orden, $direccion, $limite, $offset);
    $total = $model->contarProduccion($filtros);
    $totalPaginas = ceil($total / $limite);

    echo json_encode([
        'success' => true,
        'data' => $registros,
        'pagination' => [
            'total' => $total,
            'pagina_actual' => $pagina,
            'total_paginas' => $totalPaginas,
            'limite' => $limite
        ]
    ]);

} catch (Exception $e) {
    error_log("Error al listar producción: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al listar producción: ' . $e->getMessage()
    ]);
}
?>
