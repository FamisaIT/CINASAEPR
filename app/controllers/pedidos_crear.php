<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/pedidos_model.php';
require_once __DIR__ . '/../models/clientes_model.php';

header('Content-Type: application/json');

$model = new PedidosModel($pdo);
$clientesModel = new ClientesModel($pdo);

$numero_pedido = $_POST['numero_pedido'] ?? null;
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

// Debug logging
error_log("Items recibidos: " . json_encode($items));
error_log("Cantidad de items: " . count($items));

$errores = [];

// Validaciones
if (empty($numero_pedido)) {
    $errores[] = 'El número de pedido es requerido';
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

if (!empty($numero_pedido) && !$model->verificarNumeroPedidoUnico($numero_pedido)) {
    $errores[] = 'El número de pedido ya existe';
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

    // Crear el pedido
    $datos_pedido = [
        ':numero_pedido' => $numero_pedido,
        ':cliente_id' => $cliente_id,
        ':facturacion' => $facturacion,
        ':entrega' => $entrega,
        ':contacto' => $contacto,
        ':telefono' => $telefono,
        ':correo' => $correo,
        ':estatus' => 'creada',
        ':observaciones' => $observaciones,
        ':fecha_entrega' => !empty($fecha_entrega) ? $fecha_entrega : null,
        ':usuario_creacion' => $_SESSION['user_id'] ?? 'sistema'
    ];

    $pedido_id = $model->crearPedido($datos_pedido);

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
        'message' => 'Pedido creado exitosamente',
        'pedido_id' => $pedido_id
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear pedido: ' . $e->getMessage()
    ]);
}
