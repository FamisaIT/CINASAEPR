<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/clientes_model.php';

$model = new ClientesModel($pdo);

$id = $_GET['id'] ?? null;

if (empty($id)) {
    die('ID de cliente no especificado');
}

try {
    $cliente = $model->obtenerClientePorId($id);

    if (!$cliente) {
        die('Cliente no encontrado');
    }

    require_once __DIR__ . '/../views/pdf_cliente.php';
    generarPDFCliente($cliente);

} catch (Exception $e) {
    die('Error al generar PDF: ' . $e->getMessage());
}
