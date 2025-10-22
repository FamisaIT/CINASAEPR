<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/clientes_model.php';

header('Content-Type: application/json');

$model = new ClientesModel($pdo);

try {
    $vendedores = $model->obtenerVendedores();
    $paises = $model->obtenerPaises();
    
    echo json_encode([
        'success' => true,
        'vendedores' => $vendedores,
        'paises' => $paises
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener filtros: ' . $e->getMessage()
    ]);
}
