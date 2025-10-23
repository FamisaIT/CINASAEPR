// Módulo de Crear Pedido - CINASA
(function() {
    'use strict';

    let proximaLine = 1;
    let clienteSeleccionado = null;
    let inputProductoActivo = null;
    let contenedorProductos = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Crear contenedor flotante para dropdown de productos
        crearContenedorFlotante();

        // Inicializar eventos
        const inputBusquedaCliente = document.getElementById('cliente_busqueda');
        const btnLimpiarCliente = document.getElementById('btnLimpiarCliente');
        const btnAgregarItem = document.getElementById('btnAgregarItem');
        const btnGuardarPedido = document.getElementById('btnGuardarPedido');

        // Eventos de cliente
        if (inputBusquedaCliente) {
            inputBusquedaCliente.addEventListener('focus', mostrarListaClientes);
            inputBusquedaCliente.addEventListener('input', filtrarClientes);
            document.addEventListener('click', cerrarListaSiEsNecesario);
        }

        btnLimpiarCliente.addEventListener('click', limpiarCliente);
        btnAgregarItem.addEventListener('click', agregarFila);
        btnGuardarPedido.addEventListener('click', guardarPedido);

        // Cerrar dropdown flotante al hacer click fuera o scroll
        window.addEventListener('scroll', cerrarDropdownFloatante);
        document.addEventListener('click', (e) => {
            // Si no está dentro del dropdown flotante ni en un input de búsqueda
            if (contenedorProductos &&
                e.target !== contenedorProductos &&
                !contenedorProductos.contains(e.target) &&
                !e.target.classList.contains('producto-busqueda')) {
                cerrarDropdownFloatante();
            }
        });

        // Agregar primera fila de item
        agregarFila();
    });

    // ==================== CONTENEDOR FLOTANTE ====================
    function crearContenedorFlotante() {
        if (document.getElementById('dropdown-flotante-productos')) return;

        const container = document.createElement('div');
        container.id = 'dropdown-flotante-productos';
        container.style.cssText = `
            position: fixed;
            display: none;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 10000;
            max-height: 400px;
            overflow-y: auto;
            min-width: 250px;
        `;
        document.body.appendChild(container);
        contenedorProductos = container;
    }

    function posicionarYMostrarDropdown(input) {
        const rect = input.getBoundingClientRect();
        contenedorProductos.style.left = rect.left + 'px';
        contenedorProductos.style.top = (rect.bottom + 5) + 'px';
        contenedorProductos.style.width = rect.width + 'px';
        contenedorProductos.style.display = 'block';
    }

    function cerrarDropdownFloatante() {
        if (contenedorProductos) {
            contenedorProductos.style.display = 'none';
        }
        inputProductoActivo = null;
    }

    // ==================== FUNCIONES DE CLIENTE ====================
    function mostrarListaClientes() {
        const listaDiv = document.getElementById('cliente_lista');
        const inputBusqueda = document.getElementById('cliente_busqueda');

        if (clientesData.length === 0) {
            listaDiv.innerHTML = '<div class="list-group-item">No hay clientes disponibles</div>';
            listaDiv.style.display = 'block';
            return;
        }

        mostrarOpcionesClientes(clientesData);
        listaDiv.style.display = 'block';
    }

    function filtrarClientes() {
        const query = document.getElementById('cliente_busqueda').value.toLowerCase().trim();
        const listaDiv = document.getElementById('cliente_lista');

        if (query.length === 0) {
            mostrarOpcionesClientes(clientesData);
            listaDiv.style.display = 'block';
            return;
        }

        const filtrados = clientesData.filter(cliente =>
            cliente.razon_social.toLowerCase().includes(query) ||
            cliente.rfc.toLowerCase().includes(query) ||
            (cliente.contacto_principal && cliente.contacto_principal.toLowerCase().includes(query))
        );

        if (filtrados.length === 0) {
            listaDiv.innerHTML = '<div class="list-group-item">No se encontraron clientes</div>';
        } else {
            mostrarOpcionesClientes(filtrados);
        }

        listaDiv.style.display = 'block';
    }

    function mostrarOpcionesClientes(clientes) {
        const listaDiv = document.getElementById('cliente_lista');
        listaDiv.innerHTML = '';

        clientes.forEach(cliente => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action p-3';
            item.innerHTML = `
                <div class="d-flex w-100 justify-content-between align-items-start">
                    <div>
                        <strong>${escapeHtml(cliente.razon_social)}</strong>
                        <small class="d-block text-muted">${cliente.rfc}</small>
                        <small class="d-block text-muted">${escapeHtml(cliente.contacto_principal || 'Sin contacto')}</small>
                    </div>
                </div>
            `;
            item.addEventListener('click', (e) => {
                e.preventDefault();
                seleccionarCliente(cliente);
            });
            listaDiv.appendChild(item);
        });
    }

    function seleccionarCliente(cliente) {
        clienteSeleccionado = cliente;
        document.getElementById('cliente_id').value = cliente.id;
        document.getElementById('cliente_busqueda').value = escapeHtml(cliente.razon_social);
        document.getElementById('contacto').value = cliente.contacto_principal || '';
        document.getElementById('telefono').value = cliente.telefono || '';
        document.getElementById('correo').value = cliente.correo || '';
        document.getElementById('facturacion').value = cliente.direccion || '';
        document.getElementById('entrega').value = cliente.direccion || '';
        document.getElementById('cliente_lista').style.display = 'none';
    }

    function limpiarCliente() {
        document.getElementById('cliente_id').value = '';
        document.getElementById('cliente_busqueda').value = '';
        document.getElementById('contacto').value = '';
        document.getElementById('telefono').value = '';
        document.getElementById('correo').value = '';
        document.getElementById('facturacion').value = '';
        document.getElementById('entrega').value = '';
        document.getElementById('cliente_lista').style.display = 'none';
        clienteSeleccionado = null;
    }

    function cerrarListaSiEsNecesario(event) {
        const listaDiv = document.getElementById('cliente_lista');
        const inputBusqueda = document.getElementById('cliente_busqueda');
        const btnLimpiar = document.getElementById('btnLimpiarCliente');

        if (event.target !== inputBusqueda && event.target !== btnLimpiar && !listaDiv.contains(event.target)) {
            listaDiv.style.display = 'none';
        }
    }

    // ==================== FUNCIONES DE ITEMS/PRODUCTOS ====================
    function agregarFila() {
        const tbody = document.getElementById('tbody_items');
        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td style="text-align: center; vertical-align: middle; padding: 8px 5px;">
                <input type="number" class="form-control form-control-sm line-number" value="${proximaLine}" min="1" readonly style="font-weight: bold; text-align: center;">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm producto-busqueda" placeholder="Escribe o selecciona..." autocomplete="off">
                <input type="hidden" class="producto-id" name="producto_id">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm descripcion" placeholder="Descripción" readonly>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm unidad-medida" placeholder="EA" readonly style="font-size: 0.85rem;">
                <input type="hidden" class="unidad-medida-hidden" name="unidad_medida">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm cantidad" placeholder="0" step="0.01" min="0" value="1">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm precio-unitario" placeholder="0.00" readonly step="0.01" min="0" style="font-size: 0.85rem;">
                <input type="hidden" class="precio-unitario-hidden" name="precio_unitario">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm subtotal" placeholder="0.00" readonly style="font-weight: bold; text-align: right;">
                <input type="hidden" class="subtotal-hidden" name="subtotal">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="window.eliminarFila(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        // Event listeners para búsqueda de producto
        const inputProducto = tr.querySelector('.producto-busqueda');
        const inputCantidad = tr.querySelector('.cantidad');

        inputProducto.addEventListener('focus', () => {
            inputProductoActivo = inputProducto;
            mostrarListaProductos(inputProducto);
        });

        inputProducto.addEventListener('input', () => {
            filtrarProductos(inputProducto);
        });

        inputProducto.addEventListener('blur', () => {
            // Pequeño delay para permitir click en dropdown
            setTimeout(() => {
                if (inputProductoActivo === inputProducto) {
                    cerrarDropdownFloatante();
                }
            }, 200);
        });

        // Event listener para recalcular subtotal cuando cambia la cantidad
        inputCantidad.addEventListener('change', () => {
            calcularSubtotal(tr);
        });

        inputCantidad.addEventListener('input', () => {
            calcularSubtotal(tr);
        });

        tbody.appendChild(tr);
        proximaLine++;
    }

    function mostrarListaProductos(input) {
        posicionarYMostrarDropdown(input);

        if (productosData.length === 0) {
            contenedorProductos.innerHTML = '<div class="list-group-item p-3">No hay productos disponibles</div>';
            return;
        }

        mostrarOpcionesProductos(productosData);
    }

    function filtrarProductos(input) {
        const query = input.value.toLowerCase().trim();

        if (query.length === 0) {
            mostrarOpcionesProductos(productosData);
            posicionarYMostrarDropdown(input);
            return;
        }

        const filtrados = productosData.filter(producto =>
            producto.material_code.toLowerCase().includes(query) ||
            producto.descripcion.toLowerCase().includes(query) ||
            producto.id.toString().includes(query)
        );

        if (filtrados.length === 0) {
            contenedorProductos.innerHTML = '<div class="list-group-item p-3">No se encontraron productos</div>';
        } else {
            mostrarOpcionesProductos(filtrados);
        }

        posicionarYMostrarDropdown(input);
    }

    function mostrarOpcionesProductos(productos) {
        contenedorProductos.innerHTML = '';

        productos.forEach(producto => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action p-3';
            item.style.borderRadius = '0';
            item.style.borderLeft = 'none';
            item.style.borderRight = 'none';
            item.style.borderTop = 'none';
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div class="w-100">
                        <strong>[${producto.id}] ${escapeHtml(producto.material_code)}</strong>
                        <small class="d-block text-muted" style="white-space: normal;">${escapeHtml(producto.descripcion || 'Sin descripción')}</small>
                    </div>
                </div>
            `;
            item.addEventListener('mousedown', (e) => {
                e.preventDefault();
                seleccionarProducto(producto, inputProductoActivo);
            });
            contenedorProductos.appendChild(item);
        });
    }

    function seleccionarProducto(producto, input) {
        if (!input) return;
        const tr = input.closest('tr');

        // Llenar datos del producto
        tr.querySelector('.producto-id').value = producto.id;
        tr.querySelector('.producto-busqueda').value = `[${producto.id}] ${escapeHtml(producto.material_code)}`;
        tr.querySelector('.descripcion').value = escapeHtml(producto.descripcion || 'N/A');

        // Llenar unidad de medida
        const unidadMedida = producto.unidad_medida || 'EA';
        tr.querySelector('.unidad-medida').value = unidadMedida;
        tr.querySelector('.unidad-medida-hidden').value = unidadMedida;

        // Llenar precio unitario
        const precioUnitario = parseFloat(producto.precio_unitario || 0);
        tr.querySelector('.precio-unitario').value = precioUnitario.toFixed(2);
        tr.querySelector('.precio-unitario-hidden').value = precioUnitario;

        // Calcular subtotal
        calcularSubtotal(tr);

        cerrarDropdownFloatante();
    }

    function calcularSubtotal(tr) {
        const cantidad = parseFloat(tr.querySelector('.cantidad').value || 0);
        const precioUnitario = parseFloat(tr.querySelector('.precio-unitario-hidden').value || 0);
        const subtotal = cantidad * precioUnitario;

        tr.querySelector('.subtotal').value = subtotal.toFixed(2);
        tr.querySelector('.subtotal-hidden').value = subtotal.toFixed(2);
    }

    function eliminarFila(btn) {
        btn.closest('tr').remove();
    }

    // ==================== FUNCIONES DE GUARDAR ====================
    function guardarPedido() {
        // Validaciones básicas
        if (!document.getElementById('numero_pedido').value) {
            alert('El número de pedido es requerido');
            return;
        }

        if (!document.getElementById('cliente_id').value) {
            alert('Debe seleccionar un cliente');
            return;
        }

        if (!document.getElementById('facturacion').value) {
            alert('La dirección de facturación es requerida');
            return;
        }

        if (!document.getElementById('entrega').value) {
            alert('La dirección de entrega es requerida');
            return;
        }

        // Validar que haya al menos un item
        const items = obtenerItems();
        if (items.length === 0) {
            alert('Debe agregar al menos un producto al pedido');
            return;
        }

        // Validar que todos los items tengan datos válidos
        for (const item of items) {
            if (!item.producto_id || !item.cantidad) {
                alert('Todos los items deben tener producto y cantidad');
                return;
            }
        }

        console.log('Items a guardar:', items);

        // Crear FormData
        const formData = new FormData(document.getElementById('formCrearPedido'));
        formData.append('items', JSON.stringify(items));

        const btn = document.getElementById('btnGuardarPedido');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        fetch(`${BASE_URL}/app/controllers/pedidos_crear.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Guardar Pedido';

            if (data.success) {
                mostrarExito(data.message);
                setTimeout(() => {
                    window.location.href = `${BASE_URL}/pedidos.php`;
                }, 2000);
            } else {
                mostrarError(data.message || 'Error al guardar pedido');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Guardar Pedido';
            mostrarError('Error de conexión al guardar pedido');
        });
    }

    function obtenerItems() {
        const items = [];
        document.querySelectorAll('#tbody_items tr').forEach((tr, index) => {
            const productoId = tr.querySelector('.producto-id').value;
            const cantidad = tr.querySelector('.cantidad').value;

            if (productoId && cantidad) {
                items.push({
                    line: index + 1,
                    producto_id: productoId,
                    descripcion: tr.querySelector('.descripcion').value,
                    cantidad: parseFloat(cantidad),
                    unidad_medida: tr.querySelector('.unidad-medida-hidden').value || '',
                    precio_unitario: parseFloat(tr.querySelector('.precio-unitario-hidden').value || 0),
                    subtotal: parseFloat(tr.querySelector('.subtotal-hidden').value || 0),
                    notas: ''
                });
            }
        });
        return items;
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
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Exponer funciones globales
    window.eliminarFila = eliminarFila;
})();
