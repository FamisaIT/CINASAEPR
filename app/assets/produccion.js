// Módulo de Tracking de Producción - CINASA
(function() {
    'use strict';

    let paginaActual = 1;
    let ordenActual = 'numero_pedido';
    let direccionActual = 'ASC';

    document.addEventListener('DOMContentLoaded', function() {
        cargarProduccion();

        // Event listeners
        document.getElementById('btnBuscar').addEventListener('click', () => cargarProduccion(1));
        document.getElementById('btnLimpiarFiltros').addEventListener('click', limpiarFiltros);

        const buscarInput = document.getElementById('buscar');
        if (buscarInput) {
            buscarInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    cargarProduccion(1);
                }
            });
        }

        // Evento para el filtro de fecha
        const fechaFiltro = document.getElementById('fechaFiltro');
        if (fechaFiltro) {
            fechaFiltro.addEventListener('change', () => cargarProduccion(1));
        }
    });

    function cargarProduccion(pagina = 1) {
        paginaActual = pagina;

        const fechaFiltro = document.getElementById('fechaFiltro').value;

        const filtros = {
            buscar: document.getElementById('buscar').value,
            estatus: document.getElementById('estatus').value,
            fecha_desde: fechaFiltro ? fechaFiltro : '',
            fecha_hasta: fechaFiltro ? fechaFiltro : '',
            orden: ordenActual,
            direccion: direccionActual,
            pagina: pagina
        };

        const queryString = new URLSearchParams(filtros).toString();

        fetch(`${BASE_URL}/app/controllers/produccion_listar.php?${queryString}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarOrdenes(data.data);
                    actualizarPaginacion(data.pagination);
                    actualizarContador(data.pagination.total);
                } else {
                    mostrarError('Error al cargar producción: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error de conexión al cargar producción');
            });
    }

    function mostrarOrdenes(registros) {
        const container = document.getElementById('ordenesContainer');

        if (registros.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>No se encontraron registros de producción</p>
                    </div>
                </div>
            `;
            return;
        }

        // Agrupar registros por número de pedido y obtener pedido_id
        const ordenes = {};
        registros.forEach(registro => {
            const numeroPedido = registro.numero_pedido;
            if (!ordenes[numeroPedido]) {
                ordenes[numeroPedido] = {
                    numero_pedido: numeroPedido,
                    pedido_id: registro.pedido_id,
                    fecha_entrega: registro.fecha_entrega,
                    items: []
                };
            }
            ordenes[numeroPedido].items.push(registro);
        });

        // Generar HTML con grid layout
        let gridHtml = '<div class="row g-3">';

        Object.keys(ordenes).forEach(numeroPedido => {
            const orden = ordenes[numeroPedido];
            const tarjetaHtml = crearTarjetaOrdenHTML(orden);
            gridHtml += `<div class="col-lg-6 col-xl-4">${tarjetaHtml}</div>`;
        });

        gridHtml += '</div>';
        container.innerHTML = gridHtml;
    }

    function crearTarjetaOrdenHTML(orden) {
        // Calcular totales de la orden
        let totalSolicitado = 0;
        let totalProducido = 0;
        let totalPendiente = 0;

        orden.items.forEach(item => {
            totalSolicitado += parseFloat(item.qty_solicitada) || 0;
            totalProducido += parseFloat(item.prod_total) || 0;
            totalPendiente += parseFloat(item.qty_pendiente) || 0;
        });

        const porcentajeCompletado = totalSolicitado > 0 ? (totalProducido / totalSolicitado) * 100 : 0;
        const colorProgreso = porcentajeCompletado >= 100 ? 'bg-success' :
                             porcentajeCompletado >= 75 ? 'bg-info' :
                             porcentajeCompletado >= 50 ? 'bg-warning' : 'bg-danger';

        // Formatear fecha de entrega
        const fechaEntrega = orden.fecha_entrega ? new Date(orden.fecha_entrega).toLocaleDateString('es-MX') : 'Sin fecha';

        // Crear HTML de la tarjeta (lista simplificada)
        const itemsResumenHtml = orden.items.map(item => {
            const porcentajeItem = item.qty_solicitada > 0 ? (item.prod_total / item.qty_solicitada) * 100 : 0;
            const colorItem = porcentajeItem >= 100 ? 'bg-success' :
                             porcentajeItem >= 75 ? 'bg-info' :
                             porcentajeItem >= 50 ? 'bg-warning' : 'bg-danger';

            return `
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom small">
                    <div class="flex-grow-1 min-w-0">
                        <span class="badge bg-primary me-1" style="font-size: 0.7rem;">${escapeHtml(item.item_code)}</span>
                        <small class="text-truncate d-inline-block" style="max-width: 120px;" title="${escapeHtml(item.descripcion || 'N/A')}">${escapeHtml((item.descripcion || 'N/A').substring(0, 25))}</small>
                    </div>
                    <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                        <small class="text-muted" style="font-size: 0.75rem;">
                            ${parseFloat(item.prod_total).toFixed(2)}/${parseFloat(item.qty_solicitada).toFixed(2)}
                        </small>
                        <div class="progress" style="width: 40px; height: 14px;">
                            <div class="progress-bar ${colorItem}" style="width: ${Math.min(porcentajeItem, 100)}%"></div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        const html = `
            <div class="card shadow-sm border-0 h-100 d-flex flex-column" style="cursor: pointer; transition: all 0.3s ease;"
                 onmouseenter="this.style.boxShadow='0 0.5rem 1rem rgba(0,0,0,0.15), 0 0 2rem rgba(59,130,246,0.2)'; this.style.transform='translateY(-4px)';"
                 onmouseleave="this.style.boxShadow=''; this.style.transform='';">

                <div class="card-header bg-light d-flex justify-content-between align-items-start gap-2">
                    <div class="flex-grow-1 min-w-0">
                        <h6 class="mb-1 text-truncate">
                            <i class="fas fa-box text-primary me-1"></i>
                            <strong>${escapeHtml(orden.numero_pedido)}</strong>
                        </h6>
                        <small class="text-muted d-block">
                            <i class="far fa-calendar me-1"></i>${escapeHtml(fechaEntrega)}
                        </small>
                    </div>
                    <span class="badge bg-secondary flex-shrink-0">${orden.items.length}</span>
                </div>

                <div class="card-body flex-grow-1 d-flex flex-column py-2">
                    <!-- Resumen de items -->
                    <div class="mb-2 overflow-auto" style="max-height: 120px; font-size: 0.85rem;">
                        ${itemsResumenHtml}
                    </div>

                    <!-- Barra de progreso general -->
                    <div class="mb-3 mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Progreso</small>
                            <small class="fw-bold">${porcentajeCompletado.toFixed(0)}%</small>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar ${colorProgreso}" style="width: ${Math.min(porcentajeCompletado, 100)}%; line-height: 20px; font-size: 0.7rem;">
                                ${porcentajeCompletado.toFixed(0)}%
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas rápidas -->
                    <div class="row g-1 text-center mb-3" style="font-size: 0.85rem;">
                        <div class="col-4">
                            <small class="text-muted d-block">Sol.</small>
                            <strong>${totalSolicitado.toFixed(1)}</strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Prod.</small>
                            <strong class="text-success">${totalProducido.toFixed(1)}</strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Pend.</small>
                            <strong class="${totalPendiente > 0 ? 'text-warning' : 'text-success'}">${totalPendiente.toFixed(1)}</strong>
                        </div>
                    </div>

                    <!-- Botón de acción -->
                    <button class="btn btn-primary btn-sm w-100" onclick="window.abrirDetalle(${orden.pedido_id})">
                        <i class="fas fa-arrow-right me-1"></i>Ver Detalles
                    </button>
                </div>
            </div>
        `;

        return html;
    }

    function abrirDetalle(pedidoId) {
        window.location.href = `${BASE_URL}/produccion_detalle.php?pedido_id=${pedidoId}`;
    }

    function actualizarPaginacion(pagination) {
        const div = document.getElementById('paginacion');

        if (pagination.total_paginas <= 1) {
            div.innerHTML = '';
            return;
        }

        let html = '<nav><ul class="pagination pagination-sm mb-0">';

        // Botón anterior
        if (pagination.pagina_actual > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="window.cambiarPagina(${pagination.pagina_actual - 1}); return false;">Anterior</a></li>`;
        } else {
            html += '<li class="page-item disabled"><span class="page-link">Anterior</span></li>';
        }

        // Números de página
        for (let i = 1; i <= pagination.total_paginas; i++) {
            if (i === pagination.pagina_actual) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else if (i === 1 || i === pagination.total_paginas || (i >= pagination.pagina_actual - 2 && i <= pagination.pagina_actual + 2)) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="window.cambiarPagina(${i}); return false;">${i}</a></li>`;
            } else if (i === pagination.pagina_actual - 3 || i === pagination.pagina_actual + 3) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Botón siguiente
        if (pagination.pagina_actual < pagination.total_paginas) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="window.cambiarPagina(${pagination.pagina_actual + 1}); return false;">Siguiente</a></li>`;
        } else {
            html += '<li class="page-item disabled"><span class="page-link">Siguiente</span></li>';
        }

        html += '</ul></nav>';
        div.innerHTML = html;
    }

    function actualizarContador(total) {
        const elemento = document.getElementById('contador');
        if (elemento) {
            elemento.textContent = `Mostrando ${total} ítem${total !== 1 ? 's' : ''}`;
        }
    }

    function cambiarPagina(pagina) {
        paginaActual = pagina;
        cargarProduccion();
        window.scrollTo(0, 0);
    }

    function limpiarFiltros() {
        document.getElementById('buscar').value = '';
        document.getElementById('estatus').value = 'en_produccion';
        document.getElementById('fechaFiltro').value = '';
        paginaActual = 1;
        cargarProduccion();
    }

    function mostrarExito(mensaje) {
        const alerta = document.createElement('div');
        alerta.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        alerta.style.zIndex = '9999';
        alerta.innerHTML = `
            ${escapeHtml(mensaje)}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alerta);

        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }

    function mostrarError(mensaje) {
        alert('Error: ' + mensaje);
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

    // Exponer funciones globalmente
    window.cargarProduccion = cargarProduccion;
    window.cambiarPagina = cambiarPagina;
    window.limpiarFiltros = limpiarFiltros;
    window.abrirDetalle = abrirDetalle;
})();
