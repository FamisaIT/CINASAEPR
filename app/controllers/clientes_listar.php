<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/clientes_model.php';

header('Content-Type: application/json');

$model = new ClientesModel($pdo);

$filtros = [
    'buscar' => $_GET['buscar'] ?? '',
    'estatus' => $_GET['estatus'] ?? '',
    'vendedor' => $_GET['vendedor'] ?? '',
    'pais' => $_GET['pais'] ?? ''
];

$orden = $_GET['orden'] ?? 'razon_social';
$direccion = $_GET['direccion'] ?? 'ASC';
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
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener clientes: ' . $e->getMessage()
    ]);
}
