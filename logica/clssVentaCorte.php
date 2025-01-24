<?php
include("bd.php");

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorVentaCorte($accion);
}

function controladorVentaCorte($accion)
{
    switch ($accion) {
        case 'CONSULTARRESERVA':
            $venta_id = $_POST["venta_id"];

            consultarDetalleReserva($venta_id);
            break;
        case 'REGISTRARRESERVA':
            $data = json_decode($_POST["data"], true); // Decodificar JSON
            registrar_reserva($data);
            break;
        case 'CAMBIARCONTRASEÑA':
            break;
    }
}

function consultarDetalleReserva($venta_id)
{
    global $conectar;

    try {
        $orden = $conectar->prepare("
        SELECT 
        rva.id AS rel_venta_articulo_id,
        rva.venta_id,
        rva.articulo_id,
        CASE 
            WHEN ar.dimension_id IS NOT NULL THEN
                CONCAT(ar.nombre, ' (', dim.medida, ')')
            WHEN ar.nombre IS NULL THEN
                m.descripcion
            ELSE
                ar.nombre 
        END as articulo_nombre,
        rva.cantidad,
        rva.precio_unitario_articulo,
        rva.minutos,
        rva.costo_por_minuto,
        rva.sub_total,
        ar.corte,
        rva.movimiento_id
        FROM rel_venta_articulo AS rva
        JOIN movimiento as m ON rva.movimiento_id=m.id
        LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
        LEFT  JOIN dimension AS dim ON ar.dimension_id = dim.id
        WHERE rva.venta_id = :id;");
        $orden->bindParam(":id", $venta_id);
        $orden->execute();

        $lista = $orden->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($lista);
    } catch (\Throwable $th) {
        echo json_encode(["error" => $th->getMessage()]);
    }
}

function registrar_reserva($datos = array())
{
    global $conectar;

    try {
        $conectar->beginTransaction();

        $orden = $conectar->prepare("INSERT INTO venta(usuario_id, cliente_id,total,fecha, estado_pago, estado_venta) 
                                     VALUES (:usuario_id, :cliente_id,:total,current_date, 'P', 'R');");
        $orden->bindParam(":usuario_id", $datos['usuario_id']);
        $orden->bindParam(":cliente_id", $datos['cliente_id']);
        $orden->bindParam(":total", $datos['total']);
        $orden->execute();
        $venta_id = $conectar->lastInsertId(); // Obtener el ID de la venta recién creada
        $orden->closeCursor();

        // Insertar en la tabla rel_venta_articulo y descontar stock
        foreach ($datos['articulos'] as $articulo) {
            $orden = $conectar->prepare("INSERT INTO rel_venta_articulo(venta_id, articulo_id, minutos, costo_por_minuto, precio_unitario_articulo, cantidad, sub_total,movimiento_id) 
                                         VALUES (:venta_id, :articulo_id, :minutos, :costo_por_minuto, :precio_unitario, :cantidad, :sub_total, :movimiento_id);");
            $orden->bindParam(":venta_id", $venta_id);
            $orden->bindParam(":articulo_id", $articulo['articulo_id']);

            // Convertir valores "-" a NULL
            $minutos = ($articulo['minutos'] === '-' || $articulo['minutos'] === null) ? null : intval($articulo['minutos']);
            $costo_por_minuto = ($articulo['costoxminuto'] === '-' || $articulo['costoxminuto'] === null) ? null : floatval($articulo['costoxminuto']);

            // Manejar parámetros con tipos correctos
            $orden->bindValue(":minutos", $minutos, $minutos === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $orden->bindValue(":costo_por_minuto", $costo_por_minuto, $costo_por_minuto === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $orden->bindParam(":precio_unitario", $articulo['precio_unitario']);
            $orden->bindParam(":cantidad", $articulo['cantidad']);
            $orden->bindParam(":sub_total", $articulo['sub_total']);
            $orden->bindParam(":movimiento_id", $articulo['movimiento_id']);
            $orden->execute();
            $orden->closeCursor();

            $orden = $conectar->prepare("UPDATE articulo 
                                         SET stock = stock - :cantidad 
                                         WHERE id = :articulo_id;");
            $orden->bindParam(":cantidad", $articulo['cantidad']);
            $orden->bindParam(":articulo_id", $articulo['articulo_id']);
            $orden->execute();
            $orden->closeCursor();
        }

        $conectar->commit();
        echo json_encode(["success" => true, "venta_id" => $venta_id]);
    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_reserva: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}
