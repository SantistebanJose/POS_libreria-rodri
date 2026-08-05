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
        case 'TOTAL_COMPRAS_RANGO':
            $fecha_desde = $_POST['fecha_desde'] ?? null;
            $fecha_hasta = $_POST['fecha_hasta'] ?? null;
            echo json_encode(fnTotalComprasPorRango($fecha_desde, $fecha_hasta));
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
        //EJECUTARETL
        case 'EJECUTARETL':
            if (isset($_POST["EJECUTARETL"])) {
                $cadena = $_POST["EJECUTARETL"];
                $result = fnEjecutarETL();
                echo json_encode($result ? $result : []);
            }
            break;
        
        case 'EJECUTARETLARTICULOSNUBE':
            if (isset($_POST["EJECUTARETLARTICULOSNUBE"])) {
                $cadena = $_POST["EJECUTARETLARTICULOSNUBE"];
                $result = fnEjecutarETLArticulosNube();
                echo json_encode($result ? $result : []);
            }
            break;
        case 'RANKING_CLIENTES':
            $datos = fnRankingClientes();
            echo json_encode($datos);
            break;
        case 'VENTAS_POR_RANGO':
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'];
            echo json_encode(fnListForVentasPorRango($fecha_inicio, $fecha_fin));
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
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        return [];
    }
}
function executeQueryv2($query)
{
    global $conectar;
    try {

        $stmt = $conectar->query($query);

        if (!$stmt) {
            throw new Exception("Error en la consulta SQL");
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        return [];
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}
#nueva funcion
function fnTotalComprasPorRango($desde, $hasta): array {
    // Convertir strings vacíos a NULL explícitamente
    $desde = (empty($desde)) ? null : $desde;
    $hasta = (empty($hasta)) ? null : $hasta;

    if ($desde === null && $hasta === null) {
        // Sin filtro: total histórico completo
        $query = "
            SELECT 
                COUNT(DISTINCT c.id) AS total_compras,
                COALESCE(SUM((item->>'sub_total_')::NUMERIC), 0) AS gran_total_productos
            FROM compra c,
            LATERAL jsonb_array_elements(c.js_detalle_compra::jsonb) AS item
            WHERE c.js_detalle_compra IS NOT NULL
        ";
        return executeQuery($query);
    } else {
        // Con filtro de fechas usando c.fecha (fecha de compra)
        $query = "
            SELECT 
                COUNT(DISTINCT c.id) AS total_compras,
                COALESCE(SUM((item->>'sub_total_')::NUMERIC), 0) AS gran_total_productos
            FROM compra c,
            LATERAL jsonb_array_elements(c.js_detalle_compra::jsonb) AS item
            WHERE c.js_detalle_compra IS NOT NULL
            AND (:desde::DATE IS NULL OR c.fecha >= :desde::DATE)
            AND (:hasta::DATE IS NULL OR c.fecha <= :hasta::DATE)
        ";
        return executeQuery($query, ['desde' => $desde, 'hasta' => $hasta]);
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

function fnListForVentasPorRango($fecha_inicio, $fecha_fin) {
    $query = "
        SELECT 
            v.fecha_fin_transaccion,
            concat('T',LPAD(v.id::TEXT,8,'0'),'-','F',to_char(v.fecha_fin_transaccion::date, 'YYYYMMDD')) as codigo_tiket,
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
            CASE 
                WHEN p.tipo_persona = 'JURIDICA' THEN 
                    COALESCE(p.razon_social, p.nombre_comercial, 'SIN NOMBRE')
                ELSE 
                    CONCAT(p.nombres, ' ', p.apellidos)
            END AS cliente, 
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
            (v.total - v.monto_venta_final) as perdida_utilidad,
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
        AND v.fecha_fin_transaccion::DATE BETWEEN :fecha_inicio AND :fecha_fin
        ORDER BY v.fecha_fin_transaccion DESC
    ";
    
    return executeQuery($query, [
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin' => $fecha_fin
    ]);
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
    $query = "SELECT * FROM movimiento WHERE deleted_at IS NULL AND id NOT IN (1,4,6,15) ORDER BY 1";
    return executeQuery($query);
}

function listarProductosVenta1(): array
{
    $query = "SELECT * FROM view_articulos WHERE precio_venta is not null;";

    return executeQuery($query);
}

function listarTipoArticuloMantenimiento(): array
{
    $query = "select * from tipo";
    return executeQuery($query);
}

function listarDimensionArticuloMantenimiento(): array
{
    $query = "select * from dimension";
    return executeQuery($query);
}

function listarEscalaArticuloMantenimiento(): array
{
    $query = "select * from escala";
    return executeQuery($query);
}

function listarCategoriaArticuloMantenimiento(): array
{
    $query = "select * from categoria";
    return executeQuery($query);
}
function listarArticulosSinStockMin(): array
{
    $query = "SELECT * FROM view_articulos -- WHERE flag_stock_minimo <> 'OK'";
    return executeQuery($query);
}

function listarArticuloSinview(): array
{
    $query = "
    SELECT 
    a.id as articulo_id,a.nombre as articulo,a.precio_venta,a.stock,a.deleted_at,a.corte,a.marca, d.medida as dimension, t.abreviatura as tipo,e.abreviatura as escala,c.abreviatura as categoria, a.disponibilidad_venta_fh,
    a.color,
    CASE 
		WHEN a.color IS NULL THEN
			'SIN COLOR'
		ELSE
			a.color
	END color_v2,
    a.*
    FROM articulo a
    LEFT JOIN categoria c ON c.id = a.categoria_id 
    LEFT JOIN dimension d ON d.id = a.dimension_id 
    LEFT JOIN tipo t ON t.id = a.tipo_id
    LEFT JOIN escala e ON e.id = a.escala_id
    WHERE a.deleted_at is null
    order by a.id";
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

function listarVentasNoDeclaradas()
{
    $sql = "
        --select * from emisor ;
        --update emisor set usuario_sol = 'FACVYSAM', clave_sol = 'Jose04_42696143'

        --delete from comprobante
        --select * from comprobante
        -- COMPROBANTES NO DECLARADOS
        SELECT 
        --cb.*,
        -- otros campos que ya tienes
        v.id as venta_id,
        concat(SUBSTRING(v.tipo_comprobante,1,1),'001') as serie,

        v.id as correlativo,
        concat(SUBSTRING(v.tipo_comprobante,1,1),'001-',lpad(v.id::text, 6, '0')) as serie_correltavio_referencial,
        concat('P', LPAD(p.id::TEXT, 6, '0'), 'F', to_char(p.created_at::date, 'YYYYMMDD')) as codigo_pago,
        case
            WHEN p1.tipo_persona = 'JURIDICA' and v.js_detalles_receptor_factura is null then '6'
            WHEN p1.tipo_persona = 'NATURAL' and v.js_detalles_receptor_factura is null  then '1'
            WHEN v.tipo_comprobante = 'FACTURA' AND v.js_detalles_receptor_factura is not null THEN '6'
            else ''
        end ca_cliente_tipo_documento_sunat,
        p1.direccion as ca_cliente_direccion_sunat,
        case
            WHEN p1.numero_documento = '999999999' THEN ''
            WHEN v.tipo_comprobante = 'FACTURA' AND v.js_detalles_receptor_factura is not null THEN v.js_detalles_receptor_factura->>'ruc'
            else p1.numero_documento
        end ca_cliente_numero_documento_sunat,
        p.monto_venta_final,
        case
            WHEN v.tipo_comprobante = 'FACTURA' AND v.js_detalles_receptor_factura is not null THEN v.js_detalles_receptor_factura->>'razon_social'
            
            WHEN p1.tipo_persona = 'JURIDICA' then p1.razon_social
            WHEN p1.tipo_persona = 'NATURAL' then CONCAT(p1.nombres, ' ', p1.apellidos)
            else 'CLIENTE VARIOS'
        end AS ca_cliente_cliente_sunat, 
        TO_CHAR(p.created_at, 'YYYY-MM-DD') as fecha,
        p.created_at::TIME as hora,
        TO_CHAR(p.created_at, 'HH12:MI:SS AM') as hora_formateada,
        p.monto_venta_original,
        p.monto_venta_final,
        v.tipo_comprobante,
        -- Cálculo del descuento directamente en SQL
        (p.monto_venta_original - p.monto_venta_final) AS descuento,  -- Calculamos el descuento en la consulta
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
                    'cantidad_sunat', CASE 
                        WHEN m.id = 1 THEN rva.cantidad
                        else 1
                    END,
                    'cantidad_real', rva.cantidad,
                    'precio_unitario_articulo', rva.precio_unitario_articulo,
                    'minutos', rva.minutos,
                    'costo_por_minuto', rva.costo_por_minuto,
                    'pu_con_igv', rva.sub_total,
                    'afectacion', 'SI',
                    'pu_sin_igv', (rva.sub_total / 1.18),
                    'IGV', ((rva.sub_total) - (rva.sub_total / 1.18)),
                    'unidad_medida', 'NIU',
                    'codigo_igv', 1000,
                    'afecto_igv_sunat', 'S',
                    'codigo_afectación', 10,
                    'valor_agregado', 'VAT',
                    'factor_icbper', 0.30,
                    'icbper', 0
                )
            ) AS resultado_json
            FROM rel_venta_articulo AS rva
            JOIN movimiento AS m ON rva.movimiento_id = m.id 
            LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
            LEFT JOIN dimension AS dim ON ar.dimension_id = dim.id
            WHERE rva.venta_id = p.id_venta
        ) AS js_detalle_venta,
        (
            SELECT 
            json_agg(
                jsonb_build_object(
                    'ID_DETALLE', dfp.id,
                    'FORMA_PAGO', fp.nombre,
                    'MONTO', dfp.monto,
                    'COLOR', fp.color
                )
            ) AS resultado
            FROM detalle_forma_pago dfp
            JOIN forma_pago fp ON dfp.id_forma_pago = fp.id
            WHERE dfp.id_venta = p.id_venta
        ) as js_detalle_forma_pago
        FROM pago p
        JOIN venta v ON p.id_venta = v.id AND v.tipo_comprobante IN ('BOLETA','FACTURA')
        JOIN persona p1 ON p1.id = v.cliente_id
        LEFT JOIN comprobante cb ON v.id = cb.venta_id
        WHERE cb.venta_id is null 
        --AND 
        --WHERE p.created_at >= (CURRENT_TIMESTAMP - INTERVAL '2 days')
        order by 1 desc
    ";
    return executeQuery($sql);
}
function listarVentasPagadasParaComprobantes(): array
{
    $query = "
        SELECT 
            -- otros campos que ya tienes
            v.id as venta_id,
            concat(SUBSTRING(v.tipo_comprobante,1,1),'001') as serie,
            
            v.id as correlativo,
            concat(SUBSTRING(v.tipo_comprobante,1,1),'001-',lpad(v.id::text, 6, '0')) as serie_correltavio_referencial,
            concat('P', LPAD(p.id::TEXT, 6, '0'), 'F', to_char(p.created_at::date, 'YYYYMMDD')) as codigo_pago,
            case
                WHEN p1.tipo_persona = 'JURIDICA' and v.js_detalles_receptor_factura is null then '6'
                WHEN p1.tipo_persona = 'NATURAL' and v.js_detalles_receptor_factura is null  then '1'
                WHEN v.tipo_comprobante = 'FACTURA' AND v.js_detalles_receptor_factura is not null THEN '6'
                else ''
            end ca_cliente_tipo_documento_sunat,
            p1.direccion as ca_cliente_direccion_sunat,
            case
                WHEN p1.numero_documento = '999999999' THEN ''
                WHEN v.tipo_comprobante = 'FACTURA' AND v.js_detalles_receptor_factura is not null THEN v.js_detalles_receptor_factura->>'ruc'
                else p1.numero_documento
            end ca_cliente_numero_documento_sunat,
            p.monto_venta_final,
            case
                WHEN v.tipo_comprobante = 'FACTURA' AND v.js_detalles_receptor_factura is not null THEN v.js_detalles_receptor_factura->>'razon_social'
                
                WHEN p1.tipo_persona = 'JURIDICA' then p1.razon_social
                WHEN p1.tipo_persona = 'NATURAL' then CONCAT(p1.nombres, ' ', p1.apellidos)
                else 'CLIENTE VARIOS'
            end AS ca_cliente_cliente_sunat, 
            TO_CHAR(p.created_at, 'YYYY-MM-DD') as fecha,
            p.created_at::TIME as hora,
            TO_CHAR(p.created_at, 'HH12:MI:SS AM') as hora_formateada,
            p.monto_venta_original,
            p.monto_venta_final,
            v.tipo_comprobante,
            -- Cálculo del descuento directamente en SQL
            (p.monto_venta_original - p.monto_venta_final) AS descuento,  -- Calculamos el descuento en la consulta
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
                        'cantidad_sunat', CASE 
                            WHEN m.id = 1 THEN rva.cantidad
                            else 1
                        END,
                        'cantidad_real', rva.cantidad,
                        'precio_unitario_articulo', rva.precio_unitario_articulo,
                        'minutos', rva.minutos,
                        'costo_por_minuto', rva.costo_por_minuto,
                        'pu_con_igv', rva.sub_total,
                        'afectacion', 'SI',
                        'pu_sin_igv', (rva.sub_total / 1.18),
                        'IGV', ((rva.sub_total) - (rva.sub_total / 1.18)),
                        'unidad_medida', 'NIU',
                        'codigo_igv', 1000,
                        'afecto_igv_sunat', 'S',
                        'codigo_afectación', 10,
                        'valor_agregado', 'VAT',
                        'factor_icbper', 0.30,
                        'icbper', 0
                    )
                ) AS resultado_json
                FROM rel_venta_articulo AS rva
                JOIN movimiento AS m ON rva.movimiento_id = m.id 
                LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
                LEFT JOIN dimension AS dim ON ar.dimension_id = dim.id
                WHERE rva.venta_id = p.id_venta
            ) AS js_detalle_venta,
            (
                SELECT 
                json_agg(
                    jsonb_build_object(
                        'ID_DETALLE', dfp.id,
                        'FORMA_PAGO', fp.nombre,
                        'MONTO', dfp.monto,
                        'COLOR', fp.color
                    )
                ) AS resultado
                FROM detalle_forma_pago dfp
                JOIN forma_pago fp ON dfp.id_forma_pago = fp.id
                WHERE dfp.id_venta = p.id_venta
            ) as js_detalle_forma_pago
        FROM pago p
        JOIN venta v ON p.id_venta = v.id AND v.tipo_comprobante IN ('BOLETA','FACTURA')
        JOIN persona p1 ON p1.id = v.cliente_id
        LEFT JOIN comprobante cb ON v.id = cb.venta_id
        WHERE cb.venta_id is null 
        AND p.created_at >= (CURRENT_TIMESTAMP - INTERVAL '2 days')
        order by 1 desc

    ";
    return executeQuery($query);
}
function listComprobantesDeclarados(): array
{
    $query = "select * from comprobante where estado_envio = true order by 1";
    return executeQuery($query);
}
function listarFormaPago(): array
{
    $query = "SELECT *,updated_at::date as fecha, TO_CHAR(updated_at, 'HH12:MI:SS AM') as hora FROM forma_pago WHERE deleted_at IS NULL AND unsubscribe IS NULL  order by orden";
    return executeQuery($query);
}
function listarFormaPago_v2(): array
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
    
    WHERE p.deleted_at IS NULL AND u.deleted_at_v2 is null;
    ";
    return executeQuery($query);
}
function fnListarEmisor()
{
    $sql = "
        select * from emisor limit 1
    ";
    return executeQuery($sql);
}
function fnSiguienteCorrelativo($tipo_comprobante)
{
    $sql = "
        select 
        coalesce(max(correlativo),0)+1 as correlativo_siguiente,
        LPAD((coalesce(max(correlativo),0)+1)::text,6,0::text) as correlativo_texto
        from comprobante where tipo_comprobante=:tipo_comprobante AND estado_envio=true
         and mensaje_sunat='Ok';;
    ";
    return executeQuery($sql, params: [":tipo_comprobante" => $tipo_comprobante]);
}
function fnListForVentasDiarias(): array
{
    $query = "
            SELECT 
            v.fecha_fin_transaccion,
            concat('T',LPAD(v.id::TEXT,8,'0'),'-','F',to_char(v.fecha_fin_transaccion::date, 'YYYYMMDD')) as codigo_tiket,
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
            concat('T',LPAD(v.id::TEXT,8,'0'),'-','F',to_char(v.fecha_fin_transaccion::date, 'YYYYMMDD')) as codigo_tiket,
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
            concat('T',LPAD(v.id::TEXT,8,'0'),'-','F',to_char(v.fecha_fin_transaccion::date, 'YYYYMMDD')) as codigo_tiket,
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

function fnUltimaVentaPorIdVenta($id_venta): array
{
    $query = "
    WITH with_detalle AS (
        SELECT 
            rva.id AS rel_venta_articulo_id,
            rva.venta_id,
            rva.articulo_id,
            m.descripcion,
            CASE 
                WHEN ar.dimension_id IS NOT NULL THEN
                    CONCAT(ar.nombre, ' (', dim.medida, ')')
                WHEN ar.nombre IS NULL THEN
                    UPPER(TRIM(SPLIT_PART(REPLACE(COALESCE(rva.nota_archivo, m.descripcion), 'Cotización', ''), ' / ', 1)))
                ELSE
                    UPPER(TRIM(SPLIT_PART(REPLACE(COALESCE(rva.nota_archivo, ar.nombre), 'Cotización', ''), ' / ', 1)))
            END as descripcion_2,
            rva.cantidad,
            rva.precio_unitario_articulo,
            rva.minutos,
            rva.costo_por_minuto,
            rva.sub_total
        FROM rel_venta_articulo AS rva
        JOIN movimiento as m ON rva.movimiento_id = m.id
        LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
        LEFT JOIN dimension AS dim ON ar.dimension_id = dim.id
    ),
    with_detalle_pago AS(
        SELECT 
            fpu.id_venta,
            fpu.id as ID_DETALLE,
            fp.nombre as FORMA_PAGO,
            fpu.monto
        FROM detalle_forma_pago fpu
        JOIN forma_pago fp ON fpu.id_forma_pago = fp.id
    )
    SELECT 
    v.tipo_comprobante,
    concat(SUBSTRING(v.tipo_comprobante,1,1),'001 - ',LPAD(v.id::TEXT,6,'0')) as codigo_tiket,
    v.fecha_fin_transaccion,
    v.id AS venta_id, 
    CASE 
        WHEN p.tipo_persona = 'JURIDICA' THEN 
            COALESCE(p.razon_social, p.nombre_comercial, 'SIN NOMBRE')
        ELSE 
            CONCAT(p.nombres, ' ', p.apellidos)
    END AS cliente,
    TO_CHAR(v.fecha_fin_transaccion, 'YYYY-MM-DD') AS fecha, 
    TO_CHAR(v.fecha_fin_transaccion, 'HH12:MI:SS AM') AS hora, 
    p.telefonomovil AS telefonomovil_cliente,
    p.email AS email_cliente, 
    p.numero_documento AS numero_doc_cliente,
    CONCAT(usua.nombres, ', ', usua.apellidos) AS usuario, 
    v.atencion_final_usuario,
    p.id AS id_persona,
    v.usuario_id,
    v.monto_venta_final,
    v.total, 
    (v.total - v.monto_venta_final) as perdida_utilidad,
    CASE 
        WHEN v.estado_pago = 'P' THEN 'PAGADO'
        WHEN v.estado_pago = 'C' THEN 'CREDITO'
    END AS estado_pago,
    v.estado_final,
    (
        SELECT jsonb_agg(
            jsonb_build_object(
                'rel_venta_articulo_id', wf.rel_venta_articulo_id,
                'venta_id', wf.venta_id,
                'articulo_id', wf.articulo_id,
                'descripcion', wf.descripcion,
                'descripcion_2', wf.descripcion_2,
                'cantidad', wf.cantidad,
                'precio_unitario_articulo', wf.precio_unitario_articulo,
                'minutos', wf.minutos,
                'costo_por_minuto', wf.costo_por_minuto,
                'sub_total', wf.sub_total
            )
        )
        FROM with_detalle as wf
        WHERE wf.venta_id = v.id
    ) AS js_detalle,
    (
        SELECT jsonb_agg(
            jsonb_build_object(
                'id_venta', id_venta,
                'id_detalle', wdf.ID_DETALLE,
                'forma_pago', wdf.FORMA_PAGO,
                'monto', wdf.monto
            )
        )
        FROM with_detalle_pago wdf
        WHERE wdf.id_venta = v.id
    ) as js_detalle_forma_pago
    FROM venta AS v
    LEFT JOIN deuda AS du ON v.id=du.id_venta
    INNER JOIN usuario AS us ON v.usuario_id = us.id  
    INNER JOIN persona AS usua ON us.persona_id = usua.id
    LEFT JOIN persona AS p ON v.cliente_id = p.id
    WHERE v.estado_venta = 'VR' 
    AND v.id = :idVenta
    AND v.deleted_at IS NULL
    ORDER BY v.fecha_fin_transaccion;
    ";
    return executeQuery($query, params: ['idVenta' => $id_venta]);
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
            v.id as venta_id,
            v.tipo_comprobante,
            concat(SUBSTRING(v.tipo_comprobante,1,1),'001-',lpad(v.id::text, 6, '0')) as serie_correltavio_referencial,
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
                        'codigo_tiket',concat('T',LPAD(v.id::TEXT,8,'0'),'-','F',to_char(v.fecha_fin_transaccion::date, 'YYYYMMDD')),
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
            JOIN venta as v ON v.id = p.id_venta
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
            v.id as venta_id,
            v.tipo_comprobante,
            concat(SUBSTRING(v.tipo_comprobante,1,1),'001-',lpad(v.id::text, 6, '0')) as serie_correltavio_referencial,
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
                        'codigo_tiket',concat('T',LPAD(v.id::TEXT,8,'0'),'-','F',to_char(v.fecha_fin_transaccion::date, 'YYYYMMDD')),
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
            JOIN venta as v ON v.id = p.id_venta
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
            v.id as venta_id,
            v.tipo_comprobante,
            concat(SUBSTRING(v.tipo_comprobante,1,1),'001-',lpad(v.id::text, 6, '0')) as serie_correltavio_referencial,
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
                        'codigo_tiket',concat('T',LPAD(v.id::TEXT,8,'0'),'-','F',to_char(v.fecha_fin_transaccion::date, 'YYYYMMDD')),
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
            JOIN venta as v ON v.id = p.id_venta
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
    $query = "
    SELECT 
        c.id as compra_id,
        CONCAT(us.nombres,' ',us.apellidos) AS realizada_por,
        CASE 
            WHEN c.proveedor_id IS NOT NULL THEN
                CONCAT(proveedor.numero_documento,' - ', UPPER(proveedor.nombre_comercial))
            ELSE	
                'SIN REGISTRO DE PROVEEDOR'
        END proveedor,
        proveedor.numero_documento as proveedor_num_doc,
        UPPER(proveedor.nombre_comercial) as nombre_comercial_proveedor,
        CASE 
            WHEN c.fecha IS NULL THEN
                'SIN REGISTRO'
            ELSE
                TO_CHAR(c.fecha, 'YYYY-MM-DD')
        END fecha_compra,
        c.numero_comprobante,
        CASE 
            WHEN c.total IS NULL THEN
                'SIN REGISTRO'
            ELSE
                CONCAT('S/',' ',c.total)
        END total,
        c.created_at::DATE as fecha_registro,
        TO_CHAR(c.created_at, 'HH12:MI:SS AM') as hora,
        js_detalle_compra,
        c.created_at as fecha_hora_registro
    FROM compra c
    JOIN usuario u ON u.id = c.usuario_id
    JOIN persona us ON u.persona_id = us.id
    LEFT JOIN persona proveedor ON c.proveedor_id = proveedor.id
    ORDER BY c.id DESC;
    ";

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

    return executeQuery($query, params: ["tipo_caja" => $tipoCaja]);
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


function fnVerificarUsarioSession($id): int
{
    $query = "     
    SELECT 
    COUNT(*) as cantidad
    FROM usuario
    WHERE id = :idUsuario AND deleted_at IS NULL
    ";

    // Ejecutar la consulta
    $result = executeQuery($query, ['idUsuario' => $id]);

    // Verificar si el usuario existe
    if ($result[0]['cantidad'] > 0) {
        // Si el usuario existe y no está eliminado, devolver 1
        return 1;
    } else {
        // Si el usuario no existe o está eliminado, devolver 0
        return 0;
    }
}
function fnEjecutarETL(): array
{
    $query = "SELECT * FROM fn_etl_vysam();";
    $result = executeQuery($query);
    if ($result) {

        return ['respuesta' => $result[0]['fn_etl_vysam']];
    } else {

        return ['respuesta' => 'ERROR'];
    }
}


function fnEjecutarETLArticulosNube(): array
{
    $query = "SELECT * FROM fn_etl_articulo();";
    $result = executeQuery($query);
    if ($result) {

        return ['respuesta' => $result[0]['fn_etl_articulo']];
    } else {

        return ['respuesta' => 'ERROR'];
    }
}



function fnListadoDeReservasWeb(): array
{
    $query = "
    select * from view_foreingdatabase_reservas_web where estado = 'pendiente'
    ";

    return executeQuery($query);
}


function fnListadoDeEmisor(): array
{
    $query = "SELECT * FROM emisor";


    $result = executeQuery($query);

    //var_dump($result);


    return $result;
}


function u(string $texto): string
{
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto);
}
 
// ---------------------------------------------------------------------------
// FUNCIÓN PRINCIPAL — punto de entrada
// ---------------------------------------------------------------------------
function fnGenerarTicket($idVenta, string $formato = 'TICKET'): void
{
    $datosprueba = fnUltimaVentaPorIdVenta($idVenta)[0];
    $datoEmisor  = fnListadoDeEmisor()[0];
    $productos   = json_decode($datosprueba["js_detalle"], true) ?? [];       // ← agrega ?? []
    $pagos       = json_decode($datosprueba["js_detalle_forma_pago"], true) ?? []; // ← agrega ?? []
 
    $datosVenta = [
        "codigo_tiket"       => $datosprueba["codigo_tiket"],
        "tipo_comprobante"   => $datosprueba["tipo_comprobante"],
        "fecha"              => $datosprueba["fecha"],
        "hora"               => $datosprueba["hora"],
        "cliente"            => $datosprueba["cliente"],
        "numero_doc_cliente" => $datosprueba["numero_doc_cliente"],
        "usuario_final"      => $datosprueba["atencion_final_usuario"],
        "total"              => $datosprueba["total"],
        "monto_venta_final"  => $datosprueba["monto_venta_final"],
        "estado_pago"        => $datosprueba["estado_pago"],
        "descuento"          => $datosprueba["perdida_utilidad"],
    ];
 
    ob_clean();
 
    if ($formato === 'A4') {
        fnGenerarTicketA4($datoEmisor, $datosVenta, $datosprueba, $productos, $pagos);
    } else {
        fnGenerarTicketTermico($datoEmisor, $datosVenta, $datosprueba, $productos, $pagos);
    }
}
 
// ---------------------------------------------------------------------------
// FORMATO A4 — diseño mejorado + UTF-8 corregido
// ---------------------------------------------------------------------------
function fnGenerarTicketA4(array $datoEmisor, array $datosVenta, array $datosprueba, array $productos, array $pagos): void
{
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->SetMargins(15, 15, 15);
    $pdf->AddPage();
 
    $pageW    = $pdf->GetPageWidth();   // 210
    $mL       = 15;
    $mR       = 15;
    $ancho    = $pageW - $mL - $mR;    // 180
 
    // -----------------------------------------------------------------------
    // FRANJA SUPERIOR AZUL OSCURO
    // -----------------------------------------------------------------------
    $pdf->SetFillColor(20, 40, 80);
    $pdf->Rect(0, 0, $pageW, 12, 'F');
 
    // -----------------------------------------------------------------------
    // CABECERA: logo + datos emisor
    // -----------------------------------------------------------------------
    $logoPath = 'logica/logo.jpeg';
    $logoW    = 28;
    $logoY    = 14;
 
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, $mL, $logoY, $logoW);
    }
 
    $xTexto = $mL + $logoW + 6;
    $wTexto = $ancho - $logoW - 6;
 
    $pdf->SetXY($xTexto, $logoY);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor(20, 40, 80);
    $pdf->Cell($wTexto, 7, u($datoEmisor["razon_social"]), 0, 1, 'L');
 
    $pdf->SetX($xTexto);
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->Cell($wTexto, 5, u('RUC: ' . $datoEmisor["ruc"]), 0, 1, 'L');
 
    $pdf->SetX($xTexto);
    $pdf->SetFont('Arial', '', 8);
    $pdf->MultiCell($wTexto, 4, u($datoEmisor["direccion"]), 0, 'L');
        if (!empty($datoEmisor["telefono"])) {
        $pdf->SetX($xTexto);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell($wTexto, 4, u('Tel: ' . $datoEmisor["telefono"]), 0, 1, 'L');
    }
    if (!empty($datoEmisor["correo_electronico"])) {
        $pdf->SetX($xTexto);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell($wTexto, 4, u('Email: ' . $datoEmisor["correo_electronico"]), 0, 1, 'L');
    }
 
    // Línea separadora delgada
    $pdf->SetY(max($pdf->GetY() + 2, $logoY + $logoW * 0.85));
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->SetLineWidth(0.3);
    $pdf->Line($mL, $pdf->GetY(), $pageW - $mR, $pdf->GetY());
    $pdf->Ln(5);
 
    // -----------------------------------------------------------------------
    // BLOQUE TIPO COMPROBANTE (fondo azul oscuro, texto blanco)
    // -----------------------------------------------------------------------
    $pdf->SetFillColor(20, 40, 80);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->Cell($ancho, 9, u($datosprueba["tipo_comprobante"] . ' DE VENTA ELECTRONICA'), 0, 1, 'C', true);
 
    $pdf->SetFillColor(40, 70, 130);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell($ancho, 6, u($datosVenta["codigo_tiket"]), 0, 1, 'C', true);
 
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(5);
 
    // -----------------------------------------------------------------------
    // DATOS DEL CLIENTE
    // -----------------------------------------------------------------------
    // Cabecera de sección
    $pdf->SetFillColor(20, 40, 80);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($ancho, 6, u('  DATOS DEL CLIENTE'), 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);
 
    $col1 = 30;
    $col2 = ($ancho / 2) - $col1;
    $col3 = 30;
    $col4 = ($ancho / 2) - $col3;
 
    $fila = function($pdf, $label1, $val1, $label2, $val2) use ($col1, $col2, $col3, $col4) {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($col1, 5, u($label1), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell($col2, 5, u($val1), 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($col3, 5, u($label2), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell($col4, 5, u($val2), 0, 1, 'L');
    };
 
    $fila($pdf, 'Cliente:', $datosVenta["cliente"], 'DNI/RUC:', $datosVenta["numero_doc_cliente"]);
    $fila($pdf, 'Fecha:', $datosVenta["fecha"] . '  ' . $datosVenta["hora"], 'Estado pago:', $datosVenta["estado_pago"]);
 
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($col1, 5, u('Atendido por:'), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell($ancho - $col1, 5, u($datosVenta["usuario_final"]), 0, 1, 'L');
 
    $pdf->Ln(4);
 
    // -----------------------------------------------------------------------
    // TABLA DE PRODUCTOS
    // -----------------------------------------------------------------------
    $pdf->SetFillColor(20, 40, 80);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($ancho, 6, u('  DETALLE DE PRODUCTOS / SERVICIOS'), 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
 
    // Anchos de columna
    $cDesc = 90;
    $cCant = 18;
    $cPU   = 36;
    $cSub  = $ancho - $cDesc - $cCant - $cPU;
 
    // Encabezado tabla
    $pdf->SetFillColor(230, 235, 245);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($cDesc, 6, u('DESCRIPCION'),   1, 0, 'C', true);
    $pdf->Cell($cCant, 6, u('CANT.'),         1, 0, 'C', true);
    $pdf->Cell($cPU,   6, u('PRECIO UNIT.'),  1, 0, 'C', true);
    $pdf->Cell($cSub,  6, u('SUBTOTAL'),      1, 1, 'C', true);
 
    $pdf->SetFont('Arial', '', 8);
    $fillRow = false;
    foreach ($productos as $producto) {
        $pdf->SetFillColor($fillRow ? 245 : 255, $fillRow ? 247 : 255, $fillRow ? 252 : 255);
 
        $yIni = $pdf->GetY();
        $xIni = $pdf->GetX();
 
        $pdf->MultiCell($cDesc, 5, u($producto["descripcion_2"]), 1, 'L', $fillRow);
        $yFin = $pdf->GetY();
        $h    = $yFin - $yIni;
 
        $pdf->SetXY($xIni + $cDesc, $yIni);
        $pdf->Cell($cCant, $h, $producto["cantidad"], 1, 0, 'C', $fillRow);
        $pdf->Cell($cPU,   $h, 'S/ ' . number_format($producto["precio_unitario_articulo"], 2), 1, 0, 'R', $fillRow);
        $pdf->Cell($cSub,  $h, 'S/ ' . number_format($producto["sub_total"], 2), 1, 0, 'R', $fillRow);
        $pdf->Ln();
 
        $fillRow = !$fillRow;
    }
 
    $pdf->Ln(5);
 
    // -----------------------------------------------------------------------
    // FORMAS DE PAGO (izquierda) + TOTALES (derecha)
    // -----------------------------------------------------------------------
    $yBloque = $pdf->GetY();
    $mitad   = $ancho * 0.5;
 
    // -- Formas de pago --
    $pdf->SetFillColor(20, 40, 80);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($mitad - 5, 6, u('  FORMA DE PAGO'), 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
 
    $pdf->SetFont('Arial', '', 8);
    foreach ($pagos as $x) {
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(($mitad - 5) * 0.55, 5, u($x["forma_pago"]), 0, 0, 'L');
        $pdf->Cell(($mitad - 5) * 0.45, 5, 'S/ ' . number_format($x["monto"], 2), 0, 1, 'R');
    }
 
    // -- Totales (derecha) --
    $yTot = $yBloque;
    $xTot = $mL + $mitad + 5;
    $wTot = $mitad - 5;
 
    $pdf->SetXY($xTot, $yTot);
    $pdf->SetFillColor(20, 40, 80);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($wTot, 6, u('  RESUMEN'), 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
 
    $filaTotal = function($pdf, $xTot, $wTot, $label, $valor, $bold = false) {
        $pdf->SetX($xTot);
        $pdf->SetFont('Arial', $bold ? 'B' : '', 8);
        $pdf->Cell($wTot * 0.55, 5, u($label), 0, 0, 'L');
        $pdf->Cell($wTot * 0.45, 5, u($valor), 0, 1, 'R');
    };
 
    $filaTotal($pdf, $xTot, $wTot, 'Descuento:', 'S/ ' . number_format($datosVenta["descuento"], 2));
    $filaTotal($pdf, $xTot, $wTot, 'Total bruto:', 'S/ ' . number_format($datosVenta["total"], 2));
 
    // Fila TOTAL destacada
    $pdf->SetX($xTot);
    $pdf->SetFillColor(20, 40, 80);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell($wTot, 8, u('TOTAL:  S/ ' . number_format($datosVenta["monto_venta_final"], 2)), 0, 1, 'R', true);
    $pdf->SetTextColor(0, 0, 0);
 
    $pdf->Ln(6);
 
    // -----------------------------------------------------------------------
    // TOTAL EN LETRAS
    // -----------------------------------------------------------------------
    $letras = 'Son: ' . strtoupper(number_format($datosVenta["total"], 2) . ' /100 SOLES');
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell($ancho, 5, u($letras), 0, 1, 'L');
    $pdf->SetTextColor(0, 0, 0);
 
    $pdf->Ln(4);
 
    // -----------------------------------------------------------------------
    // MENSAJE NO DEVOLUCIONES
    // -----------------------------------------------------------------------
    $pdf->SetLineWidth(0.4);
    $pdf->SetDrawColor(20, 40, 80);
    $pdf->Line($mL, $pdf->GetY(), $pageW - $mR, $pdf->GetY());
    $pdf->Ln(3);
 
    $rectH  = 9;
    $yRect  = $pdf->GetY();
 
    // Dibujar rectángulo amarillo con borde dorado
    $pdf->SetFillColor(255, 243, 205);
    $pdf->SetDrawColor(200, 150, 0);
    $pdf->SetLineWidth(0.6);
    $pdf->Rect($mL, $yRect, $ancho, $rectH, 'DF');
 
    // Posicionar cursor SOBRE el rectángulo ya dibujado y escribir el texto
    $pdf->SetXY($mL, $yRect);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor(120, 80, 0);
    $pdf->Cell($ancho, $rectH, u('** NO SE ACEPTAN DEVOLUCIONES **'), 0, 1, 'C');
 
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->SetLineWidth(0.3);
 
    $pdf->Ln(5);
 
    // -----------------------------------------------------------------------
    // PIE DE PÁGINA
    // -----------------------------------------------------------------------
    $pdf->SetFillColor(245, 245, 245);
    $pdf->Rect($mL, $pdf->GetY(), $ancho, 18, 'F');
 
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetTextColor(120, 120, 120);
    $pdf->MultiCell(
        $ancho, 4,
        u('Representacion impresa de la ' . $datosprueba["tipo_comprobante"] . ' de venta electronica. Consulte su comprobante en: www.sunat.gob.pe'),
        0, 'C'
    );
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell($ancho, 4, u('Gracias por su preferencia  —  ' . $datoEmisor["razon_social"]), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell($ancho, 4, u('Desarrollado por CAPTAIN'), 0, 1, 'C');
    $pdf->SetTextColor(0, 0, 0);
 
    ob_clean();
    $pdf->Output('I', 'comprobante_a4.pdf');
}
 
 
// ---------------------------------------------------------------------------
// FORMATO TICKET TÉRMICO 80 mm — UTF-8 corregido
// ---------------------------------------------------------------------------
function fnGenerarTicketTermico(array $datoEmisor, array $datosVenta, array $datosprueba, array $productos, array $pagos): void
{
    $pdf = new FPDF('P', 'mm', [80, 200]);
    $pdf->AddPage();
 
    $logoPath = 'logica/logo.jpeg';
    if (file_exists($logoPath)) {
        $logoW   = 20;
        $centerX = (80 - $logoW) / 2;
        $pdf->Image($logoPath, $centerX, 5, $logoW);
        $pdf->Ln(20);
    }
 
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 4, u($datoEmisor["razon_social"]), 0, 1, 'C');
    $pdf->Cell(60, 4, u('RUC: ' . $datoEmisor["ruc"]), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 6);
    $pdf->MultiCell(60, 4, u($datoEmisor["direccion"]), 0, 'C');
    if (!empty($datoEmisor["telefono"])) {
    $pdf->SetFont('Arial', '', 6);
    $pdf->Cell(60, 4, u('Tel: ' . $datoEmisor["telefono"]), 0, 1, 'C');
    }
    if (!empty($datoEmisor["correo_electronico"])) {
    $pdf->SetFont('Arial', '', 6);
    $pdf->Cell(60, 4, u('Email: ' . $datoEmisor["correo_electronico"]), 0, 1, 'C');
    }
 
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(60, 4, u($datosprueba["tipo_comprobante"] . ' DE VENTA ELECTRONICA'), 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 4, u($datosVenta["codigo_tiket"]), 0, 1, 'C');
    $pdf->Ln(1);
 
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 4, u('Cliente: ' . $datosVenta["cliente"]), 0, 1, 'L');
    $pdf->Cell(60, 4, u('DNI/RUC: ' . $datosVenta["numero_doc_cliente"]), 0, 1, 'L');
    $pdf->Cell(60, 4, u('Fecha: ' . $datosVenta["fecha"] . ' ' . $datosVenta["hora"]), 0, 1, 'L');
    $pdf->Ln(1);
    $pdf->Cell(60, 3, str_repeat('_', 25), 0, 1, 'C');
    $pdf->Ln(1);
 
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(30, 3, u('DESCRIPCION'), 0, 0, 'L');
    $pdf->Cell(8,  3, 'CANT.', 0, 0, 'C');
    $pdf->Cell(12, 3, 'P.U',   0, 0, 'C');
    $pdf->Cell(10, 3, 'TOTAL', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Ln(1);
 
    foreach ($productos as $producto) {
        $yIni = $pdf->GetY();
        $pdf->MultiCell(30, 3, u($producto["descripcion_2"]), 0, 'L');
        $yFin = $pdf->GetY();
        $h    = $yFin - $yIni;
        $pdf->SetY($yIni);
        $pdf->SetX(40);
        $pdf->Cell(8,  $h, $producto["cantidad"], 0, 0, 'C');
        $pdf->Cell(12, $h, 'S/ ' . number_format($producto["precio_unitario_articulo"], 2), 0, 0, 'C');
        $pdf->Cell(10, $h, 'S/ ' . number_format($producto["sub_total"], 2), 0, 1, 'C');
    }
 
    $pdf->Ln(1);
    $pdf->Cell(60, 3, str_repeat('_', 25), 0, 1, 'C');
    $pdf->Ln(1);
 
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(20, 3, u('Estado:'),    0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(15, 3, u($datosVenta["estado_pago"]), 0, 1, 'L');
    $pdf->Ln(1);
 
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(20, 3, u('Descuento:'), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(15, 3, 'S/ ' . number_format($datosVenta["descuento"], 2), 0, 1, 'L');
    $pdf->Ln(1);
 
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(30, 3, u('Forma de Pago'), 0, 0, 'L');
    $pdf->Cell(20, 3, 'Monto',           0, 1, 'R');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Ln(1);
    foreach ($pagos as $x) {
        $pdf->Cell(30, 3, u($x["forma_pago"]), 0, 0, 'L');
        $pdf->Cell(20, 3, 'S/ ' . number_format($x["monto"], 2), 0, 1, 'R');
    }
 
    $pdf->Ln(1);
    $pdf->Cell(60, 3, str_repeat('_', 25), 0, 1, 'C');
    $pdf->Ln(1);
 
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 4, u('TOTAL: S/ ' . number_format($datosVenta["monto_venta_final"], 2)), 0, 1, 'C');
    $pdf->Ln(1);
 
    $letras = strtoupper(number_format($datosVenta["total"], 2) . ' /100 PEN');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(60, 3, u($letras), 0, 1, 'C');
    $pdf->Ln(1);
 
    $pdf->SetFont('Arial', 'B', 6);
    $pdf->Cell(60, 3, u('ATENDIDO POR: ' . $datosVenta["usuario_final"]), 0, 1, 'C');
    $pdf->Ln(1);
 
    // Mensaje NO DEVOLUCIONES
    $pdf->Cell(60, 3, str_repeat('_', 25), 0, 1, 'C');
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 4, u('** NO SE ACEPTAN DEVOLUCIONES **'), 0, 1, 'C');
    $pdf->Ln(1);
 
    $pdf->SetFont('Arial', '', 7);
    $pdf->MultiCell(60, 3, u('Representacion impresa de la ' . $datosprueba["tipo_comprobante"] . ' de venta electronica'), 0, 'C');
    $pdf->MultiCell(60, 3, u('Gracias por su preferencia'), 0, 'C');
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 3, ' ', 0, 1, 'C');
    $pdf->Cell(60, 3, u($datoEmisor["razon_social"]), 0, 1, 'C');
    $pdf->Cell(60, 3, u('Desarrollado por CAPTAIN'), 0, 1, 'C');
    $pdf->Ln(4);
 
    ob_clean();
    $pdf->Output('I', 'ticket_venta.pdf');
}


function fnRankingClientes() {
    $query = "
        SELECT
            p.nombres || ' ' || p.apellidos AS nombre_cliente,
            SUM(v.monto_venta_final) AS total_compras_acumulado
        FROM
            public.venta v
        INNER JOIN
            public.persona p ON v.cliente_id = p.id
        WHERE
            v.estado_venta = 'VR'
            AND v.deleted_at IS NULL
        GROUP BY
            p.id, p.nombres, p.apellidos
        ORDER BY
            total_compras_acumulado DESC
    ";
    
    return executeQuery($query);
}