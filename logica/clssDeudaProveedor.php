<?php
/**
 * logica/clssDeudaProveedor.php
 * Backend de Deudas a Proveedores — LB Rodri POS
 * Soporte de abonos parciales con saldo acumulado.
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

    /* ════════════════════════════════════════════
       GUARDAR (INSERT o UPDATE)
    ════════════════════════════════════════════ */
    case 'GUARDAR':
        $raw     = file_get_contents('php://input');
        $payload = json_decode($raw, true) ?: $_POST;

        $id          = (int)($payload['id']          ?? 0);
        $proveedor   = trim($payload['proveedor']    ?? '');
        $monto       = (float)($payload['monto']     ?? 0);
        $fechaLimite = trim($payload['fecha_limite'] ?? '');
        $descripcion = trim($payload['descripcion']  ?? '');
        $cuotas      = trim($payload['cuotas']       ?? '');

        if (!$proveedor)   jsonResponse(false, null, 'El nombre del proveedor es requerido.');
        if ($monto <= 0)   jsonResponse(false, null, 'El monto debe ser mayor a cero.');
        if (!$fechaLimite) jsonResponse(false, null, 'La fecha límite de pago es requerida.');

        try {
            $pdo = getConexion();

            if ($id > 0) {
                // Editar: no tocamos monto_pagado ni estado
                $pdo->prepare("
                    UPDATE deudas_proveedor SET
                        proveedor      = :proveedor,
                        monto          = :monto,
                        fecha_limite   = :fecha_limite,
                        descripcion    = :descripcion,
                        cuotas         = :cuotas,
                        actualizado_en = NOW()
                    WHERE id = :id
                ")->execute([
                    ':proveedor'    => $proveedor,
                    ':monto'        => $monto,
                    ':fecha_limite' => $fechaLimite,
                    ':descripcion'  => $descripcion,
                    ':cuotas'       => $cuotas,
                    ':id'           => $id,
                ]);
                jsonResponse(true, ['id' => $id], 'Deuda actualizada correctamente.');

            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO deudas_proveedor
                        (proveedor, monto, monto_pagado, fecha_limite, descripcion, cuotas, estado)
                    VALUES
                        (:proveedor, :monto, 0, :fecha_limite, :descripcion, :cuotas, 'pendiente')
                    RETURNING id
                ");
                $stmt->execute([
                    ':proveedor'    => $proveedor,
                    ':monto'        => $monto,
                    ':fecha_limite' => $fechaLimite,
                    ':descripcion'  => $descripcion,
                    ':cuotas'       => $cuotas,
                ]);
                jsonResponse(true, ['id' => $stmt->fetchColumn()], 'Deuda registrada correctamente.');
            }
        } catch (Exception $e) {
            jsonResponse(false, null, 'Error al guardar: ' . $e->getMessage());
        }
        break;

    /* ════════════════════════════════════════════
       LISTAR
    ════════════════════════════════════════════ */
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
                    id,
                    proveedor,
                    monto                                            AS monto_total,
                    COALESCE(monto_pagado, 0)                        AS monto_pagado,
                    (monto - COALESCE(monto_pagado, 0))              AS saldo_pendiente,
                    ROUND(
                        CASE WHEN monto > 0
                             THEN COALESCE(monto_pagado, 0) / monto * 100
                             ELSE 0
                        END
                    )                                                AS porcentaje_pagado,
                    fecha_limite::text                               AS fecha_limite,
                    descripcion,
                    cuotas,
                    estado,
                    creado_en::text                                  AS creado_en,
                    CASE
                        WHEN estado = 'pagada'                                  THEN 'pagada'
                        WHEN fecha_limite < CURRENT_DATE                        THEN 'vencida'
                        WHEN fecha_limite <= CURRENT_DATE + INTERVAL '7 days'   THEN 'proxima'
                        ELSE 'ok'
                    END                                              AS semaforo,
                    (fecha_limite - CURRENT_DATE)                    AS dias_restantes
                FROM deudas_proveedor
                $where
                ORDER BY
                    CASE estado WHEN 'pagada' THEN 1 ELSE 0 END,
                    fecha_limite ASC
            ")->fetchAll();

            foreach ($rows as &$r) {
                $r['monto_total']       = (float)$r['monto_total'];
                $r['monto_pagado']      = (float)$r['monto_pagado'];
                $r['saldo_pendiente']   = (float)$r['saldo_pendiente'];
                $r['porcentaje_pagado'] = (int)$r['porcentaje_pagado'];
                $r['dias_restantes']    = (int)$r['dias_restantes'];
            }
            jsonResponse(true, $rows);
        } catch (Exception $e) {
            jsonResponse(false, null, $e->getMessage());
        }
        break;

    /* ════════════════════════════════════════════
       ESTADÍSTICAS
    ════════════════════════════════════════════ */
    case 'ESTADISTICAS':
        try {
            $pdo = getConexion();
            $row = $pdo->query("
                SELECT
                    COALESCE(SUM(
                        CASE WHEN estado='pendiente'
                             THEN monto - COALESCE(monto_pagado, 0) END
                    ), 0)                                                            AS total_adeudado,
                    COUNT(CASE WHEN estado='pendiente' AND fecha_limite < CURRENT_DATE
                               THEN 1 END)                                           AS total_vencidas,
                    COUNT(CASE WHEN estado='pendiente'
                               AND fecha_limite BETWEEN CURRENT_DATE
                                                    AND CURRENT_DATE + INTERVAL '7 days'
                               THEN 1 END)                                           AS proximas_7dias,
                    COUNT(CASE WHEN estado='pagada'    THEN 1 END)                   AS total_pagadas,
                    COUNT(CASE WHEN estado='pendiente' THEN 1 END)                   AS total_pendientes
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

    /* ════════════════════════════════════════════
       CAMBIAR ESTADO (manual)
    ════════════════════════════════════════════ */
    case 'CAMBIAR_ESTADO':
        $id     = (int)($_POST['id']    ?? 0);
        $estado = trim($_POST['estado'] ?? '');
        if (!$id || !in_array($estado, ['pendiente', 'pagada']))
            jsonResponse(false, null, 'Parámetros inválidos.');
        try {
            $pdo  = getConexion();
            $stmt = $pdo->prepare("
                UPDATE deudas_proveedor
                SET estado = :estado, actualizado_en = NOW()
                WHERE id = :id
                RETURNING id
            ");
            $stmt->execute([':estado' => $estado, ':id' => $id]);
            if ($stmt->rowCount() === 0) jsonResponse(false, null, 'Deuda no encontrada.');
            $msg = $estado === 'pagada' ? '¡Deuda marcada como pagada!' : 'Deuda reabierta.';
            jsonResponse(true, null, $msg);
        } catch (Exception $e) {
            jsonResponse(false, null, $e->getMessage());
        }
        break;

    /* ════════════════════════════════════════════
       ELIMINAR DEUDA
    ════════════════════════════════════════════ */
    case 'ELIMINAR':
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if (!$id) jsonResponse(false, null, 'ID requerido.');
        try {
            $pdo  = getConexion();
            // Los abonos se eliminan en cascada (ON DELETE CASCADE)
            $stmt = $pdo->prepare("DELETE FROM deudas_proveedor WHERE id = :id RETURNING id");
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() === 0) jsonResponse(false, null, 'Deuda no encontrada.');
            jsonResponse(true, null, 'Deuda eliminada correctamente.');
        } catch (Exception $e) {
            jsonResponse(false, null, $e->getMessage());
        }
        break;

    /* ════════════════════════════════════════════
       LISTAR ABONOS de una deuda
    ════════════════════════════════════════════ */
    case 'LISTAR_ABONOS':
        $deudaId = (int)($_GET['deuda_id'] ?? 0);
        if (!$deudaId) jsonResponse(false, null, 'deuda_id requerido.');
        try {
            $pdo = getConexion();

            $stDeuda = $pdo->prepare("
                SELECT proveedor,
                       monto                              AS monto_total,
                       COALESCE(monto_pagado, 0)          AS monto_pagado,
                       (monto - COALESCE(monto_pagado,0)) AS saldo_pendiente,
                       estado
                FROM deudas_proveedor WHERE id = :id
            ");
            $stDeuda->execute([':id' => $deudaId]);
            $deuda = $stDeuda->fetch();
            if (!$deuda) jsonResponse(false, null, 'Deuda no encontrada.');

            $stAbonos = $pdo->prepare("
                SELECT id, monto, nota, fecha::text AS fecha, creado_en::text AS creado_en
                FROM deudas_abonos
                WHERE deuda_id = :id
                ORDER BY fecha DESC, creado_en DESC
            ");
            $stAbonos->execute([':id' => $deudaId]);
            $abonos = $stAbonos->fetchAll();

            foreach ($abonos as &$a) {
                $a['monto'] = (float)$a['monto'];
            }

            jsonResponse(true, [
                'proveedor'       => $deuda['proveedor'],
                'monto_total'     => (float)$deuda['monto_total'],
                'monto_pagado'    => (float)$deuda['monto_pagado'],
                'saldo_pendiente' => (float)$deuda['saldo_pendiente'],
                'estado'          => $deuda['estado'],
                'abonos'          => $abonos,
            ]);
        } catch (Exception $e) {
            jsonResponse(false, null, $e->getMessage());
        }
        break;

    /* ════════════════════════════════════════════
       AGREGAR ABONO
    ════════════════════════════════════════════ */
    case 'AGREGAR_ABONO':
        $raw     = file_get_contents('php://input');
        $payload = json_decode($raw, true) ?: $_POST;

        $deudaId = (int)($payload['deuda_id'] ?? 0);
        $monto   = (float)($payload['monto']  ?? 0);
        $nota    = trim($payload['nota']       ?? '');
        $fecha   = trim($payload['fecha']      ?? date('Y-m-d'));

        if (!$deudaId)   jsonResponse(false, null, 'deuda_id requerido.');
        if ($monto <= 0) jsonResponse(false, null, 'El monto del abono debe ser mayor a cero.');

        try {
            $pdo = getConexion();
            $pdo->beginTransaction();

            // Bloquear fila para evitar race condition
            $stDeuda = $pdo->prepare("
                SELECT monto, COALESCE(monto_pagado, 0) AS monto_pagado
                FROM deudas_proveedor
                WHERE id = :id
                FOR UPDATE
            ");
            $stDeuda->execute([':id' => $deudaId]);
            $deuda = $stDeuda->fetch();
            if (!$deuda) {
                $pdo->rollBack();
                jsonResponse(false, null, 'Deuda no encontrada.');
            }

            $saldo = round((float)$deuda['monto'] - (float)$deuda['monto_pagado'], 2);
            if ($monto > $saldo + 0.005) {
                $pdo->rollBack();
                jsonResponse(false, null,
                    'El abono (S/ ' . number_format($monto, 2) . ') supera el saldo pendiente (S/ ' . number_format($saldo, 2) . ').'
                );
            }

            // Insertar abono
            $pdo->prepare("
                INSERT INTO deudas_abonos (deuda_id, monto, nota, fecha)
                VALUES (:deuda_id, :monto, :nota, :fecha)
            ")->execute([
                ':deuda_id' => $deudaId,
                ':monto'    => $monto,
                ':nota'     => $nota ?: null,
                ':fecha'    => $fecha,
            ]);

            // Actualizar monto_pagado y recalcular estado
            $nuevoPagado = round((float)$deuda['monto_pagado'] + $monto, 2);
            $nuevoEstado = ($nuevoPagado >= (float)$deuda['monto'] - 0.005) ? 'pagada' : 'pendiente';

            $pdo->prepare("
                UPDATE deudas_proveedor
                SET monto_pagado   = :pagado,
                    estado         = :estado,
                    actualizado_en = NOW()
                WHERE id = :id
            ")->execute([
                ':pagado' => $nuevoPagado,
                ':estado' => $nuevoEstado,
                ':id'     => $deudaId,
            ]);

            $pdo->commit();

            $saldoRestante = round((float)$deuda['monto'] - $nuevoPagado, 2);
            $msg = $nuevoEstado === 'pagada'
                ? '¡Abono registrado! La deuda quedó completamente pagada. 🎉'
                : 'Abono registrado. Saldo restante: S/ ' . number_format($saldoRestante, 2);

            jsonResponse(true, [
                'monto_pagado'    => $nuevoPagado,
                'saldo_pendiente' => $saldoRestante,
                'estado'          => $nuevoEstado,
            ], $msg);

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            jsonResponse(false, null, 'Error al registrar abono: ' . $e->getMessage());
        }
        break;

    /* ════════════════════════════════════════════
       ELIMINAR ABONO
    ════════════════════════════════════════════ */
    case 'ELIMINAR_ABONO':
        $abonoId = (int)($_POST['abono_id'] ?? 0);
        if (!$abonoId) jsonResponse(false, null, 'abono_id requerido.');

        try {
            $pdo = getConexion();
            $pdo->beginTransaction();

            $stAbono = $pdo->prepare("SELECT deuda_id, monto FROM deudas_abonos WHERE id = :id");
            $stAbono->execute([':id' => $abonoId]);
            $abono = $stAbono->fetch();
            if (!$abono) {
                $pdo->rollBack();
                jsonResponse(false, null, 'Abono no encontrado.');
            }

            $pdo->prepare("DELETE FROM deudas_abonos WHERE id = :id")->execute([':id' => $abonoId]);

            // Restar el monto y recalcular estado
            $pdo->prepare("
                UPDATE deudas_proveedor
                SET monto_pagado   = GREATEST(0, monto_pagado - :monto),
                    estado         = CASE
                                         WHEN GREATEST(0, monto_pagado - :monto) >= monto
                                         THEN 'pagada'
                                         ELSE 'pendiente'
                                     END,
                    actualizado_en = NOW()
                WHERE id = :id
            ")->execute([
                ':monto' => $abono['monto'],
                ':id'    => $abono['deuda_id'],
            ]);

            $pdo->commit();
            jsonResponse(true, null, 'Abono eliminado y saldo restaurado.');

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            jsonResponse(false, null, 'Error: ' . $e->getMessage());
        }
        break;

    default:
        jsonResponse(false, null, "Acción '$accion' no reconocida.");
}
