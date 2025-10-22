<?php

class ClientesModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function listarClientes($filtros = [], $orden = 'razon_social', $direccion = 'ASC', $limite = 20, $offset = 0) {
        $where = [];
        $params = [];
        
        if (!empty($filtros['buscar'])) {
            $where[] = "(razon_social LIKE :buscar OR rfc LIKE :buscar OR contacto_principal LIKE :buscar)";
            $params[':buscar'] = '%' . $filtros['buscar'] . '%';
        }
        
        if (!empty($filtros['estatus'])) {
            $where[] = "estatus = :estatus";
            $params[':estatus'] = $filtros['estatus'];
        }
        
        if (!empty($filtros['vendedor'])) {
            $where[] = "vendedor_asignado = :vendedor";
            $params[':vendedor'] = $filtros['vendedor'];
        }
        
        if (!empty($filtros['pais'])) {
            $where[] = "pais = :pais";
            $params[':pais'] = $filtros['pais'];
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $columnasPermitidas = ['razon_social', 'rfc', 'estatus', 'fecha_alta', 'vendedor_asignado', 'limite_credito'];
        if (!in_array($orden, $columnasPermitidas)) {
            $orden = 'razon_social';
        }
        
        $direccion = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
        
        $sql = "SELECT * FROM clientes {$whereClause} ORDER BY {$orden} {$direccion} LIMIT :limite OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function contarClientes($filtros = []) {
        $where = [];
        $params = [];
        
        if (!empty($filtros['buscar'])) {
            $where[] = "(razon_social LIKE :buscar OR rfc LIKE :buscar OR contacto_principal LIKE :buscar)";
            $params[':buscar'] = '%' . $filtros['buscar'] . '%';
        }
        
        if (!empty($filtros['estatus'])) {
            $where[] = "estatus = :estatus";
            $params[':estatus'] = $filtros['estatus'];
        }
        
        if (!empty($filtros['vendedor'])) {
            $where[] = "vendedor_asignado = :vendedor";
            $params[':vendedor'] = $filtros['vendedor'];
        }
        
        if (!empty($filtros['pais'])) {
            $where[] = "pais = :pais";
            $params[':pais'] = $filtros['pais'];
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT COUNT(*) as total FROM clientes {$whereClause}";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $resultado = $stmt->fetch();
        return $resultado['total'];
    }
    
    public function obtenerClientePorId($id) {
        $sql = "SELECT * FROM clientes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function obtenerClientePorRFC($rfc, $excluirId = null) {
        $sql = "SELECT * FROM clientes WHERE rfc = :rfc";
        
        if ($excluirId !== null) {
            $sql .= " AND id != :id";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':rfc', $rfc);
        
        if ($excluirId !== null) {
            $stmt->bindValue(':id', $excluirId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function crearCliente($datos) {
        $sql = "INSERT INTO clientes (
            razon_social, rfc, regimen_fiscal, direccion, pais, contacto_principal,
            telefono, correo, dias_credito, limite_credito, condiciones_pago,
            moneda, uso_cfdi, metodo_pago, forma_pago, banco, cuenta_bancaria,
            estatus, vendedor_asignado
        ) VALUES (
            :razon_social, :rfc, :regimen_fiscal, :direccion, :pais, :contacto_principal,
            :telefono, :correo, :dias_credito, :limite_credito, :condiciones_pago,
            :moneda, :uso_cfdi, :metodo_pago, :forma_pago, :banco, :cuenta_bancaria,
            :estatus, :vendedor_asignado
        )";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datos);
        return $this->pdo->lastInsertId();
    }
    
    public function actualizarCliente($id, $datos) {
        $sql = "UPDATE clientes SET
            razon_social = :razon_social,
            rfc = :rfc,
            regimen_fiscal = :regimen_fiscal,
            direccion = :direccion,
            pais = :pais,
            contacto_principal = :contacto_principal,
            telefono = :telefono,
            correo = :correo,
            dias_credito = :dias_credito,
            limite_credito = :limite_credito,
            condiciones_pago = :condiciones_pago,
            moneda = :moneda,
            uso_cfdi = :uso_cfdi,
            metodo_pago = :metodo_pago,
            forma_pago = :forma_pago,
            banco = :banco,
            cuenta_bancaria = :cuenta_bancaria,
            estatus = :estatus,
            vendedor_asignado = :vendedor_asignado
        WHERE id = :id";
        
        $datos['id'] = $id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($datos);
    }
    
    public function eliminarCliente($id) {
        $sql = "UPDATE clientes SET estatus = 'bloqueado' WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function obtenerVendedores() {
        $sql = "SELECT DISTINCT vendedor_asignado FROM clientes WHERE vendedor_asignado IS NOT NULL AND vendedor_asignado != '' ORDER BY vendedor_asignado";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function obtenerPaises() {
        $sql = "SELECT DISTINCT pais FROM clientes WHERE pais IS NOT NULL AND pais != '' ORDER BY pais";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
