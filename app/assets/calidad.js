// ==================== VARIABLES GLOBALES ====================
let defectosDisponibles = [];
let paginaActual = 1;
const limitePorPagina = 20;

// ==================== INICIALIZACIÓN ====================
document.addEventListener('DOMContentLoaded', function() {
    cargarFiltrosYPiezas();
    configurarEventos();
    cargarDefectos();
});

// ==================== CONFIGURACIÓN DE EVENTOS ====================
function configurarEventos() {
    // Tabs
    document.querySelectorAll('.nav-tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            cambiarTab(this.dataset.tab);
        });
    });

    // Filtros
    document.getElementById('btn_buscar').addEventListener('click', () => {
        paginaActual = 1;
        cargarPiezas();
    });

    document.getElementById('btn_limpiar').addEventListener('click', limpiarFiltros);

    // Inspección
    document.getElementById('form_inspeccion').addEventListener('submit', registrarInspeccion);

    // Validación de cantidades
    document.getElementById('cantidad_inspeccionada').addEventListener('change', validarCantidades);
    document.getElementById('cantidad_aceptada').addEventListener('change', validarCantidades);
    document.getElementById('cantidad_rechazada').addEventListener('change', validarCantidades);
}

// ==================== NAVEGACIÓN DE TABS ====================
function cambiarTab(tabName) {
    // Ocultar todos los tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });

    // Desactivar botones
    document.querySelectorAll('.nav-tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Mostrar tab seleccionado
    document.getElementById(tabName).classList.add('active');
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

    // Si es estadísticas, cargar datos
    if (tabName === 'estadisticas-calidad') {
        cargarEstadisticas();
    }
}

// ==================== CARGA DE DATOS INICIALES ====================
async function cargarFiltrosYPiezas() {
    try {
        const response = await fetch('app/controllers/calidad_listar.php?pagina=1&limite=' + limitePorPagina);
        const data = await response.json();

        if (!data.exito) {
            mostrarError('Error al cargar datos: ' + data.error);
            return;
        }

        // Cargar opciones de filtros
        if (data.filtros) {
            cargarOpcionesSelect('filtro_item', data.filtros.items, 'item_code');
            cargarOpcionesSelect('filtro_supervisor', data.filtros.supervisores, 'supervisor');
            cargarOpcionesSelect('filtro_cliente', data.filtros.clientes, 'id', 'razon_social');
        }

        // Cargar piezas
        mostrarPiezas(data.piezas);
        actualizarPaginacion(data.total, data.pagina, data.paginas_totales);
        actualizarContador(data.total);

    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

async function cargarPiezas(pagina = 1) {
    try {
        const filtros = obtenerFiltros();
        const params = new URLSearchParams({
            pagina: pagina,
            limite: limitePorPagina,
            ...filtros
        });

        const response = await fetch('app/controllers/calidad_listar.php?' + params);
        const data = await response.json();

        if (!data.exito) {
            mostrarError('Error al cargar piezas: ' + data.error);
            return;
        }

        mostrarPiezas(data.piezas);
        actualizarPaginacion(data.total, data.pagina, data.paginas_totales);
        actualizarContador(data.total);
        paginaActual = pagina;

    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

async function cargarDefectos() {
    try {
        const response = await fetch('app/controllers/calidad_obtener_defectos.php');
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

async function cargarEstadisticas() {
    try {
        const filtros = obtenerFiltros();
        const params = new URLSearchParams(filtros);

        const response = await fetch('app/controllers/calidad_listar.php?' + params);
        const data = await response.json();

        if (!data.exito) {
            console.error('Error al cargar estadísticas');
            return;
        }

        // Calcular estadísticas
        let pendientes = 0, liberadas = 0, rechazadas = 0;

        data.piezas.forEach(pieza => {
            if (pieza.estatus === 'por_inspeccionar' || pieza.estatus === 'pendiente_reinspeccion') {
                pendientes++;
            } else if (pieza.estatus === 'liberada') {
                liberadas++;
            } else if (pieza.estatus === 'rechazada') {
                rechazadas++;
            }
        });

        const total = liberadas + rechazadas;
        const porcentaje = total > 0 ? ((liberadas / total) * 100).toFixed(2) : 0;

        document.getElementById('stat_pendientes').textContent = pendientes;
        document.getElementById('stat_liberadas').textContent = liberadas;
        document.getElementById('stat_rechazadas').textContent = rechazadas;
        document.getElementById('stat_porcentaje').textContent = porcentaje + '%';

    } catch (error) {
        console.error('Error al cargar estadísticas: ' + error.message);
    }
}

// ==================== FUNCIONES AUXILIARES ====================
function obtenerFiltros() {
    return {
        buscar: document.getElementById('filtro_buscar').value,
        fecha: document.getElementById('filtro_fecha').value,
        item_code: document.getElementById('filtro_item').value,
        supervisor: document.getElementById('filtro_supervisor').value,
        cliente_id: document.getElementById('filtro_cliente').value
    };
}

function limpiarFiltros() {
    document.getElementById('filtro_buscar').value = '';
    document.getElementById('filtro_fecha').value = '';
    document.getElementById('filtro_item').value = '';
    document.getElementById('filtro_supervisor').value = '';
    document.getElementById('filtro_cliente').value = '';
    paginaActual = 1;
    cargarPiezas();
}

function cargarOpcionesSelect(selectId, datos, campo, campoTexto = null) {
    const select = document.getElementById(selectId);
    if (!select) return;

    datos.forEach(item => {
        const option = document.createElement('option');
        option.value = item[campo];
        option.textContent = campoTexto ? item[campoTexto] : item[campo];
        select.appendChild(option);
    });
}

function mostrarPiezas(piezas) {
    const contenedor = document.getElementById('piezas_agrupadas');

    if (piezas.length === 0) {
        contenedor.innerHTML = `
            <div class="alert alert-info text-center">
                <i class="fas fa-box-open fa-2x mb-2"></i>
                <p class="mb-0">No hay piezas por inspeccionar</p>
            </div>
        `;
        return;
    }

    // Agrupar piezas por número de pedido
    const piezasPorPedido = {};
    piezas.forEach(pieza => {
        const pedido = pieza.numero_pedido;
        if (!piezasPorPedido[pedido]) {
            piezasPorPedido[pedido] = {
                numero_pedido: pedido,
                cliente: pieza.cliente || 'N/A',
                item_code: pieza.item_code,
                piezas: []
            };
        }
        piezasPorPedido[pedido].piezas.push(pieza);
    });

    // Generar HTML agrupado
    let html = '';
    Object.values(piezasPorPedido).forEach(grupo => {
        const totalPiezas = grupo.piezas.length;
        
        html += `
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-header bg-gradient-to-r from-blue-50 to-indigo-50 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">
                                <i class="fas fa-box text-primary me-2"></i>
                                <strong>Pedido: ${grupo.numero_pedido}</strong>
                            </h6>
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>${grupo.cliente} | 
                                <i class="fas fa-tag me-1"></i>${grupo.item_code}
                            </small>
                        </div>
                        <span class="badge bg-primary">${totalPiezas} pieza${totalPiezas !== 1 ? 's' : ''}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Folio de Pieza</th>
                                    <th>Fecha Producción</th>
                                    <th>Supervisor</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
        `;

        grupo.piezas.forEach(pieza => {
            html += `
                <tr>
                    <td><strong class="text-primary">${pieza.folio_pieza}</strong></td>
                    <td>${formatearFecha(pieza.fecha_produccion)}</td>
                    <td>${pieza.supervisor_produccion}</td>
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

        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    });

    contenedor.innerHTML = html;
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

function actualizarContador(total) {
    document.getElementById('contador_piezas').textContent = `Total: ${total} pieza(s)`;
}

function actualizarPaginacion(total, pagina, paginas) {
    const contenedor = document.getElementById('paginacion_piezas');
    contenedor.innerHTML = '';

    if (paginas <= 1) return;

    // Botón Anterior
    if (pagina > 1) {
        const btnAnterior = document.createElement('button');
        btnAnterior.className = 'btn btn-sm btn-outline-primary';
        btnAnterior.innerHTML = '<i class="fas fa-chevron-left"></i> Anterior';
        btnAnterior.onclick = () => cargarPiezas(pagina - 1);
        contenedor.appendChild(btnAnterior);
    }

    // Números de página
    for (let i = 1; i <= paginas; i++) {
        if (i >= pagina - 1 && i <= pagina + 1) {
            const btnPagina = document.createElement('button');
            btnPagina.className = `btn btn-sm ${i === pagina ? 'btn-primary' : 'btn-outline-primary'}`;
            btnPagina.textContent = i;
            btnPagina.onclick = () => cargarPiezas(i);
            contenedor.appendChild(btnPagina);
        }
    }

    // Botón Siguiente
    if (pagina < paginas) {
        const btnSiguiente = document.createElement('button');
        btnSiguiente.className = 'btn btn-sm btn-outline-primary';
        btnSiguiente.innerHTML = 'Siguiente <i class="fas fa-chevron-right"></i>';
        btnSiguiente.onclick = () => cargarPiezas(pagina + 1);
        contenedor.appendChild(btnSiguiente);
    }
}

// ==================== MODAL DE INSPECCIÓN ====================
async function abrirModalInspeccion(folioPieza) {
    try {
        const response = await fetch('app/controllers/calidad_obtener.php?folio=' + encodeURIComponent(folioPieza));
        const data = await response.json();

        if (!data.exito) {
            mostrarError('Error al obtener pieza: ' + data.error);
            return;
        }

        const pieza = data.pieza;
        const inspecciones = data.inspecciones || [];

        // Llenar datos de la pieza
        document.getElementById('modal_folio').textContent = pieza.folio_pieza;
        document.getElementById('modal_pedido').textContent = pieza.numero_pedido;
        document.getElementById('modal_item').textContent = pieza.item_code;
        document.getElementById('modal_fecha').textContent = formatearFecha(pieza.fecha_produccion);
        document.getElementById('modal_supervisor').textContent = pieza.supervisor_produccion;
        document.getElementById('modal_cliente').textContent = pieza.cliente || 'N/A';

        // Llenar folio en el input
        document.getElementById('folio_pieza_input').value = folioPieza;

        // Mostrar inspecciones previas
        mostrarInspeccionesPrevias(inspecciones);

        // Llenar lista de defectos
        llenarListaDefectos();

        // Limpiar formulario
        document.getElementById('form_inspeccion').reset();
        document.getElementById('alerta_validacion').style.display = 'none';

        // Mostrar modal correctamente
        const modalElement = document.getElementById('modal_inspeccionar');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();

    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

// Exponer función globalmente para que pueda ser llamada desde onclick
window.abrirModalInspeccion = abrirModalInspeccion;

function mostrarInspeccionesPrevias(inspecciones) {
    const contenedor = document.getElementById('modal_inspecciones_previas');

    if (inspecciones.length === 0) {
        contenedor.innerHTML = '<p class="text-muted">Sin inspecciones previas</p>';
        return;
    }

    contenedor.innerHTML = inspecciones.map(insp => `
        <div class="mb-2 p-2 border rounded bg-white">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">Inspección del:</small><br>
                    <strong>${formatearFecha(insp.fecha_inspeccion)}</strong>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">Inspector:</small><br>
                    <strong>${insp.inspector_calidad}</strong>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <small>Inspeccionada: <strong>${insp.cantidad_inspeccionada}</strong></small>
                </div>
                <div class="col-md-4">
                    <small>Aceptada: <strong class="text-success">${insp.cantidad_aceptada}</strong></small>
                </div>
                <div class="col-md-4">
                    <small>Rechazada: <strong class="text-danger">${insp.cantidad_rechazada}</strong></small>
                </div>
            </div>
            ${insp.observaciones ? `<p class="mt-2 mb-0"><small><em>${insp.observaciones}</em></small></p>` : ''}
        </div>
    `).join('');
}

function llenarListaDefectos() {
    const contenedor = document.getElementById('lista_defectos');

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

// ==================== VALIDACIÓN DE CANTIDADES ====================
function validarCantidades() {
    const inspeccionada = parseFloat(document.getElementById('cantidad_inspeccionada').value) || 0;
    const aceptada = parseFloat(document.getElementById('cantidad_aceptada').value) || 0;
    const rechazada = parseFloat(document.getElementById('cantidad_rechazada').value) || 0;

    const suma = aceptada + rechazada;
    const alerta = document.getElementById('alerta_validacion');
    const msgAlerta = document.getElementById('msg_validacion');

    if (inspeccionada > 0 && suma > 0) {
        const diferencia = Math.abs(suma - inspeccionada);

        if (diferencia > 0.01) {
            alerta.style.display = 'block';
            alerta.className = 'alert alert-warning';
            msgAlerta.innerHTML = `
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Advertencia:</strong> Aceptada (${aceptada}) + Rechazada (${rechazada}) = ${suma.toFixed(2)}
                pero la Inspeccionada es ${inspeccionada}
            `;
        } else {
            alerta.style.display = 'block';
            alerta.className = 'alert alert-success';
            msgAlerta.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <strong>Correcto:</strong> Las cantidades coinciden ✓
            `;
        }
    } else {
        alerta.style.display = 'none';
    }
}

// ==================== REGISTRO DE INSPECCIÓN ====================
async function registrarInspeccion(e) {
    e.preventDefault();

    const folio = document.getElementById('folio_pieza_input').value;
    const inspector = document.getElementById('inspector_calidad').value;
    const inspeccionada = parseFloat(document.getElementById('cantidad_inspeccionada').value) || 0;
    const aceptada = parseFloat(document.getElementById('cantidad_aceptada').value) || 0;
    const rechazada = parseFloat(document.getElementById('cantidad_rechazada').value) || 0;
    const observaciones = document.getElementById('observaciones').value;

    // Validar que aceptada + rechazada = inspeccionada
    const suma = aceptada + rechazada;
    if (Math.abs(suma - inspeccionada) > 0.01) {
        mostrarError('La suma de aceptadas y rechazadas debe ser igual a inspeccionadas');
        return;
    }

    // Obtener defectos seleccionados
    const defectos = {};
    document.querySelectorAll('.defecto-checkbox:checked').forEach(checkbox => {
        const defectoId = checkbox.dataset.defectoId;
        const cantidadInput = document.querySelector(`.defecto-cantidad[data-defecto-id="${defectoId}"]`);
        const cantidad = parseInt(cantidadInput.value) || 0;
        if (cantidad > 0) {
            defectos[defectoId] = cantidad;
        }
    });

    const datos = {
        folio_pieza: folio,
        inspector_calidad: inspector,
        cantidad_inspeccionada: inspeccionada,
        cantidad_aceptada: aceptada,
        cantidad_rechazada: rechazada,
        observaciones: observaciones,
        defectos: defectos
    };

    try {
        const response = await fetch('app/controllers/calidad_inspeccionar.php', {
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
        bootstrap.Modal.getInstance(document.getElementById('modal_inspeccionar')).hide();
        cargarPiezas(paginaActual);

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
