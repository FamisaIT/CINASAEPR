<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/clientes_model.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$model = new ClientesModel($pdo);

$errores = [];

$razon_social = trim($_POST['razon_social'] ?? '');
$rfc = strtoupper(trim($_POST['rfc'] ?? ''));
$regimen_fiscal = trim($_POST['regimen_fiscal'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$pais = trim($_POST['pais'] ?? 'México');
$contacto_principal = trim($_POST['contacto_principal'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$dias_credito = $_POST['dias_credito'] ?? 0;
$limite_credito = $_POST['limite_credito'] ?? 0.00;
$condiciones_pago = trim($_POST['condiciones_pago'] ?? '');
$moneda = trim($_POST['moneda'] ?? 'MXN');
$uso_cfdi = trim($_POST['uso_cfdi'] ?? '');
$metodo_pago = trim($_POST['metodo_pago'] ?? '');
$forma_pago = trim($_POST['forma_pago'] ?? '');
$banco = trim($_POST['banco'] ?? '');
$cuenta_bancaria = trim($_POST['cuenta_bancaria'] ?? '');
$estatus = trim($_POST['estatus'] ?? 'activo');
$vendedor_asignado = trim($_POST['vendedor_asignado'] ?? '');

if (empty($razon_social)) {
    $errores[] = 'La razón social es obligatoria';
} elseif (strlen($razon_social) < 3 || strlen($razon_social) > 250) {
    $errores[] = 'La razón social debe tener entre 3 y 250 caracteres';
}

if (empty($rfc)) {
    $errores[] = 'El RFC es obligatorio';
} elseif (!preg_match('/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/', $rfc)) {
    $errores[] = 'El RFC no tiene un formato válido';
}

if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El correo electrónico no es válido';
}

$dias_credito_permitidos = [0, 15, 30, 45, 60, 90];
if (!in_array((int)$dias_credito, $dias_credito_permitidos)) {
    $errores[] = 'Los días de crédito deben ser: 0, 15, 30, 45, 60 o 90';
}

if ($limite_credito < 0) {
    $errores[] = 'El límite de crédito no puede ser negativo';
}

$estatus_permitidos = ['activo', 'suspendido', 'bloqueado'];
if (!in_array($estatus, $estatus_permitidos)) {
    $errores[] = 'El estatus debe ser: activo, suspendido o bloqueado';
}

if (empty($errores)) {
    $clienteExistente = $model->obtenerClientePorRFC($rfc);
    if ($clienteExistente) {
        $errores[] = 'Ya existe un cliente con ese RFC';
    }
}

if (!empty($errores)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errores]);
    exit;
}

try {
    $datos = [
        ':razon_social' => $razon_social,
        ':rfc' => $rfc,
        ':regimen_fiscal' => $regimen_fiscal,
        ':direccion' => $direccion,
        ':pais' => $pais,
        ':contacto_principal' => $contacto_principal,
        ':telefono' => $telefono,
        ':correo' => $correo,
        ':dias_credito' => (int)$dias_credito,
        ':limite_credito' => (float)$limite_credito,
        ':condiciones_pago' => $condiciones_pago,
        ':moneda' => $moneda,
        ':uso_cfdi' => $uso_cfdi,
        ':metodo_pago' => $metodo_pago,
        ':forma_pago' => $forma_pago,
        ':banco' => $banco,
        ':cuenta_bancaria' => $cuenta_bancaria,
        ':estatus' => $estatus,
        ':vendedor_asignado' => $vendedor_asignado
    ];
    
    $id = $model->crearCliente($datos);
    
    echo json_encode([
        'success' => true,
        'message' => 'Cliente creado exitosamente',
        'id' => $id
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear el cliente: ' . $e->getMessage()
    ]);
}
