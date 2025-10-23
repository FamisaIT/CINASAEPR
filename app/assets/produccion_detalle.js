// Módulo de Detalle de Producción - CINASA
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const pedidoId = urlParams.get('pedido_id');

        if (!pedidoId) {
            mostrarError('ID de pedido no especificado');
            return;
        }

        cargarDetalleProduccion(pedidoId);
    });

    function cargarDetalleProduccion(pedidoId) {
        fetch(`${BASE_URL}/app/controllers/produccion_obtener_detalle.php?pedido_id=${pedidoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarInformacionPedido(data.pedido);
                    mostrarInformacionCliente(data.pedido);
                    mostrarItemsProduccion(data.items);
                } else {
                    mostrarError('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error de conexión');
            });
    }

    function mostrarInformacionPedido(pedido) {
        const fechaEntrega = pedido.fecha_entrega ? new Date(pedido.fecha_entrega).toLocaleDateString('es-MX') : 'Sin especificar';

        document.getElementById('numeroPedidoTitle').innerHTML = `
            <i class="fas fa-box me-2"></i>${escapeHtml(pedido.numero_pedido)}
        `;
        document.getElementById('dateInfo').textContent = `Fecha de entrega: ${escapeHtml(fechaEntrega)}`;
    }

    function mostrarInformacionCliente(pedido) {
        const container = document.getElementById('infoCliente');

        const clasesEstatus = {
            'creada': 'bg-secondary',
            'en_produccion': 'bg-warning text-dark',
            'completada': 'bg-success',
            'cancelada': 'bg-danger'
        };

        const claseEstatus = clasesEstatus[pedido.estatus] || 'bg-secondary';

        container.innerHTML = `
            <div class="mb-3">
                <label class="text-muted small d-block mb-1">
                    <i class="fas fa-building me-1"></i>Razón Social
                </label>
                <p class="mb-0 fw-bold text-primary">${escapeHtml(pedido.cliente_nombre || 'N/A')}</p>
            </div>

            <hr class="my-2">

            <div class="mb-3">
                <label class="text-muted small d-block mb-1">
                    <i class="fas fa-user me-1"></i>Contacto Principal
                </label>
                <p class="mb-0">${escapeHtml(pedido.contacto_principal || 'N/A')}</p>
            </div>

            <div class="mb-3">
                <label class="text-muted small d-block mb-1">
                    <i class="fas fa-phone me-1"></i>Teléfono
                </label>
                <p class="mb-0">
                    <a href="tel:${escapeHtml(pedido.telefono || '')}" class="text-decoration-none">
                        ${escapeHtml(pedido.telefono || 'N/A')}
                    </a>
                </p>
            </div>

            <div class="mb-3">
                <label class="text-muted small d-block mb-1">
                    <i class="fas fa-envelope me-1"></i>Correo
                </label>
                <p class="mb-0">
                    <a href="mailto:${escapeHtml(pedido.correo || '')}" class="text-decoration-none text-truncate d-block" style="max-width: 200px;" title="${escapeHtml(pedido.correo || '')}">
                        ${escapeHtml(pedido.correo || 'N/A')}
                    </a>
                </p>
            </div>

            <hr class="my-2">

            <div class="mb-3">
                <label class="text-muted small d-block mb-1">
                    <i class="fas fa-tag me-1"></i>Estatus
                </label>
                <span class="badge ${claseEstatus}">
                    ${escapeHtml(pedido.estatus.replace('_', ' ').toUpperCase())}
                </span>
            </div>

            ${pedido.observaciones ? `
            <div class="alert alert-light border-left-warning p-2 mt-3" role="alert">
                <small class="text-muted d-block mb-1">
                    <i class="fas fa-sticky-note me-1"></i>Observaciones
                </small>
                <small>${escapeHtml(pedido.observaciones)}</small>
            </div>
            ` : ''}
        `;
    }

    function mostrarItemsProduccion(items) {
        const container = document.getElementById('itemsContainer');

        if (items.length === 0) {
            container.innerHTML = `
                <div class="alert alert-info m-3" role="alert">
                    <i class="fas fa-info-circle me-2"></i>No hay items de producción para este pedido.
                </div>
            `;
            return;
        }

        let html = '';

        items.forEach((item, index) => {
            const porcentaje = item.qty_solicitada > 0 ? (item.prod_total / item.qty_solicitada) * 100 : 0;
            const colorProgreso = porcentaje >= 100 ? 'bg-success' :
                                 porcentaje >= 75 ? 'bg-info' :
                                 porcentaje >= 50 ? 'bg-warning' : 'bg-danger';

            const colorPendiente = item.qty_pendiente > 0 ? 'text-warning fw-bold' :
                                  item.qty_pendiente < 0 ? 'text-danger fw-bold' : 'text-success fw-bold';

            html += `
                <div class="border-bottom">
                    <div class="p-3">
                        <!-- Header del Item -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <span class="badge bg-primary me-2">${escapeHtml(item.item_code)}</span>
                                    <span>${escapeHtml(item.descripcion || 'Sin descripción')}</span>
                                </h6>
                                <small class="text-muted">
                                    Unidad: ${escapeHtml(item.unidad_medida || 'N/A')}
                                </small>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-sm btn-outline-info" onclick="window.verHistorialItem(${item.id})" title="Ver histórico">
                                    <i class="fas fa-history me-1"></i>Histórico
                                </button>
                            </div>
                        </div>

                        <!-- Estadísticas del Item -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-3">
                                <div class="bg-light p-2 rounded text-center">
                                    <small class="text-muted d-block">Solicitado</small>
                                    <strong>${parseFloat(item.qty_solicitada).toFixed(2)}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-light p-2 rounded text-center">
                                    <small class="text-muted d-block">Producido</small>
                                    <strong class="text-success">${parseFloat(item.prod_total).toFixed(2)}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-light p-2 rounded text-center">
                                    <small class="text-muted d-block">Pendiente</small>
                                    <strong class="${colorPendiente}">${parseFloat(item.qty_pendiente).toFixed(2)}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-light p-2 rounded text-center">
                                    <small class="text-muted d-block">Progreso</small>
                                    <strong>${porcentaje.toFixed(0)}%</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Barra de Progreso -->
                        <div class="mb-3">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar ${colorProgreso}" style="width: ${Math.min(porcentaje, 100)}%"></div>
                            </div>
                        </div>

                        <!-- Formulario de Registro -->
                        <div class="bg-light p-3 rounded mb-3">
                            <h6 class="mb-3">
                                <i class="fas fa-plus-circle me-2 text-success"></i>Registrar Producción
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small mb-1">Cantidad</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control prod-hoy-input"
                                               id="prodHoy${item.id}"
                                               placeholder="0.00"
                                               step="0.01"
                                               min="0"
                                               data-id="${item.id}"
                                               data-qty-solicitada="${item.qty_solicitada}"
                                               data-prod-total="${item.prod_total}">
                                        <span class="input-group-text">${escapeHtml(item.unidad_medida || 'un')}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small mb-1">Supervisor</label>
                                    <input type="text" class="form-control form-control-sm"
                                           id="supervisor${item.id}"
                                           placeholder="Nombre del supervisor">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small mb-1">Observaciones</label>
                                    <input type="text" class="form-control form-control-sm"
                                           id="observaciones${item.id}"
                                           placeholder="Notas...">
                                </div>
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-success btn-sm" onclick="window.guardarProduccion(${item.id})">
                                    <i class="fas fa-save me-1"></i>Guardar Producción
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function guardarProduccion(itemId) {
        const input = document.querySelector(`.prod-hoy-input[data-id="${itemId}"]`);
        const cantidad = parseFloat(input.value);
        const supervisor = document.getElementById(`supervisor${itemId}`).value.trim();
        const observaciones = document.getElementById(`observaciones${itemId}`).value.trim();

        if (isNaN(cantidad) || cantidad <= 0) {
            alert('Por favor ingrese una cantidad válida');
            return;
        }

        const qtyTotal = parseFloat(input.dataset.qtySolicitada);
        const prodTotal = parseFloat(input.dataset.prodTotal);
        const nuevoTotal = prodTotal + cantidad;

        if (nuevoTotal > qtyTotal * 1.05) {
            alert('La producción total excedería la cantidad solicitada en más del 5%');
            return;
        }

        const formData = new FormData();
        formData.append('id', itemId);
        formData.append('cantidad_hoy', cantidad);
        formData.append('supervisor', supervisor || '');
        formData.append('observaciones', observaciones || '');

        fetch(`${BASE_URL}/app/controllers/produccion_actualizar.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                document.getElementById(`supervisor${itemId}`).value = '';
                document.getElementById(`observaciones${itemId}`).value = '';

                const urlParams = new URLSearchParams(window.location.search);
                const pedidoId = urlParams.get('pedido_id');
                cargarDetalleProduccion(pedidoId);

                mostrarExito('Producción registrada correctamente');
            } else {
                alert('Error: ' + (data.message || 'No se pudo guardar'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al guardar');
        });
    }

    function verHistorialItem(itemId) {
        fetch(`${BASE_URL}/app/controllers/produccion_historial.php?id=${itemId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarHistorialCompleto(data.produccion, data.historial, itemId);
                } else {
                    alert('Error al cargar histórico');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión');
            });
    }

    function mostrarHistorialCompleto(produccion, historial, id) {
        const historialHTML = historial.map(h => `
            <tr>
                <td>${new Date(h.fecha_produccion).toLocaleDateString('es-MX')}</td>
                <td class="text-end"><strong>${parseFloat(h.cantidad_producida).toFixed(2)}</strong></td>
                <td>${escapeHtml(h.supervisor || 'N/A')}</td>
                <td><small>${escapeHtml(h.observaciones || '-')}</small></td>
            </tr>
        `).join('');

        const modalHTML = `
            <div class="modal fade" id="modalHistorial" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-light border-bottom">
                            <h5 class="modal-title">
                                <i class="fas fa-history text-primary me-2"></i>
                                Histórico - ${escapeHtml(produccion.numero_pedido)} / ${escapeHtml(produccion.item_code)}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-4 text-center">
                                    <small class="text-muted d-block mb-1">Cantidad Solicitada</small>
                                    <h6 class="mb-0">${parseFloat(produccion.qty_solicitada).toFixed(2)}</h6>
                                </div>
                                <div class="col-md-4 text-center">
                                    <small class="text-muted d-block mb-1">Total Producido</small>
                                    <h6 class="mb-0 text-success">${parseFloat(produccion.prod_total).toFixed(2)}</h6>
                                </div>
                                <div class="col-md-4 text-center">
                                    <small class="text-muted d-block mb-1">Pendiente</small>
                                    <h6 class="mb-0 ${produccion.qty_pendiente > 0 ? 'text-warning' : 'text-success'}">${parseFloat(produccion.qty_pendiente).toFixed(2)}</h6>
                                </div>
                            </div>

                            <hr>

                            <h6 class="mb-3"><i class="fas fa-list-ul me-2 text-primary"></i>Registros Históricos</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th class="text-end">Cantidad</th>
                                            <th>Supervisor</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${historialHTML || '<tr><td colspan="4" class="text-center text-muted py-3">Sin registros</td></tr>'}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const existingModal = document.getElementById('modalHistorial');
        if (existingModal) {
            existingModal.remove();
        }

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        const modal = new bootstrap.Modal(document.getElementById('modalHistorial'));
        modal.show();
    }

    function mostrarExito(mensaje) {
        const alerta = document.createElement('div');
        alerta.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        alerta.style.zIndex = '9999';
        alerta.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>${escapeHtml(mensaje)}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alerta);

        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }

    function mostrarError(mensaje) {
        const alerta = document.createElement('div');
        alerta.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        alerta.style.zIndex = '9999';
        alerta.innerHTML = `
            <i class="fas fa-exclamation-circle me-2"></i>${escapeHtml(mensaje)}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alerta);

        setTimeout(() => {
            alerta.remove();
        }, 5000);
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
    window.guardarProduccion = guardarProduccion;
    window.verHistorialItem = verHistorialItem;
})();
