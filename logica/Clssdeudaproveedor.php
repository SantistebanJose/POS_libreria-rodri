<?php
/**
 * logica/clssDeudaProveedor.php
 * Backend de Deudas a Proveedores — LB Rodri POS
 * Mismo patrón que clssCotizacion.php / clssInsertPA.php
 */

function getConexion(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $host   = 'localhost';
        $port   = '5432';
        $dbname = 'sistema_libreria_rodri';
        $user   = 'postgres';
        $pass   = '76008509';
        $dsn    = "pgsql:host=$host;port=$port;dbname=$dbname";
        $pdo    = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function jsonResponse(bool $ok, $data = null, string $msg = ''): void {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => $ok, 'data' => $data, 'msg' => $msg]);
    exit;
}

/* ══════════════════════════════════════════════
   ENRUTADOR
══════════════════════════════════════════════ */
if (!isset($_REQUEST['accion'])) {
    jsonResponse(false, null, 'Acción requerida.');
}

$accion = strtoupper(trim($_REQUEST['accion']));

switch ($accion) {

    /* ── GUARDAR (INSERT o UPDATE) ── */
    case 'GUARDAR':
        $raw     = file_get_contents('php://input');
        $payload = json_decode($raw, true) ?: $_POST;

        $id          = (int)($payload['id']          ?? 0);
        $proveedor   = trim($payload['proveedor']    ?? '');
        $monto       = (float)($payload['monto']     ?? 0);
        $fechaLimite = trim($payload['fecha_limite'] ?? '');
        $descripcion = trim($payload['descripcion']  ?? '');
        $cuotas      = trim($payload['cuotas']       ?? '');
        $estado      = trim($payload['estado']       ?? 'pendiente');

        if (!$proveedor)   jsonResponse(false, null, 'El nombre del proveedor es requerido.');
        if ($monto <= 0)   jsonResponse(false, null, 'El monto debe ser mayor a cero.');
        if (!$fechaLimite) jsonResponse(false, null, 'La fecha límite de pago es requerida.');
        if (!in_array($estado, ['pendiente', 'pagada'])) $estado = 'pendiente';

        try {
            $pdo = getConexion();
            if ($id > 0) {
                $pdo->prepare("
                    UPDATE deudas_proveedor SET
                        proveedor    = :proveedor,
                        monto        = :monto,
                        fecha_limite = :fecha_limite,
                        descripcion  = :descripcion,
                        cuotas       = :cuotas,
                        estado       = :estado
                    WHERE id = :id
                ")->execute([
                    ':proveedor'    => $proveedor,
                    ':monto'        => $monto,
                    ':fecha_limite' => $fechaLimite,
                    ':descripcion'  => $descripcion,
                    ':cuotas'       => $cuotas,
                    ':estado'       => $estado,
                    ':id'           => $id,
                ]);
                jsonResponse(true, ['id' => $id], 'Deuda actualizada correctamente.');
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO deudas_proveedor
                        (proveedor, monto, fecha_limite, descripcion, cuotas, estado)
                    VALUES
                        (:proveedor, :monto, :fecha_limite, :descripcion, :cuotas, :estado)
                    RETURNING id
                ");
                $stmt->execute([
                    ':proveedor'    => $proveedor,
                    ':monto'        => $monto,
                    ':fecha_limite' => $fechaLimite,
                    ':descripcion'  => $descripcion,
                    ':cuotas'       => $cuotas,
                    ':estado'       => $estado,
                ]);
                jsonResponse(true, ['id' => $stmt->fetchColumn()], 'Deuda registrada correctamente.');
            }
        } catch (Exception $e) {
            jsonResponse(false, null, 'Error al guardar: ' . $e->getMessage());
        }
        break;

    /* ── LISTAR ── */
    case 'LISTAR':
        $filtro = trim($_GET['filtro'] ?? 'todas');
        $where  = match($filtro) {
            'pendiente' => "WHERE estado = 'pendiente'",
            'vencida'   => "WHERE estado = 'pendiente' AND fecha_limite < CURRENT_DATE",
            'pagada'    => "WHERE estado = 'pagada'",
            default     => '',
        };
        try {
            $pdo  = getConexion();
            $rows = $pdo->query("
                SELECT
                    id, proveedor, monto,
                    fecha_limite::text  AS fecha_limite,
                    descripcion, cuotas, estado,
                    creado_en::text     AS creado_en,
                    CASE
                        WHEN estado = 'pagada'                                THEN 'pagada'
                        WHEN fecha_limite < CURRENT_DATE                      THEN 'vencida'
                        WHEN fecha_limite <= CURRENT_DATE + INTERVAL '7 days' THEN 'proxima'
                        ELSE 'ok'
                    END                 AS semaforo,
                    (fecha_limite - CURRENT_DATE) AS dias_restantes
                FROM deudas_proveedor
                $where
                ORDER BY
                    CASE estado WHEN 'pagada' THEN 1 ELSE 0 END,
                    fecha_limite ASC
            ")->fetchAll();

            foreach ($rows as &$r) {
                $r['monto']          = (float)$r['monto'];
                $r['dias_restantes'] = (int)$r['dias_restantes'];
            }
            jsonResponse(true, $rows);
        } catch (Exception $e) {
            jsonResponse(false, null, $e->getMessage());
        }
        break;

    /* ── ESTADÍSTICAS ── */
    case 'ESTADISTICAS':
        try {
            $pdo = getConexion();
            $row = $pdo->query("
                SELECT
                    COALESCE(SUM(CASE WHEN estado='pendiente' THEN monto END), 0)                                   AS total_adeudado,
                    COUNT(CASE WHEN estado='pendiente' AND fecha_limite < CURRENT_DATE THEN 1 END)                   AS total_vencidas,
                    COUNT(CASE WHEN estado='pendiente'
                               AND fecha_limite BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL '7 days'
                          THEN 1 END)                                                                               AS proximas_7dias,
                    COUNT(CASE WHEN estado='pagada'    THEN 1 END)                                                  AS total_pagadas,
                    COUNT(CASE WHEN estado='pendiente' THEN 1 END)                                                  AS total_pendientes
                FROM deudas_proveedor
            ")->fetch();
            $row['total_adeudado']   = (float)$row['total_adeudado'];
            $row['total_vencidas']   = (int)$row['total_vencidas'];
            $row['proximas_7dias']   = (int)$row['proximas_7dias'];
            $row['total_pagadas']    = (int)$row['total_pagadas'];
            $row['total_pendientes'] = (int)$row['total_pendientes'];
            jsonResponse(true, $row);
        } catch (Exception $e) {
            jsonResponse(false, null, $e->getMessage());
        }
        break;

    /* ── CAMBIAR ESTADO ── */
    case 'CAMBIAR_ESTADO':
        $id     = (int)($_POST['id']     ?? 0);
        $estado = trim($_POST['estado']  ?? '');
        if (!$id || !in_array($estado, ['pendiente', 'pagada']))
            jsonResponse(false, null, 'Parámetros inválidos.');
        try {
            $pdo  = getConexion();
            $stmt = $pdo->prepare("UPDATE deudas_proveedor SET estado=:estado WHERE id=:id RETURNING id");
            $stmt->execute([':estado' => $estado, ':id' => $id]);
            if ($stmt->rowCount() === 0) jsonResponse(false, null, 'Deuda no encontrada.');
            jsonResponse(true, null, "Estado actualizado a '$estado'.");
        } catch (Exception $e) {
            jsonResponse(false, null, $e->getMessage());
        }
        break;

    /* ── ELIMINAR ── */
    case 'ELIMINAR':
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if (!$id) jsonResponse(false, null, 'ID requerido.');
        try {
            $pdo  = getConexion();
            $stmt = $pdo->prepare("DELETE FROM deudas_proveedor WHERE id=:id RETURNING id");
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() === 0) jsonResponse(false, null, 'Deuda no encontrada.');
            jsonResponse(true, null, 'Deuda eliminada correctamente.');
        } catch (Exception $e) {
            jsonResponse(false, null, $e->getMessage());
        }
        break;

    default:
        jsonResponse(false, null, "Acción '$accion' no reconocida.");
}