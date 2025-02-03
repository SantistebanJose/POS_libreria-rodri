<?php
include("bd.php");



if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorConsultasPTMRE($accion);
}

function controladorConsultasPTMRE($accion)
{
    switch ($accion) {
        case 'DETALLEVENTA_VENTA_ID':
            if (isset($_POST["venta_id"])) {
                $venta_id_ = $_POST["venta_id"];
                $result = fnListarDetalleVentaID($venta_id_);
                echo json_encode($result);
            }
            break;
        case 'VENTAIDCLIENTEDEMRD':
            if (isset($_POST["cliente_id"])) {
                $cliente_id_ = $_POST["cliente_id"];
                $result = fnListForDeudaPendientes($cliente_id_);
                echo json_encode($result);
            }
            break;
        case 'PAGOS_ABONADOS_CLIENTE_ID':
            if (isset($_POST["cliente_id"])) {
                $cliente_id_ = $_POST["cliente_id"];
                $result = fnListForAbonosConsolidadoCliente($cliente_id_);
                echo json_encode($result);
            }
            break;
        case 'DETALLE_ABONO_DEUDA_CLIENTEDDRMD':
            if (isset($_POST["abono_id"])) {
                $abono_id = $_POST["abono_id"];
                $result = fnListForAbonosClientePorVentaPagadas($abono_id);
                echo json_encode($result);
            }
            break;
    }
}
function executeQuery(string $query, array $params = []): array
{
    global $conectar;
    try {
        $orden = $conectar->prepare($query);
        $orden->execute($params);
        $datos = $orden->fetchAll(PDO::FETCH_ASSOC);
        $orden->closeCursor();
        return $datos;
    } catch (\Throwable $th) {
        return [];
    }
}

function listarInsumosCompra(): array
{
    $query = "
        SELECT 
            ci.id,
            c.fecha,
            ins.descripcion as nombre,
            ins.parte_postre,
            ci.cantidad,
            u.nombre_corto as medida,
            ci.total as precio
        FROM compra AS c
        JOIN rel_insumo_compra AS ci ON c.id=ci.compra_id
        JOIN insumo AS ins ON ci.insumo_id=ins.id
        JOIN unidad AS u ON u.id=ins.unidad_id
        ORDER BY c.fecha ASC
    ";
    return executeQuery($query);
}

function listarPostres(): array
{
    $query = "SELECT id, nombre, descripcion FROM postre WHERE deleted_at IS NULL";
    return executeQuery($query);
}

function listarMovimientos(): array
{
    $query = "SELECT id, descripcion, ruta_php FROM movimiento WHERE deleted_at IS NULL ORDER BY 1";
    return executeQuery($query);
}

function listarProductosVenta1(): array
{
    $query = "SELECT * FROM view_articulos";
    return executeQuery($query);
}

function listarVentaReservaCorte(): array
{
    $query = "
        SELECT 
            v.id AS venta_id, 
            TO_CHAR(v.created_at, 'YYYY-MM-DD') AS fecha, 
            TO_CHAR(v.created_at, 'HH12:MI:SS AM') AS hora, 
            CONCAT(p.nombres, ' ', p.apellidos) AS cliente, 
            p.telefonomovil as telefonomovil_cliente,
            p.email as email_cliente, 
            p.numero_documento as numero_doc_cliente,
            CONCAT(us.id,'-',usua.nombres, ', ', usua.apellidos) AS usuario, 
            p.id as id_persona,
            v.usuario_id,
            v.total, 
            CASE 
                WHEN v.estado_venta = 'VR' THEN 'VENTA REALIZADA'
                WHEN v.estado_venta = 'R' THEN 'RESERVA'
                ELSE 'Estado Desconocido'
            END AS estado_venta
        FROM venta AS v
        INNER JOIN usuario AS us ON v.usuario_id = us.id
        INNER JOIN persona AS usua ON us.persona_id = usua.id
        INNER JOIN persona AS p ON v.cliente_id = p.id
        WHERE v.deleted_at IS NULL
          AND v.estado_venta <> 'VR';
    ";
    return executeQuery($query);
}

function listarFormaPago(): array
{
    $query = "SELECT id, nombre FROM forma_pago WHERE deleted_at IS NULL order by id";
    return executeQuery($query);
}

function fnListForVentasDiarias(): array
{
    $query = "
            SELECT 
            v.fecha_fin_transaccion,
            v.id AS venta_id, 
            CASE 
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 0 THEN UPPER('Domingo')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 1 THEN UPPER('Lunes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 2 THEN UPPER('Martes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 3 THEN UPPER('Miércoles')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 4 THEN UPPER('Jueves')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 5 THEN UPPER('Viernes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 6 THEN UPPER('Sábado')
            END AS dia_nombre,
            CONCAT(p.nombres, ' ', p.apellidos) AS cliente, 
            TO_CHAR(v.fecha_fin_transaccion, 'YYYY-MM-DD') AS fecha, 
            TO_CHAR(v.fecha_fin_transaccion, 'HH12:MI:SS AM') AS hora, 
            
            p.telefonomovil AS telefonomovil_cliente,
            p.email AS email_cliente, 
            p.numero_documento AS numero_doc_cliente,
            CONCAT(us.id, '-', usua.nombres, ', ', usua.apellidos) AS usuario, 
            p.id AS id_persona,
            v.usuario_id,
            v.monto_venta_final,
            v.total, 
            (v.monto_venta_final-v.total)as perdida_utilidad,
            CASE 
                WHEN v.estado_pago = 'P' THEN 'PAGADO'
                WHEN v.estado_pago = 'C' THEN 'CREDITO'
            END AS estado_pago,
            v.estado_final,
            du.acumulado AS acumulado_deuda
        FROM venta AS v
        LEFT JOIN deuda AS du ON v.id=du.id_venta 
        INNER JOIN usuario AS us ON v.usuario_id = us.id  
        INNER JOIN persona AS usua ON us.persona_id = usua.id
        INNER JOIN persona AS p ON v.cliente_id = p.id
        WHERE v.estado_venta = 'VR' 
        AND v.deleted_at IS NULL
        AND v.fecha_fin_transaccion::DATE = CURRENT_DATE
        ORDER BY v.id desc;
    ";
    return executeQuery($query);
}


function fnListForVentasSemanales(): array
{
    $query = "
            SELECT 
            v.fecha_fin_transaccion,
            v.id AS venta_id, 
            CASE 
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 0 THEN UPPER('Domingo')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 1 THEN UPPER('Lunes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 2 THEN UPPER('Martes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 3 THEN UPPER('Miércoles')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 4 THEN UPPER('Jueves')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 5 THEN UPPER('Viernes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 6 THEN UPPER('Sábado')
            END AS dia_nombre,
            CONCAT(p.nombres, ' ', p.apellidos) AS cliente, 
            TO_CHAR(v.fecha_fin_transaccion, 'YYYY-MM-DD') AS fecha, 
            TO_CHAR(v.fecha_fin_transaccion, 'HH12:MI:SS AM') AS hora, 
            
            p.telefonomovil AS telefonomovil_cliente,
            p.email AS email_cliente, 
            p.numero_documento AS numero_doc_cliente,
            CONCAT(us.id, '-', usua.nombres, ', ', usua.apellidos) AS usuario, 
            v.atencion_final_usuario,
            p.id AS id_persona,
            v.usuario_id,
            v.monto_venta_final,
            v.total, 
            (v.monto_venta_final-v.total)as perdida_utilidad,
            CASE 
                WHEN v.estado_pago = 'P' THEN 'PAGADO'
                WHEN v.estado_pago = 'C' THEN 'CREDITO'
            END AS estado_pago,
            v.estado_final,
            du.acumulado AS acumulado_deuda
            FROM venta AS v
        LEFT JOIN deuda AS du ON v.id=du.id_venta
        INNER JOIN usuario AS us ON v.usuario_id = us.id  
        INNER JOIN persona AS usua ON us.persona_id = usua.id
        INNER JOIN persona AS p ON v.cliente_id = p.id
        WHERE v.estado_venta = 'VR' 
        AND v.deleted_at IS NULL
        --AND v.fecha_fin_transaccion >= date_trunc('week', CURRENT_DATE)
        --AND v.fecha_fin_transaccion < CURRENT_DATE + INTERVAL '1 day'
        ORDER BY v.fecha_fin_transaccion;
    ";
    return executeQuery($query);
}


function fnListarDetalleVentaID($idVenta): array
{
    $query = "
        SELECT 
        rva.id AS rel_venta_articulo_id,
        rva.venta_id,
        rva.articulo_id,
        m.descripcion,
        CASE 
            WHEN ar.dimension_id IS NOT NULL THEN
                CONCAT(ar.nombre, ' (', dim.medida, ')')
            WHEN ar.nombre IS NULL THEN
                m.descripcion
            ELSE
                ar.nombre 
        END as descripcion,
        rva.cantidad,
        rva.precio_unitario_articulo,
        rva.minutos,
        rva.costo_por_minuto,
        rva.sub_total
        FROM rel_venta_articulo AS rva
        JOIN movimiento as m ON rva.movimiento_id = m.id
        LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
        LEFT JOIN dimension AS dim ON ar.dimension_id = dim.id
        WHERE rva.venta_id = :idVenta;";
    return executeQuery($query, params: ['idVenta' => $idVenta]);
}

function fnListForClientesDeuda(): array
{
    $query = "
    SELECT 
        DISTINCT
        cliente.id AS cliente_id,
        cliente.numero_documento,
        cliente.nombres,
        cliente.apellidos,
        cliente.telefonofijo,
        cliente.telefonomovil,
        cliente.email,
        CONCAT(cliente.nombres, ' ', cliente.apellidos) AS cliente, 
        (SELECT SUM (monto-acumulado) FROM deuda WHERE cliente_id=cliente.id) as monto_deuda_pendiente
    FROM 
    persona as cliente
    JOIN deuda as d ON d.cliente_id = cliente.id
    WHERE d.estado='PENDIENTE';
    ";
    return executeQuery($query);
}
function fnListForClientesDeudaPagasAndNoPagadas(): array
{
    $query = "
    SELECT 
        DISTINCT
        cliente.id AS cliente_id,
        cliente.numero_documento,
        cliente.nombres,
        cliente.apellidos,
        cliente.telefonofijo,
        cliente.telefonomovil,
        cliente.email,
        CONCAT(cliente.nombres, ' ', cliente.apellidos) AS cliente, 
        (SELECT SUM (monto-acumulado) FROM deuda WHERE cliente_id=cliente.id) as monto_deuda_pendiente
    FROM 
    persona as cliente
    JOIN deuda as d ON d.cliente_id = cliente.id;
    --WHERE d.estado='PENDIENTE';
    ";
    return executeQuery($query);
}
function fnListForDeudaPendientes($idCliente): array
{
    $query = "
    SELECT 
    id_venta,
    created_at::date,
    monto,
    CONCAT(created_at::date,' [S/',(monto-acumulado),'] ','<b>',estado,'</b>')as formato,
    acumulado,
    (monto-acumulado) AS deuda_pendiente
    FROM deuda 
    WHERE estado <>'PAGADO'
    AND cliente_id= :id_clientedemrd;
    ";
    return executeQuery($query, params: ['id_clientedemrd' => $idCliente]);
}
function fnListForAbonosConsolidadoCliente($idCliente): array
{
    $query = "
    SELECT 
        d.id_venta AS id_general,
        'id_venta' as nombre_id,
        'PAGO INICIAL' as estacion,
        CONCAT('<b>[Pago Inicial] </b>',' ID VENTA: ',d.id_venta,' - VENTA de ',d.created_at::date,' [S/',monto_inicial,']')as formato,
        CONCAT(c.nombres, ' ', c.apellidos) AS cliente, 

        d.created_at::DATE as fecha,
        TO_CHAR(d.created_at, 'HH12:MI AM') AS hora,
        d.created_at AS fecha_general,
        (SELECT SUM(monto) FROM detalle_forma_pago_deuda WHERE id_deuda=d.id) AS monto,
        (
            SELECT 
            json_agg
            (
                jsonb_build_object(
                    'ID_DETALLE',fpu.id,
                    'FORMA_PAGO', fp.nombre,
                    'MONTO', fpu.monto,
                    'COLOR',fp.color
                )
            ) AS resultado
            FROM detalle_forma_pago_deuda fpu
            JOIN forma_pago fp ON fpu.id_forma_pago = fp.id
            WHERE fpu.id_deuda=d.id
        )::JSON AS js_detalle_forma_pago,
        d.estado as estado_deuda,
        d.monto as monto_deuda
    FROM deuda d
    JOIN persona as c ON c.id=d.cliente_id AND d.monto_inicial>0
    WHERE d.cliente_id=:idCliente

    UNION ALL
    -- ABONOS DE CLIENTES 
    SELECT 
        a.id as id_general,
        'abono_id',
        'ABONO' as estacion,

        --CONCAT('ID A: ',a.id,' - ', a.created_at::DATE,' [',c.nombres, ' ', c.apellidos,'] - S/',a.monto) AS formato,
        CONCAT('<b>[Pago de Abono] </b>',' ID ABONO: ',a.id,' - ABONO de ', a.created_at::DATE,' [S/',a.monto,']') AS formato2,
        CONCAT(c.nombres, ' ', c.apellidos) AS cliente, 
        a.created_at::date AS fecha,
        TO_CHAR(a.created_at, 'HH12:MI AM') AS hora,
        a.created_at AS fecha_general,
        a.monto,
        (
            SELECT 
            json_agg
            (
                jsonb_build_object(
                    'ID_DETALLE',fpa.id,
                    'FORMA_PAGO', fp.nombre,
                    'MONTO', fpa.monto,
                    'COLOR',fp.color
                )
            ) AS resultado
            FROM detalle_forma_pago_abono fpa
            JOIN forma_pago fp ON fpa.forma_pago_id = fp.id
            WHERE fpa.id_abono=a.id
        )::JSON as js_detalle_forma_pago,
        'ABONADO' AS estad_,
        0 as monto_deuda
    FROM abono AS a
    JOIN persona as c ON c.id=a.cliente_id
    WHERE c.id=:idCliente 
    order by 8 desc
    limit 10;
    ";
    return executeQuery($query, params: ['idCliente' => $idCliente]);
}
function fnListForAbonosCliente($idCliente): array
{
    $query = "
    SELECT 
    a.id as abono_id,
    CONCAT('ID A: ',a.id,' - ', a.created_at::DATE,' [',c.nombres, ' ', c.apellidos,'] - S/',a.monto) AS formato,
    CONCAT('ID A: ',a.id,' - ', a.created_at::DATE,' [S/',a.monto,']') AS formato2,
    CONCAT(c.nombres, ' ', c.apellidos) AS cliente, 
    a.created_at::date AS fecha,
    TO_CHAR(a.created_at, 'HH12:MI AM') AS hora,
    a.monto
    FROM
    abono AS a
    JOIN persona as c ON c.id=a.cliente_id
    WHERE c.id=:id_clientedemrd ;

    ";
    return executeQuery($query, params: ['id_clientedemrd' => $idCliente]);
}

function fnListForAbonosClientePorVentaPagadas($idAbono): array
{
    $query = "
    SELECT 
    --d.id AS id_deuda,
    d.id_venta,
    ad.abono_id,
    d.created_at::date as fecha,
    ad.monto,
    CONCAT('<b>ID VENTA: ',d.id_venta,'</b> - VENTA de ',d.created_at::date,' [S/',ad.monto,'] ','',ad.estado_momento,'')as formato,
    (d.monto-d.acumulado) AS deuda_pendiente
    FROM deuda as d
    JOIN abono_deuda AS ad ON ad.deuda_id=d.id
    WHERE ad.abono_id=:abono_id
    ORDER BY 1;
    ";
    return executeQuery($query, params: ['abono_id' => $idAbono]);
}
