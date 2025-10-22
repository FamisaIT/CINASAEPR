<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content overflow-hidden border border-white/10 bg-slate-950/90 text-slate-100 shadow-[0_45px_120px_-35px_rgba(37,99,235,0.55)]">
            <div class="modal-header border-b border-white/10 bg-gradient-to-r from-corporate-600 via-corporate-500 to-sky-500 text-white">
                <h5 class="modal-title text-lg font-semibold tracking-tight" id="modalClienteTitle">Nuevo Cliente</h5>
                <button type="button" class="btn-close invert" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body px-6 py-6">
                <form id="formCliente" class="space-y-10">
                    <input type="hidden" id="cliente_id" name="id">

                    <div id="erroresCliente" class="hidden rounded-2xl border border-rose-400/40 bg-rose-500/10 px-5 py-4 text-sm font-medium text-rose-200 shadow-inner shadow-rose-900/40"></div>

                    <section class="glass-panel">
                        <header class="section-header">
                            <span class="section-icon bg-gradient-to-br from-indigo-500 via-sky-500 to-cyan-500">
                                <i class="fas fa-building"></i>
                            </span>
                            <div>
                                <h6 class="section-title">Datos Fiscales</h6>
                                <p class="section-subtitle">Información legal y estatus corporativo clave.</p>
                            </div>
                        </header>
                        <div class="grid gap-5 lg:grid-cols-12">
                            <div class="lg:col-span-8">
                                <label for="razon_social" class="form-label-modern">Razón Social <span class="text-rose-400">*</span></label>
                                <input type="text" class="form-input-modern" id="razon_social" name="razon_social" required maxlength="250">
                            </div>
                            <div class="lg:col-span-4">
                                <label for="rfc" class="form-label-modern">RFC <span class="text-rose-400">*</span></label>
                                <input type="text" class="form-input-modern uppercase" id="rfc" name="rfc" required maxlength="14" pattern="[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}">
                            </div>
                            <div class="lg:col-span-6">
                                <label for="regimen_fiscal" class="form-label-modern">Régimen Fiscal</label>
                                <input type="text" class="form-input-modern" id="regimen_fiscal" name="regimen_fiscal" maxlength="100">
                            </div>
                            <div class="lg:col-span-3">
                                <label for="uso_cfdi" class="form-label-modern">Uso CFDI</label>
                                <select class="form-input-modern" id="uso_cfdi" name="uso_cfdi">
                                    <option value="">Seleccionar...</option>
                                    <option value="G01">G01 - Adquisición de mercancías</option>
                                    <option value="G02">G02 - Devoluciones</option>
                                    <option value="G03">G03 - Gastos en general</option>
                                    <option value="I04">I04 - Construcciones</option>
                                    <option value="P01">P01 - Por definir</option>
                                </select>
                            </div>
                            <div class="lg:col-span-3">
                                <label for="estatus" class="form-label-modern">Estatus <span class="text-rose-400">*</span></label>
                                <select class="form-input-modern" id="estatus" name="estatus" required>
                                    <option value="activo">Activo</option>
                                    <option value="suspendido">Suspendido</option>
                                    <option value="bloqueado">Bloqueado</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <section class="glass-panel">
                        <header class="section-header">
                            <span class="section-icon bg-gradient-to-br from-emerald-500 via-lime-500 to-amber-500">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <div>
                                <h6 class="section-title">Ubicación</h6>
                                <p class="section-subtitle">Dirección operativa y país asignado.</p>
                            </div>
                        </header>
                        <div class="grid gap-5 md:grid-cols-12">
                            <div class="md:col-span-9">
                                <label for="direccion" class="form-label-modern">Dirección Completa</label>
                                <textarea class="form-input-modern" id="direccion" name="direccion" rows="2"></textarea>
                            </div>
                            <div class="md:col-span-3">
                                <label for="pais" class="form-label-modern">País</label>
                                <input type="text" class="form-input-modern" id="pais" name="pais" value="México" maxlength="100">
                            </div>
                        </div>
                    </section>

                    <section class="glass-panel">
                        <header class="section-header">
                            <span class="section-icon bg-gradient-to-br from-purple-500 via-corporate-400 to-slate-500">
                                <i class="fas fa-address-book"></i>
                            </span>
                            <div>
                                <h6 class="section-title">Datos de Contacto</h6>
                                <p class="section-subtitle">Responsables directos y canales de comunicación.</p>
                            </div>
                        </header>
                        <div class="grid gap-5 md:grid-cols-12">
                            <div class="md:col-span-4">
                                <label for="contacto_principal" class="form-label-modern">Contacto Principal</label>
                                <input type="text" class="form-input-modern" id="contacto_principal" name="contacto_principal" maxlength="150">
                            </div>
                            <div class="md:col-span-4">
                                <label for="telefono" class="form-label-modern">Teléfono</label>
                                <input type="text" class="form-input-modern" id="telefono" name="telefono" maxlength="30">
                            </div>
                            <div class="md:col-span-4">
                                <label for="correo" class="form-label-modern">Correo Electrónico</label>
                                <input type="email" class="form-input-modern" id="correo" name="correo" maxlength="150">
                            </div>
                        </div>
                    </section>

                    <section class="glass-panel">
                        <header class="section-header">
                            <span class="section-icon bg-gradient-to-br from-amber-400 via-orange-500 to-rose-500">
                                <i class="fas fa-credit-card"></i>
                            </span>
                            <div>
                                <h6 class="section-title">Condiciones Comerciales</h6>
                                <p class="section-subtitle">Parámetros financieros y compromisos de pago.</p>
                            </div>
                        </header>
                        <div class="grid gap-5 md:grid-cols-12">
                            <div class="md:col-span-3">
                                <label for="dias_credito" class="form-label-modern">Días de Crédito</label>
                                <select class="form-input-modern" id="dias_credito" name="dias_credito">
                                    <option value="0">Contado (0 días)</option>
                                    <option value="15">15 días</option>
                                    <option value="30">30 días</option>
                                    <option value="45">45 días</option>
                                    <option value="60">60 días</option>
                                    <option value="90">90 días</option>
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label for="limite_credito" class="form-label-modern">Límite de Crédito</label>
                                <input type="number" class="form-input-modern" id="limite_credito" name="limite_credito" step="0.01" min="0" value="0.00">
                            </div>
                            <div class="md:col-span-3">
                                <label for="moneda" class="form-label-modern">Moneda</label>
                                <select class="form-input-modern" id="moneda" name="moneda">
                                    <option value="MXN">MXN - Peso Mexicano</option>
                                    <option value="USD">USD - Dólar</option>
                                    <option value="EUR">EUR - Euro</option>
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label for="condiciones_pago" class="form-label-modern">Condiciones de Pago</label>
                                <input type="text" class="form-input-modern" id="condiciones_pago" name="condiciones_pago" maxlength="100">
                            </div>
                            <div class="md:col-span-4">
                                <label for="metodo_pago" class="form-label-modern">Método de Pago</label>
                                <select class="form-input-modern" id="metodo_pago" name="metodo_pago">
                                    <option value="">Seleccionar...</option>
                                    <option value="PUE">PUE - Pago en una sola exhibición</option>
                                    <option value="PPD">PPD - Pago en parcialidades</option>
                                </select>
                            </div>
                            <div class="md:col-span-4">
                                <label for="forma_pago" class="form-label-modern">Forma de Pago</label>
                                <select class="form-input-modern" id="forma_pago" name="forma_pago">
                                    <option value="">Seleccionar...</option>
                                    <option value="01">01 - Efectivo</option>
                                    <option value="02">02 - Cheque</option>
                                    <option value="03">03 - Transferencia</option>
                                    <option value="04">04 - Tarjeta de crédito</option>
                                    <option value="28">28 - Tarjeta de débito</option>
                                    <option value="99">99 - Por definir</option>
                                </select>
                            </div>
                            <div class="md:col-span-4">
                                <label for="vendedor_asignado" class="form-label-modern">Vendedor Asignado</label>
                                <input type="text" class="form-input-modern" id="vendedor_asignado" name="vendedor_asignado" maxlength="100">
                            </div>
                        </div>
                    </section>

                    <section class="glass-panel">
                        <header class="section-header">
                            <span class="section-icon bg-gradient-to-br from-sky-500 via-corporate-400 to-indigo-500">
                                <i class="fas fa-university"></i>
                            </span>
                            <div>
                                <h6 class="section-title">Información Bancaria</h6>
                                <p class="section-subtitle">Referencias de pago para operaciones seguras.</p>
                            </div>
                        </header>
                        <div class="grid gap-5 md:grid-cols-12">
                            <div class="md:col-span-6">
                                <label for="banco" class="form-label-modern">Banco</label>
                                <input type="text" class="form-input-modern" id="banco" name="banco" maxlength="150">
                            </div>
                            <div class="md:col-span-6">
                                <label for="cuenta_bancaria" class="form-label-modern">Cuenta Bancaria</label>
                                <input type="text" class="form-input-modern" id="cuenta_bancaria" name="cuenta_bancaria" maxlength="50">
                            </div>
                        </div>
                    </section>
                </form>
            </div>
            <div class="modal-footer border-t border-white/10 bg-slate-950/80 px-6 py-4">
                <button type="button" class="rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-sm font-semibold uppercase tracking-[0.28em] text-slate-200 transition duration-300 hover:-translate-y-0.5 hover:border-white/30 hover:bg-white/10" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="group inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-corporate-500 via-sky-500 to-emerald-500 px-6 py-3 text-sm font-semibold uppercase tracking-[0.28em] text-white shadow-lg shadow-emerald-500/25 transition duration-300 hover:scale-[1.02] hover:from-corporate-400 hover:via-sky-400 hover:to-emerald-400" id="btnGuardarCliente">
                    <i class="fas fa-save"></i> Guardar Cliente
                </button>
            </div>
        </div>
    </div>
</div>
