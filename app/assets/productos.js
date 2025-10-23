// Módulo de Productos - CINASA
(function() {
    'use strict';
    
    let paginaActual = 1;
    let ordenActual = 'material_code';
    let direccionActual = 'ASC';

    document.addEventListener('DOMContentLoaded', function() {
        cargarProductos();

        // Event listeners
        document.getElementById('btnNuevoProducto').addEventListener('click', abrirModalCrear);
        document.getElementById('btnBuscar').addEventListener('click', () => cargarProductos(1));
        document.getElementById('btnLimpiarFiltros').addEventListener('click', limpiarFiltros);
        document.getElementById('btnGuardarProducto').addEventListener('click', guardarProducto);

        const buscarInput = document.getElementById('buscar');
        if (buscarInput) {
            buscarInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    cargarProductos(1);
                }
            });
        }
        
        // Ordenamiento
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
                cargarProductos(paginaActual);
            });
        });
    });

    function actualizarIconosOrden() {
        document.querySelectorAll('.sortable').forEach(th => {
            const icon = th.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-sort';
            }
            
            if (th.dataset.column === ordenActual) {
                if (icon) {
                    icon.className = direccionActual === 'ASC' ? 'fas fa-sort-up' : 'fas fa-sort-down';
                }
                th.classList.add('active');
            } else {
                th.classList.remove('active');
            }
        });
    }

    function cargarProductos(pagina = 1) {
    paginaActual = pagina;
    
    const filtros = {
        buscar: document.getElementById('buscar').value,
        estatus: document.getElementById('estatus').value,
        pais_origen: document.getElementById('pais_origen').value,
        categoria: document.getElementById('categoria').value,
        orden: ordenActual,
        direccion: direccionActual,
        pagina: pagina
    };
    
    const queryString = new URLSearchParams(filtros).toString();
    
    fetch(`${BASE_URL}/app/controllers/productos_listar.php?${queryString}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarProductos(data.data);
                mostrarPaginacion(data.pagination);
            } else {
                mostrarError('Error al cargar productos: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error de conexión al cargar productos');
        });
}

function mostrarProductos(productos) {
    const tbody = document.getElementById('tablaProductos');
    
    if (productos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>No se encontraron productos</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = '';
    productos.forEach(producto => {
        const tr = document.createElement('tr');
        
        const estatusClass = `badge-${producto.estatus}`;
        const estatusText = producto.estatus.charAt(0).toUpperCase() + producto.estatus.slice(1);
        
        tr.innerHTML = `
            <td>
                <div class="font-weight-bold">${escapeHtml(producto.material_code || 'N/A')}</div>
                ${producto.drawing_number ? `<small class="text-muted">Dwg: ${escapeHtml(producto.drawing_number)}</small>` : ''}
            </td>
            <td>
                <div class="text-truncate" style="max-width: 300px;" title="${escapeHtml(producto.descripcion || '')}">${escapeHtml(producto.descripcion || 'N/A')}</div>
                ${producto.tipo_parte ? `<small class="text-muted">${escapeHtml(producto.tipo_parte)}</small>` : ''}
            </td>
            <td>${escapeHtml(producto.unidad_medida || 'N/A')}</td>
            <td>
                ${escapeHtml(producto.drawing_number || 'N/A')}
                ${producto.drawing_version ? `<br><small class="text-muted">v${escapeHtml(producto.drawing_version)}</small>` : ''}
            </td>
            <td>${escapeHtml(producto.categoria || 'N/A')}</td>
            <td><span class="badge ${estatusClass}">${estatusText}</span></td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-sm btn-info text-white" onclick="window.verDetalleProducto(${producto.id})" title="Ver detalle">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="window.editarProducto(${producto.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-success" onclick="window.exportarProductoPDF(${producto.id})" title="Exportar PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="window.confirmarEliminar(${producto.id}, '${escapeHtml(producto.material_code || 'este producto')}')" title="Marcar como descontinuado">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        
        tbody.appendChild(tr);
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

function getEstatusClase(estatus) {
    // Esta función ya no se usa, pero la dejo por compatibilidad
    return '';
}

function mostrarPaginacion(pagination) {
    const div = document.getElementById('paginacion');
    const contador = document.getElementById('contador');
    
    if (contador) {
        contador.textContent = `Mostrando ${pagination.total} producto${pagination.total !== 1 ? 's' : ''}`;
    }
    
    if (pagination.total_paginas <= 1) {
        div.innerHTML = '';
        return;
    }
    
    let html = '<nav><ul class="pagination pagination-sm mb-0">';
    
    // Botón anterior
    if (pagination.pagina_actual > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="window.cargarProductos(${pagination.pagina_actual - 1}); return false;">Anterior</a></li>`;
    } else {
        html += '<li class="page-item disabled"><span class="page-link">Anterior</span></li>';
    }
    
    // Números de página
    for (let i = 1; i <= pagination.total_paginas; i++) {
        if (i === pagination.pagina_actual) {
            html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else if (i === 1 || i === pagination.total_paginas || (i >= pagination.pagina_actual - 2 && i <= pagination.pagina_actual + 2)) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="window.cargarProductos(${i}); return false;">${i}</a></li>`;
        } else if (i === pagination.pagina_actual - 3 || i === pagination.pagina_actual + 3) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Botón siguiente
    if (pagination.pagina_actual < pagination.total_paginas) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="window.cargarProductos(${pagination.pagina_actual + 1}); return false;">Siguiente</a></li>`;
    } else {
        html += '<li class="page-item disabled"><span class="page-link">Siguiente</span></li>';
    }
    
    html += '</ul></nav>';
    div.innerHTML = html;
}

function ordenarPor(columna) {
    if (ordenActual === columna) {
        direccionActual = direccionActual === 'ASC' ? 'DESC' : 'ASC';
    } else {
        ordenActual = columna;
        direccionActual = 'ASC';
    }
    cargarProductos(paginaActual);
}

function limpiarFiltros() {
    document.getElementById('buscar').value = '';
    document.getElementById('estatus').value = '';
    document.getElementById('pais_origen').value = '';
    document.getElementById('categoria').value = '';
    cargarProductos(1);
}

function abrirModalCrear() {
    const titulo = document.getElementById('modalTitulo');
    titulo.innerHTML = '<i class="fas fa-plus-circle mr-2"></i><span>Nuevo Producto</span>';
    document.getElementById('btnGuardarTexto').textContent = 'Guardar Producto';
    document.getElementById('formProducto').reset();
    document.getElementById('producto_id').value = '';

    ocultarErrores();
    const modal = new bootstrap.Modal(document.getElementById('modalProducto'));
    modal.show();
}

function cerrarModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalProducto'));
    if (modal) {
        modal.hide();
    }
    document.getElementById('formProducto').reset();
}

function editarProducto(id) {
    fetch(`${BASE_URL}/app/controllers/productos_detalle.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const producto = data.data;

                document.getElementById('modalTitulo').innerHTML = '<i class="fas fa-edit mr-2"></i><span>Editar Producto</span>';
                document.getElementById('btnGuardarTexto').textContent = 'Actualizar Producto';
                
                // Llenar el formulario
                document.getElementById('producto_id').value = producto.id;
                document.getElementById('material_code').value = producto.material_code || '';
                document.getElementById('descripcion').value = producto.descripcion || '';
                document.getElementById('unidad_medida').value = producto.unidad_medida || '';
                document.getElementById('pais_origen').value = producto.pais_origen || '';
                document.getElementById('hts_code').value = producto.hts_code || '';
                document.getElementById('hts_descripcion').value = producto.hts_descripcion || '';
                document.getElementById('tipo_parte').value = producto.tipo_parte || '';
                document.getElementById('sistema_calidad').value = producto.sistema_calidad || '';
                document.getElementById('categoria').value = producto.categoria || '';
                document.getElementById('drawing_number').value = producto.drawing_number || '';
                document.getElementById('drawing_version').value = producto.drawing_version || '';
                document.getElementById('drawing_sheet').value = producto.drawing_sheet || '';
                document.getElementById('ecm_number').value = producto.ecm_number || '';
                document.getElementById('material_revision').value = producto.material_revision || '';
                document.getElementById('change_number').value = producto.change_number || '';
                document.getElementById('nivel_componente').value = producto.nivel_componente || '';
                document.getElementById('componente_linea').value = producto.componente_linea || '';
                document.getElementById('ref_documento').value = producto.ref_documento || '';
                document.getElementById('peso').value = producto.peso || '';
                document.getElementById('unidad_peso').value = producto.unidad_peso || '';
                document.getElementById('material').value = producto.material || '';
                document.getElementById('acabado').value = producto.acabado || '';
                document.getElementById('notas').value = producto.notas || '';
                document.getElementById('especificaciones').value = producto.especificaciones || '';
                document.getElementById('estatus').value = producto.estatus || 'activo';

                const modal = new bootstrap.Modal(document.getElementById('modalProducto'));
                modal.show();
            } else {
                mostrarError('Error al cargar producto: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error de conexión al cargar producto');
        });
}

function guardarProducto() {
    const formData = new FormData(document.getElementById('formProducto'));
    const id = document.getElementById('producto_id').value;
    const url = id ?
        `${BASE_URL}/app/controllers/productos_editar.php` :
        `${BASE_URL}/app/controllers/productos_crear.php`;

    const btn = document.getElementById('btnGuardarProducto');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save transition-transform group-hover:scale-125"></i><span class="ml-1" id="btnGuardarTexto">Guardar Producto</span>';

        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalProducto'));
            if (modal) {
                modal.hide();
            }
            cargarProductos(paginaActual);
            mostrarExito(data.message);
        } else {
            mostrarError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save transition-transform group-hover:scale-125"></i><span class="ml-1" id="btnGuardarTexto">Guardar Producto</span>';
        mostrarError('Error de conexión al guardar producto');
    });
}

function confirmarEliminar(id, nombre) {
    if (confirm(`¿Está seguro de que desea marcar como descontinuado el producto "${nombre}"?`)) {
        eliminarProducto(id);
    }
}

function eliminarProducto(id) {
    const formData = new FormData();
    formData.append('id', id);
    
    fetch(`${BASE_URL}/app/controllers/productos_eliminar.php`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarExito(data.message);
            cargarProductos(paginaActual);
        } else {
            mostrarError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('Error de conexión al eliminar producto');
    });
}

function verDetalleProducto(id) {
    fetch(`${BASE_URL}/app/controllers/productos_detalle.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarDetalleModal(data.data);
            } else {
                mostrarError('Error al cargar el detalle del producto');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error de conexión');
        });
}

function mostrarDetalleModal(producto) {
    const detalleHTML = `
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-3 text-blue-700">
                            <i class="fas fa-info-circle text-blue-600 me-2"></i>
                            Información Básica
                        </h6>
                        <div class="detail-item">
                            <span class="detail-label">Código Material</span>
                            <span class="detail-value">${escapeHtml(producto.material_code || 'N/A')}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Descripción</span>
                            <span class="detail-value">${escapeHtml(producto.descripcion || 'N/A')}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Unidad Medida</span>
                            <span class="detail-value">${escapeHtml(producto.unidad_medida || 'N/A')}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">País Origen</span>
                            <span class="detail-value">${escapeHtml(producto.pais_origen || 'N/A')}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-3 text-blue-700">
                            <i class="fas fa-drafting-compass text-blue-600 me-2"></i>
                            Información Técnica
                        </h6>
                        <div class="detail-item">
                            <span class="detail-label">Número Dibujo</span>
                            <span class="detail-value">${escapeHtml(producto.drawing_number || 'N/A')}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Versión</span>
                            <span class="detail-value">${escapeHtml(producto.drawing_version || 'N/A')}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Categoría</span>
                            <span class="detail-value">${escapeHtml(producto.categoria || 'N/A')}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tipo Parte</span>
                            <span class="detail-value">${escapeHtml(producto.tipo_parte || 'N/A')}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-3 text-blue-700">
                            <i class="fas fa-weight text-blue-600 me-2"></i>
                            Especificaciones Físicas
                        </h6>
                        <div class="detail-item">
                            <span class="detail-label">Peso</span>
                            <span class="detail-value">${producto.peso ? producto.peso + ' ' + (producto.unidad_peso || '') : 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Material</span>
                            <span class="detail-value">${escapeHtml(producto.material || 'N/A')}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Acabado</span>
                            <span class="detail-value">${escapeHtml(producto.acabado || 'N/A')}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-3 text-blue-700">
                            <i class="fas fa-file-invoice text-blue-600 me-2"></i>
                            Clasificación Arancelaria
                        </h6>
                        <div class="detail-item">
                            <span class="detail-label">Código HTS</span>
                            <span class="detail-value">${escapeHtml(producto.hts_code || 'N/A')}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Sistema Calidad</span>
                            <span class="detail-value">${escapeHtml(producto.sistema_calidad || 'N/A')}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Estatus</span>
                            <span class="detail-value"><span class="badge badge-${producto.estatus}">${producto.estatus.toUpperCase()}</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ${producto.notas ? `
        <div class="row g-3 mt-1">
            <div class="col-md-12">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <h6 class="card-title mb-2 text-blue-700">
                            <i class="fas fa-sticky-note text-blue-600 me-2"></i>
                            Notas
                        </h6>
                        <p class="mb-0">${escapeHtml(producto.notas)}</p>
                    </div>
                </div>
            </div>
        </div>
        ` : ''}
    `;

    // Agregar estilos inline para los detalles
    const styles = `
        <style>
            .detail-item {
                display: flex;
                flex-direction: column;
                margin-bottom: 1rem;
                padding-bottom: 1rem;
                border-bottom: 1px solid rgba(0,0,0,0.1);
            }
            .detail-item:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }
            .detail-label {
                font-size: 0.85rem;
                font-weight: 600;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 0.3rem;
            }
            .detail-value {
                font-size: 1rem;
                color: #1e293b;
                font-weight: 500;
            }
        </style>
    `;

    const modalHTML = `
        <div class="modal fade" id="modalDetalle" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-box-open mr-2"></i>Detalle del Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${detalleHTML}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success" onclick="window.exportarProductoPDF(${producto.id})">
                            <i class="fas fa-file-pdf"></i> Exportar PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    const existingStyle = document.getElementById('detailItemStyles');
    if (!existingStyle) {
        document.head.insertAdjacentHTML('beforeend', `<style id="detailItemStyles">${styles.slice(7, -8)}</style>`);
    }

    const existingModal = document.getElementById('modalDetalle');
    if (existingModal) {
        existingModal.remove();
    }

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
    modal.show();
}

function formatearFecha(fecha) {
    if (!fecha) return 'N/A';
    const date = new Date(fecha);
    return date.toLocaleDateString('es-MX', { year: 'numeric', month: 'short', day: 'numeric' });
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

function exportarProductoPDF(id) {
    window.open(`${BASE_URL}/app/controllers/export_pdf_producto.php?id=${id}`, '_blank');
}

function ocultarErrores() {
    // Placeholder para consistencia con clientes
}

    // Exponer funciones globalmente para que puedan ser llamadas desde HTML
    window.cargarProductos = cargarProductos;
    window.ordenarPor = ordenarPor;
    window.limpiarFiltros = limpiarFiltros;
    window.abrirModalCrear = abrirModalCrear;
    window.cerrarModal = cerrarModal;
    window.editarProducto = editarProducto;
    window.confirmarEliminar = confirmarEliminar;
    window.verDetalleProducto = verDetalleProducto;
    window.exportarProductoPDF = exportarProductoPDF;
})();
