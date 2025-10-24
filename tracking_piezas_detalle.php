<?php
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/session.php';
require_once __DIR__ . '/app/config/database.php';

// Verificar si las tablas de calidad existen
try {
    $verificacion = $pdo->query("SHOW TABLES LIKE 'piezas_producidas'")->fetch();
    if (empty($verificacion)) {
        header('Location: calidad.php');
        exit;
    }
} catch (Exception $e) {
    header('Location: calidad.php');
    exit;
}

// Obtener número de pedido de la URL
$numero_pedido = $_GET['pedido'] ?? null;
if (!$numero_pedido) {
    header('Location: tracking_piezas.php');
    exit;
}

$pageTitle = 'Detalle de Tracking - Pedido ' . htmlspecialchars($numero_pedido);

include __DIR__ . '/app/views/header.php';
?>

<!-- Logo de Fondo Transparente -->
<div class="logo-background" style="position: fixed; top: 50%; left: 50%; width: 1200px; height: 1200px; margin-left: -600px; margin-top: -600px; background-image: url('<?php echo BASE_PATH; ?>/app/assets/img/logo.png'); background-size: 60%; background-repeat: no-repeat; background-position: center; background-attachment: fixed; opacity: 0.25; pointer-events: none; z-index: -1;"></div>

<!-- Botón de regresar -->
<div class="mb-3">
    <a href="<?php echo BASE_PATH; ?>/tracking_piezas.php" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-arrow-left"></i> Volver al Índice
    </a>
</div>

<!-- Contenedor del detalle del pedido -->
<div id="contenedor_detalle">
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="text-muted mt-2">Cargando detalle del pedido...</p>
    </div>
</div>

</main>

<footer class="bg-gradient-to-r from-slate-100 via-blue-50 to-slate-100 text-center py-6 mt-8 shadow-inner">
    <div class="container">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
            <img src="<?php echo BASE_PATH; ?>/app/assets/img/logo.png" alt="CINASA Logo" class="h-8 w-8">
            <p class="text-slate-600 mb-0 font-medium">
                <i class="fas fa-copyright text-blue-600"></i>
                <?php echo date('Y'); ?> Tracking de Piezas - CINASA
            </p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const NUMERO_PEDIDO = '<?php echo htmlspecialchars($numero_pedido); ?>';
</script>
<script src="<?php echo BASE_PATH; ?>/app/assets/tracking_piezas_detalle.js?v=<?php echo time(); ?>"></script>
</body>
</html>
