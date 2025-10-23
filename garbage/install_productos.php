<?php
/**
 * Script de instalación para crear la tabla de productos
 * Ejecutar este archivo una sola vez desde el navegador: http://localhost/CINASA-main/install_productos.php
 */

require_once __DIR__ . '/app/config/database.php';

try {
    $sql = file_get_contents(__DIR__ . '/database/productos.sql');
    
    $pdo->exec($sql);
    
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Instalación Exitosa</title>
        <script src='https://cdn.tailwindcss.com'></script>
    </head>
    <body class='bg-gray-100 flex items-center justify-center min-h-screen'>
        <div class='bg-white rounded-lg shadow-lg p-8 max-w-md'>
            <div class='text-center'>
                <div class='mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4'>
                    <svg class='h-6 w-6 text-green-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'></path>
                    </svg>
                </div>
                <h3 class='text-lg font-medium text-gray-900 mb-2'>¡Instalación Exitosa!</h3>
                <p class='text-sm text-gray-500 mb-4'>La tabla de productos ha sido creada correctamente.</p>
                <a href='productos.php' class='inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'>
                    Ir al módulo de productos
                </a>
            </div>
            <div class='mt-6 p-4 bg-yellow-50 rounded-md'>
                <p class='text-xs text-yellow-800'>
                    <strong>Importante:</strong> Por seguridad, elimine este archivo (install_productos.php) después de la instalación.
                </p>
            </div>
        </div>
    </body>
    </html>";
    
} catch (PDOException $e) {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Error de Instalación</title>
        <script src='https://cdn.tailwindcss.com'></script>
    </head>
    <body class='bg-gray-100 flex items-center justify-center min-h-screen'>
        <div class='bg-white rounded-lg shadow-lg p-8 max-w-md'>
            <div class='text-center'>
                <div class='mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4'>
                    <svg class='h-6 w-6 text-red-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12'></path>
                    </svg>
                </div>
                <h3 class='text-lg font-medium text-gray-900 mb-2'>Error de Instalación</h3>
                <p class='text-sm text-red-600 mb-4'>" . htmlspecialchars($e->getMessage()) . "</p>
            </div>
        </div>
    </body>
    </html>";
}
