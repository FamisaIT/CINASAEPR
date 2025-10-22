<?php
// Configuración de sesiones

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
    
    session_start();
}

// Función para verificar si el usuario está autenticado
function isAuthenticated() {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

// Función para autenticar usuario (simplificada para este módulo)
function authenticate() {
    $_SESSION['authenticated'] = true;
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
}

// Auto-autenticación para este módulo (en producción integrar con sistema de login real)
if (!isAuthenticated()) {
    authenticate();
}
