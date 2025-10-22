<?php
// Configuración de la base de datos
// Copia este archivo a database.php y ajusta los valores según tu configuración

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "clientes_db";
$db_charset = "utf8mb4";

// Crear conexión con PDO
try {
    $dsn = "mysql:host={$db_host};dbname={$db_name};charset={$db_charset}";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
