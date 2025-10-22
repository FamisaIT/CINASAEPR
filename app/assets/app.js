let paginaActual = 1;
let filtrosActuales = {};
let ordenActual = 'razon_social';
let direccionActual = 'ASC';

const ESTATUS_STYLES = {
    activo: 'inline-flex items-center gap-2 rounded-full border border-emerald-400/50 bg-emerald-500/10 px-3 py-1 text-[0.7rem] font-semibold uppercase tracking-[0.28em] text-emerald-200 shadow-sm shadow-emerald-900/40 backdrop-blur',
    suspendido: 'inline-flex items-center gap-2 rounded-full border border-amber-400/50 bg-amber-500/10 px-3 py-1 text-[0.7rem] font-semibold uppercase tracking-[0.28em] text-amber-200 shadow-sm shadow-amber-900/40 backdrop-blur',
    bloqueado: 'inline-flex items-center gap-2 rounded-full border border-rose-400/60 bg-rose-500/10 px-3 py-1 text-[0.7rem] font-semibold uppercase tracking-[0.28em] text-rose-200 shadow-sm shadow-rose-900/40 backdrop-blur'
};

const ACTION_BUTTON_BASE = 'group relative flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-200 transition duration-300 hover:-translate-y-1 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-corporate-300 shadow-[0_18px_45px_-30px_rgba(59,130,246,0.65)]';
const ACTION_BUTTON_VARIANTS = {
    view: 'hover:border-sky-400 hover:bg-sky-500/20 hover:text-sky-100',
    edit: 'hover:border-amber-400 hover:bg-amber-500/20 hover:text-amber-100',
    pdf: 'hover:border-emerald-400 hover:bg-emerald-500/20 hover:text-emerald-100',
    block: 'hover:border-rose-400 hover:bg-rose-500/20 hover:text-rose-100'
};

const LOADING_ROW_HTML = `
    <tr>
        <td colspan="9" class="px-6 py-12">
            <div class="flex flex-col items-center justify-center gap-4 text-slate-400">
                <span class="loading-spinner h-12 w-12"></span>
                <span class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Cargando clientes...</span>
            </div>
        </td>
    </tr>
`;

const EMPTY_STATE_HTML = `
    <tr>
        <td colspan="9" class="px-6 py-14">
            <div class="flex flex-col items-center gap-4 text-center text-slate-400">
                <span class="flex h-16 w-16 items-center justify-center rounded-3xl border border-dashed border-white/15 bg-white/5 text-2xl text-corporate-200/80">
                    <i class="fas fa-user-slash"></i>
                </span>
                <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500/80">No se encontraron clientes</p>
                <p class="max-w-md text-xs text-slate-500/70">Ajusta los filtros o registra un nuevo cliente para visualizarlo inmediatamente en este panel ejecutivo.</p>
            </div>
        </td>
    </tr>
`;

const PAGINATION_BUTTON_BASE = 'inline-flex min-w-[2.75rem] items-center justify-center rounded-2xl px-4 py-2 text-[0.65rem] font-semibold uppercase tracking-[0.28em] transition duration-300';
const PAGINATION_BUTTON_DEFAULT = 'border border-white/10 bg-slate-950/70 text-slate-200 hover:-translate-y-0.5 hover:border-corporate-300 hover:bg-slate-900/80 hover:text-white';
const PAGINATION_BUTTON_ACTIVE = 'border border-transparent bg-gradient-to-r from-corporate-500 to-sky-500 text-white shadow-lg shadow-sky-900/40';
const PAGINATION_BUTTON_DISABLED = 'cursor-not-allowed border border-white/5 bg-white/5 text-slate-500/60';

document.addEventListener('DOMContentLoaded', function() {
    cargarFiltros();
    cargarClientes();
    
    document.getElementById('btnNuevoCliente').addEventListener('click', abrirModalNuevo);
    document.getElementById('btnGuardarCliente').addEventListener('click', guardarCliente);
    document.getElementById('btnBuscar').addEventListener('click', aplicarFiltros);
    document.getElementById('btnLimpiarFiltros').addEventListener('click', limpiarFiltros);
    document.getElementById('btnExportarCSV').addEventListener('click', exportarCSV);
    
    document.getElementById('buscar').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            aplicarFiltros();
        }
    });
    
    document.querySelectorAll('.sortable').forEach(th => {
        th.addEventListener('click', function() {
            const columna = this.dataset.column;
            if (ordenActual === columna) {
                direccionActual = direccionActual === 'ASC' ? 'DESC' : 'ASC';
            } else {
                ordenActual = columna;
                direccionActual = 'ASC';
            }
            actualizarIconosOrden();
            cargarClientes();
        });
    });
});

function cargarFiltros() {
    fetch('app/controllers/obtener_filtros.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const selectVendedor = document.getElementById('filtro_vendedor');
                const selectPais = document.getElementById('filtro_pais');
                
                data.vendedores.forEach(vendedor => {
                    const option = document.createElement('option');
                    option.value = vendedor;
                    option.textContent = vendedor;
                    selectVendedor.appendChild(option);
                });
                
                data.paises.forEach(pais => {
                    const option = document.createElement('option');
                    option.value = pais;
                    option.textContent = pais;
                    selectPais.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error al cargar filtros:', error));
}

function cargarClientes() {
    const params = new URLSearchParams({
        ...filtrosActuales,
        orden: ordenActual,
        direccion: direccionActual,
        pagina: paginaActual
    });
    
    const tbody = document.getElementById('tablaClientes');
    tbody.innerHTML = LOADING_ROW_HTML;
    
    fetch(`app/controllers/clientes_listar.php?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarClientes(data.data);
                actualizarPaginacion(data.pagination);
                actualizarContador(data.pagination.total);
            } else {
                tbody.innerHTML = EMPTY_STATE_HTML;
                mostrarError(data.message || 'Error al cargar los clientes');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = buildErrorRow('Error de conexión al cargar los clientes');
            mostrarError('Error de conexión al cargar los clientes');
        });
}

function mostrarClientes(clientes) {
    const tbody = document.getElementById('tablaClientes');
    tbody.innerHTML = '';
    
    if (clientes.length === 0) {
        tbody.innerHTML = EMPTY_STATE_HTML;
        return;
    }
    
    clientes.forEach(cliente => {
        const tr = document.createElement('tr');
        const estatusText = cliente.estatus.charAt(0).toUpperCase() + cliente.estatus.slice(1);
        const estatusClass = ESTATUS_STYLES[cliente.estatus] || ESTATUS_STYLES.activo;
        
        tr.innerHTML = `
            <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold tracking-tight text-slate-200">${cliente.id}</td>
            <td class="px-5 py-4">
                <p class="text-sm font-semibold text-white">${escapeHtml(cliente.razon_social)}</p>
            </td>
            <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-slate-400">${cliente.rfc}</td>
            <td class="px-5 py-4 text-sm text-slate-300">${escapeHtml(cliente.contacto_principal || 'N/A')}</td>
            <td class="px-5 py-4 text-sm text-slate-300">${escapeHtml(cliente.telefono || 'N/A')}</td>
            <td class="px-5 py-4 text-sm text-slate-300">${escapeHtml(cliente.correo || 'N/A')}</td>
            <td class="px-5 py-4"><span class="${estatusClass}">${estatusText}</span></td>
            <td class="px-5 py-4 text-sm text-slate-300">${escapeHtml(cliente.vendedor_asignado || 'N/A')}</td>
            <td class="px-5 py-4">
                <div class="flex items-center justify-end gap-2">
                    <button type="button" class="${ACTION_BUTTON_BASE} ${ACTION_BUTTON_VARIANTS.view}" onclick="verDetalle(${cliente.id})" title="Ver detalle">
                        <i class="fas fa-eye text-base"></i>
                    </button>
                    <button type="button" class="${ACTION_BUTTON_BASE} ${ACTION_BUTTON_VARIANTS.edit}" onclick="editarCliente(${cliente.id})" title="Editar">
                        <i class="fas fa-edit text-base"></i>
                    </button>
                    <button type="button" class="${ACTION_BUTTON_BASE} ${ACTION_BUTTON_VARIANTS.pdf}" onclick="exportarPDF(${cliente.id})" title="Exportar PDF">
                        <i class="fas fa-file-pdf text-base"></i>
                    </button>
                    <button type="button" class="${ACTION_BUTTON_BASE} ${ACTION_BUTTON_VARIANTS.block}" onclick="confirmarEliminar(${cliente.id}, '${escapeHtml(cliente.razon_social)}')" title="Bloquear">
                        <i class="fas fa-ban text-base"></i>
                    </button>
                </div>
            </td>
        `;
        
        tbody.appendChild(tr);
    });
}

function actualizarPaginacion(pagination) {
    const paginacionDiv = document.getElementById('paginacion');
    paginacionDiv.innerHTML = '';
    
    if (pagination.total_paginas <= 1) return;
    
    const nav = document.createElement('nav');
    const ul = document.createElement('ul');
    ul.className = 'flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-2 py-2 shadow-lg shadow-black/45 backdrop-blur';
    
    ul.appendChild(crearItemPaginacion('Anterior', pagination.pagina_actual - 1, pagination.pagina_actual === 1));
    
    const inicio = Math.max(1, pagination.pagina_actual - 2);
    const fin = Math.min(pagination.total_paginas, pagination.pagina_actual + 2);
    
    if (inicio > 1) {
        ul.appendChild(crearItemPaginacion('1', 1, false, pagination.pagina_actual === 1));
        if (inicio > 2) {
            ul.appendChild(crearSeparadorPaginacion());
        }
    }
    
    for (let i = inicio; i <= fin; i++) {
        ul.appendChild(crearItemPaginacion(String(i), i, false, pagination.pagina_actual === i));
    }
    
    if (fin < pagination.total_paginas) {
        if (fin < pagination.total_paginas - 1) {
            ul.appendChild(crearSeparadorPaginacion());
        }
        ul.appendChild(crearItemPaginacion(String(pagination.total_paginas), pagination.total_paginas, false, pagination.pagina_actual === pagination.total_paginas));
    }
    
    ul.appendChild(crearItemPaginacion('Siguiente', pagination.pagina_actual + 1, pagination.pagina_actual === pagination.total_paginas));
    
    nav.appendChild(ul);
    paginacionDiv.appendChild(nav);
}

function crearItemPaginacion(label, pagina, deshabilitado = false, activo = false) {
    const li = document.createElement('li');
    const enlace = document.createElement('a');
    enlace.href = '#';
    enlace.textContent = label;
    enlace.className = `${PAGINATION_BUTTON_BASE} ${deshabilitado ? PAGINATION_BUTTON_DISABLED : PAGINATION_BUTTON_DEFAULT} ${activo ? PAGINATION_BUTTON_ACTIVE : ''}`;
    
    if (!deshabilitado) {
        enlace.addEventListener('click', function(event) {
            event.preventDefault();
            cambiarPagina(pagina);
        });
    } else {
        enlace.addEventListener('click', event => event.preventDefault());
    }
    
    li.appendChild(enlace);
    return li;
}

function crearSeparadorPaginacion() {
    const li = document.createElement('li');
    const span = document.createElement('span');
    span.className = 'px-2 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500';
    span.textContent = '···';
    li.appendChild(span);
    return li;
}

function actualizarContador(total) {
    document.getElementById('totalClientes').textContent = total;
}

function cambiarPagina(pagina) {
    paginaActual = pagina;
    cargarClientes();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function aplicarFiltros() {
    filtrosActuales = {
        buscar: document.getElementById('buscar').value.trim(),
        estatus: document.getElementById('filtro_estatus').value,
        vendedor: document.getElementById('filtro_vendedor').value,
        pais: document.getElementById('filtro_pais').value
    };
    
    paginaActual = 1;
    cargarClientes();
}

function limpiarFiltros() {
    document.getElementById('buscar').value = '';
    document.getElementById('filtro_estatus').value = '';
    document.getElementById('filtro_vendedor').value = '';
    document.getElementById('filtro_pais').value = '';
    
    filtrosActuales = {};
    paginaActual = 1;
    cargarClientes();
}

function abrirModalNuevo() {
    document.getElementById('formCliente').reset();
    document.getElementById('cliente_id').value = '';
    document.getElementById('modalClienteTitle').textContent = 'Nuevo Cliente';
    document.getElementById('estatus').value = 'activo';
    document.getElementById('pais').value = 'México';
    document.getElementById('moneda').value = 'MXN';
    document.getElementById('dias_credito').value = '0';
    ocultarErrores();
    
    const modal = new bootstrap.Modal(document.getElementById('modalCliente'));
    modal.show();
}

function editarCliente(id) {
    fetch(`app/controllers/clientes_detalle.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cliente = data.data;
                
                document.getElementById('cliente_id').value = cliente.id;
                document.getElementById('razon_social').value = cliente.razon_social;
                document.getElementById('rfc').value = cliente.rfc;
                document.getElementById('regimen_fiscal').value = cliente.regimen_fiscal || '';
                document.getElementById('direccion').value = cliente.direccion || '';
                document.getElementById('pais').value = cliente.pais;
                document.getElementById('contacto_principal').value = cliente.contacto_principal || '';
                document.getElementById('telefono').value = cliente.telefono || '';
                document.getElementById('correo').value = cliente.correo || '';
                document.getElementById('dias_credito').value = cliente.dias_credito;
                document.getElementById('limite_credito').value = cliente.limite_credito;
                document.getElementById('condiciones_pago').value = cliente.condiciones_pago || '';
                document.getElementById('moneda').value = cliente.moneda;
                document.getElementById('uso_cfdi').value = cliente.uso_cfdi || '';
                document.getElementById('metodo_pago').value = cliente.metodo_pago || '';
                document.getElementById('forma_pago').value = cliente.forma_pago || '';
                document.getElementById('banco').value = cliente.banco || '';
                document.getElementById('cuenta_bancaria').value = cliente.cuenta_bancaria || '';
                document.getElementById('estatus').value = cliente.estatus;
                document.getElementById('vendedor_asignado').value = cliente.vendedor_asignado || '';
                
                document.getElementById('modalClienteTitle').textContent = 'Editar Cliente';
                ocultarErrores();
                
                const modal = new bootstrap.Modal(document.getElementById('modalCliente'));
                modal.show();
            } else {
                mostrarError('Error al cargar el cliente');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error de conexión al cargar el cliente');
        });
}

function guardarCliente() {
    const formData = new FormData(document.getElementById('formCliente'));
    const id = document.getElementById('cliente_id').value;
    const url = id ? 'app/controllers/clientes_editar.php' : 'app/controllers/clientes_crear.php';
    
    const btn = document.getElementById('btnGuardarCliente');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Guardar Cliente';
        
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalCliente')).hide();
            cargarClientes();
            mostrarExito(data.message);
        } else {
            if (data.errors) {
                mostrarErroresFormulario(data.errors);
            } else {
                mostrarError(data.message);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Guardar Cliente';
        mostrarError('Error de conexión al guardar el cliente');
    });
}

function confirmarEliminar(id, razonSocial) {
    if (confirm(`¿Está seguro de bloquear el cliente "${razonSocial}"?\n\nEl cliente quedará con estatus "bloqueado".`)) {
        eliminarCliente(id);
    }
}

function eliminarCliente(id) {
    const formData = new FormData();
    formData.append('id', id);
    
    fetch('app/controllers/clientes_eliminar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarClientes();
            mostrarExito(data.message);
        } else {
            mostrarError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('Error de conexión al bloquear el cliente');
    });
}

function verDetalle(id) {
    fetch(`app/controllers/clientes_detalle.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarDetalleModal(data.data);
            } else {
                mostrarError('Error al cargar el detalle del cliente');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error de conexión');
        });
}

function mostrarDetalleModal(cliente) {
    const detalleHTML = `
        <div class="space-y-6">
            <section class="glass-panel">
                <div class="grid gap-5 md:grid-cols-2">
                    <div class="space-y-4">
                        <h6 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400">Datos Fiscales</h6>
                        <div class="space-y-4 text-sm text-slate-200">
                            <p class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-[0.3em] text-slate-500/80">Razón Social</span>
                                <span class="text-base font-semibold text-white">${escapeHtml(cliente.razon_social)}</span>
                            </p>
                            <p class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-[0.3em] text-slate-500/80">RFC</span>
                                <span class="font-mono text-sm">${cliente.rfc}</span>
                            </p>
                            <p class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-[0.3em] text-slate-500/80">Régimen Fiscal</span>
                                <span>${escapeHtml(cliente.regimen_fiscal || 'N/A')}</span>
                            </p>
                            <p class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-[0.3em] text-slate-500/80">Estatus</span>
                                <span class="${ESTATUS_STYLES[cliente.estatus] || ESTATUS_STYLES.activo}">${cliente.estatus.toUpperCase()}</span>
                            </p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h6 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400">Ubicación</h6>
                        <div class="space-y-4 text-sm text-slate-200">
                            <p class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-[0.3em] text-slate-500/80">Dirección</span>
                                <span>${escapeHtml(cliente.direccion || 'N/A')}</span>
                            </p>
                            <p class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-[0.3em] text-slate-500/80">País</span>
                                <span>${escapeHtml(cliente.pais)}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
            <section class="glass-panel">
                <div class="grid gap-5 md:grid-cols-2">
                    <div class="space-y-4">
                        <h6 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400">Contacto</h6>
                        <div class="space-y-4 text-sm text-slate-200">
                            <p class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-[0.3em] text-slate-500/80">Contacto Principal</span>
                                <span>${escapeHtml(cliente.contacto_principal || 'N/A')}</span>
                            </p>
                            <p class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-[0.3em] text-slate-500/80">Teléfono</span>
                                <span>${escapeHtml(cliente.telefono || 'N/A')}</span>
                            </p>
                            <p class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-[0.3em] text-slate-500/80">Correo</span>
                                <span>${escapeHtml(cliente.correo || 'N/A')}</span>
                            </p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h6 class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400">Condiciones Comerciales</h6>
                        <div class="space-y-4 text-sm text-slate-200">
                            <p class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-[0.3em] text-slate-500/80">Días de Crédito</span>
                                <span>${cliente.dias_credito} días</span>
                            </p>
                            <p class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-[0.3em] text-slate-500/80">Límite de Crédito</span>
                                <span>$${parseFloat(cliente.limite_credito).toFixed(2)} ${cliente.moneda}</span>
                            </p>
                            <p class="flex flex-col gap-1">
                                <span class="text-xs uppercase tracking-[0.3em] text-slate-500/80">Vendedor Asignado</span>
                                <span>${escapeHtml(cliente.vendedor_asignado || 'N/A')}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    `;
    
    const modalHTML = `
        <div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content overflow-hidden border border-white/10 bg-slate-950/90 text-slate-100 shadow-[0_45px_120px_-35px_rgba(59,130,246,0.55)]">
                    <div class="modal-header border-b border-white/10 bg-gradient-to-r from-corporate-600 via-corporate-500 to-sky-500 text-white">
                        <h5 class="modal-title text-lg font-semibold tracking-tight">Detalle del Cliente</h5>
                        <button type="button" class="btn-close invert" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body px-6 py-6">
                        ${detalleHTML}
                    </div>
                    <div class="modal-footer border-t border-white/10 bg-slate-950/80 px-6 py-4">
                        <button type="button" class="rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-sm font-semibold uppercase tracking-[0.28em] text-slate-200 transition duration-300 hover:-translate-y-0.5 hover:border-white/30 hover:bg-white/10" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="group inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-corporate-500 via-sky-500 to-emerald-500 px-6 py-3 text-sm font-semibold uppercase tracking-[0.28em] text-white shadow-lg shadow-emerald-500/25 transition duration-300 hover:scale-[1.02] hover:from-corporate-400 hover:via-sky-400 hover:to-emerald-400" onclick="exportarPDF(${cliente.id})">
                            <i class="fas fa-file-pdf"></i> Exportar PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const existingModal = document.getElementById('modalDetalle');
    if (existingModal) {
        existingModal.remove();
    }
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
    modal.show();
}

function exportarCSV() {
    const params = new URLSearchParams(filtrosActuales);
    window.location.href = `app/controllers/export_csv.php?${params}`;
}

function exportarPDF(id) {
    window.open(`app/controllers/export_pdf.php?id=${id}`, '_blank');
}

function actualizarIconosOrden() {
    document.querySelectorAll('.sortable').forEach(th => {
        th.classList.remove('asc', 'desc');
        if (th.dataset.column === ordenActual) {
            th.classList.add(direccionActual.toLowerCase());
        }
    });
}

function mostrarErroresFormulario(errores) {
    const divErrores = document.getElementById('erroresCliente');
    divErrores.innerHTML = '<ul class="space-y-1">' + errores.map(e => `<li class="flex items-start gap-2 text-sm"><i class="fas fa-circle-exclamation mt-0.5 text-rose-300"></i><span>${escapeHtml(e)}</span></li>`).join('') + '</ul>';
    divErrores.classList.remove('hidden');
    divErrores.classList.remove('animate-toast-out');
    divErrores.classList.add('animate-toast-in');
}

function ocultarErrores() {
    const divErrores = document.getElementById('erroresCliente');
    divErrores.classList.add('hidden');
    divErrores.classList.remove('animate-toast-in');
    divErrores.innerHTML = '';
}

function mostrarError(mensaje) {
    mostrarToast(mensaje, 'error');
}

function mostrarExito(mensaje) {
    mostrarToast(mensaje, 'success');
}

function mostrarToast(mensaje, tipo) {
    const palettes = {
        success: {
            container: 'border-emerald-500/30 bg-slate-900/95 text-emerald-100 ring-1 ring-emerald-500/25 shadow-[0_35px_90px_-35px_rgba(16,185,129,0.6)]',
            iconWrapper: 'flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-500/20 text-xl text-emerald-300',
            icon: 'fa-check'
        },
        error: {
            container: 'border-rose-500/40 bg-slate-900/95 text-rose-100 ring-1 ring-rose-500/25 shadow-[0_35px_90px_-35px_rgba(244,63,94,0.6)]',
            iconWrapper: 'flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-500/20 text-xl text-rose-200',
            icon: 'fa-triangle-exclamation'
        }
    };
    const palette = palettes[tipo] || palettes.success;
    
    const wrapper = document.createElement('div');
    wrapper.className = 'fixed inset-x-0 top-6 z-[9999] flex justify-center px-4 sm:px-6';
    wrapper.innerHTML = `
        <div class="toast-card flex max-w-md items-center gap-4 rounded-3xl border px-5 py-4 text-sm font-medium leading-relaxed ${palette.container} animate-toast-in">
            <span class="${palette.iconWrapper}">
                <i class="fas ${palette.icon}"></i>
            </span>
            <span class="flex-1">${escapeHtml(mensaje)}</span>
        </div>
    `;
    
    document.body.appendChild(wrapper);
    const toast = wrapper.querySelector('.toast-card');
    
    setTimeout(() => {
        toast.classList.remove('animate-toast-in');
        toast.classList.add('animate-toast-out');
    }, 2600);
    
    setTimeout(() => {
        wrapper.remove();
    }, 3200);
}

function buildErrorRow(mensaje) {
    return `
        <tr>
            <td colspan="9" class="px-6 py-14">
                <div class="flex flex-col items-center gap-4 text-center text-rose-200/90">
                    <span class="flex h-16 w-16 items-center justify-center rounded-3xl border border-rose-400/40 bg-rose-500/10 text-2xl">
                        <i class="fas fa-triangle-exclamation"></i>
                    </span>
                    <p class="text-sm font-semibold uppercase tracking-[0.35em]">${escapeHtml(mensaje)}</p>
                </div>
            </td>
        </tr>
    `;
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
