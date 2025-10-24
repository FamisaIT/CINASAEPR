// ==================== VARIABLES GLOBALES ====================
let pedidosActuales = [];
let clientesDisponibles = [];

// ==================== INICIALIZACIÓN ====================
document.addEventListener('DOMContentLoaded', function() {
    cargarPedidos();
    configurarEventos();
});

// ==================== CONFIGURACIÓN DE EVENTOS ====================
function configurarEventos() {
    document.getElementById('btn_buscar_pedidos').addEventListener('click', cargarPedidos);
    document.getElementById('btn_limpiar_pedidos').addEventListener('click', limpiarFiltrosPedidos);
    document.getElementById('filtro_buscar_pedidos').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            cargarPedidos();
        }
    });
}

// ==================== CARGAR PEDIDOS ====================
async function cargarPedidos() {
    try {
        const response = await fetch(`${BASE_URL}/app/controllers/calidad_obtener_pedidos.php`);
        const data = await response.json();

        if (!data.exito) {
            mostrarError('Error al cargar pedidos: ' + data.error);
            return;
        }

        // Guardar pedidos originales
        pedidosActuales = data.pedidos;

        // Extraer clientes únicos para el filtro
        extraerClientesUnicos(data.pedidos);

        // Mostrar pedidos con filtro aplicado
        aplicarFiltrosYMostrar();

    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

// ==================== GESTIÓN DE FILTROS ====================
function extraerClientesUnicos(pedidos) {
    const clientesSet = new Set();

    pedidos.forEach(pedido => {
        if (pedido.razon_social) {
            clientesSet.add(JSON.stringify({
                id: pedido.cliente_id,
                razon_social: pedido.razon_social
            }));
        }
    });

    clientesDisponibles = Array.from(clientesSet).map(item => JSON.parse(item));

    // Llenar select de clientes
    const selectCliente = document.getElementById('filtro_cliente_pedidos');
    const opcionesExistentes = selectCliente.querySelectorAll('option').length;

    if (opcionesExistentes === 1) { // Solo existe la opción "Todos"
        clientesDisponibles.forEach(cliente => {
            const option = document.createElement('option');
            option.value = cliente.id;
            option.textContent = cliente.razon_social;
            selectCliente.appendChild(option);
        });
    }
}

function obtenerFiltrosPedidos() {
    return {
        buscar: document.getElementById('filtro_buscar_pedidos').value.toLowerCase(),
        cliente: document.getElementById('filtro_cliente_pedidos').value
    };
}

function limpiarFiltrosPedidos() {
    document.getElementById('filtro_buscar_pedidos').value = '';
    document.getElementById('filtro_cliente_pedidos').value = '';
    aplicarFiltrosYMostrar();
}

function aplicarFiltrosYMostrar() {
    const filtros = obtenerFiltrosPedidos();

    // Filtrar pedidos
    const pedidosFiltrados = pedidosActuales.filter(pedido => {
        let cumpleFiltros = true;

        // Filtro búsqueda
        if (filtros.buscar) {
            const coincideBusqueda =
                pedido.numero_pedido.toLowerCase().includes(filtros.buscar) ||
                (pedido.razon_social && pedido.razon_social.toLowerCase().includes(filtros.buscar));
            cumpleFiltros = cumpleFiltros && coincideBusqueda;
        }

        // Filtro cliente
        if (filtros.cliente) {
            cumpleFiltros = cumpleFiltros && pedido.cliente_id == filtros.cliente;
        }

        return cumpleFiltros;
    });

    // Mostrar pedidos filtrados
    mostrarPedidos(pedidosFiltrados);
}

// ==================== MOSTRAR PEDIDOS ====================
function mostrarPedidos(pedidos) {
    const contenedor = document.getElementById('contenedor_pedidos');

    if (pedidos.length === 0) {
        contenedor.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    <i class="fas fa-inbox me-2"></i>
                    No hay pedidos con piezas pendientes de inspeccionar
                </td>
            </tr>
        `;
        document.getElementById('contador_pedidos').textContent = 'Total: 0 pedido(s)';
        return;
    }

    let html = '';
    pedidos.forEach(pedido => {
        const colorBadge = pedido.cantidad_piezas_pendientes > 10 ? 'danger' :
                           pedido.cantidad_piezas_pendientes > 5 ? 'warning' : 'info';

        html += `
            <tr>
                <td>
                    <strong>${escapeHtml(pedido.numero_pedido)}</strong>
                </td>
                <td>${escapeHtml(pedido.razon_social || 'Cliente N/A')}</td>
                <td>
                    <span class="badge bg-${colorBadge}">
                        ${pedido.cantidad_piezas_pendientes}
                    </span>
                </td>
                <td class="text-center">
                    <a href="${BASE_URL}/calidad_pedido.php?pedido=${encodeURIComponent(pedido.numero_pedido)}"
                       class="btn btn-sm btn-primary" title="Inspeccionar pedido">
                        <i class="fas fa-arrow-right"></i>
                        Inspeccionar
                    </a>
                </td>
            </tr>
        `;
    });

    contenedor.innerHTML = html;
    const totalPiezas = pedidos.reduce((sum, p) => sum + p.cantidad_piezas_pendientes, 0);
    document.getElementById('contador_pedidos').textContent =
        `Total: ${pedidos.length} pedido(s) con ${totalPiezas} pieza(s) pendientes`;
}

// ==================== FUNCIÓN AUXILIAR ====================
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

// ==================== FUNCIONES DE UTILIDAD ====================
function mostrarError(mensaje) {
    alert('❌ ' + mensaje);
}

function mostrarExito(mensaje) {
    alert('✓ ' + mensaje);
}
