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

$pageTitle = 'Tracking de Piezas';

include __DIR__ . '/app/views/header.php';
?>

<!-- Logo de Fondo Transparente -->
<div class="logo-background" style="position: fixed; top: 50%; left: 50%; width: 1200px; height: 1200px; margin-left: -600px; margin-top: -600px; background-image: url('<?php echo BASE_PATH; ?>/app/assets/img/logo.png'); background-size: 60%; background-repeat: no-repeat; background-position: center; background-attachment: fixed; opacity: 0.25; pointer-events: none; z-index: -1;"></div>

<!-- Filtros de Búsqueda -->
<div class="filter-section">
    <h5 class="mb-3">
        <i class="fas fa-filter text-blue-600"></i>
        <span class="ms-2">Filtros de Búsqueda</span>
    </h5>
    <div class="row g-3">
        <div class="col-md-4">
            <label for="filtro_buscar" class="form-label">Buscar</label>
            <input type="text" class="form-control" id="filtro_buscar" placeholder="Número de pedido, cliente...">
        </div>
        <div class="col-md-3">
            <label for="filtro_estatus" class="form-label">Estatus de Pieza</label>
            <select class="form-select" id="filtro_estatus">
                <option value="">Todos</option>
                <option value="por_inspeccionar">Por Inspeccionar</option>
                <option value="liberada">Liberada</option>
                <option value="rechazada">Rechazada</option>
                <option value="pendiente_reinspeccion">Pendiente Reinspección</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button class="btn btn-primary w-100" id="btn_buscar">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <button class="btn btn-secondary btn-sm" id="btn_limpiar">
                <i class="fas fa-eraser"></i>
                <span class="ms-1">Limpiar Filtros</span>
            </button>
        </div>
    </div>
</div>

<!-- Tabla de Pedidos -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="d-flex align-items-center">
            <i class="fas fa-boxes me-2"></i> Pedidos con Piezas Producidas
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Número Pedido</th>
                        <th>Cliente</th>
                        <th>Total Piezas</th>
                        <th>Pendientes</th>
                        <th>Liberadas</th>
                        <th>Rechazadas</th>
                        <th>Aprobación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="contenedor_pedidos">
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div id="contador_pedidos" class="text-muted"></div>
            <div id="paginacion" class="pagination-container"></div>
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
                <?php echo date('Y'); ?> Tracking de Piezas - CINASA
            </p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const BASE_URL = '<?php echo BASE_PATH; ?>';
</script>
<script src="<?php echo BASE_PATH; ?>/app/assets/tracking_piezas.js?v=<?php echo time(); ?>"></script>
</body>
</html>
