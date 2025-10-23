<?php
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/session.php';
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/models/produccion_model.php';

$pageTitle = 'Tracking de Producción';
$model = new ProductionModel($pdo);

include __DIR__ . '/app/views/header.php';
?>

<!-- Logo de Fondo Transparente -->
<div class="logo-background" style="position: fixed; top: 50%; left: 50%; width: 1200px; height: 1200px; margin-left: -600px; margin-top: -600px; background-image: url('<?php echo BASE_PATH; ?>/app/assets/img/logo.png'); background-size: 60%; background-repeat: no-repeat; background-position: center; background-attachment: fixed; opacity: 0.25; pointer-events: none; z-index: -1;"></div>

<!-- Enhanced Filter Section -->
<div class="filter-section">
    <h5 class="mb-3 flex items-center text-xl">
        <i class="fas fa-filter text-blue-600"></i>
        <span class="ml-2">Filtros de Búsqueda</span>
    </h5>
    <div class="row g-3">
        <div class="col-md-4">
            <label for="buscar" class="form-label">Buscar</label>
            <div class="relative">
                <input type="text" class="form-control pl-10" id="buscar" placeholder="Número de pedido, código de ítem...">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        <div class="col-md-3">
            <label for="estatus" class="form-label">Estatus</label>
            <select class="form-select" id="estatus">
                <option value="">Todos</option>
                <option value="en_produccion" selected>En Producción</option>
                <option value="completada">Completada</option>
                <option value="cancelada">Cancelada</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="fechaFiltro" class="form-label">Fecha Entrega</label>
            <input type="date" class="form-control" id="fechaFiltro">
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button class="btn btn-primary w-100 group" id="btnBuscar" title="Buscar">
                <i class="fas fa-search transition-transform group-hover:scale-125"></i>
            </button>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <button class="btn btn-secondary btn-sm group" id="btnLimpiarFiltros">
                <i class="fas fa-eraser transition-transform group-hover:rotate-12"></i>
                <span class="ml-1">Limpiar Filtros</span>
            </button>
        </div>
    </div>
</div>


<!-- Órdenes Separadas -->
<div id="ordenesContainer">
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>
</div>

<!-- Paginación -->
<div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
    <div id="contador" class="text-muted"></div>
    <div id="paginacion" class="pagination-container"></div>
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
    <script src="<?php echo BASE_PATH; ?>/app/assets/produccion.js?v=<?php echo time(); ?>"></script>
</body>
</html>
