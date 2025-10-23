<?php

function generarPDFCliente($cliente) {
    require_once __DIR__ . '/../../vendor/autoload.php';

    $pdf = new TCPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);
    
    // Configuración del documento
    $pdf->SetCreator('CINASA');
    $pdf->SetAuthor('CINASA');
    $pdf->SetTitle('Ficha de Cliente');
    
    // Márgenes
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    // Agregar página
    $pdf->AddPage();
    
    // Título
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'FICHA DE CLIENTE', 0, 1, 'C');
    $pdf->Ln(5);

    // DATOS FISCALES
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, 'DATOS FISCALES', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'Razón Social:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(0, 6, $cliente['razon_social'], 0, 'L');

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'RFC:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(60, 6, $cliente['rfc'], 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(30, 6, 'Estatus:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, strtoupper($cliente['estatus']), 0, 1);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'Régimen Fiscal:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(0, 6, $cliente['regimen_fiscal'] ?? 'N/A', 0, 'L');

    // UBICACIÓN
    $pdf->Ln(3);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, 'UBICACIÓN', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'Dirección:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(0, 6, $cliente['direccion'] ?? 'N/A', 0, 'L');

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'País:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $cliente['pais'], 0, 1);

    // CONTACTO
    $pdf->Ln(3);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, 'CONTACTO', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'Contacto Principal:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $cliente['contacto_principal'] ?? 'N/A', 0, 1);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'Teléfono:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(60, 6, $cliente['telefono'] ?? 'N/A', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(30, 6, 'Correo:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $cliente['correo'] ?? 'N/A', 0, 1);

    // CONDICIONES COMERCIALES
    $pdf->Ln(3);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, 'CONDICIONES COMERCIALES', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'Días de Crédito:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(60, 6, $cliente['dias_credito'] . ' días', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'Límite de Crédito:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, '$' . number_format($cliente['limite_credito'], 2) . ' ' . $cliente['moneda'], 0, 1);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'Condiciones de Pago:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $cliente['condiciones_pago'] ?? 'N/A', 0, 1);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'Uso CFDI:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(60, 6, $cliente['uso_cfdi'] ?? 'N/A', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'Método de Pago:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $cliente['metodo_pago'] ?? 'N/A', 0, 1);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'Forma de Pago:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(60, 6, $cliente['forma_pago'] ?? 'N/A', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'Vendedor Asignado:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $cliente['vendedor_asignado'] ?? 'N/A', 0, 1);

    // INFORMACIÓN BANCARIA
    $pdf->Ln(3);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, 'INFORMACIÓN BANCARIA', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'Banco:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $cliente['banco'] ?? 'N/A', 0, 1);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'Cuenta Bancaria:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $cliente['cuenta_bancaria'] ?? 'N/A', 0, 1);

    // PIE DE PÁGINA
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 6, 'Fecha de alta: ' . date('d/m/Y H:i', strtotime($cliente['fecha_alta'])), 0, 1);
    $pdf->Cell(0, 6, 'Generado: ' . date('d/m/Y H:i:s'), 0, 1);

    $pdf->Output('cliente_' . $cliente['rfc'] . '.pdf', 'I');
}
