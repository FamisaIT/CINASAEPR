<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/database.php';
require_once __DIR__ . '/../models/calidad_model.php';

try {
    $model = new CalidadModel($pdo);

    // Obtener parámetros
    $filtros = [
        'buscar' => $_GET['buscar'] ?? '',
        'fecha' => $_GET['fecha'] ?? '',
        'fecha_desde' => $_GET['fecha_desde'] ?? '',
        'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
        'item_code' => $_GET['item_code'] ?? '',
        'supervisor' => $_GET['supervisor'] ?? '',
        'cliente_id' => $_GET['cliente_id'] ?? ''
    ];

    $pagina = (int)($_GET['pagina'] ?? 1);
    $limite = (int)($_GET['limite'] ?? 20);
    $offset = ($pagina - 1) * $limite;
    $orden = $_GET['orden'] ?? 'fecha_produccion';
    $direccion = $_GET['direccion'] ?? 'DESC';

    // Obtener datos
    $piezas = $model->listarPiezasPorInspeccionar($filtros, $orden, $direccion, $limite, $offset);
    $total = $model->contarPiezasPorInspeccionar($filtros);

    // Obtener opciones para filtros
    $supervisores = $model->obtenerSupervisores();
    $items = $model->obtenerItems();

    // Obtener clientes (desde BD directamente)
    $sql_clientes = "SELECT DISTINCT id, razon_social FROM clientes WHERE estatus = 'activo' ORDER BY razon_social";
    $stmt = $pdo->query($sql_clientes);
    $clientes = $stmt->fetchAll();

    echo json_encode([
        'exito' => true,
        'piezas' => $piezas,
        'total' => $total,
        'pagina' => $pagina,
        'limite' => $limite,
        'paginas_totales' => ceil($total / $limite),
        'filtros' => [
            'supervisores' => $supervisores,
            'items' => $items,
            'clientes' => $clientes
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}
?>
