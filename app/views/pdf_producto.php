<?php

function generarPDFProducto($producto) {
    require_once __DIR__ . '/../../vendor/autoload.php';

    $pdf = new TCPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);

    // Configuración del documento
    $pdf->SetCreator('CINASA');
    $pdf->SetAuthor('CINASA');
    $pdf->SetTitle('Ficha de Producto');

    // Márgenes
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);

    // Agregar página
    $pdf->AddPage();

    // Título
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'FICHA DE PRODUCTO', 0, 1, 'C');
    $pdf->Ln(5);

    // INFORMACIÓN BÁSICA
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, 'INFORMACIÓN BÁSICA', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Código de Material:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $producto['material_code'] ?? 'N/A', 0, 1);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Descripción:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(0, 6, $producto['descripcion'] ?? 'N/A', 0, 'L');

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Unidad de Medida:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(60, 6, $producto['unidad_medida'] ?? 'N/A', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(30, 6, 'País Origen:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $producto['pais_origen'] ?? 'N/A', 0, 1);

    // INFORMACIÓN TÉCNICA
    $pdf->Ln(3);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, 'INFORMACIÓN TÉCNICA DEL DIBUJO', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Número de Dibujo:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(60, 6, $producto['drawing_number'] ?? 'N/A', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(20, 6, 'Versión:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $producto['drawing_version'] ?? 'N/A', 0, 1);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Hoja:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(60, 6, $producto['drawing_sheet'] ?? 'N/A', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(20, 6, 'ECM:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $producto['ecm_number'] ?? 'N/A', 0, 1);

    // CLASIFICACIÓN ARANCELARIA
    $pdf->Ln(3);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, 'CLASIFICACIÓN ARANCELARIA', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Código HTS:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $producto['hts_code'] ?? 'N/A', 0, 1);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Descripción HTS:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(0, 6, $producto['hts_descripcion'] ?? 'N/A', 0, 'L');

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Tipo de Parte:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $producto['tipo_parte'] ?? 'N/A', 0, 1);

    // SISTEMA DE CALIDAD Y CATEGORÍA
    $pdf->Ln(3);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, 'SISTEMA DE CALIDAD Y CATEGORÍA', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Sistema de Calidad:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(60, 6, $producto['sistema_calidad'] ?? 'N/A', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(30, 6, 'Categoría:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $producto['categoria'] ?? 'N/A', 0, 1);

    // ESPECIFICACIONES FÍSICAS
    $pdf->Ln(3);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, 'ESPECIFICACIONES FÍSICAS', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Peso:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $peso = $producto['peso'] ? $producto['peso'] . ' ' . ($producto['unidad_peso'] ?? '') : 'N/A';
    $pdf->Cell(60, 6, $peso, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(20, 6, 'Material:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $producto['material'] ?? 'N/A', 0, 1);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Acabado:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, $producto['acabado'] ?? 'N/A', 0, 1);

    // ESTATUS
    $pdf->Ln(3);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 6, 'Estatus:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, strtoupper($producto['estatus'] ?? 'N/A'), 0, 1);

    // NOTAS Y ESPECIFICACIONES
    if (!empty($producto['notas']) || !empty($producto['especificaciones'])) {
        $pdf->Ln(3);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetFillColor(52, 58, 64);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, 'NOTAS Y ESPECIFICACIONES', 0, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(2);

        if (!empty($producto['notas'])) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(50, 6, 'Notas:', 0, 0);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->MultiCell(0, 6, $producto['notas'], 0, 'L');
            $pdf->Ln(2);
        }

        if (!empty($producto['especificaciones'])) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(50, 6, 'Especificaciones:', 0, 0);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->MultiCell(0, 6, $producto['especificaciones'], 0, 'L');
        }
    }

    // PIE DE PÁGINA
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 6, 'Generado: ' . date('d/m/Y H:i:s'), 0, 1);

    $pdf->Output('producto_' . ($producto['material_code'] ?? 'sin_codigo') . '.pdf', 'I');
}
