<?php

function generarPDFCliente($cliente) {
    require_once __DIR__ . '/../../vendor/fpdf.php';
    
    $pdf = new FPDF('P', 'mm', 'Letter');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    
    $pdf->Cell(0, 10, 'FICHA DE CLIENTE', 0, 1, 'C');
    $pdf->Ln(5);
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, 'DATOS FISCALES', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'Razon Social:', 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->MultiCell(0, 6, utf8_decode($cliente['razon_social']));
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'RFC:', 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(60, 6, $cliente['rfc'], 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(30, 6, 'Estatus:', 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, strtoupper($cliente['estatus']), 0, 1);
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_decode('Régimen Fiscal:'), 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->MultiCell(0, 6, utf8_decode($cliente['regimen_fiscal'] ?? 'N/A'));
    
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, utf8_decode('UBICACIÓN'), 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_decode('Dirección:'), 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->MultiCell(0, 6, utf8_decode($cliente['direccion'] ?? 'N/A'));
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_decode('País:'), 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, utf8_decode($cliente['pais']), 0, 1);
    
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, 'CONTACTO', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'Contacto Principal:', 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, utf8_decode($cliente['contacto_principal'] ?? 'N/A'), 0, 1);
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_decode('Teléfono:'), 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(60, 6, $cliente['telefono'] ?? 'N/A', 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(30, 6, 'Correo:', 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, $cliente['correo'] ?? 'N/A', 0, 1);
    
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, 'CONDICIONES COMERCIALES', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_decode('Días de Crédito:'), 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(60, 6, $cliente['dias_credito'] . ' dias', 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_decode('Límite de Crédito:'), 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, '$' . number_format($cliente['limite_credito'], 2) . ' ' . $cliente['moneda'], 0, 1);
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'Condiciones de Pago:', 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, utf8_decode($cliente['condiciones_pago'] ?? 'N/A'), 0, 1);
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'Uso CFDI:', 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(60, 6, $cliente['uso_cfdi'] ?? 'N/A', 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_decode('Método de Pago:'), 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, $cliente['metodo_pago'] ?? 'N/A', 0, 1);
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'Forma de Pago:', 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(60, 6, $cliente['forma_pago'] ?? 'N/A', 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'Vendedor Asignado:', 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, utf8_decode($cliente['vendedor_asignado'] ?? 'N/A'), 0, 1);
    
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, utf8_decode('INFORMACIÓN BANCARIA'), 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'Banco:', 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, utf8_decode($cliente['banco'] ?? 'N/A'), 0, 1);
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'Cuenta Bancaria:', 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, $cliente['cuenta_bancaria'] ?? 'N/A', 0, 1);
    
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 6, 'Fecha de alta: ' . date('d/m/Y H:i', strtotime($cliente['fecha_alta'])), 0, 1);
    $pdf->Cell(0, 6, 'Generado: ' . date('d/m/Y H:i:s'), 0, 1);
    
    $pdf->Output('I', 'cliente_' . $cliente['rfc'] . '.pdf');
}
