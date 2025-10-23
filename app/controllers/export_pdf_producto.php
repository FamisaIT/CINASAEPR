<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/productos_model.php';

$model = new ProductosModel($pdo);

$id = $_GET['id'] ?? null;

if (empty($id)) {
    die('ID de producto no especificado');
}

try {
    $producto = $model->obtenerProductoPorId($id);

    if (!$producto) {
        die('Producto no encontrado');
    }

    require_once __DIR__ . '/../views/pdf_producto.php';
    generarPDFProducto($producto);

} catch (Exception $e) {
    die('Error al generar PDF: ' . $e->getMessage());
}
