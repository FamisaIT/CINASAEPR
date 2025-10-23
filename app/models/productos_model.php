<?php

class ProductosModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function listarProductos($filtros = [], $orden = 'material_code', $direccion = 'ASC', $limite = 20, $offset = 0) {
        $where = [];
        $params = [];
        
        if (!empty($filtros['buscar'])) {
            $buscar = trim($filtros['buscar']);
            $where[] = "(material_code LIKE ? OR descripcion LIKE ? OR drawing_number LIKE ?)";
            $params[] = '%' . $buscar . '%';
            $params[] = '%' . $buscar . '%';
            $params[] = '%' . $buscar . '%';
        }
        
        if (!empty($filtros['estatus'])) {
            $where[] = "estatus = ?";
            $params[] = $filtros['estatus'];
        }
        
        if (!empty($filtros['pais_origen'])) {
            $where[] = "pais_origen = ?";
            $params[] = $filtros['pais_origen'];
        }
        
        if (!empty($filtros['categoria'])) {
            $where[] = "categoria LIKE ?";
            $params[] = '%' . $filtros['categoria'] . '%';
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $columnasPermitidas = ['id', 'material_code', 'descripcion', 'drawing_number', 'categoria', 'estatus', 'fecha_alta'];
        if (!in_array($orden, $columnasPermitidas)) {
            $orden = 'material_code';
        }
        
        $direccion = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
        
        $sql = "SELECT * FROM productos {$whereClause} ORDER BY {$orden} {$direccion} LIMIT ? OFFSET ?";
        
        $params[] = (int)$limite;
        $params[] = (int)$offset;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    public function contarProductos($filtros = []) {
        $where = [];
        $params = [];
        
        if (!empty($filtros['buscar'])) {
            $buscar = trim($filtros['buscar']);
            $where[] = "(material_code LIKE ? OR descripcion LIKE ? OR drawing_number LIKE ?)";
            $params[] = '%' . $buscar . '%';
            $params[] = '%' . $buscar . '%';
            $params[] = '%' . $buscar . '%';
        }
        
        if (!empty($filtros['estatus'])) {
            $where[] = "estatus = ?";
            $params[] = $filtros['estatus'];
        }
        
        if (!empty($filtros['pais_origen'])) {
            $where[] = "pais_origen = ?";
            $params[] = $filtros['pais_origen'];
        }
        
        if (!empty($filtros['categoria'])) {
            $where[] = "categoria LIKE ?";
            $params[] = '%' . $filtros['categoria'] . '%';
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT COUNT(*) as total FROM productos {$whereClause}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        $resultado = $stmt->fetch();
        return $resultado['total'];
    }
    
    public function obtenerProductoPorId($id) {
        $sql = "SELECT * FROM productos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function obtenerProductoPorMaterialCode($material_code, $excluirId = null) {
        $sql = "SELECT * FROM productos WHERE material_code = :material_code";
        
        if ($excluirId !== null) {
            $sql .= " AND id != :id";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':material_code', $material_code);
        
        if ($excluirId !== null) {
            $stmt->bindValue(':id', $excluirId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function crearProducto($datos) {
        $sql = "INSERT INTO productos (
            material_code, descripcion, unidad_medida, pais_origen,
            hts_code, hts_descripcion, sistema_calidad, categoria, tipo_parte,
            drawing_number, drawing_version, drawing_sheet, ecm_number, 
            material_revision, change_number, nivel_componente, componente_linea, 
            ref_documento, peso, unidad_peso, material, acabado,
            notas, especificaciones, estatus, usuario_creacion
        ) VALUES (
            :material_code, :descripcion, :unidad_medida, :pais_origen,
            :hts_code, :hts_descripcion, :sistema_calidad, :categoria, :tipo_parte,
            :drawing_number, :drawing_version, :drawing_sheet, :ecm_number,
            :material_revision, :change_number, :nivel_componente, :componente_linea,
            :ref_documento, :peso, :unidad_peso, :material, :acabado,
            :notas, :especificaciones, :estatus, :usuario_creacion
        )";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datos);
        return $this->pdo->lastInsertId();
    }
    
    public function actualizarProducto($id, $datos) {
        $sql = "UPDATE productos SET
            material_code = :material_code,
            descripcion = :descripcion,
            unidad_medida = :unidad_medida,
            pais_origen = :pais_origen,
            hts_code = :hts_code,
            hts_descripcion = :hts_descripcion,
            sistema_calidad = :sistema_calidad,
            categoria = :categoria,
            tipo_parte = :tipo_parte,
            drawing_number = :drawing_number,
            drawing_version = :drawing_version,
            drawing_sheet = :drawing_sheet,
            ecm_number = :ecm_number,
            material_revision = :material_revision,
            change_number = :change_number,
            nivel_componente = :nivel_componente,
            componente_linea = :componente_linea,
            ref_documento = :ref_documento,
            peso = :peso,
            unidad_peso = :unidad_peso,
            material = :material,
            acabado = :acabado,
            notas = :notas,
            especificaciones = :especificaciones,
            estatus = :estatus
        WHERE id = :id";
        
        $datos['id'] = $id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($datos);
    }
    
    public function eliminarProducto($id) {
        $sql = "UPDATE productos SET estatus = 'descontinuado' WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function obtenerPaisesOrigen() {
        $sql = "SELECT DISTINCT pais_origen FROM productos WHERE pais_origen IS NOT NULL AND pais_origen != '' ORDER BY pais_origen";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function obtenerCategorias() {
        $sql = "SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
