<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/productos_model.php';

header('Content-Type: application/json');

$model = new ProductosModel($pdo);

$filtros = [
    'buscar' => isset($_GET['buscar']) ? trim($_GET['buscar']) : '',
    'estatus' => isset($_GET['estatus']) ? trim($_GET['estatus']) : '',
    'pais_origen' => isset($_GET['pais_origen']) ? trim($_GET['pais_origen']) : '',
    'categoria' => isset($_GET['categoria']) ? trim($_GET['categoria']) : ''
];

$orden = isset($_GET['orden']) ? trim($_GET['orden']) : 'material_code';
$direccion = isset($_GET['direccion']) ? trim($_GET['direccion']) : 'ASC';
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$limite = 20;
$offset = ($pagina - 1) * $limite;

try {
    $productos = $model->listarProductos($filtros, $orden, $direccion, $limite, $offset);
    $total = $model->contarProductos($filtros);
    $totalPaginas = ceil($total / $limite);
    
    echo json_encode([
        'success' => true,
        'data' => $productos,
        'pagination' => [
            'total' => $total,
            'pagina_actual' => $pagina,
            'total_paginas' => $totalPaginas,
            'por_pagina' => $limite
        ]
    ]);
} catch (PDOException $e) {
    error_log("Error SQL: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos al obtener productos',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener productos: ' . $e->getMessage()
    ]);
}
