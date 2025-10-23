<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "1. Inicio del test<br>";

try {
    echo "2. Incluyendo config.php<br>";
    require_once __DIR__ . '/app/config/config.php';
    
    echo "3. BASE_PATH: " . (defined('BASE_PATH') ? BASE_PATH : 'NO DEFINIDO') . "<br>";
    
    echo "4. Incluyendo session.php<br>";
    require_once __DIR__ . '/app/config/session.php';
    
    echo "5. Incluyendo database.php<br>";
    require_once __DIR__ . '/app/config/database.php';
    
    echo "6. Incluyendo productos_model.php<br>";
    require_once __DIR__ . '/app/models/productos_model.php';
    
    echo "7. Creando modelo<br>";
    $model = new ProductosModel($pdo);
    
    echo "8. Obteniendo países<br>";
    $paises = $model->obtenerPaisesOrigen();
    echo "9. Países obtenidos: " . count($paises) . "<br>";
    
    echo "10. Obteniendo categorías<br>";
    $categorias = $model->obtenerCategorias();
    echo "11. Categorías obtenidas: " . count($categorias) . "<br>";
    
    echo "<br><strong style='color: green;'>✓ Todo funciona correctamente</strong><br>";
    echo "<a href='productos.php'>Ir a productos.php</a>";
    
} catch (Exception $e) {
    echo "<br><strong style='color: red;'>✗ Error: " . $e->getMessage() . "</strong><br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
