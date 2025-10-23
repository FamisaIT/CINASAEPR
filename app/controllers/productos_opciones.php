<?php
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../config/session.php';
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../models/productos_model.php';

    $model = new ProductosModel($pdo);

    $paises = $model->obtenerPaisesOrigen();
    $categorias = $model->obtenerCategorias();

    // Asegurar que los arrays no sean nulos
    if (!is_array($paises)) {
        $paises = [];
    }
    if (!is_array($categorias)) {
        $categorias = [];
    }

    echo json_encode([
        'success' => true,
        'paises' => array_values($paises), // Resetear índices del array
        'categorias' => array_values($categorias)
    ]);

} catch (Exception $e) {
    error_log("Error al obtener opciones: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener opciones: ' . $e->getMessage()
    ]);
}
?>
