<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/clientes_model.php';

header('Content-Type: application/json');

$model = new ClientesModel($pdo);

$filtros = [
    'buscar' => isset($_GET['buscar']) ? trim($_GET['buscar']) : '',
    'estatus' => isset($_GET['estatus']) ? trim($_GET['estatus']) : '',
    'vendedor' => isset($_GET['vendedor']) ? trim($_GET['vendedor']) : '',
    'pais' => isset($_GET['pais']) ? trim($_GET['pais']) : ''
];

$orden = isset($_GET['orden']) ? trim($_GET['orden']) : 'razon_social';
$direccion = isset($_GET['direccion']) ? trim($_GET['direccion']) : 'ASC';
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$limite = 20;
$offset = ($pagina - 1) * $limite;

try {
    $clientes = $model->listarClientes($filtros, $orden, $direccion, $limite, $offset);
    $total = $model->contarClientes($filtros);
    $totalPaginas = ceil($total / $limite);
    
    echo json_encode([
        'success' => true,
        'data' => $clientes,
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
        'message' => 'Error de base de datos al obtener clientes',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener clientes: ' . $e->getMessage()
    ]);
}
