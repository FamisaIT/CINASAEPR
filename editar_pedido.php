<?php
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/session.php';
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/models/pedidos_model.php';
require_once __DIR__ . '/app/models/clientes_model.php';
require_once __DIR__ . '/app/models/productos_model.php';

$pageTitle = 'Editar Pedido';
$pedidosModel = new PedidosModel($pdo);
$clientesModel = new ClientesModel($pdo);
$productosModel = new ProductosModel($pdo);

// Obtener ID del pedido
$pedido_id = $_GET['id'] ?? null;

if (empty($pedido_id)) {
    header('Location: ' . BASE_PATH . '/pedidos.php');
    exit;
}

// Cargar los datos del pedido
$pedido = $pedidosModel->obtenerPedidoPorId($pedido_id);

if (!$pedido) {
    header('Location: ' . BASE_PATH . '/pedidos.php');
    exit;
}

// Obtener los items del pedido
$items = $pedidosModel->obtenerItemsPedido($pedido_id);

// Obtener clientes y productos para los selects
$clientes = $clientesModel->listarClientes(['estatus' => 'activo'], 'razon_social', 'ASC', 1000);
$productos = $productosModel->listarProductos(['estatus' => 'activo'], 'material_code', 'ASC', 1000);

include __DIR__ . '/app/views/header.php';
?>

<!-- Logo de Fondo Transparente -->
<div class="logo-background" style="position: fixed; top: 50%; left: 50%; width: 1200px; height: 1200px; margin-left: -600px; margin-top: -600px; background-image: url('<?php echo BASE_PATH; ?>/app/assets/img/logo.png'); background-size: 60%; background-repeat: no-repeat; background-position: center; background-attachment: fixed; opacity: 0.25; pointer-events: none; z-index: -1;"></div>

<!-- Breadcrumb -->
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?php echo BASE_PATH; ?>/pedidos.php">Pedidos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar Pedido</li>
        </ol>
    </nav>
</div>

<!-- Main Card -->
<div class="card shadow-lg border-0">
    <div class="card-header bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4">
        <h3 class="mb-0">
            <i class="fas fa-file-invoice mr-2"></i>Editar Pedido: <?php echo htmlspecialchars($pedido['numero_pedido']); ?>
        </h3>
    </div>

    <div class="card-body p-4">
        <form id="formEditarPedido">
            <input type="hidden" id="pedido_id" name="pedido_id" value="<?php echo $pedido_id; ?>">

            <!-- Sección: Datos Generales del Pedido -->
            <div class="mb-5">
                <h5 class="border-bottom pb-3 mb-4 flex items-center text-blue-700">
                    <div class="bg-blue-100 p-2 rounded-lg mr-2">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <span>Datos Generales del Pedido</span>
                </h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="numero_pedido" class="form-label">Número/Folio de Pedido <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="numero_pedido" name="numero_pedido" placeholder="PED-001, OP-2024-001, etc." value="<?php echo htmlspecialchars($pedido['numero_pedido']); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_entrega" class="form-label">Fecha de Entrega Estimada</label>
                        <input type="date" class="form-control form-control-lg" id="fecha_entrega" name="fecha_entrega" value="<?php echo $pedido['fecha_entrega'] ? date('Y-m-d', strtotime($pedido['fecha_entrega'])) : ''; ?>">
                    </div>
                </div>
            </div>

            <!-- Sección: Selección de Cliente -->
            <div class="mb-5">
                <h5 class="border-bottom pb-3 mb-4 flex items-center text-blue-700">
                    <div class="bg-blue-100 p-2 rounded-lg mr-2">
                        <i class="fas fa-users"></i>
                    </div>
                    <span>Información del Cliente</span>
                </h5>
                <div class="row g-4">
                    <div class="col-md-8 position-relative">
                        <label for="cliente_busqueda" class="form-label">Seleccionar Cliente <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" id="cliente_busqueda" placeholder="Buscar cliente..." autocomplete="off" value="<?php echo htmlspecialchars($pedido['razon_social']); ?>" required>
                            <button class="btn btn-outline-secondary" type="button" id="btnLimpiarCliente" title="Limpiar selección">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div id="cliente_lista" class="list-group mt-1" style="display: none; max-height: 400px; overflow-y: auto; position: absolute; width: 100%; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
                        <input type="hidden" id="cliente_id" name="cliente_id" value="<?php echo $pedido['cliente_id']; ?>" required>
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <label for="contacto" class="form-label">Contacto</label>
                        <input type="text" class="form-control form-control-lg" id="contacto" name="contacto" value="<?php echo htmlspecialchars($pedido['contacto'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control form-control-lg" id="telefono" name="telefono" value="<?php echo htmlspecialchars($pedido['telefono'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" class="form-control form-control-lg" id="correo" name="correo" value="<?php echo htmlspecialchars($pedido['correo'] ?? ''); ?>" readonly>
                    </div>
                </div>
            </div>

            <!-- Sección: Direcciones -->
            <div class="mb-5">
                <h5 class="border-bottom pb-3 mb-4 flex items-center text-blue-700">
                    <div class="bg-blue-100 p-2 rounded-lg mr-2">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <span>Direcciones de Envío</span>
                </h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="facturacion" class="form-label">Facturación (Bill To) <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-lg" id="facturacion" name="facturacion" rows="4" placeholder="Dirección de facturación" required><?php echo htmlspecialchars($pedido['facturacion']); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="entrega" class="form-label">Entrega (Ship To) <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-lg" id="entrega" name="entrega" rows="4" placeholder="Dirección de entrega" required><?php echo htmlspecialchars($pedido['entrega']); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Sección: Productos/Items -->
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h5 class="mb-0 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <span>Productos/Items del Pedido</span>
                    </h5>
                    <button type="button" class="btn btn-sm btn-primary" id="btnAgregarItem">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">Line</th>
                                <th style="width: 150px;">Producto (ID)</th>
                                <th>Descripción</th>
                                <th style="width: 80px;">Unidad</th>
                                <th style="width: 100px;">Cantidad</th>
                                <th style="width: 120px;">Precio Unit.</th>
                                <th style="width: 120px;">Subtotal</th>
                                <th style="width: 70px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_items">
                            <!-- Los items se agregan aquí dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sección: Observaciones -->
            <div class="mb-5">
                <h5 class="border-bottom pb-3 mb-4 flex items-center text-blue-700">
                    <div class="bg-blue-100 p-2 rounded-lg mr-2">
                        <i class="fas fa-sticky-note"></i>
                    </div>
                    <span>Observaciones</span>
                </h5>
                <div>
                    <textarea class="form-control form-control-lg" id="observaciones" name="observaciones" rows="4" placeholder="Notas adicionales del pedido..."><?php echo htmlspecialchars($pedido['observaciones'] ?? ''); ?></textarea>
                </div>
            </div>
        </form>
    </div>

    <div class="card-footer bg-light py-4 border-top">
        <div class="d-flex gap-2 justify-content-end flex-wrap">
            <a href="<?php echo BASE_PATH; ?>/pedidos.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <button type="button" class="btn btn-danger btn-lg" id="btnCancelarPedido">
                <i class="fas fa-ban"></i> Cancelar Pedido
            </button>
            <button type="button" class="btn btn-primary btn-lg" id="btnGuardarPedido">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </div>
</div>

<!-- Script de datos iniciales -->
<script>
    // Datos de clientes y productos para el autocompletado
    const clientesData = <?php echo json_encode($clientes); ?>;
    const productosData = <?php echo json_encode($productos); ?>;
    const itemsExistentes = <?php echo json_encode($items); ?>;
</script>

    </main>
    <footer class="bg-gradient-to-r from-slate-100 via-blue-50 to-slate-100 text-center py-6 mt-8 shadow-inner">
        <div class="container">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <img src="<?php echo BASE_PATH; ?>/app/assets/img/logo.png" alt="CINASA Logo" class="h-8 w-8">
                <p class="text-slate-600 mb-0 font-medium">
                    <i class="fas fa-copyright text-blue-600"></i>
                    <?php echo date('Y'); ?> Sistema de Gestión de Pedidos - CINASA
                </p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_PATH; ?>/app/assets/editar_pedido.js?v=<?php echo time(); ?>"></script>
</body>
</html>
