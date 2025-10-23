<!-- Modal para Crear/Editar Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title flex items-center" id="modalTitulo">
                    <i class="fas fa-box mr-2"></i>
                    <span>Nuevo Producto</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formProducto">
                    <input type="hidden" id="producto_id" name="id">

                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <span>Información Básica del Producto</span>
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="material_code" class="form-label">Código de Material/Pieza</label>
                            <input type="text" class="form-control" id="material_code" name="material_code" placeholder="100099089">
                        </div>
                        <div class="col-md-4">
                            <label for="unidad_medida_modal" class="form-label">Unidad de Medida</label>
                            <select class="form-select" id="unidad_medida_modal" name="unidad_medida">
                                <option value="">Seleccionar...</option>
                                <option value="EA">EA - Each (Pieza)</option>
                                <option value="PZ">PZ - Pieza</option>
                                <option value="KG">KG - Kilogramo</option>
                                <option value="LB">LB - Libra</option>
                                <option value="MT">MT - Metro</option>
                                <option value="M2">M2 - Metro Cuadrado</option>
                                <option value="M3">M3 - Metro Cúbico</option>
                                <option value="LT">LT - Litro</option>
                                <option value="GL">GL - Galón</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="pais_origen_modal" class="form-label">País de Origen</label>
                            <input type="text" class="form-control" id="pais_origen_modal" name="pais_origen" placeholder="ej: México, China">
                        </div>
                        <div class="col-md-12">
                            <label for="descripcion" class="form-label">Descripción del Producto</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2" placeholder="COVER, ACCESS"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="precio_unitario" class="form-label">Precio Unitario (USD)</label>
                            <input type="number" step="0.01" class="form-control" id="precio_unitario" name="precio_unitario" placeholder="0.00">
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <span>Clasificación Arancelaria</span>
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="hts_code" class="form-label">Código HTS</label>
                            <input type="text" class="form-control" id="hts_code" name="hts_code" placeholder="8431499030">
                        </div>
                        <div class="col-md-6">
                            <label for="tipo_parte" class="form-label">Tipo de Parte</label>
                            <input type="text" class="form-control" id="tipo_parte" name="tipo_parte" placeholder="Standard Part, Custom">
                        </div>
                        <div class="col-md-12">
                            <label for="hts_descripcion" class="form-label">Descripción Código HTS</label>
                            <textarea class="form-control" id="hts_descripcion" name="hts_descripcion" rows="2" placeholder="COAL, ROCK CUTTERS, TUNNEL MACHINE PARTS"></textarea>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <span>Sistema de Calidad y Categoría</span>
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="sistema_calidad_modal" class="form-label">Sistema de Calidad</label>
                            <select class="form-select" id="sistema_calidad_modal" name="sistema_calidad">
                                <option value="">Seleccionar...</option>
                                <option value="J02">J02</option>
                                <option value="ISO9001">ISO9001</option>
                                <option value="ISO14001">ISO14001</option>
                                <option value="IATF16949">IATF16949</option>
                                <option value="AS9100">AS9100</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="categoria_modal" class="form-label">Categoría</label>
                            <input type="text" class="form-control" id="categoria_modal" name="categoria" placeholder="ej: Electrónica, Accesorios">
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-drafting-compass"></i>
                        </div>
                        <span>Información Técnica del Dibujo</span>
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="drawing_number" class="form-label">Número de Dibujo</label>
                            <input type="text" class="form-control" id="drawing_number" name="drawing_number" placeholder="100099089">
                        </div>
                        <div class="col-md-3">
                            <label for="drawing_version" class="form-label">Versión</label>
                            <input type="text" class="form-control" id="drawing_version" name="drawing_version" placeholder="06">
                        </div>
                        <div class="col-md-3">
                            <label for="drawing_sheet" class="form-label">Hoja</label>
                            <input type="text" class="form-control" id="drawing_sheet" name="drawing_sheet" placeholder="001">
                        </div>
                        <div class="col-md-3">
                            <label for="ecm_number" class="form-label">Número ECM</label>
                            <input type="text" class="form-control" id="ecm_number" name="ecm_number" placeholder="1194615">
                        </div>
                        <div class="col-md-4">
                            <label for="material_revision" class="form-label">Revisión Material</label>
                            <input type="text" class="form-control" id="material_revision" name="material_revision" placeholder="06">
                        </div>
                        <div class="col-md-4">
                            <label for="change_number" class="form-label">Número de Cambio</label>
                            <input type="text" class="form-control" id="change_number" name="change_number" placeholder="1194615">
                        </div>
                        <div class="col-md-4">
                            <label for="ref_documento" class="form-label">Documento de Referencia</label>
                            <input type="text" class="form-control" id="ref_documento" name="ref_documento" placeholder="Doc/Sheet/Ver">
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <span>Información de Componentes/BOM</span>
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="nivel_componente" class="form-label">Nivel Componente</label>
                            <input type="text" class="form-control" id="nivel_componente" name="nivel_componente" placeholder="1">
                        </div>
                        <div class="col-md-4">
                            <label for="componente_linea" class="form-label">Componente Línea</label>
                            <input type="text" class="form-control" id="componente_linea" name="componente_linea" placeholder="001, 002">
                        </div>
                        <div class="col-md-4">
                            <label for="ref_documento" class="form-label">Documento Referencia</label>
                            <input type="text" class="form-control" id="ref_documento_bom" name="ref_documento_bom" placeholder="Doc/Sheet/Ver">
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-weight"></i>
                        </div>
                        <span>Especificaciones Físicas</span>
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="peso" class="form-label">Peso</label>
                            <input type="number" step="0.001" class="form-control" id="peso" name="peso">
                        </div>
                        <div class="col-md-3">
                            <label for="unidad_peso" class="form-label">Unidad Peso</label>
                            <input type="text" class="form-control" id="unidad_peso" name="unidad_peso" placeholder="KG, LB">
                        </div>
                        <div class="col-md-3">
                            <label for="material" class="form-label">Material</label>
                            <input type="text" class="form-control" id="material" name="material" placeholder="Acero, Aluminio">
                        </div>
                        <div class="col-md-3">
                            <label for="acabado" class="form-label">Acabado</label>
                            <input type="text" class="form-control" id="acabado" name="acabado" placeholder="Pintado, Anodizado">
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 flex items-center text-blue-700">
                        <div class="bg-blue-100 p-2 rounded-lg mr-2">
                            <i class="fas fa-sticky-note"></i>
                        </div>
                        <span>Notas y Estatus</span>
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="estatus_modal" class="form-label">Estatus</label>
                            <select class="form-select" id="estatus_modal" name="estatus">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                                <option value="descontinuado">Descontinuado</option>
                            </select>
                        </div>
                        <div class="col-md-8"></div>
                        <div class="col-md-12">
                            <label for="notas" class="form-label">Notas Generales</label>
                            <textarea class="form-control" id="notas" name="notas" rows="2" placeholder="Notas adicionales del producto..."></textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="especificaciones" class="form-label">Especificaciones Técnicas</label>
                            <textarea class="form-control" id="especificaciones" name="especificaciones" rows="2" placeholder="All product and processes supplied must conform to requirements..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary group" data-bs-dismiss="modal">
                    <i class="fas fa-times transition-transform group-hover:rotate-90"></i>
                    <span class="ml-1">Cancelar</span>
                </button>
                <button type="button" class="btn btn-primary group" id="btnGuardarProducto">
                    <i class="fas fa-save transition-transform group-hover:scale-125"></i>
                    <span class="ml-1" id="btnGuardarTexto">Guardar Producto</span>
                </button>
            </div>
        </div>
    </div>
</div>
