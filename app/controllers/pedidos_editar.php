<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/pedidos_model.php';
require_once __DIR__ . '/../models/clientes_model.php';

header('Content-Type: application/json');

$model = new PedidosModel($pdo);
$clientesModel = new ClientesModel($pdo);

$pedido_id = $_POST['pedido_id'] ?? null;
$cliente_id = $_POST['cliente_id'] ?? null;
$facturacion = $_POST['facturacion'] ?? null;
$entrega = $_POST['entrega'] ?? null;
$contacto = $_POST['contacto'] ?? null;
$telefono = $_POST['telefono'] ?? null;
$correo = $_POST['correo'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;
$fecha_entrega = $_POST['fecha_entrega'] ?? null;

// Decodificar items que vienen como JSON string
$items_json = $_POST['items'] ?? '[]';
$items = is_string($items_json) ? json_decode($items_json, true) : $items_json;
if (!is_array($items)) {
    $items = [];
}

$errores = [];

// Validaciones
if (empty($pedido_id)) {
    $errores[] = 'ID de pedido no especificado';
}

if (empty($cliente_id)) {
    $errores[] = 'Debe seleccionar un cliente';
}

if (empty($facturacion)) {
    $errores[] = 'La dirección de facturación es requerida';
}

if (empty($entrega)) {
    $errores[] = 'La dirección de entrega es requerida';
}

if (!empty($errores)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error de validación',
        'errors' => $errores
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    // Actualizar el pedido
    $datos_pedido = [
        ':id' => $pedido_id,
        ':cliente_id' => $cliente_id,
        ':facturacion' => $facturacion,
        ':entrega' => $entrega,
        ':contacto' => $contacto,
        ':telefono' => $telefono,
        ':correo' => $correo,
        ':observaciones' => $observaciones,
        ':fecha_entrega' => !empty($fecha_entrega) ? $fecha_entrega : null,
        ':usuario_actualizacion' => $_SESSION['user_id'] ?? 'sistema'
    ];

    // Ejecutar actualización del pedido
    $sql = "UPDATE pedidos SET
            cliente_id = :cliente_id,
            facturacion = :facturacion,
            entrega = :entrega,
            contacto = :contacto,
            telefono = :telefono,
            correo = :correo,
            observaciones = :observaciones,
            fecha_entrega = :fecha_entrega,
            usuario_actualizacion = :usuario_actualizacion,
            fecha_actualizacion = CURRENT_TIMESTAMP
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($datos_pedido);

    // Eliminar items existentes
    $sql_delete = "DELETE FROM pedidos_items WHERE pedido_id = :pedido_id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->execute([':pedido_id' => $pedido_id]);

    // Procesar items si existen
    if (!empty($items) && is_array($items)) {
        foreach ($items as $index => $item) {
            if (!empty($item['producto_id']) && !empty($item['cantidad'])) {
                $datos_item = [
                    ':pedido_id' => $pedido_id,
                    ':line' => $index + 1,
                    ':producto_id' => $item['producto_id'],
                    ':descripcion' => $item['descripcion'],
                    ':cantidad' => $item['cantidad'],
                    ':unidad_medida' => $item['unidad_medida'] ?? null,
                    ':precio_unitario' => $item['precio_unitario'] ?? 0,
                    ':subtotal' => ($item['cantidad'] * ($item['precio_unitario'] ?? 0)),
                    ':notas' => $item['notas'] ?? null
                ];

                $model->agregarItemPedido($datos_item);
            }
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Pedido actualizado exitosamente',
        'pedido_id' => $pedido_id
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar pedido: ' . $e->getMessage()
    ]);
}
