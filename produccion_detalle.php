<?php
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/session.php';
require_once __DIR__ . '/app/config/database.php';

$pageTitle = 'Detalle de Producción';

include __DIR__ . '/app/views/header.php';
?>

<!-- Logo de Fondo Transparente -->
<div class="logo-background" style="position: fixed; top: 50%; left: 50%; width: 1200px; height: 1200px; margin-left: -600px; margin-top: -600px; background-image: url('<?php echo BASE_PATH; ?>/app/assets/img/logo.png'); background-size: 60%; background-repeat: no-repeat; background-position: center; background-attachment: fixed; opacity: 0.25; pointer-events: none; z-index: -1;"></div>

<div class="container-fluid py-4">
    <!-- Botón de regreso y título -->
    <div class="mb-4">
        <a href="<?php echo BASE_PATH; ?>/produccion.php" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>

    <!-- Encabezado con información rápida -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-gradient-to-r from-blue-600 to-blue-700 text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1" id="numeroPedidoTitle">
                        <i class="fas fa-box me-2"></i>Cargando...
                    </h4>
                    <small id="dateInfo">Fecha de entrega: </small>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-dark fs-6" id="badgeItemsCount">0 items</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Columna izquierda: Información del pedido -->
        <div class="col-lg-3 mb-4">
            <!-- Card: Información del Cliente -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-user me-2"></i>Información del Cliente
                    </h6>
                </div>
                <div class="card-body" id="infoCliente">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary spinner-border-sm" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Columna derecha: Items de producción -->
        <div class="col-lg-9 mb-4">
            <!-- Card: Items de Producción -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-cogs me-2"></i>Items de Producción
                        </h6>
                        <span class="badge bg-light text-dark" id="itemCountBadge">0</span>
                    </div>
                </div>
                <div class="card-body p-0" id="itemsContainer">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    </main>
    <footer class="bg-gradient-to-r from-slate-100 via-blue-50 to-slate-100 text-center py-6 mt-8 shadow-inner">
        <div class="container">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <img src="<?php echo BASE_PATH; ?>/app/assets/img/logo.png" alt="CINASA Logo" class="h-8 w-8">
                <p class="text-slate-600 mb-0 font-medium">
                    <i class="fas fa-copyright text-blue-600"></i>
                    <?php echo date('Y'); ?> Tracking de Producción - CINASA
                </p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_PATH; ?>/app/assets/produccion_detalle.js?v=<?php echo time(); ?>"></script>
</body>
</html>
