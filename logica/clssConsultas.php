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
    $query = "SELECT id, nombre FROM forma_pago WHERE deleted_at IS NULL";
    return executeQuery($query);
}

function fnListForVentasDiarias(): array
{
    $query = "
        SELECT 
            v.id AS venta_id, 
            CASE 
                WHEN EXTRACT(DOW FROM v.created_at) = 0 THEN UPPER('Domingo')
                WHEN EXTRACT(DOW FROM v.created_at) = 1 THEN UPPER('Lunes')
                WHEN EXTRACT(DOW FROM v.created_at) = 2 THEN UPPER('Martes')
                WHEN EXTRACT(DOW FROM v.created_at) = 3 THEN UPPER('Miércoles')
                WHEN EXTRACT(DOW FROM v.created_at) = 4 THEN UPPER('Jueves')
                WHEN EXTRACT(DOW FROM v.created_at) = 5 THEN UPPER('Viernes')
                WHEN EXTRACT(DOW FROM v.created_at) = 6 THEN UPPER('Sábado')
            END AS dia_nombre,
            CONCAT(p.nombres, ' ', p.apellidos) AS cliente, 
            TO_CHAR(v.created_at, 'YYYY-MM-DD') AS fecha, 
            TO_CHAR(v.created_at, 'HH12:MI:SS AM') AS hora, 
            
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
            END AS estado_final_venta,
            v.estado_final
        FROM venta AS v
        INNER JOIN usuario AS us ON v.usuario_id = us.id  
        INNER JOIN persona AS usua ON us.persona_id = usua.id
        INNER JOIN persona AS p ON v.cliente_id = p.id
        WHERE v.estado_venta = 'VR' 
        AND v.deleted_at IS NULL
        AND v.created_at::DATE = CURRENT_DATE
        ORDER BY v.id;
    ";
    return executeQuery($query);
}


function fnListForVentasSemanales(): array
{
    $query = "
        SELECT 
            v.id AS venta_id, 
            CASE 
                WHEN EXTRACT(DOW FROM v.created_at) = 0 THEN UPPER('Domingo')
                WHEN EXTRACT(DOW FROM v.created_at) = 1 THEN UPPER('Lunes')
                WHEN EXTRACT(DOW FROM v.created_at) = 2 THEN UPPER('Martes')
                WHEN EXTRACT(DOW FROM v.created_at) = 3 THEN UPPER('Miércoles')
                WHEN EXTRACT(DOW FROM v.created_at) = 4 THEN UPPER('Jueves')
                WHEN EXTRACT(DOW FROM v.created_at) = 5 THEN UPPER('Viernes')
                WHEN EXTRACT(DOW FROM v.created_at) = 6 THEN UPPER('Sábado')
            END AS dia_nombre,
            CONCAT(p.nombres, ' ', p.apellidos) AS cliente, 
            TO_CHAR(v.created_at, 'YYYY-MM-DD') AS fecha, 
            TO_CHAR(v.created_at, 'HH12:MI:SS AM') AS hora, 
            
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
            END AS estado_final_venta,
            v.estado_final
        FROM venta AS v
        INNER JOIN usuario AS us ON v.usuario_id = us.id  
        INNER JOIN persona AS usua ON us.persona_id = usua.id
        INNER JOIN persona AS p ON v.cliente_id = p.id
        WHERE v.estado_venta = 'VR' 
        AND v.deleted_at IS NULL
        AND v.created_at >= date_trunc('week', CURRENT_DATE)
        AND v.created_at < CURRENT_DATE + INTERVAL '1 day'
        ORDER BY v.id;
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
