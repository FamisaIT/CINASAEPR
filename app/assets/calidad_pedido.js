// ==================== VARIABLES GLOBALES ====================
let defectosDisponibles = [];
let paginaActual = 1;
const limitePorPagina = 20;
let numeroPedidoActual = null;

// Obtener número de pedido de la URL
function obtenerNumeroPedidoDelURL() {
    const params = new URLSearchParams(window.location.search);
    return params.get('pedido');
}

// ==================== INICIALIZACIÓN ====================
document.addEventListener('DOMContentLoaded', function() {
    numeroPedidoActual = obtenerNumeroPedidoDelURL();
    if (!numeroPedidoActual) {
        alert('Error: Número de pedido no especificado');
        window.location.href = BASE_URL + '/calidad.php';
        return;
    }

    cargarPiezasPedido();
    configurarEventos();
    cargarDefectos();
    actualizarHeaderPedido();
});

// ==================== CONFIGURACIÓN DE EVENTOS ====================
function configurarEventos() {
    document.getElementById('btn_buscar_pedido').addEventListener('click', () => {
        paginaActual = 1;
        cargarPiezasPedido();
    });

    document.getElementById('btn_limpiar_pedido').addEventListener('click', limpiarFiltrosPedido);

    // Nota: El form_inspeccion_pedido se configura dinámicamente cuando se abre el modal
}


// ==================== CARGAR DATOS ====================
async function actualizarHeaderPedido() {
    try {
        const response = await fetch(`${BASE_URL}/app/controllers/calidad_piezas_por_pedido.php?numero_pedido=${encodeURIComponent(numeroPedidoActual)}&limite=1`);
        const data = await response.json();

        if (data.exito && data.pedido) {
            document.getElementById('numero_pedido_display').textContent = data.pedido.numero_pedido;
            document.getElementById('cliente_display').textContent = `Cliente: ${data.pedido.cliente || 'N/A'}`;
        }
    } catch (error) {
        console.error('Error al actualizar header:', error);
    }
}

async function cargarPiezasPedido(pagina = 1) {
    try {
        const filtros = obtenerFiltrosPedido();
        const params = new URLSearchParams({
            numero_pedido: numeroPedidoActual,
            pagina: pagina,
            limite: limitePorPagina,
            ...filtros
        });

        const response = await fetch(`${BASE_URL}/app/controllers/calidad_piezas_por_pedido.php?${params}`);
        const data = await response.json();

        if (!data.exito) {
            mostrarError('Error al cargar piezas: ' + data.error);
            return;
        }

        mostrarPiezasPedido(data.piezas);
        actualizarPaginacionPedido(data.total, data.pagina, data.paginas_totales);
        actualizarContadorPedido(data.total);
        cargarOpcionesSelectPedido(data.filtros);
        paginaActual = pagina;

    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

async function cargarDefectos() {
    try {
        const response = await fetch(`${BASE_URL}/app/controllers/calidad_obtener_defectos.php`);
        const data = await response.json();

        if (!data.exito) {
            console.error('Error al cargar defectos: ' + data.error);
            return;
        }

        defectosDisponibles = data.defectos;

    } catch (error) {
        console.error('Error al cargar defectos: ' + error.message);
    }
}

// ==================== FUNCIONES AUXILIARES ====================
function obtenerFiltrosPedido() {
    return {
        buscar: document.getElementById('filtro_buscar_pedido').value,
        item_code: document.getElementById('filtro_item_pedido').value,
        supervisor: document.getElementById('filtro_supervisor_pedido').value
    };
}

function limpiarFiltrosPedido() {
    document.getElementById('filtro_buscar_pedido').value = '';
    document.getElementById('filtro_item_pedido').value = '';
    document.getElementById('filtro_supervisor_pedido').value = '';
    paginaActual = 1;
    cargarPiezasPedido();
}

function cargarOpcionesSelectPedido(filtros) {
    const selectItem = document.getElementById('filtro_item_pedido');
    const selectSupervisor = document.getElementById('filtro_supervisor_pedido');

    // Cargar items del pedido
    if (filtros && filtros.items && filtros.items.length > 0) {
        // Solo agregar si no existen ya
        if (selectItem.children.length === 1) {
            filtros.items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.item_code;
                option.textContent = item.item_code;
                selectItem.appendChild(option);
            });
        }
    }

    // Cargar supervisores
    if (filtros && filtros.supervisores && filtros.supervisores.length > 0) {
        if (selectSupervisor.children.length === 1) {
            filtros.supervisores.forEach(sup => {
                const option = document.createElement('option');
                option.value = sup.supervisor_produccion;
                option.textContent = sup.supervisor_produccion;
                selectSupervisor.appendChild(option);
            });
        }
    }
}

function mostrarPiezasPedido(piezas) {
    const tbody = document.getElementById('tabla_piezas_pedido');

    if (piezas.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                    <p class="text-muted">No hay piezas por inspeccionar</p>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    piezas.forEach(pieza => {
        html += `
            <tr>
                <td><strong>${pieza.folio_pieza}</strong></td>
                <td>${pieza.item_code}</td>
                <td>${pieza.descripcion || 'N/A'}</td>
                <td>${pieza.supervisor_produccion || 'N/A'}</td>
                <td>${formatearFecha(pieza.fecha_produccion)}</td>
                <td>
                    <span class="badge bg-${obtenerColorEstatus(pieza.estatus)}">
                        ${obtenerTextoEstatus(pieza.estatus)}
                    </span>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-success" onclick="window.abrirModalInspeccion('${pieza.folio_pieza}')" title="Inspeccionar pieza">
                        <i class="fas fa-check-circle"></i> Inspeccionar
                    </button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}

function obtenerColorEstatus(estatus) {
    const colores = {
        'por_inspeccionar': 'warning',
        'pendiente_reinspeccion': 'info',
        'liberada': 'success',
        'rechazada': 'danger'
    };
    return colores[estatus] || 'secondary';
}

function obtenerTextoEstatus(estatus) {
    const textos = {
        'por_inspeccionar': 'Por Inspeccionar',
        'pendiente_reinspeccion': 'Reinspección Pendiente',
        'liberada': 'Liberada',
        'rechazada': 'Rechazada'
    };
    return textos[estatus] || estatus;
}

function actualizarContadorPedido(total) {
    document.getElementById('contador_piezas_pedido').textContent = `Total: ${total} pieza(s)`;
    document.getElementById('total_piezas').textContent = total;
}

function actualizarPaginacionPedido(total, pagina, paginas) {
    const contenedor = document.getElementById('paginacion_piezas_pedido');
    contenedor.innerHTML = '';

    if (paginas <= 1) return;

    // Botón Anterior
    if (pagina > 1) {
        const btnAnterior = document.createElement('button');
        btnAnterior.className = 'btn btn-sm btn-outline-primary';
        btnAnterior.innerHTML = '<i class="fas fa-chevron-left"></i> Anterior';
        btnAnterior.onclick = () => cargarPiezasPedido(pagina - 1);
        contenedor.appendChild(btnAnterior);
    }

    // Números de página
    for (let i = 1; i <= paginas; i++) {
        if (i >= pagina - 1 && i <= pagina + 1) {
            const btnPagina = document.createElement('button');
            btnPagina.className = `btn btn-sm ${i === pagina ? 'btn-primary' : 'btn-outline-primary'}`;
            btnPagina.textContent = i;
            btnPagina.onclick = () => cargarPiezasPedido(i);
            contenedor.appendChild(btnPagina);
        }
    }

    // Botón Siguiente
    if (pagina < paginas) {
        const btnSiguiente = document.createElement('button');
        btnSiguiente.className = 'btn btn-sm btn-outline-primary';
        btnSiguiente.innerHTML = 'Siguiente <i class="fas fa-chevron-right"></i>';
        btnSiguiente.onclick = () => cargarPiezasPedido(pagina + 1);
        contenedor.appendChild(btnSiguiente);
    }
}

// ==================== MODAL DE INSPECCIÓN ====================
async function abrirModalInspeccion(folioPieza) {
    try {
        console.log('🔍 Abriendo modal para pieza:', folioPieza);

        // Verificar que Bootstrap está disponible
        if (typeof bootstrap === 'undefined') {
            throw new Error('Bootstrap no está disponible');
        }

        // Fetch de datos
        console.log('📡 Obteniendo datos de la pieza...');
        const response = await fetch(`${BASE_URL}/app/controllers/calidad_obtener.php?folio=${encodeURIComponent(folioPieza)}`);
        const data = await response.json();

        if (!data.exito) {
            mostrarError('Error al obtener pieza: ' + data.error);
            return;
        }

        console.log('✅ Datos obtenidos:', data);

        const pieza = data.pieza;
        const inspecciones = data.inspecciones || [];

        // Crear HTML del modal dinámicamente
        const inspeccionesPreviasHTML = generarHTMLInspeccionesPrevias(inspecciones);
        const defectosHTML = generarHTMLDefectos();

        const modalHTML = `
            <div class="modal fade" id="modal_inspeccionar_pedido" tabindex="-1" aria-labelledby="modalInspeccionarLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="modalInspeccionarLabel">
                                <i class="fas fa-clipboard-check"></i> Registrar Inspección
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Información de Pieza -->
                            <div class="card mb-4 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">Información de Pieza</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <strong>Folio:</strong> <span id="modal_folio_pedido">${pieza.folio_pieza}</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Pedido:</strong> <span id="modal_pedido_pedido">${pieza.numero_pedido}</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Código de Ítem:</strong> <span id="modal_item_pedido">${pieza.item_code}</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Fecha Producción:</strong> <span id="modal_fecha_pedido">${formatearFecha(pieza.fecha_produccion)}</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Supervisor:</strong> <span id="modal_supervisor_pedido">${pieza.supervisor_produccion}</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Cliente:</strong> <span id="modal_cliente_pedido">${pieza.cliente || 'N/A'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Inspecciones Previas -->
                            <div class="card mb-4 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">Inspecciones Previas</h6>
                                    <div id="modal_inspecciones_previas_pedido">
                                        ${inspeccionesPreviasHTML}
                                    </div>
                                </div>
                            </div>

                            <!-- Formulario de Inspección -->
                            <form id="form_inspeccion_pedido">
                                <input type="hidden" id="folio_pieza_input_pedido" value="${folioPieza}">

                                <div class="mb-3">
                                    <label class="form-label">Inspector de Calidad</label>
                                    <input type="text" class="form-control" id="inspector_calidad_pedido" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Resultado de la Inspección</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="resultado_inspeccion" id="resultado_aprobado" value="aprobado" required>
                                            <label class="form-check-label text-success fw-bold" for="resultado_aprobado">
                                                <i class="fas fa-check-circle"></i> Aprobado
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="resultado_inspeccion" id="resultado_rechazado" value="rechazado">
                                            <label class="form-check-label text-danger fw-bold" for="resultado_rechazado">
                                                <i class="fas fa-times-circle"></i> Rechazado
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección de Defectos (solo visible si se rechaza) -->
                                <div class="card mb-3" id="seccion_defectos" style="display: none;">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3 text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Defectos Encontrados
                                        </h6>
                                        <div id="lista_defectos_pedido">
                                            ${defectosHTML}
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Observaciones</label>
                                    <textarea class="form-control" id="observaciones_pedido" rows="3" placeholder="Observaciones adicionales..."></textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success flex-grow-1">
                                        <i class="fas fa-check-circle"></i> Registrar Inspección
                                    </button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remover modal existente si lo hay
        const existingModal = document.getElementById('modal_inspeccionar_pedido');
        if (existingModal) {
            existingModal.remove();
        }

        // Insertar modal en el DOM
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // Configurar eventos del formulario después de insertarlo
        const form = document.getElementById('form_inspeccion_pedido');
        form.addEventListener('submit', registrarInspeccion);

        // Configurar eventos para mostrar/ocultar defectos según el resultado
        document.getElementById('resultado_aprobado').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('seccion_defectos').style.display = 'none';
                // Limpiar defectos seleccionados
                document.querySelectorAll('.defecto-checkbox:checked').forEach(cb => {
                    cb.checked = false;
                    const cantidadInput = document.querySelector(`.defecto-cantidad[data-defecto-id="${cb.dataset.defectoId}"]`);
                    if (cantidadInput) {
                        cantidadInput.value = '';
                        cantidadInput.disabled = true;
                    }
                });
            }
        });

        document.getElementById('resultado_rechazado').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('seccion_defectos').style.display = 'block';
            }
        });

        // Configurar eventos de defectos
        configurarEventosDefectos();

        // Mostrar modal
        const modalElement = document.getElementById('modal_inspeccionar_pedido');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();

        console.log('✅ Modal mostrado correctamente');

    } catch (error) {
        console.error('❌ Error al abrir modal:', error);
        mostrarError('Error: ' + error.message);
    }
}

// Exponer función globalmente
window.abrirModalInspeccion = abrirModalInspeccion;

function generarHTMLInspeccionesPrevias(inspecciones) {
    if (inspecciones.length === 0) {
        return '<p class="text-muted">Sin inspecciones previas</p>';
    }

    return inspecciones.map(insp => {
        const esAprobada = insp.cantidad_aceptada > 0;
        const badgeClass = esAprobada ? 'bg-success' : 'bg-danger';
        const badgeIcon = esAprobada ? 'fa-check-circle' : 'fa-times-circle';
        const badgeText = esAprobada ? 'APROBADA' : 'RECHAZADA';
        
        return `
            <div class="mb-2 p-2 border rounded bg-white">
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted">Fecha:</small><br>
                        <strong>${formatearFecha(insp.fecha_inspeccion)}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Inspector:</small><br>
                        <strong>${insp.inspector_calidad}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Resultado:</small><br>
                        <span class="badge ${badgeClass}">
                            <i class="fas ${badgeIcon}"></i> ${badgeText}
                        </span>
                    </div>
                </div>
                ${insp.observaciones ? `<p class="mt-2 mb-0"><small><em>${insp.observaciones}</em></small></p>` : ''}
            </div>
        `;
    }).join('');
}

function generarHTMLDefectos() {
    if (defectosDisponibles.length === 0) {
        return '<p class="text-muted">No hay defectos disponibles</p>';
    }

    return defectosDisponibles.map(defecto => `
        <div class="form-check mb-2">
            <div class="input-group input-group-sm">
                <div class="input-group-text">
                    <input class="form-check-input defecto-checkbox" type="checkbox"
                           data-defecto-id="${defecto.id}" value="${defecto.id}">
                </div>
                <label class="form-control form-control-plaintext">
                    <strong>${defecto.nombre}</strong>
                    ${defecto.descripcion ? ` - ${defecto.descripcion}` : ''}
                </label>
                <input type="number" class="form-control defecto-cantidad"
                       data-defecto-id="${defecto.id}" min="1" step="1"
                       placeholder="Cant." style="max-width: 80px;" disabled>
            </div>
        </div>
    `).join('');
}

function configurarEventosDefectos() {
    document.querySelectorAll('.defecto-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const cantidadInput = document.querySelector(`.defecto-cantidad[data-defecto-id="${this.dataset.defectoId}"]`);
            cantidadInput.disabled = !this.checked;
            if (this.checked) {
                cantidadInput.value = '1';
                cantidadInput.focus();
            } else {
                cantidadInput.value = '';
            }
        });
    });
}

function mostrarInspeccionesPrevias(inspecciones) {
    // This function is no longer used, kept for backwards compatibility
    const contenedor = document.getElementById('modal_inspecciones_previas_pedido');
    if (!contenedor) return;

    if (inspecciones.length === 0) {
        contenedor.innerHTML = '<p class="text-muted">Sin inspecciones previas</p>';
        return;
    }

    contenedor.innerHTML = inspecciones.map(insp => {
        const esAprobada = insp.cantidad_aceptada > 0;
        const badgeClass = esAprobada ? 'bg-success' : 'bg-danger';
        const badgeIcon = esAprobada ? 'fa-check-circle' : 'fa-times-circle';
        const badgeText = esAprobada ? 'APROBADA' : 'RECHAZADA';
        
        return `
            <div class="mb-2 p-2 border rounded bg-white">
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted">Fecha:</small><br>
                        <strong>${formatearFecha(insp.fecha_inspeccion)}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Inspector:</small><br>
                        <strong>${insp.inspector_calidad}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Resultado:</small><br>
                        <span class="badge ${badgeClass}">
                            <i class="fas ${badgeIcon}"></i> ${badgeText}
                        </span>
                    </div>
                </div>
                ${insp.observaciones ? `<p class="mt-2 mb-0"><small><em>${insp.observaciones}</em></small></p>` : ''}
            </div>
        `;
    }).join('');
}

function llenarListaDefectos() {
    // This function is no longer used, kept for backwards compatibility
    const contenedor = document.getElementById('lista_defectos_pedido');
    if (!contenedor) return;

    if (defectosDisponibles.length === 0) {
        contenedor.innerHTML = '<p class="text-muted">No hay defectos disponibles</p>';
        return;
    }

    contenedor.innerHTML = defectosDisponibles.map(defecto => `
        <div class="form-check mb-2">
            <div class="input-group input-group-sm">
                <div class="input-group-text">
                    <input class="form-check-input defecto-checkbox" type="checkbox"
                           data-defecto-id="${defecto.id}" value="${defecto.id}">
                </div>
                <label class="form-control form-control-plaintext">
                    <strong>${defecto.nombre}</strong>
                    ${defecto.descripcion ? ` - ${defecto.descripcion}` : ''}
                </label>
                <input type="number" class="form-control defecto-cantidad"
                       data-defecto-id="${defecto.id}" min="1" step="1"
                       placeholder="Cant." style="max-width: 80px;" disabled>
            </div>
        </div>
    `).join('');

    // Agregar eventos a los checkboxes
    document.querySelectorAll('.defecto-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const cantidadInput = document.querySelector(`.defecto-cantidad[data-defecto-id="${this.dataset.defectoId}"]`);
            cantidadInput.disabled = !this.checked;
            if (this.checked) {
                cantidadInput.value = '1';
                cantidadInput.focus();
            } else {
                cantidadInput.value = '';
            }
        });
    });
}

// ==================== VALIDACIÓN (Legacy - No longer used) ====================
function validarCantidades() {
    // Esta función ya no se usa con el nuevo formato de aprobado/rechazado
    // Se mantiene por compatibilidad pero no tiene efecto
}

// ==================== REGISTRO DE INSPECCIÓN ====================
async function registrarInspeccion(e) {
    e.preventDefault();

    const folio = document.getElementById('folio_pieza_input_pedido').value;
    const inspector = document.getElementById('inspector_calidad_pedido').value;
    const observaciones = document.getElementById('observaciones_pedido').value;
    
    // Obtener resultado (aprobado/rechazado)
    const resultadoAprobado = document.getElementById('resultado_aprobado').checked;
    const resultadoRechazado = document.getElementById('resultado_rechazado').checked;

    if (!resultadoAprobado && !resultadoRechazado) {
        mostrarError('Debe seleccionar si la pieza fue aprobada o rechazada');
        return;
    }

    // Si está rechazada, validar que haya al menos un defecto seleccionado
    if (resultadoRechazado) {
        const defectosSeleccionados = document.querySelectorAll('.defecto-checkbox:checked');
        if (defectosSeleccionados.length === 0) {
            mostrarError('Debe seleccionar al menos un defecto para piezas rechazadas');
            return;
        }
    }

    // Obtener defectos seleccionados (solo si está rechazada)
    const defectos = {};
    if (resultadoRechazado) {
        document.querySelectorAll('.defecto-checkbox:checked').forEach(checkbox => {
            const defectoId = checkbox.dataset.defectoId;
            const cantidadInput = document.querySelector(`.defecto-cantidad[data-defecto-id="${defectoId}"]`);
            const cantidad = parseInt(cantidadInput.value) || 1;
            defectos[defectoId] = cantidad;
        });
    }

    // Para el backend: si es aprobada = cantidad_aceptada: 1, rechazada: 0
    //                  si es rechazada = cantidad_aceptada: 0, rechazada: 1
    const datos = {
        folio_pieza: folio,
        inspector_calidad: inspector,
        cantidad_inspeccionada: 1, // Siempre 1 pieza
        cantidad_aceptada: resultadoAprobado ? 1 : 0,
        cantidad_rechazada: resultadoRechazado ? 1 : 0,
        observaciones: observaciones,
        defectos: defectos
    };

    try {
        const response = await fetch(`${BASE_URL}/app/controllers/calidad_inspeccionar.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        });

        const result = await response.json();

        if (!result.exito) {
            mostrarError('Error al registrar inspección: ' + result.error);
            return;
        }

        mostrarExito('Inspección registrada correctamente');
        
        // Cerrar modal correctamente
        const modalElement = document.getElementById('modal_inspeccionar_pedido');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.hide();
        }
        
        // Recargar la lista de piezas
        cargarPiezasPedido(paginaActual);

    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

// ==================== FUNCIONES DE UTILIDAD ====================
function formatearFecha(fecha) {
    if (!fecha) return 'N/A';
    const date = new Date(fecha);
    return date.toLocaleDateString('es-MX', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
}

function mostrarError(mensaje) {
    alert('❌ ' + mensaje);
}

function mostrarExito(mensaje) {
    alert('✓ ' + mensaje);
}
