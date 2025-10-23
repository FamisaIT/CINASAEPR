<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/pedidos_model.php';

header('Content-Type: application/json');

$model = new PedidosModel($pdo);

$filtros = [
    'buscar' => isset($_GET['buscar']) ? trim($_GET['buscar']) : '',
    'estatus' => isset($_GET['estatus']) ? trim($_GET['estatus']) : '',
    'cliente_id' => isset($_GET['cliente_id']) ? trim($_GET['cliente_id']) : ''
];

$orden = isset($_GET['orden']) ? trim($_GET['orden']) : 'fecha_creacion';
$direccion = isset($_GET['direccion']) ? trim($_GET['direccion']) : 'DESC';
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$limite = 20;
$offset = ($pagina - 1) * $limite;

try {
    $pedidos = $model->listarPedidos($filtros, $orden, $direccion, $limite, $offset);
    $total = $model->contarPedidos($filtros);
    $totalPaginas = ceil($total / $limite);

    echo json_encode([
        'success' => true,
        'data' => $pedidos,
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
        'message' => 'Error de base de datos al obtener pedidos',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener pedidos: ' . $e->getMessage()
    ]);
}
