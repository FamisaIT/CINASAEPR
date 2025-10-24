<?php
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/session.php';
require_once __DIR__ . '/app/config/database.php';

// Verificar si las tablas de calidad existen
try {
    $verificacion = $pdo->query("SHOW TABLES LIKE 'piezas_producidas'")->fetch();
    if (empty($verificacion)) {
        header('Location: instalar_calidad.php');
        exit;
    }
} catch (Exception $e) {
    header('Location: instalar_calidad.php');
    exit;
}

require_once __DIR__ . '/app/models/calidad_model.php';

// Obtener número de pedido de la URL
$numero_pedido = $_GET['pedido'] ?? null;
if (!$numero_pedido) {
    header('Location: calidad.php');
    exit;
}

$pageTitle = 'Inspección de Calidad - Pedido ' . htmlspecialchars($numero_pedido);
$model = new CalidadModel($pdo);

include __DIR__ . '/app/views/header.php';
?>

<!-- Logo de Fondo Transparente -->
<div class="logo-background" style="position: fixed; top: 50%; left: 50%; width: 1200px; height: 1200px; margin-left: -600px; margin-top: -600px; background-image: url('<?php echo BASE_PATH; ?>/app/assets/img/logo.png'); background-size: 60%; background-repeat: no-repeat; background-position: center; background-attachment: fixed; opacity: 0.25; pointer-events: none; z-index: -1;"></div>

<!-- Botón Volver -->
<div class="mb-3">
    <a href="<?php echo BASE_PATH; ?>/calidad.php" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-arrow-left"></i> Volver a Pedidos
    </a>
</div>

<!-- Header del Pedido -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-0">Pedido: <span id="numero_pedido_display" class="text-primary"></span></h5>
            <p class="text-muted mb-0 mt-2" id="cliente_display"></p>
        </div>
        <div class="badge bg-info" style="font-size: 1rem;">
            <i class="fas fa-cubes me-1"></i>
            Piezas Pendientes: <span id="total_piezas">0</span>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="filter-section">
    <h5 class="mb-3">
        <i class="fas fa-filter text-blue-600"></i>
        <span class="ms-2">Filtros</span>
    </h5>
    <div class="row g-3">
        <div class="col-md-4">
            <label for="filtro_buscar_pedido" class="form-label">Buscar Folio / Código</label>
            <input type="text" class="form-control" id="filtro_buscar_pedido" placeholder="Ej: PROD-2025-01-15-00001">
        </div>
        <div class="col-md-4">
            <label for="filtro_item_pedido" class="form-label">Código de Ítem</label>
            <select class="form-select" id="filtro_item_pedido">
                <option value="">Todos los ítems</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="filtro_supervisor_pedido" class="form-label">Supervisor</label>
            <select class="form-select" id="filtro_supervisor_pedido">
                <option value="">Todos</option>
            </select>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary" id="btn_buscar_pedido">
                <i class="fas fa-search"></i> Buscar
            </button>
            <button class="btn btn-secondary" id="btn_limpiar_pedido">
                <i class="fas fa-eraser"></i> Limpiar Filtros
            </button>
        </div>
    </div>
</div>

<!-- Lista de Piezas -->
<div class="mt-4">
    <h5 class="mb-3">
        <i class="fas fa-list text-blue-600"></i> Piezas por Inspeccionar
    </h5>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Folio</th>
                    <th>Código de Ítem</th>
                    <th>Descripción</th>
                    <th>Supervisor</th>
                    <th>Fecha Producción</th>
                    <th>Estado</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody id="tabla_piezas_pedido">
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="text-muted mt-2">Cargando piezas...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div id="contador_piezas_pedido" class="text-muted"></div>
        <div id="paginacion_piezas_pedido" class="pagination-container"></div>
    </div>
</div>

<!-- Modal will be inserted here dynamically -->

</main>
<footer class="bg-gradient-to-r from-slate-100 via-blue-50 to-slate-100 text-center py-6 mt-8 shadow-inner">
    <div class="container">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
            <img src="<?php echo BASE_PATH; ?>/app/assets/img/logo.png" alt="CINASA Logo" class="h-8 w-8">
            <p class="text-slate-600 mb-0 font-medium">
                <i class="fas fa-copyright text-blue-600"></i>
                <?php echo date('Y'); ?> Inspección de Calidad - CINASA
            </p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo BASE_PATH; ?>/app/assets/calidad_pedido.js?v=<?php echo time(); ?>"></script>
</body>
</html>
