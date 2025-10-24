// Integración de Calidad con Producción - CINASA

/**
 * Mostrar información de calidad en tarjeta de producción
 * @param {Object} item - Objeto de producción
 * @returns {string} HTML con información de calidad
 */
function obtenerResumenCalidad(item) {
    const liberadas = item.prod_liberada || 0;
    const rechazadas = item.prod_rechazada || 0;
    const porInspeccionar = item.qty_por_inspeccionar || 0;
    const total = item.prod_total || 0;

    // Determinar color del estado
    let colorEstado = 'warning';
    let textoEstado = 'Por inspeccionar';

    if (item.estado_calidad === 'completamente_inspeccionada' && rechazadas === 0) {
        colorEstado = 'success';
        textoEstado = 'Todas liberadas ✓';
    } else if (item.estado_calidad === 'sin_inspeccionar') {
        colorEstado = 'secondary';
        textoEstado = 'Sin inspeccionar';
    } else if (item.estado_calidad === 'con_rechazos') {
        colorEstado = 'danger';
        textoEstado = 'Con rechazos';
    } else if (item.estado_calidad === 'parcialmente_inspeccionada') {
        colorEstado = 'info';
        textoEstado = 'Parcialmente inspección';
    }

    return `
        <div class="card-footer bg-light">
            <div class="row g-2 small">
                <div class="col-6">
                    <span class="badge badge-${colorEstado} w-100">
                        ${textoEstado}
                    </span>
                </div>
                <div class="col-6">
                    <span class="badge bg-success w-100">
                        <i class="fas fa-check-circle"></i> ${liberadas}
                    </span>
                </div>
                <div class="col-6">
                    <span class="badge bg-danger w-100">
                        <i class="fas fa-times-circle"></i> ${rechazadas}
                    </span>
                </div>
                <div class="col-6">
                    <span class="badge bg-warning w-100">
                        <i class="fas fa-hourglass-half"></i> ${porInspeccionar}
                    </span>
                </div>
            </div>
        </div>
    `;
}

/**
 * Mostrar modal con piezas de una producción
 * @param {number} produccionId - ID de la producción
 */
async function mostrarPiezasProduccion(produccionId) {
    try {
        const response = await fetch(`app/controllers/produccion_obtener_piezas.php?produccion_id=${produccionId}`);
        const data = await response.json();

        if (!data.exito) {
            alert('Error: ' + data.error);
            return;
        }

        const piezas = data.piezas || [];
        const resumen = data.resumen || {};

        let html = `
            <div class="modal fade" id="modal_piezas_${produccionId}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-cubes"></i> Piezas Producidas - ${data.numero_pedido}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Resumen de Calidad -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="text-muted">Total</h5>
                                            <h3 class="text-dark">${resumen.total_piezas || 0}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="text-success">Liberadas</h5>
                                            <h3 class="text-success">${resumen.piezas_liberadas || 0}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="text-danger">Rechazadas</h5>
                                            <h3 class="text-danger">${resumen.piezas_rechazadas || 0}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="text-warning">Por Inspeccionar</h5>
                                            <h3 class="text-warning">${resumen.piezas_pendientes || 0}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla de Piezas -->
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Folio</th>
                                            <th>Estado</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
        `;

        piezas.forEach(pieza => {
            let badgeColor = 'warning';
            let badgeTexto = 'Por Inspeccionar';

            if (pieza.estatus === 'liberada') {
                badgeColor = 'success';
                badgeTexto = 'Liberada';
            } else if (pieza.estatus === 'rechazada') {
                badgeColor = 'danger';
                badgeTexto = 'Rechazada';
            } else if (pieza.estatus === 'pendiente_reinspeccion') {
                badgeColor = 'info';
                badgeTexto = 'Reinspección';
            }

            html += `
                                        <tr>
                                            <td><strong>${pieza.folio_pieza}</strong></td>
                                            <td><span class="badge badge-${badgeColor}">${badgeTexto}</span></td>
                                            <td>
                                                <a href="calidad.php" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-edit"></i> Inspeccionar
                                                </a>
                                            </td>
                                        </tr>
            `;
        });

        html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <a href="calidad.php" class="btn btn-primary" target="_blank">
                                <i class="fas fa-list"></i> Ver todas las inspecciones
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Agregar modal al DOM
        document.body.insertAdjacentHTML('beforeend', html);

        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById(`modal_piezas_${produccionId}`));
        modal.show();

    } catch (error) {
        console.error('Error al obtener piezas:', error);
        alert('Error al obtener piezas: ' + error.message);
    }
}

/**
 * Generar piezas cuando se registra producción
 * @param {number} produccionId - ID de la producción
 * @param {number} cantidadPiezas - Cantidad de piezas a generar
 */
async function generarPiezasProduccion(produccionId, cantidadPiezas) {
    try {
        const response = await fetch('app/controllers/produccion_generar_piezas.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                produccion_id: produccionId,
                cantidad_piezas: cantidadPiezas
            })
        });

        const data = await response.json();

        if (!data.exito) {
            throw new Error(data.error);
        }

        console.log(`Se han creado ${data.cantidad_creadas} piezas correctamente`);
        return data;

    } catch (error) {
        console.error('Error al generar piezas:', error);
        throw error;
    }
}

/**
 * Actualizar vista de producción cuando cambia calidad
 * @param {number} produccionId - ID de la producción
 */
async function actualizarProduccionConCalidad(produccionId) {
    try {
        const response = await fetch(`app/controllers/produccion_obtener_con_calidad.php?id=${produccionId}`);
        const data = await response.json();

        if (!data.exito) {
            console.error('Error al actualizar:', data.error);
            return;
        }

        // Actualizar información de calidad en la tarjeta
        const produccion = data.produccion;
        const resumen = obtenerResumenCalidad(produccion);

        // Encontrar la tarjeta y actualizar
        const tarjeta = document.querySelector(`[data-produccion-id="${produccionId}"]`);
        if (tarjeta) {
            const footer = tarjeta.querySelector('.card-footer');
            if (footer) {
                footer.innerHTML = resumen;
            }
        }

    } catch (error) {
        console.error('Error al actualizar producción:', error);
    }
}

// Escuchar cambios en calidad (si se usa en otra ventana)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        // Recargar datos si la página vuelve a ser visible
        const produccionItems = document.querySelectorAll('[data-produccion-id]');
        produccionItems.forEach(item => {
            const produccionId = item.dataset.produccionId;
            actualizarProduccionConCalidad(produccionId);
        });
    }
});
