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
        case 'BUSQUEDAD_PROVEEDOR':
            if (isset($_POST["cadenaBusqueda"])) {
                $cadena = $_POST["cadenaBusqueda"];
                $result = fnListadoProveedores($cadena);

                // Si no hay resultados, devuelves un array vacío
                echo json_encode($result ? $result : []);
            }
            break;
        case 'BUSQUEDAD_FILTRO_ARTICULOS':
            if (isset($_POST["cadenaBusqueda"])) {
                $cadena = $_POST["cadenaBusqueda"];
                $result = fnListadoProductos($cadena);

                echo json_encode($result ? $result : []);
            }
            break;
            //
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

function listarUsuarios(): array
{
    $query = "
        SELECT 
            u.id, 
           u.username, 
            CASE 
                WHEN  u.rol = '1' THEN 'ADMINISTRADOR'
                ELSE 'EMPLEADO'
            END AS rol,
            CONCAT(p.numero_documento, ' - ', p.nombres, ' ', p.apellidos) AS persona_concatenada,
            CASE 
                WHEN u.deleted_at IS NULL THEN 'ACTIVO'
                ELSE 'BLOQUEADO'
            END AS estado
        FROM 
            usuario AS u
        INNER JOIN 
            persona AS p 
        ON 
            u.persona_id = p.id
        order BY u.id;
    ";
    return executeQuery($query);
}

function listarPersonas(): array
{
    $query = "
        select id,
        numero_documento, 
        tipo_persona,
        condicion, 
        nombres ,
        apellidos,
        fecha_nacimiento,
        telefonofijo,
        telefonomovil,
        email,
        direccion,
        nombre_comercial, 
        razon_social ,deleted_at
        from persona 
        where deleted_at is null   
        order BY id;
    ";
    return executeQuery($query);
}


function listarPostres(): array
{
    $query = "SELECT id, nombre, descripcion FROM postre WHERE deleted_at IS NULL";
    return executeQuery($query);
}
function listarCategoria(): array
{
    $query = "SELECT id, abreviatura FROM categoria WHERE deleted_at IS NULL ORDER BY 1";
    return executeQuery($query);
}
function listarDimension(): array
{
    $query = "SELECT id, medida FROM dimension WHERE deleted_at IS NULL ORDER BY 1";
    return executeQuery($query);
}
function listarEscala(): array
{
    $query = "SELECT id, abreviatura FROM escala WHERE deleted_at IS NULL ORDER BY 1";
    return executeQuery($query);
}
function listarTipoArticulos(): array
{
    $query = "SELECT id, abreviatura FROM tipo WHERE deleted_at IS NULL ORDER BY 1";
    return executeQuery($query);
}

function listarMovimientos(): array
{
    $query = "SELECT id, descripcion, ruta_php FROM movimiento WHERE deleted_at IS NULL ORDER BY 1";
    return executeQuery($query);
}

function listarProductosVenta1(): array
{
    $query = "SELECT * FROM view_articulos WHERE precio_venta is not null;";
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
    $query = "SELECT *,updated_at::date as fecha, TO_CHAR(updated_at, 'HH12:MI:SS AM') as hora FROM forma_pago WHERE deleted_at IS NULL order by id";
    return executeQuery($query);
}
function listarEmpleados(): array
{
    $query = "
    SELECT 
        p.id, 
        p.numero_documento, 
        CASE 
            WHEN p.nombres IS NOT NULL AND p.apellidos IS NOT NULL THEN CONCAT(nombres, ' ', apellidos)
            WHEN p.razon_social IS NOT NULL THEN p.razon_social
            ELSE '-'
        END AS empleado, 
        COALESCE(p.condicion, '-') AS condicion, 
        COALESCE(p.telefonomovil, COALESCE(p.telefonofijo, '-')) AS telefono, 
        p.deleted_at 
    FROM persona p
    JOIN usuario u on p.id = u.persona_id
    WHERE p.deleted_at IS NULL AND u.deleted_at is null;
    ";
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
            v.atencion_final_usuario,
            p.id AS id_persona,
            v.usuario_id,
            v.monto_venta_final,
            v.total, 
            (v.total - v.monto_venta_final)as perdida_utilidad,
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
        LEFT JOIN persona AS p ON v.cliente_id = p.id
        WHERE v.estado_venta = 'VR' 
        AND v.deleted_at IS NULL
        AND v.fecha_fin_transaccion::DATE = current_date
        --AND v.fecha_fin_transaccion >= date_trunc('week', CURRENT_DATE)
        --AND v.fecha_fin_transaccion < CURRENT_DATE + INTERVAL '1 day'
        ORDER BY v.fecha_fin_transaccion;
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
            (v.total - v.monto_venta_final)as perdida_utilidad,
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
        LEFT JOIN persona AS p ON v.cliente_id = p.id
        WHERE v.estado_venta = 'VR' 
        AND v.deleted_at IS NULL
        AND v.fecha_fin_transaccion >= date_trunc('week', CURRENT_DATE)
        AND v.fecha_fin_transaccion < CURRENT_DATE + INTERVAL '1 day'
        ORDER BY v.fecha_fin_transaccion;
    ";
    return executeQuery($query);
}
function fnListForVentasTodasLasVentas(): array
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
            (v.total - v.monto_venta_final)as perdida_utilidad,
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
        LEFT JOIN persona AS p ON v.cliente_id = p.id
        WHERE v.estado_venta = 'VR' 
        AND v.deleted_at IS NULL
        --AND v.fecha_fin_transaccion >= CURRENT_DATE - INTERVAL '3 months'
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

function fnListForPagos(): array
{
    $query = "   
            SELECT 
            p.created_at,
            p.id as pago_id,
            concat('P',LPAD(p.id::TEXT,10,'0'),'F',to_char(p.created_at::date, 'YYYYMMDD')) as codigo,
            CASE 
                WHEN EXTRACT(DOW FROM p.created_at) = 0 THEN UPPER('Domingo')
                WHEN EXTRACT(DOW FROM p.created_at) = 1 THEN UPPER('Lunes')
                WHEN EXTRACT(DOW FROM p.created_at) = 2 THEN UPPER('Martes')
                WHEN EXTRACT(DOW FROM p.created_at) = 3 THEN UPPER('Miércoles')
                WHEN EXTRACT(DOW FROM p.created_at) = 4 THEN UPPER('Jueves')
                WHEN EXTRACT(DOW FROM p.created_at) = 5 THEN UPPER('Viernes')
                WHEN EXTRACT(DOW FROM p.created_at) = 6 THEN UPPER('Sábado')
            END AS dia_nombre,
            
            TO_CHAR(p.created_at, 'YYYY-MM-DD') as fecha,
            TO_CHAR(p.created_at, 'HH12:MI:SS AM') as hora,
            p.monto_venta_original,
            p.monto_venta_final,
            (p.monto_venta_original - p.monto_venta_final) AS utilidad,
            (
                SELECT 
                    jsonb_build_object(
                        'venta_id', v.id,
                        'fecha_fin_transaccion', v.fecha_fin_transaccion,
                        'dia_nombre', CASE 
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 0 THEN UPPER('Domingo')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 1 THEN UPPER('Lunes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 2 THEN UPPER('Martes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 3 THEN UPPER('Miércoles')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 4 THEN UPPER('Jueves')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 5 THEN UPPER('Viernes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 6 THEN UPPER('Sábado')
                        END,
                        'cliente', CONCAT(ci.nombres, ' ', ci.apellidos),
                        'fecha', TO_CHAR(v.fecha_fin_transaccion, 'YYYY-MM-DD'),
                        'hora', TO_CHAR(v.fecha_fin_transaccion, 'HH12:MI:SS AM'),
                        'telefonomovil_cliente', ci.telefonomovil,
                        'email_cliente', ci.email,
                        'numero_doc_cliente', ci.numero_documento,
                        'usuario', CONCAT(us.id, '-', usua.nombres, ', ', usua.apellidos),
                        'id_persona', ci.id,
                        'usuario_id', v.usuario_id,
                        'monto_venta_final', v.monto_venta_final,
                        'total', v.total,
                        'perdida_utilidad', (v.monto_venta_final - v.total),
                        'estado_pago', CASE 
                            WHEN v.estado_pago = 'P' THEN 'PAGADO'
                            WHEN v.estado_pago = 'C' THEN 'CREDITO'
                        END,
                        'estado_final', v.estado_final,
                        'acumulado_deuda', du.acumulado
                ) AS resultado_json
                FROM venta AS v
                LEFT JOIN deuda AS du ON v.id = du.id_venta 
                INNER JOIN usuario AS us ON v.usuario_id = us.id  
                INNER JOIN persona AS usua ON us.persona_id = usua.id
                INNER JOIN persona AS ci ON v.cliente_id = ci.id
                WHERE v.id = p.id_venta
                --WHERE v.estado_venta = 'VR' 
                AND v.deleted_at IS NULL
            ) as js_venta,
            (
                SELECT jsonb_agg(
                            jsonb_build_object(
                                'rel_venta_articulo_id', rva.id,
                                'venta_id', rva.venta_id,
                                'articulo_id', rva.articulo_id,
                                'descripcion_movimiento', m.descripcion,
                                'descripcion_articulo', CASE 
                                    WHEN ar.dimension_id IS NOT NULL THEN CONCAT(ar.nombre, ' (', dim.medida, ')')
                                    WHEN ar.nombre IS NULL THEN m.descripcion
                                    ELSE ar.nombre 
                                END,
                                'cantidad', rva.cantidad,
                                'precio_unitario_articulo', rva.precio_unitario_articulo,
                                'minutos', rva.minutos,
                                'costo_por_minuto', rva.costo_por_minuto,
                                'sub_total', rva.sub_total
                            )
                        ) AS resultado_json
                FROM rel_venta_articulo AS rva
                JOIN movimiento AS m ON rva.movimiento_id = m.id 
                LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
                LEFT JOIN dimension AS dim ON ar.dimension_id = dim.id
                WHERE rva.venta_id = p.id_venta
            )as js_detalle_venta,
            (
                SELECT 
                json_agg
                (
                    jsonb_build_object(
                        'ID_DETALLE',dfp.id,
                        'FORMA_PAGO', fp.nombre,
                        'MONTO', dfp.monto,
                        'COLOR',fp.color
                    )
                ) AS resultado
                FROM detalle_forma_pago dfp
                JOIN forma_pago fp ON dfp.id_forma_pago = fp.id
                WHERE dfp.id_venta = p.id_venta
            ) as js_detalle_forma_pago
            FROM pago p
            -- WHERE p.id = 2
            where p.created_at::date = current_date
            order by p.created_at desc;
    ";
    return executeQuery($query);
}


function fnListForPagosSemanales(): array
{

    $query = "   
            SELECT 
            p.created_at,
            p.id as pago_id,
            concat('P',LPAD(p.id::TEXT,10,'0'),'F',to_char(p.created_at::date, 'YYYYMMDD')) as codigo,
            CASE 
                WHEN EXTRACT(DOW FROM p.created_at) = 0 THEN UPPER('Domingo')
                WHEN EXTRACT(DOW FROM p.created_at) = 1 THEN UPPER('Lunes')
                WHEN EXTRACT(DOW FROM p.created_at) = 2 THEN UPPER('Martes')
                WHEN EXTRACT(DOW FROM p.created_at) = 3 THEN UPPER('Miércoles')
                WHEN EXTRACT(DOW FROM p.created_at) = 4 THEN UPPER('Jueves')
                WHEN EXTRACT(DOW FROM p.created_at) = 5 THEN UPPER('Viernes')
                WHEN EXTRACT(DOW FROM p.created_at) = 6 THEN UPPER('Sábado')
            END AS dia_nombre,
            
            TO_CHAR(p.created_at, 'YYYY-MM-DD') as fecha,
            TO_CHAR(p.created_at, 'HH12:MI:SS AM') as hora,
            p.monto_venta_original,
            p.monto_venta_final,
            (p.monto_venta_original - p.monto_venta_final) AS utilidad,
            (
                SELECT 
                    jsonb_build_object(
                        'venta_id', v.id,
                        'fecha_fin_transaccion', v.fecha_fin_transaccion,
                        'dia_nombre', CASE 
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 0 THEN UPPER('Domingo')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 1 THEN UPPER('Lunes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 2 THEN UPPER('Martes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 3 THEN UPPER('Miércoles')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 4 THEN UPPER('Jueves')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 5 THEN UPPER('Viernes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 6 THEN UPPER('Sábado')
                        END,
                        'cliente', CONCAT(ci.nombres, ' ', ci.apellidos),
                        'fecha', TO_CHAR(v.fecha_fin_transaccion, 'YYYY-MM-DD'),
                        'hora', TO_CHAR(v.fecha_fin_transaccion, 'HH12:MI:SS AM'),
                        'telefonomovil_cliente', ci.telefonomovil,
                        'email_cliente', ci.email,
                        'numero_doc_cliente', ci.numero_documento,
                        'usuario', CONCAT(us.id, '-', usua.nombres, ', ', usua.apellidos),
                        'id_persona', ci.id,
                        'usuario_id', v.usuario_id,
                        'monto_venta_final', v.monto_venta_final,
                        'total', v.total,
                        'perdida_utilidad', (v.monto_venta_final - v.total),
                        'estado_pago', CASE 
                            WHEN v.estado_pago = 'P' THEN 'PAGADO'
                            WHEN v.estado_pago = 'C' THEN 'CREDITO'
                        END,
                        'estado_final', v.estado_final,
                        'acumulado_deuda', du.acumulado
                ) AS resultado_json
                FROM venta AS v
                LEFT JOIN deuda AS du ON v.id = du.id_venta 
                INNER JOIN usuario AS us ON v.usuario_id = us.id  
                INNER JOIN persona AS usua ON us.persona_id = usua.id
                INNER JOIN persona AS ci ON v.cliente_id = ci.id
                WHERE v.id = p.id_venta
                --WHERE v.estado_venta = 'VR' 
                AND v.deleted_at IS NULL
            ) as js_venta,
            (
                SELECT jsonb_agg(
                            jsonb_build_object(
                                'rel_venta_articulo_id', rva.id,
                                'venta_id', rva.venta_id,
                                'articulo_id', rva.articulo_id,
                                'descripcion_movimiento', m.descripcion,
                                'descripcion_articulo', CASE 
                                    WHEN ar.dimension_id IS NOT NULL THEN CONCAT(ar.nombre, ' (', dim.medida, ')')
                                    WHEN ar.nombre IS NULL THEN m.descripcion
                                    ELSE ar.nombre 
                                END,
                                'cantidad', rva.cantidad,
                                'precio_unitario_articulo', rva.precio_unitario_articulo,
                                'minutos', rva.minutos,
                                'costo_por_minuto', rva.costo_por_minuto,
                                'sub_total', rva.sub_total
                            )
                        ) AS resultado_json
                FROM rel_venta_articulo AS rva
                JOIN movimiento AS m ON rva.movimiento_id = m.id 
                LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
                LEFT JOIN dimension AS dim ON ar.dimension_id = dim.id
                WHERE rva.venta_id = p.id_venta
            )as js_detalle_venta,
            (
                SELECT 
                json_agg
                (
                    jsonb_build_object(
                        'ID_DETALLE',dfp.id,
                        'FORMA_PAGO', fp.nombre,
                        'MONTO', dfp.monto,
                        'COLOR',fp.color
                    )
                ) AS resultado
                FROM detalle_forma_pago dfp
                JOIN forma_pago fp ON dfp.id_forma_pago = fp.id
                WHERE dfp.id_venta = p.id_venta
            ) as js_detalle_forma_pago
            FROM pago p
            -- WHERE p.id = 2
            Where p.created_at::date >= date_trunc('week', CURRENT_DATE)
            AND p.created_at::date < CURRENT_DATE + INTERVAL '1 day'
            order by p.created_at desc;
    ";
    return executeQuery($query);
}

function fnListForAllPagos(): array
{

    $query = "   
            SELECT 
            p.created_at,
            p.id as pago_id,
            concat('P',LPAD(p.id::TEXT,10,'0'),'F',to_char(p.created_at::date, 'YYYYMMDD')) as codigo,
            CASE 
                WHEN EXTRACT(DOW FROM p.created_at) = 0 THEN UPPER('Domingo')
                WHEN EXTRACT(DOW FROM p.created_at) = 1 THEN UPPER('Lunes')
                WHEN EXTRACT(DOW FROM p.created_at) = 2 THEN UPPER('Martes')
                WHEN EXTRACT(DOW FROM p.created_at) = 3 THEN UPPER('Miércoles')
                WHEN EXTRACT(DOW FROM p.created_at) = 4 THEN UPPER('Jueves')
                WHEN EXTRACT(DOW FROM p.created_at) = 5 THEN UPPER('Viernes')
                WHEN EXTRACT(DOW FROM p.created_at) = 6 THEN UPPER('Sábado')
            END AS dia_nombre,
            
            TO_CHAR(p.created_at, 'YYYY-MM-DD') as fecha,
            TO_CHAR(p.created_at, 'HH12:MI:SS AM') as hora,
            p.monto_venta_original,
            p.monto_venta_final,
            (p.monto_venta_original - p.monto_venta_final) AS utilidad,
            (
                SELECT 
                    jsonb_build_object(
                        'venta_id', v.id,
                        'fecha_fin_transaccion', v.fecha_fin_transaccion,
                        'dia_nombre', 
                        CASE 
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 0 THEN UPPER('Domingo')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 1 THEN UPPER('Lunes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 2 THEN UPPER('Martes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 3 THEN UPPER('Miércoles')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 4 THEN UPPER('Jueves')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 5 THEN UPPER('Viernes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 6 THEN UPPER('Sábado')
                        END,
                        'cliente', CONCAT(ci.nombres, ' ', ci.apellidos),
                        'fecha', TO_CHAR(v.fecha_fin_transaccion, 'YYYY-MM-DD'),
                        'hora', TO_CHAR(v.fecha_fin_transaccion, 'HH12:MI:SS AM'),
                        'telefonomovil_cliente', ci.telefonomovil,
                        'email_cliente', ci.email,
                        'numero_doc_cliente', ci.numero_documento,
                        'usuario', CONCAT(us.id, '-', usua.nombres, ', ', usua.apellidos),
                        'id_persona', ci.id,
                        'usuario_id', v.usuario_id,
                        'monto_venta_final', v.monto_venta_final,
                        'total', v.total,
                        'perdida_utilidad', (v.monto_venta_final - v.total),
                        'estado_pago', CASE 
                            WHEN v.estado_pago = 'P' THEN 'PAGADO'
                            WHEN v.estado_pago = 'C' THEN 'CREDITO'
                        END,
                        'estado_final', v.estado_final,
                        'acumulado_deuda', du.acumulado
                ) AS resultado_json
                FROM venta AS v
                LEFT JOIN deuda AS du ON v.id = du.id_venta 
                INNER JOIN usuario AS us ON v.usuario_id = us.id  
                INNER JOIN persona AS usua ON us.persona_id = usua.id
                INNER JOIN persona AS ci ON v.cliente_id = ci.id
                WHERE v.id = p.id_venta
                --WHERE v.estado_venta = 'VR' 
                AND v.deleted_at IS NULL
            ) as js_venta,
            (
                SELECT jsonb_agg(
                            jsonb_build_object(
                                'rel_venta_articulo_id', rva.id,
                                'venta_id', rva.venta_id,
                                'articulo_id', rva.articulo_id,
                                'descripcion_movimiento', m.descripcion,
                                'descripcion_articulo', CASE 
                                    WHEN ar.dimension_id IS NOT NULL THEN CONCAT(ar.nombre, ' (', dim.medida, ')')
                                    WHEN ar.nombre IS NULL THEN m.descripcion
                                    ELSE ar.nombre 
                                END,
                                'cantidad', rva.cantidad,
                                'precio_unitario_articulo', rva.precio_unitario_articulo,
                                'minutos', rva.minutos,
                                'costo_por_minuto', rva.costo_por_minuto,
                                'sub_total', rva.sub_total
                            )
                        ) AS resultado_json
                FROM rel_venta_articulo AS rva
                JOIN movimiento AS m ON rva.movimiento_id = m.id 
                LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
                LEFT JOIN dimension AS dim ON ar.dimension_id = dim.id
                WHERE rva.venta_id = p.id_venta
            )as js_detalle_venta,
            (
                SELECT 
                json_agg
                (
                    jsonb_build_object(
                        'ID_DETALLE',dfp.id,
                        'FORMA_PAGO', fp.nombre,
                        'MONTO', dfp.monto,
                        'COLOR',fp.color
                    )
                ) AS resultado
                FROM detalle_forma_pago dfp
                JOIN forma_pago fp ON dfp.id_forma_pago = fp.id
                WHERE dfp.id_venta = p.id_venta
            ) as js_detalle_forma_pago
            FROM pago p
            -- WHERE p.id = 2
            --Where p.created_at::date >= date_trunc('week', CURRENT_DATE)
            --AND p.created_at::date < CURRENT_DATE + INTERVAL '1 day'
            order by p.created_at desc;
    ";
    return executeQuery($query);
}


function fnListadoProveedores($cadena): array
{
    // La consulta SQL para buscar proveedores
    $query = "   
    SELECT id, numero_documento, tipo_persona, condicion, nombre_comercial, razon_social 
    FROM persona 
    WHERE condicion = 'PROVEEDOR' 
    AND (
        upper(nombre_comercial) LIKE upper(:busqueda) OR 
        upper(razon_social) LIKE upper(:busqueda) OR
        numero_documento LIKE :busqueda
    )
     
    AND deleted_at IS NULL LIMIT 10;
    ";

    // Ejecuta la consulta con el parámetro de búsqueda
    return executeQuery($query, params: ['busqueda' => '%' . $cadena . '%']);
}

function fnListadoProductos($cadena): array
{
    // La consulta SQL para buscar proveedores
    $query = "   
    SELECT 
    CASE escala
        WHEN '-' THEN
            CONCAT(articulo,' | Tipo: ',tipo,' | Dimension: ',dimension,' |  STOCK: ',stock::INTEGER,' | ', 'Precio de Venta: S/ ',precio_venta)
        ELSE
            CONCAT(articulo,' | Tipo:',tipo,' | Dimension: ',dimension,' | ',escala,' - STOCK: ',stock::INTEGER,' | ', 'Precio de Venta: S/ ',precio_venta)
    END articulo_formato ,
    *
    FROM view_articulos 
    WHERE articulo LIKE UPPER(:busqueda) LIMIT 10;
    ";

    // Ejecuta la consulta con el parámetro de búsqueda
    return executeQuery($query, params: ['busqueda' => '%' . $cadena . '%']);
}
function fnListadoCompras(): array
{
    // La consulta SQL para buscar proveedores
    $query = "   
    SELECT 
    c.id as compra_id, 
    --c.usuario_id, --AS realizada_por,
    CONCAT (us.nombres,' ',us.apellidos) AS realizada_por,
    CASE 
        WHEN c.proveedor_id IS NOT null THEN
            CONCAT(proveedor.numero_documento,' - ', UPPER(proveedor.nombre_comercial))
        ELSE	
            'SIN REGISTRO DE PROVEEDOR'
    END proveedor,
    proveedor.numero_documento as proveedor_num_doc,
    UPPER(proveedor.nombre_comercial) as nombre_comercial_proveedor,
    --c.proveedor_id,
    CASE 
        WHEN c.fecha IS NULL THEN
            'SIN REGISTRO'
        ELSE
            TO_CHAR(c.fecha, 'YYYY-MM-DD')
    END fecha_compra,
    --c.fecha as fecha_compra,
    c.numero_comprobante,
    CASE 
        WHEN c.total IS NULL THEN
            'SIN REGISTRO'
        ELSE
            CONCAT('S/',' ',c.total)
            ----TO_CHAR(c.total, '999999999.00')
    END total,
    --c.total,
    --js_detalle_compra
    c.created_at::DATE as fecha_registro,
    TO_CHAR(c.created_at, 'HH12:MI:SS AM') as hora,
    js_detalle_compra,
    --c.created_at::TIME as hora,
    c.created_at as fecha_hora_registro
    FROM compra c
    JOIN usuario u ON u.id = c.usuario_id AND c.created_at::DATE >= CURRENT_DATE - INTERVAL '3 months'
    JOIN persona us ON u.persona_id = us.id
    LEFT JOIN persona proveedor ON c.proveedor_id = proveedor.id;
    ";

    // Ejecuta la consulta con el parámetro de búsqueda
    return executeQuery($query);
}

//
function fnListadoCajaChica(): array
{
    // La consulta SQL para buscar proveedores
    $query = "   
    WITH with_detalle_caja AS
    (
        SELECT 
        dc.id as detalle_caja_id,
        dc.caja_id,
        dc.responsable,
        c.titulo as concepto,
        dc.monto,
        dc.created_at,
        dc.created_at::DATE fecha_registro,
        TO_CHAR(dc.created_at, 'HH12:MI:SS AM') as hora_registro,
        dc.tipo_movimiento,
        dc.nota
        FROM detalle_caja_chica dc
        JOIN concepto c ON c.id = dc.concepto_id
        order by dc.id
    )
    SELECT *, 
    CASE
		WHEN saldo IS NULL THEN
			0
		ELSE
			(monto-saldo)
	END as egresos_de_caja,
    COALESCE(saldo,monto) as saldo_v2,
    COALESCE(((monto-saldo)/monto)*100,0)::INTEGER as porcentaje,
    apertura::date as fecha_apertura,
    TO_CHAR(apertura, 'HH12:MI:SS AM') as hora_apertura,
    (
        SELECT 
            json_agg(
                json_build_object(
                    'detalle_caja_id',d.detalle_caja_id,
                    'caja_id', d.detalle_caja_id,
                    'responsable', d.responsable,
                    'concepto', d.concepto,
                    'monto', d.monto,
                    'created_at',d.created_at,
                    'fecha_registro', d.fecha_registro,
                    'hora_registro', d.hora_registro,
                    'tipo_movimiento',d.tipo_movimiento,
                    'nota', d.nota
                )
            )
        FROM with_detalle_caja d WHERE d.caja_id = c.id
    ) as js_detalle_caja
    FROM caja c where cierre IS NULL ORDER BY 1 DESC LIMIT 1;  
    ";

    // Ejecuta la consulta con el parámetro de búsqueda
    return executeQuery($query);
}

function fnListadoCajaChicaCerradas(): array
{
    // La consulta SQL para buscar proveedores
    $query = "   
    WITH with_detalle_caja AS
    (
        SELECT 
        dc.id as detalle_caja_id,
        dc.caja_id,
        dc.responsable,
        c.titulo as concepto,
        dc.monto,
        dc.created_at,
        dc.created_at::DATE fecha_registro,
        TO_CHAR(dc.created_at, 'HH12:MI:SS AM') as hora_registro,
        dc.tipo_movimiento,
        dc.nota
        FROM detalle_caja_chica dc
        JOIN concepto c ON c.id = dc.concepto_id
        order by dc.id
    )
    SELECT *, 
    CASE 
        WHEN EXTRACT(DOW FROM c.apertura) = 0 THEN UPPER('Domingo')
        WHEN EXTRACT(DOW FROM c.apertura) = 1 THEN UPPER('Lunes')
        WHEN EXTRACT(DOW FROM c.apertura) = 2 THEN UPPER('Martes')
        WHEN EXTRACT(DOW FROM c.apertura) = 3 THEN UPPER('Miércoles')
        WHEN EXTRACT(DOW FROM c.apertura) = 4 THEN UPPER('Jueves')
        WHEN EXTRACT(DOW FROM c.apertura) = 5 THEN UPPER('Viernes')
        WHEN EXTRACT(DOW FROM c.apertura) = 6 THEN UPPER('Sábado')
    END dia_semana,
    CASE
        WHEN saldo IS NULL THEN
            0
        ELSE
            (monto-saldo)
    END as egresos_de_caja,
    COALESCE(saldo,monto) saldo_v2,
    COALESCE(((monto-saldo)/monto)*100,0)::INTEGER as porcentaje,
    apertura::date as fecha_apertura,
    cierre::date as fecha_cierre,

    TO_CHAR(apertura, 'HH12:MI:SS AM') as hora_apertura,
    TO_CHAR(cierre, 'HH12:MI:SS AM') as hora_cierre,
    (
        SELECT 
            json_agg(
                json_build_object(
                    'detalle_caja_id',d.detalle_caja_id,
                    'caja_id', d.caja_id,
                    'responsable', d.responsable,
                    'concepto', d.concepto,
                    'monto', d.monto,
                    'created_at',d.created_at,
                    'fecha_registro', d.fecha_registro,
                    'hora_registro', d.hora_registro,
                    'tipo_movimiento',d.tipo_movimiento,
                    'nota', d.nota
                )
            )
        FROM with_detalle_caja d WHERE d.caja_id = c.id
    ) as js_detalle_caja
    FROM caja c 
    where cierre IS NOT NULL AND deleted_at IS null
    ORDER BY 1 DESC;  

    ";

    // Ejecuta la consulta con el parámetro de búsqueda
    return executeQuery($query);
}

function fnListadoConceptosEgresos($tipoCaja): array
{
    
    $query = "     
    SELECT 
    * 
    FROM 
    concepto 
    WHERE id NOT IN (1) AND tipo_caja IN (:tipo_caja,'A')
    AND deleted_at IS null
    ORDER BY orden
    ";
    
    return executeQuery($query,params:["tipo_caja"=>$tipoCaja]);
}
function fnListadoMovimientoCajaGrande(): array
{
    
    $query = "     
    SELECT 
    dc.*,
    case 
        when dc.responsable is null then
            dc.movimiento_caja_v2
        else
            dc.responsable
    end accionado,
    fp.nombre forma_pago,
    CASE 
        WHEN EXTRACT(DOW FROM dc.created_at) = 0 THEN UPPER('Domingo')
        WHEN EXTRACT(DOW FROM dc.created_at) = 1 THEN UPPER('Lunes')
        WHEN EXTRACT(DOW FROM dc.created_at) = 2 THEN UPPER('Martes')
        WHEN EXTRACT(DOW FROM dc.created_at) = 3 THEN UPPER('Miércoles')
        WHEN EXTRACT(DOW FROM dc.created_at) = 4 THEN UPPER('Jueves')
        WHEN EXTRACT(DOW FROM dc.created_at) = 5 THEN UPPER('Viernes')
        WHEN EXTRACT(DOW FROM dc.created_at) = 6 THEN UPPER('Sábado')
    END dia_semana,
    dc.created_at::date as fecha,
    TO_CHAR(dc.created_at, 'HH12:MI:SS AM') as hora
    FROM 
    detalle_caja_grande dc 
    JOIN forma_pago fp ON fp.id=dc.forma_pago_id
    where dc.deleted_at is null -- and dc.tipo_movimiento = 'EGRESO'
    ORDER by 1;
    ";
    
    return executeQuery($query);
}

