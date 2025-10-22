<?php
/**
 * Script de prueba de conexión
 * Este archivo puede ser eliminado después de verificar que la instalación funciona
 */

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Prueba de Conexión</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background-color: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        h1 { color: #333; }
        code { background-color: #f4f4f4; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 Prueba de Conexión - Catálogo de Clientes</h1>";

// Verificar versión de PHP
echo "<div class='info'>";
echo "<strong>PHP Version:</strong> " . phpversion();
if (version_compare(phpversion(), '8.1.0', '>=')) {
    echo " ✅ (Compatible)";
} else {
    echo " ⚠️ (Se requiere PHP 8.1 o superior)";
}
echo "</div>";

// Verificar extensiones de PHP
echo "<div class='info'>";
echo "<strong>Extensiones PHP:</strong><br>";
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring'];
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "- $ext: " . ($loaded ? "✅ Cargada" : "❌ No encontrada") . "<br>";
}
echo "</div>";

// Intentar conexión a la base de datos
echo "<h2>Conexión a Base de Datos</h2>";

if (!file_exists(__DIR__ . '/app/config/database.php')) {
    echo "<div class='error'>";
    echo "❌ El archivo <code>app/config/database.php</code> no existe.<br>";
    echo "Por favor, copia <code>app/config/database.example.php</code> a <code>app/config/database.php</code> y configura tus credenciales.";
    echo "</div>";
} else {
    try {
        require_once __DIR__ . '/app/config/database.php';
        
        echo "<div class='success'>";
        echo "✅ Conexión exitosa a la base de datos!";
        echo "</div>";
        
        // Verificar si la tabla existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'clientes'");
        if ($stmt->rowCount() > 0) {
            echo "<div class='success'>";
            echo "✅ La tabla <code>clientes</code> existe en la base de datos.";
            echo "</div>";
            
            // Contar registros
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM clientes");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<div class='info'>";
            echo "<strong>Total de clientes registrados:</strong> " . $result['total'];
            echo "</div>";
        } else {
            echo "<div class='warning'>";
            echo "⚠️ La tabla <code>clientes</code> no existe.<br>";
            echo "Por favor, importa el archivo <code>database/clientes.sql</code> a tu base de datos.";
            echo "</div>";
        }
        
    } catch (PDOException $e) {
        echo "<div class='error'>";
        echo "❌ Error de conexión: " . htmlspecialchars($e->getMessage()) . "<br><br>";
        echo "<strong>Pasos para solucionar:</strong><br>";
        echo "1. Verifica que MySQL esté corriendo<br>";
        echo "2. Verifica las credenciales en <code>app/config/database.php</code><br>";
        echo "3. Asegúrate de que la base de datos <code>clientes_db</code> exista<br>";
        echo "</div>";
    }
}

echo "<hr>";
echo "<div class='info'>";
echo "<strong>📝 Nota:</strong> Este archivo (<code>test_connection.php</code>) puede ser eliminado después de verificar que todo funciona correctamente.";
echo "</div>";

echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='index.php' style='background-color: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir al Sistema →</a>";
echo "</div>";

echo "</body></html>";
