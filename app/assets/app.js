let paginaActual = 1;
let filtrosActuales = {};
let ordenActual = 'razon_social';
let direccionActual = 'ASC';

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
    fetch('/app/controllers/obtener_filtros.php')
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
    tbody.innerHTML = '<tr><td colspan="9" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>';
    
    fetch(`/app/controllers/clientes_listar.php?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarClientes(data.data);
                actualizarPaginacion(data.pagination);
                actualizarContador(data.pagination.total);
            } else {
                mostrarError('Error al cargar los clientes');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error de conexión al cargar los clientes');
        });
}

function mostrarClientes(clientes) {
    const tbody = document.getElementById('tablaClientes');
    tbody.innerHTML = '';
    
    if (clientes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No se encontraron clientes</td></tr>';
        return;
    }
    
    clientes.forEach(cliente => {
        const tr = document.createElement('tr');
        
        const estatusClass = `badge-${cliente.estatus}`;
        const estatusText = cliente.estatus.charAt(0).toUpperCase() + cliente.estatus.slice(1);
        
        tr.innerHTML = `
            <td>${cliente.id}</td>
            <td><strong>${escapeHtml(cliente.razon_social)}</strong></td>
            <td>${cliente.rfc}</td>
            <td>${escapeHtml(cliente.contacto_principal || 'N/A')}</td>
            <td>${escapeHtml(cliente.telefono || 'N/A')}</td>
            <td>${escapeHtml(cliente.correo || 'N/A')}</td>
            <td><span class="badge ${estatusClass}">${estatusText}</span></td>
            <td>${escapeHtml(cliente.vendedor_asignado || 'N/A')}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-sm btn-info text-white" onclick="verDetalle(${cliente.id})" title="Ver detalle">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="editarCliente(${cliente.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-success" onclick="exportarPDF(${cliente.id})" title="Exportar PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="confirmarEliminar(${cliente.id}, '${escapeHtml(cliente.razon_social)}')" title="Bloquear">
                        <i class="fas fa-ban"></i>
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
    ul.className = 'pagination mb-0';
    
    const anterior = document.createElement('li');
    anterior.className = `page-item ${pagination.pagina_actual === 1 ? 'disabled' : ''}`;
    anterior.innerHTML = `<a class="page-link" href="#" onclick="cambiarPagina(${pagination.pagina_actual - 1}); return false;">Anterior</a>`;
    ul.appendChild(anterior);
    
    const inicio = Math.max(1, pagination.pagina_actual - 2);
    const fin = Math.min(pagination.total_paginas, pagination.pagina_actual + 2);
    
    if (inicio > 1) {
        const li = document.createElement('li');
        li.className = 'page-item';
        li.innerHTML = `<a class="page-link" href="#" onclick="cambiarPagina(1); return false;">1</a>`;
        ul.appendChild(li);
        
        if (inicio > 2) {
            const dots = document.createElement('li');
            dots.className = 'page-item disabled';
            dots.innerHTML = `<span class="page-link">...</span>`;
            ul.appendChild(dots);
        }
    }
    
    for (let i = inicio; i <= fin; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === pagination.pagina_actual ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#" onclick="cambiarPagina(${i}); return false;">${i}</a>`;
        ul.appendChild(li);
    }
    
    if (fin < pagination.total_paginas) {
        if (fin < pagination.total_paginas - 1) {
            const dots = document.createElement('li');
            dots.className = 'page-item disabled';
            dots.innerHTML = `<span class="page-link">...</span>`;
            ul.appendChild(dots);
        }
        
        const li = document.createElement('li');
        li.className = 'page-item';
        li.innerHTML = `<a class="page-link" href="#" onclick="cambiarPagina(${pagination.total_paginas}); return false;">${pagination.total_paginas}</a>`;
        ul.appendChild(li);
    }
    
    const siguiente = document.createElement('li');
    siguiente.className = `page-item ${pagination.pagina_actual === pagination.total_paginas ? 'disabled' : ''}`;
    siguiente.innerHTML = `<a class="page-link" href="#" onclick="cambiarPagina(${pagination.pagina_actual + 1}); return false;">Siguiente</a>`;
    ul.appendChild(siguiente);
    
    nav.appendChild(ul);
    paginacionDiv.appendChild(nav);
}

function actualizarContador(total) {
    document.getElementById('totalClientes').textContent = total;
}

function cambiarPagina(pagina) {
    paginaActual = pagina;
    cargarClientes();
    window.scrollTo(0, 0);
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
    fetch(`/app/controllers/clientes_detalle.php?id=${id}`)
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
    const url = id ? '/app/controllers/clientes_editar.php' : '/app/controllers/clientes_crear.php';
    
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
    
    fetch('/app/controllers/clientes_eliminar.php', {
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
    fetch(`/app/controllers/clientes_detalle.php?id=${id}`)
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
        <div class="row">
            <div class="col-md-6">
                <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-building"></i> Datos Fiscales</h6>
                <p><strong>Razón Social:</strong> ${escapeHtml(cliente.razon_social)}</p>
                <p><strong>RFC:</strong> ${cliente.rfc}</p>
                <p><strong>Régimen Fiscal:</strong> ${escapeHtml(cliente.regimen_fiscal || 'N/A')}</p>
                <p><strong>Estatus:</strong> <span class="badge badge-${cliente.estatus}">${cliente.estatus.toUpperCase()}</span></p>
            </div>
            <div class="col-md-6">
                <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-map-marker-alt"></i> Ubicación</h6>
                <p><strong>Dirección:</strong> ${escapeHtml(cliente.direccion || 'N/A')}</p>
                <p><strong>País:</strong> ${escapeHtml(cliente.pais)}</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-address-book"></i> Contacto</h6>
                <p><strong>Contacto Principal:</strong> ${escapeHtml(cliente.contacto_principal || 'N/A')}</p>
                <p><strong>Teléfono:</strong> ${escapeHtml(cliente.telefono || 'N/A')}</p>
                <p><strong>Correo:</strong> ${escapeHtml(cliente.correo || 'N/A')}</p>
            </div>
            <div class="col-md-6">
                <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-credit-card"></i> Condiciones Comerciales</h6>
                <p><strong>Días de Crédito:</strong> ${cliente.dias_credito} días</p>
                <p><strong>Límite de Crédito:</strong> $${parseFloat(cliente.limite_credito).toFixed(2)} ${cliente.moneda}</p>
                <p><strong>Vendedor Asignado:</strong> ${escapeHtml(cliente.vendedor_asignado || 'N/A')}</p>
            </div>
        </div>
    `;
    
    const modalHTML = `
        <div class="modal fade" id="modalDetalle" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalle del Cliente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${detalleHTML}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success" onclick="exportarPDF(${cliente.id})">
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
    window.location.href = `/app/controllers/export_csv.php?${params}`;
}

function exportarPDF(id) {
    window.open(`/app/controllers/export_pdf.php?id=${id}`, '_blank');
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
    divErrores.innerHTML = '<ul class="mb-0">' + errores.map(e => `<li>${escapeHtml(e)}</li>`).join('') + '</ul>';
    divErrores.classList.remove('d-none');
}

function ocultarErrores() {
    const divErrores = document.getElementById('erroresCliente');
    divErrores.classList.add('d-none');
}

function mostrarError(mensaje) {
    alert('Error: ' + mensaje);
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

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
