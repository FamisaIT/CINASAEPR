<?php
require_once __DIR__ . '/app/config/session.php';

$pageTitle = 'Catálogo Maestro de Clientes';
require_once __DIR__ . '/app/views/header.php';
?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <h3 id="totalClientes">-</h3>
            <p><i class="fas fa-users"></i> Total de Clientes</p>
        </div>
    </div>
</div>

<div class="filter-section">
    <h5 class="mb-3"><i class="fas fa-filter"></i> Filtros de Búsqueda</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <label for="buscar" class="form-label">Buscar</label>
            <input type="text" class="form-control" id="buscar" placeholder="Razón social, RFC o contacto...">
        </div>
        <div class="col-md-2">
            <label for="filtro_estatus" class="form-label">Estatus</label>
            <select class="form-select" id="filtro_estatus">
                <option value="">Todos</option>
                <option value="activo">Activo</option>
                <option value="suspendido">Suspendido</option>
                <option value="bloqueado">Bloqueado</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="filtro_vendedor" class="form-label">Vendedor</label>
            <select class="form-select" id="filtro_vendedor">
                <option value="">Todos</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="filtro_pais" class="form-label">País</label>
            <select class="form-select" id="filtro_pais">
                <option value="">Todos</option>
            </select>
        </div>
        <div class="col-md-1 d-flex align-items-end gap-2">
            <button class="btn btn-primary w-100" id="btnBuscar" title="Buscar">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <button class="btn btn-secondary btn-sm" id="btnLimpiarFiltros">
                <i class="fas fa-eraser"></i> Limpiar Filtros
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list"></i> Listado de Clientes</span>
        <div>
            <button class="btn btn-success btn-sm me-2" id="btnExportarCSV">
                <i class="fas fa-file-csv"></i> Exportar CSV
            </button>
            <button class="btn btn-primary btn-sm" id="btnNuevoCliente">
                <i class="fas fa-plus"></i> Nuevo Cliente
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="sortable" data-column="id">ID</th>
                        <th class="sortable" data-column="razon_social">Razón Social</th>
                        <th class="sortable" data-column="rfc">RFC</th>
                        <th>Contacto</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th class="sortable" data-column="estatus">Estatus</th>
                        <th class="sortable" data-column="vendedor_asignado">Vendedor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaClientes">
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-body border-top">
        <div class="d-flex justify-content-center">
            <div id="paginacion"></div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/app/views/modal_cliente.php'; ?>
<?php require_once __DIR__ . '/app/views/footer.php'; ?>
