<?php
/**
 * Configuración global del sistema
 */

// Método simple: usar la ruta del script relativa al document root
// $_SERVER['SCRIPT_NAME'] contiene algo como: /web/CINASA-main/index.php
// Necesitamos obtener: /web/CINASA-main

$script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$base_path = dirname($script_name);

// Normalizar la ruta (en caso de Windows)
$base_path = str_replace('\\', '/', $base_path);

// Definir como constante para usar en todo el sistema
define('BASE_PATH', $base_path);
