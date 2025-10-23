<?php

class ProductionModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Listar todos los ítems de producción con paginación
    public function listarProduccion($filtros = [], $orden = 'numero_pedido', $direccion = 'ASC', $limite = 20, $offset = 0) {
        $where = [];
        $params = [];

        if (!empty($filtros['buscar'])) {
            $buscar = trim($filtros['buscar']);
            $where[] = "(p.numero_pedido LIKE ? OR p.item_code LIKE ? OR p.descripcion LIKE ?)";
            $params[] = '%' . $buscar . '%';
            $params[] = '%' . $buscar . '%';
            $params[] = '%' . $buscar . '%';
        }

        if (!empty($filtros['estatus'])) {
            $where[] = "p.estatus = ?";
            $params[] = $filtros['estatus'];
        }

        // Filtro por fecha de entrega
        if (!empty($filtros['fecha_entrega_desde'])) {
            $where[] = "ped.fecha_entrega >= ?";
            $params[] = $filtros['fecha_entrega_desde'] . ' 00:00:00';
        }

        if (!empty($filtros['fecha_entrega_hasta'])) {
            $where[] = "ped.fecha_entrega <= ?";
            $params[] = $filtros['fecha_entrega_hasta'] . ' 23:59:59';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $columnasPermitidas = ['numero_pedido', 'item_code', 'qty_solicitada', 'prod_total', 'qty_pendiente', 'fecha_creacion', 'estatus', 'fecha_entrega'];
        if (!in_array($orden, $columnasPermitidas)) {
            $orden = 'numero_pedido';
        }

        // Ajustar nombre de columna si es fecha_entrega
        $ordenColumna = $orden === 'fecha_entrega' ? 'ped.fecha_entrega' : 'p.' . $orden;

        $direccion = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT p.*, ped.fecha_entrega FROM produccion p
                LEFT JOIN pedidos ped ON p.pedido_id = ped.id
                {$whereClause}
                ORDER BY {$ordenColumna} {$direccion}
                LIMIT ? OFFSET ?";

        $params[] = (int)$limite;
        $params[] = (int)$offset;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    // Contar total de registros de producción
    public function contarProduccion($filtros = []) {
        $where = [];
        $params = [];

        if (!empty($filtros['buscar'])) {
            $buscar = trim($filtros['buscar']);
            $where[] = "(p.numero_pedido LIKE ? OR p.item_code LIKE ? OR p.descripcion LIKE ?)";
            $params[] = '%' . $buscar . '%';
            $params[] = '%' . $buscar . '%';
            $params[] = '%' . $buscar . '%';
        }

        if (!empty($filtros['estatus'])) {
            $where[] = "p.estatus = ?";
            $params[] = $filtros['estatus'];
        }

        // Filtro por fecha de entrega
        if (!empty($filtros['fecha_entrega_desde'])) {
            $where[] = "ped.fecha_entrega >= ?";
            $params[] = $filtros['fecha_entrega_desde'] . ' 00:00:00';
        }

        if (!empty($filtros['fecha_entrega_hasta'])) {
            $where[] = "ped.fecha_entrega <= ?";
            $params[] = $filtros['fecha_entrega_hasta'] . ' 23:59:59';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT COUNT(*) as total FROM produccion p
                LEFT JOIN pedidos ped ON p.pedido_id = ped.id
                {$whereClause}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $resultado = $stmt->fetch();
        return $resultado['total'];
    }

    // Obtener un registro de producción por ID
    public function obtenerProduccionPorId($id) {
        $sql = "SELECT * FROM produccion WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Crear registro de producción (cuando el pedido va a en_produccion)
    public function crearProduccion($datos) {
        $sql = "INSERT INTO produccion (
            pedido_id, producto_id, numero_pedido, item_code, descripcion,
            qty_solicitada, prod_hoy, prod_total, unidad_medida, estatus
        ) VALUES (
            :pedido_id, :producto_id, :numero_pedido, :item_code, :descripcion,
            :qty_solicitada, :prod_hoy, :prod_total, :unidad_medida, :estatus
        )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($datos);
    }

    // Actualizar la producción de hoy
    public function actualizarProduccionHoy($id, $cantidadHoy) {
        // Primero obtener el registro para validar
        $registro = $this->obtenerProduccionPorId($id);

        if (!$registro) {
            throw new Exception('Registro de producción no encontrado');
        }

        // Calcular el nuevo total
        $nuevoTotal = $registro['prod_total'] - $registro['prod_hoy'] + $cantidadHoy;

        // Validar que no exceda la cantidad solicitada (permitir un pequeño margen por redondeo)
        if ($nuevoTotal > $registro['qty_solicitada'] * 1.05) {
            throw new Exception('La producción total excedería la cantidad solicitada');
        }

        // Actualizar prod_hoy y recalcular prod_total
        $sql = "UPDATE produccion SET
            prod_hoy = :prod_hoy,
            prod_total = (
                SELECT COALESCE(SUM(cantidad_producida), 0)
                FROM produccion_historial
                WHERE produccion_id = :produccion_id
            ),
            fecha_actualizacion = CURRENT_TIMESTAMP
        WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':produccion_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':prod_hoy', $cantidadHoy, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Registrar producción en el histórico
    public function registrarProduccionHistorial($datos) {
        $sql = "INSERT INTO produccion_historial (
            produccion_id, pedido_id, producto_id, numero_pedido, item_code,
            cantidad_producida, fecha_produccion, supervisor, observaciones
        ) VALUES (
            :produccion_id, :pedido_id, :producto_id, :numero_pedido, :item_code,
            :cantidad_producida, :fecha_produccion, :supervisor, :observaciones
        )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':produccion_id', $datos['produccion_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':pedido_id', $datos['pedido_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':producto_id', $datos['producto_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':numero_pedido', $datos['numero_pedido'] ?? null);
        $stmt->bindValue(':item_code', $datos['item_code'] ?? null);
        $stmt->bindValue(':cantidad_producida', $datos['cantidad_producida'] ?? 0);
        $stmt->bindValue(':fecha_produccion', $datos['fecha_produccion'] ?? date('Y-m-d'));
        $stmt->bindValue(':supervisor', $datos['supervisor'] ?? null);
        $stmt->bindValue(':observaciones', $datos['observaciones'] ?? null);
        return $stmt->execute();
    }

    // Obtener histórico de un ítem de producción
    public function obtenerHistorialProduccion($produccionId) {
        $sql = "SELECT * FROM produccion_historial
                WHERE produccion_id = :produccion_id
                ORDER BY fecha_produccion DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':produccion_id', $produccionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener registros de producción para un pedido
    public function obtenerProduccionPorPedido($pedidoId) {
        $sql = "SELECT * FROM produccion WHERE pedido_id = :pedido_id ORDER BY item_code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':pedido_id', $pedidoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Actualizar estatus de producción
    public function actualizarEstatusProduccion($id, $estatus) {
        $sql = "UPDATE produccion SET
                estatus = :estatus,
                fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':estatus', $estatus);

        return $stmt->execute();
    }

    // Recalcular prod_total a partir del histórico
    public function recalcularProduccionTotal($produccionId) {
        $sql = "UPDATE produccion SET
                prod_total = (
                    SELECT COALESCE(SUM(cantidad_producida), 0)
                    FROM produccion_historial
                    WHERE produccion_id = :produccion_id
                )
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $produccionId, PDO::PARAM_INT);
        $stmt->bindValue(':produccion_id', $produccionId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Obtener estadísticas de producción
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT
                    COUNT(*) as total_items,
                    SUM(CASE WHEN estatus = 'en_produccion' THEN 1 ELSE 0 END) as en_produccion,
                    SUM(CASE WHEN estatus = 'completada' THEN 1 ELSE 0 END) as completada,
                    SUM(CASE WHEN qty_pendiente > 0 THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN qty_pendiente <= 0 AND qty_pendiente != 0 THEN 1 ELSE 0 END) as sobreproducidas,
                    SUM(qty_pendiente) as total_pendiente
                    FROM produccion";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetch();
        } catch (Exception $e) {
            // Si la tabla no existe, retornar valores por defecto
            return [
                'total_items' => 0,
                'en_produccion' => 0,
                'completada' => 0,
                'pendientes' => 0,
                'sobreproducidas' => 0,
                'total_pendiente' => 0
            ];
        }
    }
}
?>
