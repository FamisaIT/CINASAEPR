<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/clientes_model.php';

$model = new ClientesModel($pdo);

$filtros = [
    'buscar' => $_GET['buscar'] ?? '',
    'estatus' => $_GET['estatus'] ?? '',
    'vendedor' => $_GET['vendedor'] ?? '',
    'pais' => $_GET['pais'] ?? ''
];

try {
    $clientes = $model->listarClientes($filtros, 'razon_social', 'ASC', 10000, 0);
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="clientes_' . date('Y-m-d_His') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($output, [
        'ID',
        'Razón Social',
        'RFC',
        'Régimen Fiscal',
        'Dirección',
        'País',
        'Contacto Principal',
        'Teléfono',
        'Correo',
        'Días de Crédito',
        'Límite de Crédito',
        'Condiciones de Pago',
        'Moneda',
        'Uso CFDI',
        'Método de Pago',
        'Forma de Pago',
        'Banco',
        'Cuenta Bancaria',
        'Estatus',
        'Fecha de Alta',
        'Vendedor Asignado'
    ]);
    
    foreach ($clientes as $cliente) {
        fputcsv($output, [
            $cliente['id'],
            $cliente['razon_social'],
            $cliente['rfc'],
            $cliente['regimen_fiscal'],
            $cliente['direccion'],
            $cliente['pais'],
            $cliente['contacto_principal'],
            $cliente['telefono'],
            $cliente['correo'],
            $cliente['dias_credito'],
            $cliente['limite_credito'],
            $cliente['condiciones_pago'],
            $cliente['moneda'],
            $cliente['uso_cfdi'],
            $cliente['metodo_pago'],
            $cliente['forma_pago'],
            $cliente['banco'],
            $cliente['cuenta_bancaria'],
            $cliente['estatus'],
            $cliente['fecha_alta'],
            $cliente['vendedor_asignado']
        ]);
    }
    
    fclose($output);
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error al exportar: ' . $e->getMessage();
}
