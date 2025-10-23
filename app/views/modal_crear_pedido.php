<!-- Modal para Crear/Editar Pedido -->
<div class="modal fade" id="modalPedido" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title flex items-center" id="modalPedidoTitle">
                    <i class="fas fa-file-invoice mr-2"></i>
                    <span>Nuevo Pedido</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formPedido">
                    <input type="hidden" id="pedido_id" name="id">

                    <!-- Sección: Datos Generales del Pedido -->
                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <span>Datos Generales del Pedido</span>
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="numero_pedido" class="form-label">Número/Folio de Pedido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="numero_pedido" name="numero_pedido" placeholder="PED-001, OP-2024-001, etc." required>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_entrega" class="form-label">Fecha de Entrega Estimada</label>
                            <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega">
                        </div>
                    </div>

                    <!-- Sección: Selección de Cliente -->
                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-users"></i>
                        </div>
                        <span>Información del Cliente</span>
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label for="cliente_busqueda" class="form-label">Seleccionar Cliente <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="cliente_busqueda" placeholder="Buscar por razón social, RFC o contacto..." autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="btnLimpiarCliente">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="cliente_sugerencias" class="list-group mt-2" style="display: none;"></div>
                            <input type="hidden" id="cliente_id" name="cliente_id" required>
                        </div>
                        <div class="col-md-4">
                            <label for="contacto" class="form-label">Contacto</label>
                            <input type="text" class="form-control" id="contacto" name="contacto" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="correo" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="correo" name="correo" readonly>
                        </div>
                    </div>

                    <!-- Sección: Direcciones -->
                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <span>Direcciones</span>
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="facturacion" class="form-label">Facturación (Bill To) <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="facturacion" name="facturacion" rows="3" placeholder="Dirección de facturación" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="entrega" class="form-label">Entrega (Ship To) <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="entrega" name="entrega" rows="3" placeholder="Dirección de entrega" required></textarea>
                        </div>
                    </div>

                    <!-- Sección: Productos/Items -->
                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <span>Productos/Items del Pedido</span>
                    </h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-hover" id="tablaItems">
                            <thead>
                                <tr>
                                    <th style="width: 50px">Line</th>
                                    <th>Producto (ID)</th>
                                    <th>Descripción</th>
                                    <th style="width: 150px">Cantidad</th>
                                    <th style="width: 100px">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_items">
                                <!-- Los items se agregan aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" id="btnAgregarItem">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </button>

                    <!-- Sección: Observaciones -->
                    <div class="mt-4">
                        <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                            <div class="bg-blue-100 p-2 rounded-lg mr-2">
                                <i class="fas fa-sticky-note"></i>
                            </div>
                            <span>Observaciones</span>
                        </h6>
                        <div class="mb-3">
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Notas adicionales del pedido..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarPedido">
                    <i class="fas fa-save"></i> Guardar Pedido
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para seleccionar producto (AJAX) -->
<div class="modal fade" id="modalBuscarProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-search mr-2"></i>Buscar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control mb-3" id="producto_busqueda" placeholder="Ingresa el ID del producto...">
                <div id="resultado_producto"></div>
            </div>
        </div>
    </div>
</div>
