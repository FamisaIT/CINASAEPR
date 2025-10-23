<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/productos_model.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$model = new ProductosModel($pdo);
$id = $_POST['id'] ?? null;

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'ID de producto no especificado']);
    exit;
}

try {
    $productoExistente = $model->obtenerProductoPorId($id);
    
    if (!$productoExistente) {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        exit;
    }
    
    $datos = [
        'material_code' => !empty($_POST['material_code']) ? trim($_POST['material_code']) : null,
        'descripcion' => $_POST['descripcion'] ?? null,
        'unidad_medida' => $_POST['unidad_medida'] ?? null,
        'pais_origen' => $_POST['pais_origen'] ?? null,
        'hts_code' => $_POST['hts_code'] ?? null,
        'hts_descripcion' => $_POST['hts_descripcion'] ?? null,
        'sistema_calidad' => $_POST['sistema_calidad'] ?? null,
        'categoria' => $_POST['categoria'] ?? null,
        'tipo_parte' => $_POST['tipo_parte'] ?? null,
        'drawing_number' => $_POST['drawing_number'] ?? null,
        'drawing_version' => $_POST['drawing_version'] ?? null,
        'drawing_sheet' => $_POST['drawing_sheet'] ?? null,
        'ecm_number' => $_POST['ecm_number'] ?? null,
        'material_revision' => $_POST['material_revision'] ?? null,
        'change_number' => $_POST['change_number'] ?? null,
        'nivel_componente' => $_POST['nivel_componente'] ?? null,
        'componente_linea' => $_POST['componente_linea'] ?? null,
        'ref_documento' => $_POST['ref_documento'] ?? null,
        'peso' => !empty($_POST['peso']) ? floatval($_POST['peso']) : null,
        'unidad_peso' => $_POST['unidad_peso'] ?? null,
        'material' => $_POST['material'] ?? null,
        'acabado' => $_POST['acabado'] ?? null,
        'notas' => $_POST['notas'] ?? null,
        'especificaciones' => $_POST['especificaciones'] ?? null,
        'estatus' => $_POST['estatus'] ?? 'activo'
    ];
    
    // Validar código de material si se proporciona y cambió
    if (!empty($datos['material_code']) && $datos['material_code'] !== $productoExistente['material_code']) {
        $productoDuplicado = $model->obtenerProductoPorMaterialCode($datos['material_code'], $id);
        if ($productoDuplicado) {
            echo json_encode([
                'success' => false,
                'message' => 'Ya existe otro producto con este código de material'
            ]);
            exit;
        }
    }
    
    $model->actualizarProducto($id, $datos);
    
    echo json_encode([
        'success' => true,
        'message' => 'Producto actualizado exitosamente'
    ]);
    
} catch (PDOException $e) {
    error_log("Error SQL al actualizar producto: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos al actualizar el producto',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error al actualizar producto: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar producto: ' . $e->getMessage()
    ]);
}
