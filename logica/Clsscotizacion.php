<?php
/**
 * clssCotizacion.php
 * Manejo completo de Cotizaciones Escolares con PostgreSQL.
 * Requiere: conexión PDO disponible (ajusta getConexion() a tu proyecto).
 */

/* ──────────────────────────────────────────────
   HELPER: conexión PDO
   Ajusta host, dbname, user, password según tu proyecto.
   Si tu proyecto ya tiene una función de conexión, reemplaza
   getConexion() por la tuya.
────────────────────────────────────────────── */
function getConexion(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        // ── AJUSTA ESTOS DATOS ──
        $host   = 'localhost';
        $port   = '5432';
        $dbname = 'sistema_libreria_rodri';
        $user   = 'postgres';
        $pass   = '76008509';
        // ────────────────────────

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

/* ──────────────────────────────────────────────
   RESPUESTA JSON
────────────────────────────────────────────── */
function jsonResponse(bool $ok, $data = null, string $msg = ''): void {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => $ok, 'data' => $data, 'msg' => $msg]);
    exit;
}

/* ══════════════════════════════════════════════
   GUARDAR / ACTUALIZAR COTIZACIÓN
   Recibe JSON desde el frontend.
   Si el código ya existe → UPDATE, sino → INSERT.
══════════════════════════════════════════════ */
function guardarCotizacion(array $payload): void {
    $pdo = getConexion();

    $codigo       = trim($payload['codigo']       ?? '');
    $nombreLista  = trim($payload['nombreLista']  ?? 'Sin nombre');
    $clienteId    = $payload['cliente']['id']     ?? null;
    $clienteJson  = isset($payload['cliente']) ? json_encode($payload['cliente']) : null;
    $descuentoPct = (float)($payload['descuentoPct']   ?? 0);
    $descuentoMonto = (float)($payload['descuentoMonto'] ?? 0);
    $subtotal     = (float)($payload['subtotal']   ?? 0);
    $total        = (float)($payload['total']      ?? 0);
    $notas        = trim($payload['notas']         ?? '');
    $estado       = $payload['estado']             ?? 'pendiente';
    $items        = $payload['items']              ?? [];

    if (!$codigo) {
        jsonResponse(false, null, 'Código de cotización requerido.');
    }

    $estadosValidos = ['pendiente', 'aprobada', 'convertida', 'cancelada'];
    if (!in_array($estado, $estadosValidos)) {
        $estado = 'pendiente';
    }

    try {
        $pdo->beginTransaction();

        // ── ¿Existe? ──
        $stmtCheck = $pdo->prepare("SELECT id FROM cotizaciones WHERE codigo = :codigo");
        $stmtCheck->execute([':codigo' => $codigo]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            // UPDATE cabecera
            $cotizacionId = $existing['id'];
            $pdo->prepare("
                UPDATE cotizaciones SET
                    nombre_lista    = :nombre_lista,
                    cliente_id      = :cliente_id,
                    cliente_json    = :cliente_json,
                    descuento_pct   = :descuento_pct,
                    descuento_monto = :descuento_monto,
                    subtotal        = :subtotal,
                    total           = :total,
                    notas           = :notas,
                    estado          = :estado
                WHERE id = :id
            ")->execute([
                ':nombre_lista'    => $nombreLista,
                ':cliente_id'      => $clienteId,
                ':cliente_json'    => $clienteJson,
                ':descuento_pct'   => $descuentoPct,
                ':descuento_monto' => $descuentoMonto,
                ':subtotal'        => $subtotal,
                ':total'           => $total,
                ':notas'           => $notas,
                ':estado'          => $estado,
                ':id'              => $cotizacionId,
            ]);

            // Borrar ítems viejos y reinsertar
            $pdo->prepare("DELETE FROM cotizacion_items WHERE cotizacion_id = :id")
                ->execute([':id' => $cotizacionId]);

        } else {
            // INSERT cabecera
            $fechaCreacion = $payload['fechaCreacion'] ?? date('c');
            $stmt = $pdo->prepare("
                INSERT INTO cotizaciones
                    (codigo, nombre_lista, cliente_id, cliente_json, descuento_pct,
                     descuento_monto, subtotal, total, notas, estado, fecha_creacion)
                VALUES
                    (:codigo, :nombre_lista, :cliente_id, :cliente_json, :descuento_pct,
                     :descuento_monto, :subtotal, :total, :notas, :estado, :fecha_creacion)
                RETURNING id
            ");
            $stmt->execute([
                ':codigo'          => $codigo,
                ':nombre_lista'    => $nombreLista,
                ':cliente_id'      => $clienteId,
                ':cliente_json'    => $clienteJson,
                ':descuento_pct'   => $descuentoPct,
                ':descuento_monto' => $descuentoMonto,
                ':subtotal'        => $subtotal,
                ':total'           => $total,
                ':notas'           => $notas,
                ':estado'          => $estado,
                ':fecha_creacion'  => $fechaCreacion,
            ]);
            $cotizacionId = $stmt->fetchColumn();
        }

        // ── INSERT ítems ──
        $stmtItem = $pdo->prepare("
            INSERT INTO cotizacion_items
                (cotizacion_id, producto_id, descripcion, categoria,
                 cantidad, precio_unit, es_manual, orden)
            VALUES
                (:cotizacion_id, :producto_id, :descripcion, :categoria,
                 :cantidad, :precio_unit, :es_manual, :orden)
        ");

        foreach ($items as $i => $item) {
            $stmtItem->execute([
                ':cotizacion_id' => $cotizacionId,
                ':producto_id'   => $item['productoId'] ?? null,
                ':descripcion'   => $item['descripcion'] ?? '',
                ':categoria'     => $item['categoria']   ?? '',
                ':cantidad'      => (float)($item['cantidad']  ?? 1),
                ':precio_unit'   => (float)($item['precioUnit'] ?? 0),
                ':es_manual'     => ($item['esManual'] ?? false) ? 'true' : 'false',
                ':orden'         => $i,
            ]);
        }

        $pdo->commit();
        jsonResponse(true, ['cotizacion_id' => $cotizacionId], 'Cotización guardada correctamente.');

    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(false, null, 'Error al guardar: ' . $e->getMessage());
    }
}

/* ══════════════════════════════════════════════
   LISTAR COTIZACIONES (historial)
   Parámetros opcionales: estado, limit, offset
══════════════════════════════════════════════ */
function listarCotizaciones(string $estado = '', int $limit = 100, int $offset = 0): void {
    $pdo = getConexion();

    $where  = '';
    $params = [];

    if ($estado && in_array($estado, ['pendiente','aprobada','convertida','cancelada'])) {
        $where  = 'WHERE c.estado = :estado';
        $params[':estado'] = $estado;
    }

    $sql = "
        SELECT
            c.id,
            c.codigo,
            c.nombre_lista,
            c.cliente_id,
            c.cliente_json,
            c.descuento_pct,
            c.descuento_monto,
            c.subtotal,
            c.total,
            c.notas,
            c.estado,
            c.fecha_creacion,
            c.fecha_actualizacion,
            COUNT(ci.id)      AS total_items,
            SUM(ci.cantidad)  AS total_unidades
        FROM cotizaciones c
        LEFT JOIN cotizacion_items ci ON ci.cotizacion_id = c.id
        $where
        GROUP BY c.id
        ORDER BY c.fecha_creacion DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll();

    // Decodificar cliente_json para que el JS lo reciba como objeto
    foreach ($rows as &$row) {
        $row['cliente'] = $row['cliente_json'] ? json_decode($row['cliente_json'], true) : null;
        unset($row['cliente_json']);
        $row['total_items']    = (int)$row['total_items'];
        $row['total_unidades'] = (float)$row['total_unidades'];
    }

    jsonResponse(true, $rows);
}

/* ══════════════════════════════════════════════
   OBTENER COTIZACIÓN COMPLETA (cabecera + ítems)
══════════════════════════════════════════════ */
function obtenerCotizacion(string $codigo): void {
    $pdo = getConexion();

    $stmt = $pdo->prepare("
        SELECT
            c.*,
            c.cliente_json
        FROM cotizaciones c
        WHERE c.codigo = :codigo
    ");
    $stmt->execute([':codigo' => $codigo]);
    $cot = $stmt->fetch();

    if (!$cot) {
        jsonResponse(false, null, 'Cotización no encontrada.');
    }

    // Ítems
    $stmtItems = $pdo->prepare("
        SELECT *
        FROM cotizacion_items
        WHERE cotizacion_id = :id
        ORDER BY orden ASC
    ");
    $stmtItems->execute([':id' => $cot['id']]);
    $items = $stmtItems->fetchAll();

    // Mapear ítems al formato que espera el JS
    $itemsMapeados = array_map(function($item) {
        return [
            'id'          => $item['id'],
            'productoId'  => $item['producto_id'],
            'descripcion' => $item['descripcion'],
            'categoria'   => $item['categoria'],
            'cantidad'    => (float)$item['cantidad'],
            'precioUnit'  => (float)$item['precio_unit'],
            'esManual'    => (bool)$item['es_manual'],
        ];
    }, $items);

    $cot['cliente'] = $cot['cliente_json'] ? json_decode($cot['cliente_json'], true) : null;
    unset($cot['cliente_json']);
    $cot['items'] = $itemsMapeados;

    jsonResponse(true, $cot);
}

/* ══════════════════════════════════════════════
   CAMBIAR ESTADO DE UNA COTIZACIÓN
══════════════════════════════════════════════ */
function cambiarEstadoCotizacion(string $codigo, string $nuevoEstado): void {
    $estadosValidos = ['pendiente', 'aprobada', 'convertida', 'cancelada'];
    if (!in_array($nuevoEstado, $estadosValidos)) {
        jsonResponse(false, null, 'Estado inválido.');
    }

    $pdo  = getConexion();
    $stmt = $pdo->prepare("
        UPDATE cotizaciones SET estado = :estado
        WHERE codigo = :codigo
        RETURNING id
    ");
    $stmt->execute([':estado' => $nuevoEstado, ':codigo' => $codigo]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(false, null, 'Cotización no encontrada.');
    }
    jsonResponse(true, null, "Estado actualizado a '$nuevoEstado'.");
}

/* ══════════════════════════════════════════════
   ELIMINAR COTIZACIÓN (y sus ítems en cascada)
══════════════════════════════════════════════ */
function eliminarCotizacion(string $codigo): void {
    $pdo  = getConexion();
    $stmt = $pdo->prepare("DELETE FROM cotizaciones WHERE codigo = :codigo RETURNING id");
    $stmt->execute([':codigo' => $codigo]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(false, null, 'Cotización no encontrada.');
    }
    jsonResponse(true, null, 'Cotización eliminada.');
}

/* ══════════════════════════════════════════════
   ELIMINAR TODAS (limpiar historial)
══════════════════════════════════════════════ */
function limpiarTodasCotizaciones(): void {
    $pdo = getConexion();
    $pdo->exec("DELETE FROM cotizaciones");   // CASCADE elimina los ítems
    jsonResponse(true, null, 'Historial limpiado.');
}


/* ══════════════════════════════════════════════
   ENRUTADOR — punto de entrada vía AJAX
   Llamar como: logica/clssCotizacion.php
   con parámetro POST/GET: accion
══════════════════════════════════════════════ */
if (isset($_REQUEST['accion'])) {
    $accion = strtoupper(trim($_REQUEST['accion']));

    switch ($accion) {

        case 'GUARDAR':
            $raw     = file_get_contents('php://input');
            $payload = json_decode($raw, true);
            if (!$payload) {
                // Fallback: si viene como form-data
                $payload = $_POST;
                if (isset($payload['items'])) $payload['items'] = json_decode($payload['items'], true);
                if (isset($payload['cliente'])) $payload['cliente'] = json_decode($payload['cliente'], true);
            }
            guardarCotizacion($payload);
            break;

        case 'LISTAR':
            $estado = $_GET['estado'] ?? '';
            $limit  = (int)($_GET['limit']  ?? 100);
            $offset = (int)($_GET['offset'] ?? 0);
            listarCotizaciones($estado, $limit, $offset);
            break;

        case 'OBTENER':
            $codigo = trim($_GET['codigo'] ?? '');
            if (!$codigo) jsonResponse(false, null, 'Código requerido.');
            obtenerCotizacion($codigo);
            break;

        case 'CAMBIAR_ESTADO':
            $codigo = trim($_POST['codigo'] ?? '');
            $estado = trim($_POST['estado'] ?? '');
            if (!$codigo || !$estado) jsonResponse(false, null, 'Parámetros incompletos.');
            cambiarEstadoCotizacion($codigo, $estado);
            break;

        case 'ELIMINAR':
            $codigo = trim($_POST['codigo'] ?? $_GET['codigo'] ?? '');
            if (!$codigo) jsonResponse(false, null, 'Código requerido.');
            eliminarCotizacion($codigo);
            break;

        case 'LIMPIAR_TODO':
            limpiarTodasCotizaciones();
            break;

        default:
            jsonResponse(false, null, "Acción '$accion' no reconocida.");
    }
}