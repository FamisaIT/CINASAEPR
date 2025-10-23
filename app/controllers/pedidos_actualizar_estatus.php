<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/pedidos_model.php';
require_once __DIR__ . '/../models/produccion_model.php';

header('Content-Type: application/json');

$pedidosModel = new PedidosModel($pdo);
$produccionModel = new ProductionModel($pdo);

$id = $_POST['id'] ?? null;
$estatus = $_POST['estatus'] ?? null;

if (empty($id) || empty($estatus)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID y estatus son requeridos'
    ]);
    exit;
}

$estatus_validos = ['creada', 'en_produccion', 'completada', 'cancelada'];
if (!in_array($estatus, $estatus_validos)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Estatus inválido'
    ]);
    exit;
}

try {
    $usuario = $_SESSION['user_id'] ?? 'sistema';
    $result = $pedidosModel->actualizarEstatusPedido($id, $estatus, $usuario);

    if ($result) {
        // Si el pedido se envía a producción, crear registros de producción
        if ($estatus === 'en_produccion') {
            $pedido = $pedidosModel->obtenerPedidoPorId($id);
            $items = $pedidosModel->obtenerItemsPedido($id);

            if ($pedido && $items) {
                foreach ($items as $item) {
                    // Verificar si ya existe registro de producción para este item
                    $sqlCheck = "SELECT id FROM produccion WHERE pedido_id = :pedido_id AND producto_id = :producto_id";
                    $stmtCheck = $pdo->prepare($sqlCheck);
                    $stmtCheck->execute([
                        ':pedido_id' => $id,
                        ':producto_id' => $item['producto_id']
                    ]);

                    if (!$stmtCheck->fetch()) {
                        // Crear registro de producción
                        $datosProduccion = [
                            'pedido_id' => $id,
                            'producto_id' => $item['producto_id'],
                            'numero_pedido' => $pedido['numero_pedido'],
                            'item_code' => $item['material_code'] ?? 'N/A',
                            'descripcion' => $item['descripcion'] ?? $item['descripcion_producto'] ?? '',
                            'qty_solicitada' => floatval($item['cantidad']),
                            'prod_hoy' => 0,
                            'prod_total' => 0,
                            'unidad_medida' => $item['unidad_medida'] ?? 'EA',
                            'estatus' => 'en_produccion'
                        ];

                        try {
                            $produccionModel->crearProduccion($datosProduccion);
                            error_log("Registro de producción creado para pedido: " . $id . ", producto: " . $item['producto_id']);
                        } catch (Exception $e) {
                            error_log("Error creando registro de producción: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Estatus actualizado exitosamente'
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Pedido no encontrado'
        ]);
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar estatus: ' . $e->getMessage()
    ]);
}
