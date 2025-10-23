<?php

class PedidosModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listarPedidos($filtros = [], $orden = 'fecha_creacion', $direccion = 'DESC', $limite = 20, $offset = 0) {
        $where = [];
        $params = [];

        if (!empty($filtros['buscar'])) {
            $buscar = trim($filtros['buscar']);
            $where[] = "(numero_pedido LIKE ? OR c.razon_social LIKE ?)";
            $params[] = '%' . $buscar . '%';
            $params[] = '%' . $buscar . '%';
        }

        if (!empty($filtros['estatus'])) {
            $where[] = "p.estatus = ?";
            $params[] = $filtros['estatus'];
        }

        if (!empty($filtros['cliente_id'])) {
            $where[] = "p.cliente_id = ?";
            $params[] = $filtros['cliente_id'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $columnasPermitidas = ['numero_pedido', 'fecha_creacion', 'estatus', 'razon_social'];
        if (!in_array($orden, $columnasPermitidas)) {
            $orden = 'p.fecha_creacion';
        } else if ($orden === 'razon_social') {
            $orden = 'c.razon_social';
        } else {
            $orden = 'p.' . $orden;
        }

        $direccion = strtoupper($direccion) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT p.*, c.razon_social
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                {$whereClause}
                ORDER BY {$orden} {$direccion}
                LIMIT ? OFFSET ?";

        $params[] = (int)$limite;
        $params[] = (int)$offset;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function contarPedidos($filtros = []) {
        $where = [];
        $params = [];

        if (!empty($filtros['buscar'])) {
            $buscar = trim($filtros['buscar']);
            $where[] = "(numero_pedido LIKE ? OR c.razon_social LIKE ?)";
            $params[] = '%' . $buscar . '%';
            $params[] = '%' . $buscar . '%';
        }

        if (!empty($filtros['estatus'])) {
            $where[] = "p.estatus = ?";
            $params[] = $filtros['estatus'];
        }

        if (!empty($filtros['cliente_id'])) {
            $where[] = "p.cliente_id = ?";
            $params[] = $filtros['cliente_id'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT COUNT(*) as total
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                {$whereClause}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $resultado = $stmt->fetch();
        return $resultado['total'];
    }

    public function obtenerPedidoPorId($id) {
        $sql = "SELECT p.*, c.razon_social, c.contacto_principal, c.telefono, c.correo
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                WHERE p.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function crearPedido($datos) {
        $sql = "INSERT INTO pedidos (
            numero_pedido, cliente_id, facturacion, entrega,
            contacto, telefono, correo, estatus, observaciones,
            fecha_entrega, usuario_creacion
        ) VALUES (
            :numero_pedido, :cliente_id, :facturacion, :entrega,
            :contacto, :telefono, :correo, :estatus, :observaciones,
            :fecha_entrega, :usuario_creacion
        )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datos);
        return $this->pdo->lastInsertId();
    }

    public function actualizarPedido($id, $datos) {
        $sql = "UPDATE pedidos SET
            numero_pedido = :numero_pedido,
            cliente_id = :cliente_id,
            facturacion = :facturacion,
            entrega = :entrega,
            contacto = :contacto,
            telefono = :telefono,
            correo = :correo,
            estatus = :estatus,
            observaciones = :observaciones,
            fecha_entrega = :fecha_entrega,
            usuario_actualizacion = :usuario_actualizacion
        WHERE id = :id";

        $datos['id'] = $id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($datos);
    }

    public function actualizarEstatusPedido($id, $estatus, $usuario) {
        $sql = "UPDATE pedidos SET estatus = :estatus, usuario_actualizacion = :usuario WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':estatus', $estatus);
        $stmt->bindValue(':usuario', $usuario);
        return $stmt->execute();
    }

    public function agregarItemPedido($datos) {
        $sql = "INSERT INTO pedidos_items (
            pedido_id, line, producto_id, descripcion, cantidad,
            unidad_medida, precio_unitario, subtotal, notas
        ) VALUES (
            :pedido_id, :line, :producto_id, :descripcion, :cantidad,
            :unidad_medida, :precio_unitario, :subtotal, :notas
        )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($datos);
    }

    public function obtenerItemsPedido($pedido_id) {
        $sql = "SELECT pi.*, pr.material_code, pr.descripcion as descripcion_producto
                FROM pedidos_items pi
                LEFT JOIN productos pr ON pi.producto_id = pr.id
                WHERE pi.pedido_id = :pedido_id
                ORDER BY pi.line ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':pedido_id', $pedido_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerSiguienteLine($pedido_id) {
        $sql = "SELECT MAX(line) as max_line FROM pedidos_items WHERE pedido_id = :pedido_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':pedido_id', $pedido_id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch();
        return ($resultado['max_line'] ?? 0) + 1;
    }

    public function actualizarItemPedido($id, $datos) {
        $sql = "UPDATE pedidos_items SET
            producto_id = :producto_id,
            descripcion = :descripcion,
            cantidad = :cantidad,
            unidad_medida = :unidad_medida,
            precio_unitario = :precio_unitario,
            subtotal = :subtotal,
            notas = :notas
        WHERE id = :id";

        $datos['id'] = $id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($datos);
    }

    public function eliminarItemPedido($id) {
        $sql = "DELETE FROM pedidos_items WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function verificarNumeroPedidoUnico($numero_pedido, $excluirId = null) {
        $sql = "SELECT COUNT(*) as total FROM pedidos WHERE numero_pedido = :numero_pedido";

        if ($excluirId !== null) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':numero_pedido', $numero_pedido);

        if ($excluirId !== null) {
            $stmt->bindValue(':id', $excluirId, PDO::PARAM_INT);
        }

        $stmt->execute();
        $resultado = $stmt->fetch();
        return $resultado['total'] == 0;
    }
}
