<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title flex items-center" id="modalClienteTitle">
                    <i class="fas fa-user-plus mr-2"></i>
                    <span>Nuevo Cliente</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formCliente">
                    <input type="hidden" id="cliente_id" name="id">
                    
                    <div class="alert alert-danger d-none" id="erroresCliente"></div>
                    
                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-building"></i>
                        </div>
                        <span>Datos Fiscales</span>
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label for="razon_social" class="form-label">Razón Social <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="razon_social" name="razon_social" required maxlength="250">
                        </div>
                        <div class="col-md-4">
                            <label for="rfc" class="form-label">RFC <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" id="rfc" name="rfc" required maxlength="14" pattern="[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}">
                        </div>
                        <div class="col-md-6">
                            <label for="regimen_fiscal" class="form-label">Régimen Fiscal</label>
                            <input type="text" class="form-control" id="regimen_fiscal" name="regimen_fiscal" maxlength="100">
                        </div>
                        <div class="col-md-3">
                            <label for="uso_cfdi" class="form-label">Uso CFDI</label>
                            <select class="form-select" id="uso_cfdi" name="uso_cfdi">
                                <option value="">Seleccionar...</option>
                                <option value="G01">G01 - Adquisición de mercancías</option>
                                <option value="G02">G02 - Devoluciones</option>
                                <option value="G03">G03 - Gastos en general</option>
                                <option value="I04">I04 - Construcciones</option>
                                <option value="P01">P01 - Por definir</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="estatus" class="form-label">Estatus <span class="text-danger">*</span></label>
                            <select class="form-select" id="estatus" name="estatus" required>
                                <option value="activo">Activo</option>
                                <option value="suspendido">Suspendido</option>
                                <option value="bloqueado">Bloqueado</option>
                            </select>
                        </div>
                    </div>
                    
                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <span>Ubicación</span>
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-9">
                            <label for="direccion" class="form-label">Dirección Completa</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="2"></textarea>
                        </div>
                        <div class="col-md-3">
                            <label for="pais" class="form-label">País</label>
                            <input type="text" class="form-control" id="pais" name="pais" value="México" maxlength="100">
                        </div>
                    </div>
                    
                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-address-book"></i>
                        </div>
                        <span>Datos de Contacto</span>
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="contacto_principal" class="form-label">Contacto Principal</label>
                            <input type="text" class="form-control" id="contacto_principal" name="contacto_principal" maxlength="150">
                        </div>
                        <div class="col-md-4">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" maxlength="30">
                        </div>
                        <div class="col-md-4">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" maxlength="150">
                        </div>
                    </div>
                    
                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <span>Condiciones Comerciales</span>
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="dias_credito" class="form-label">Días de Crédito</label>
                            <select class="form-select" id="dias_credito" name="dias_credito">
                                <option value="0">Contado (0 días)</option>
                                <option value="15">15 días</option>
                                <option value="30">30 días</option>
                                <option value="45">45 días</option>
                                <option value="60">60 días</option>
                                <option value="90">90 días</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="limite_credito" class="form-label">Límite de Crédito</label>
                            <input type="number" class="form-control" id="limite_credito" name="limite_credito" step="0.01" min="0" value="0.00">
                        </div>
                        <div class="col-md-3">
                            <label for="moneda" class="form-label">Moneda</label>
                            <select class="form-select" id="moneda" name="moneda">
                                <option value="MXN">MXN - Peso Mexicano</option>
                                <option value="USD">USD - Dólar</option>
                                <option value="EUR">EUR - Euro</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="condiciones_pago" class="form-label">Condiciones de Pago</label>
                            <input type="text" class="form-control" id="condiciones_pago" name="condiciones_pago" maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <label for="metodo_pago" class="form-label">Método de Pago</label>
                            <select class="form-select" id="metodo_pago" name="metodo_pago">
                                <option value="">Seleccionar...</option>
                                <option value="PUE">PUE - Pago en una sola exhibición</option>
                                <option value="PPD">PPD - Pago en parcialidades</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="forma_pago" class="form-label">Forma de Pago</label>
                            <select class="form-select" id="forma_pago" name="forma_pago">
                                <option value="">Seleccionar...</option>
                                <option value="01">01 - Efectivo</option>
                                <option value="02">02 - Cheque</option>
                                <option value="03">03 - Transferencia</option>
                                <option value="04">04 - Tarjeta de crédito</option>
                                <option value="28">28 - Tarjeta de débito</option>
                                <option value="99">99 - Por definir</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="vendedor_asignado" class="form-label">Vendedor Asignado</label>
                            <input type="text" class="form-control" id="vendedor_asignado" name="vendedor_asignado" maxlength="100">
                        </div>
                    </div>
                    
                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-university"></i>
                        </div>
                        <span>Información Bancaria</span>
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="banco" class="form-label">Banco</label>
                            <input type="text" class="form-control" id="banco" name="banco" maxlength="150">
                        </div>
                        <div class="col-md-6">
                            <label for="cuenta_bancaria" class="form-label">Cuenta Bancaria</label>
                            <input type="text" class="form-control" id="cuenta_bancaria" name="cuenta_bancaria" maxlength="50">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary group" data-bs-dismiss="modal">
                    <i class="fas fa-times transition-transform group-hover:rotate-90"></i>
                    <span class="ml-1">Cancelar</span>
                </button>
                <button type="button" class="btn btn-primary group" id="btnGuardarCliente">
                    <i class="fas fa-save transition-transform group-hover:scale-125"></i>
                    <span class="ml-1">Guardar Cliente</span>
                </button>
            </div>
        </div>
    </div>
</div>
