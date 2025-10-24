// ==================== VARIABLES GLOBALES ====================
let paginaActual = 1;
const limitePorPagina = 10;

// ==================== INICIALIZACIÓN ====================
document.addEventListener('DOMContentLoaded', function() {
    cargarPedidos();
    configurarEventos();
});

// ==================== CONFIGURACIÓN DE EVENTOS ====================
function configurarEventos() {
    document.getElementById('btn_buscar').addEventListener('click', () => {
        paginaActual = 1;
        cargarPedidos();
    });

    document.getElementById('btn_limpiar').addEventListener('click', limpiarFiltros);

    // Enter en el campo de búsqueda
    document.getElementById('filtro_buscar').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            paginaActual = 1;
            cargarPedidos();
        }
    });
}

// ==================== CARGAR DATOS ====================
async function cargarPedidos(pagina = paginaActual) {
    try {
        const filtros = obtenerFiltros();
        const params = new URLSearchParams({
            pagina: pagina,
            limite: limitePorPagina,
            ...filtros
        });

        const response = await fetch(`${BASE_URL}/app/controllers/tracking_piezas_controller.php?${params}`);
        const data = await response.json();

        if (!data.exito) {
            mostrarError('Error al cargar pedidos: ' + data.error);
            return;
        }

        mostrarPedidos(data.pedidos);
        actualizarPaginacion(data.total, data.pagina, data.paginas_totales);
        actualizarContador(data.total);
        paginaActual = pagina;

    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

function obtenerFiltros() {
    return {
        buscar: document.getElementById('filtro_buscar').value,
        estatus: document.getElementById('filtro_estatus').value
    };
}

function limpiarFiltros() {
    document.getElementById('filtro_buscar').value = '';
    document.getElementById('filtro_estatus').value = '';
    paginaActual = 1;
    cargarPedidos();
}

// ==================== MOSTRAR DATOS ====================
function mostrarPedidos(pedidos) {
    const contenedor = document.getElementById('contenedor_pedidos');

    if (pedidos.length === 0) {
        contenedor.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fas fa-inbox me-2"></i>
                    No se encontraron pedidos con piezas producidas
                </td>
            </tr>
        `;
        return;
    }

    let html = '';

    pedidos.forEach(pedido => {
        const porcentaje = parseFloat(pedido.porcentaje_aprobacion) || 0;
        const totalPiezas = parseInt(pedido.total_piezas) || 0;

        html += `
            <tr onclick="verDetallePedido('${escapeHtml(pedido.numero_pedido)}')" style="cursor: pointer;">
                <td>
                    <strong>${escapeHtml(pedido.numero_pedido)}</strong>
                </td>
                <td>${escapeHtml(pedido.cliente_nombre || 'N/A')}</td>
                <td>
                    <span class="badge bg-info">${totalPiezas}</span>
                </td>
                <td>
                    <span class="badge bg-warning text-dark">${pedido.piezas_por_inspeccionar}</span>
                </td>
                <td>
                    <span class="badge bg-success">${pedido.piezas_liberadas}</span>
                </td>
                <td>
                    <span class="badge bg-danger">${pedido.piezas_rechazadas}</span>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress" style="min-width: 60px; height: 20px;">
                            <div class="progress-bar" style="width: ${porcentaje}%"></div>
                        </div>
                        <small>${porcentaje}%</small>
                    </div>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-primary" onclick="verDetallePedido('${escapeHtml(pedido.numero_pedido)}'); event.stopPropagation();" title="Ver detalle">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    contenedor.innerHTML = html;
}

function verDetallePedido(numeroPedido) {
    window.location.href = `${BASE_URL}/tracking_piezas_detalle.php?pedido=${encodeURIComponent(numeroPedido)}`;
}

// ==================== PAGINACIÓN ====================
function actualizarPaginacion(total, pagina, paginas) {
    const contenedor = document.getElementById('paginacion');
    contenedor.innerHTML = '';

    if (paginas <= 1) return;

    // Botón Anterior
    if (pagina > 1) {
        const btnAnterior = document.createElement('button');
        btnAnterior.className = 'btn btn-sm btn-outline-primary';
        btnAnterior.innerHTML = '<i class="fas fa-chevron-left"></i> Anterior';
        btnAnterior.onclick = () => cargarPedidos(pagina - 1);
        contenedor.appendChild(btnAnterior);
    }

    // Números de página (mostrar solo algunos)
    const rango = 2;
    for (let i = Math.max(1, pagina - rango); i <= Math.min(paginas, pagina + rango); i++) {
        const btnPagina = document.createElement('button');
        btnPagina.className = `btn btn-sm ${i === pagina ? 'btn-primary' : 'btn-outline-primary'}`;
        btnPagina.textContent = i;
        btnPagina.onclick = () => cargarPedidos(i);
        contenedor.appendChild(btnPagina);
    }

    // Botón Siguiente
    if (pagina < paginas) {
        const btnSiguiente = document.createElement('button');
        btnSiguiente.className = 'btn btn-sm btn-outline-primary';
        btnSiguiente.innerHTML = 'Siguiente <i class="fas fa-chevron-right"></i>';
        btnSiguiente.onclick = () => cargarPedidos(pagina + 1);
        contenedor.appendChild(btnSiguiente);
    }
}

function actualizarContador(total) {
    document.getElementById('contador_pedidos').textContent = `${total} pedidos`;
}

// ==================== UTILIDADES ====================
function formatearFecha(fecha) {
    if (!fecha) return 'N/A';
    const date = new Date(fecha);
    return date.toLocaleDateString('es-MX', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

function mostrarError(mensaje) {
    const contenedor = document.getElementById('contenedor_pedidos');
    contenedor.innerHTML = `
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>${escapeHtml(mensaje)}
        </div>
    `;
}
