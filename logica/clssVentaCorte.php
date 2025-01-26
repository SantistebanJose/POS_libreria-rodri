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
        case 'INSERTMOVIMIENTO':
            $data = json_decode($_POST["data"], true); // Decodificar JSON
            fn_insert_movimiento($data);
            break;
        case 'ADICIONARARTICULO':
            $data = json_decode($_POST["data"], true); // Decodificar JSON
            fn_adicionar_articulo($data);
        break;
        case 'ELIMINARARTICULO':
            $id_rel_articulo = $_POST["id_rel_articulo"];
            fn_eliminar_articulo($id_rel_articulo);
            break;
        case 'ELIMINARMOVIMIENTO':
            $id_rel_articulo = $_POST["id_rel_articulo"];
            fn_eliminar_movimiento($id_rel_articulo);
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
        WHERE rva.venta_id = :id
        order by rva.id;");
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

            $articuloId = ($articulo['articulo_id'] === 0 || (int)$articulo['articulo_id'] === 0) 
                ? null 
                : (int)$articulo['articulo_id'];

            // Asociar el parámetro con el valor validado
            $orden->bindParam(":articulo_id", $articuloId, is_null($articuloId) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            // Convertir valores "-" a NULL
            $minutos = ($articulo['minutos'] === '-' || $articulo['minutos'] === null) ? null : intval($articulo['minutos']);
            $costo_por_minuto = ($articulo['costoxminuto'] === '-' || $articulo['costoxminuto'] === null) ? null : floatval($articulo['costoxminuto']);

            // Manejar parámetros con tipos correctos
            $orden->bindValue(":minutos", $minutos, $minutos === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $orden->bindValue(":costo_por_minuto", $costo_por_minuto, $costo_por_minuto === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $precioUnitario = $articulo['precio_unitario'] === '-' ? null : $articulo['precio_unitario'];
            $orden->bindParam(":precio_unitario", $precioUnitario, PDO::PARAM_STR);
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


function fn_insert_movimiento($datos = array())
{
    global $conectar;
    try {
        // Preparar la consulta
        $orden = $conectar->prepare("SELECT fn_insert_pis_for_movimientos(:venta_id, :movimiento_id, :cantidad, :sub_total) AS respuesta;");
        $orden->bindParam(":venta_id", $datos['venta_id']);
        $orden->bindParam(":movimiento_id", $datos['movimiento_id']);
        $orden->bindParam(":cantidad", $datos['cantidad']);
        $orden->bindParam(":sub_total", $datos['sub_total']);
        
        $orden->execute();
        
        $resultado = $orden->fetch(PDO::FETCH_ASSOC);
        $orden->closeCursor();
        
        $respuesta = json_decode($resultado['respuesta'], true);

        if ($respuesta['estado'] === true) {
            echo json_encode(["success" => true, "mensaje" => $respuesta['mensaje']]);
        } else {
            echo json_encode(["success" => false, "mensaje" => $respuesta['mensaje']]);
        }
    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en insertar movimiento: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function fn_adicionar_articulo($datos = array())
{
    global $conectar;
    try {
        // Iniciar la transacción
        $conectar->beginTransaction();

        // Preparar la consulta para insertar el artículo
        $orden = $conectar->prepare("INSERT INTO rel_venta_articulo(venta_id, articulo_id, minutos, costo_por_minuto,precio_unitario_articulo , cantidad, sub_total,movimiento_id) 
                                     VALUES (:venta_id, :articulo_id, :minutos, :costo_por_minuto,:precio_unitario, :cantidad, :sub_total, :movimiento_id);");
        $orden->bindParam(":venta_id", $datos['venta_id']);
        
        $articuloId = ($datos['articulo_id'] === 0 || (int)$datos['articulo_id'] === 0) 
        ? null 
        : (int)$datos['articulo_id'];

    // Asociar el parámetro con el valor validado
        $orden->bindParam(":articulo_id", $articuloId, is_null($articuloId) ? PDO::PARAM_NULL : PDO::PARAM_INT);
        
        $orden->bindParam(":cantidad", $datos['cantidad']);
        
        // Convertir valores "-" a NULL
        $minutos = ($datos['minutos'] === '-' || $datos['minutos'] === null) ? null : intval($datos['minutos']);
        $costo_por_minuto = ($datos['costoxminuto'] === '-' || $datos['costoxminuto'] === null) ? null : floatval($datos['costoxminuto']);
        $precioUnitario = $datos['precio_unitario'] === '-' ? null : $datos['precio_unitario'];
        
        $orden->bindParam(":precio_unitario", $precioUnitario, PDO::PARAM_STR);
        $orden->bindValue(":minutos", $minutos, $minutos === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $orden->bindValue(":costo_por_minuto", $costo_por_minuto, $costo_por_minuto === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":sub_total", $datos['sub_total']);
        $orden->bindParam(":movimiento_id", $datos['movimiento_id']);

        $orden->execute();
        $id_rel_articulo = $conectar->lastInsertId();
        $orden->closeCursor();

        // Actualizar el stock del artículo
        $orden = $conectar->prepare("UPDATE articulo 
                                     SET stock = stock - :cantidad 
                                     WHERE id = :articulo_id;");
        $orden->bindParam(":cantidad", $datos['cantidad']);
        $orden->bindParam(":articulo_id", $datos['articulo_id']);
        $orden->execute();
        $orden->closeCursor();

        $orden = $conectar->prepare("UPDATE venta 
                                    SET total = total + :sub_total 
                                    WHERE id = :venta_id;");
        $orden->bindParam(":sub_total", $datos['sub_total']);
        $orden->bindParam(":venta_id", $datos['venta_id']);
        $orden->execute();
        $orden->closeCursor();

        // Confirmar la transacción si todo salió bien
        $conectar->commit();

        echo json_encode(["success" => true, "id_rel_articulo" => $id_rel_articulo]);

    } catch (\Throwable $th) {
        // Si hay un error, hacer rollback de la transacción
        $conectar->rollBack();
        error_log("Error en insertar articulo: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}


function fn_eliminar_articulo($id_rel_articulo) {
    global $conectar;
    try {
        // Iniciar la transacción
        $conectar->beginTransaction();

        // Obtener el sub_total y la cantidad del artículo
        $orden = $conectar->prepare("SELECT sub_total, cantidad, articulo_id FROM rel_venta_articulo WHERE id = :id_rel_articulo");
        $orden->bindParam(":id_rel_articulo", $id_rel_articulo, PDO::PARAM_INT);
        $orden->execute();

        $articulo = $orden->fetch(PDO::FETCH_ASSOC);
        $orden->closeCursor();

        if (!$articulo) {
            throw new Exception("No se encontró el artículo con el ID: $id_rel_articulo");
        }

        $sub_total = $articulo['sub_total'];
        $cantidad = $articulo['cantidad'];
        $articulo_id = $articulo['articulo_id'];

        // Eliminar el artículo de la tabla rel_venta_articulo
        $orden = $conectar->prepare("DELETE FROM rel_venta_articulo WHERE id = :id_rel_articulo");
        $orden->bindParam(":id_rel_articulo", $id_rel_articulo, PDO::PARAM_INT);
        $orden->execute();
        $orden->closeCursor();

        // Actualizar el stock del artículo
        $orden = $conectar->prepare("UPDATE articulo SET stock = stock + :cantidad WHERE id = :articulo_id");
        $orden->bindParam(":cantidad", $cantidad, PDO::PARAM_INT);
        $orden->bindParam(":articulo_id", $articulo_id, PDO::PARAM_INT);
        $orden->execute();
        $orden->closeCursor();

        // Actualizar el total de la venta (restando el sub_total eliminado)
        $orden = $conectar->prepare("UPDATE venta SET total = total - :sub_total WHERE id = :venta_id");
        $orden->bindParam(":sub_total", $sub_total, PDO::PARAM_STR);
        // Necesitas saber el id de la venta, supongo que puedes pasarlo desde algún lado.
        $orden->bindParam(":venta_id", $articulo['venta_id'], PDO::PARAM_INT);
        $orden->execute();
        $orden->closeCursor();

        // Confirmar la transacción si todo salió bien
        $conectar->commit();

        echo json_encode(["success" => true, "message" => "Artículo eliminado exitosamente."]);

    } catch (\Throwable $th) {
        // Si hay un error, hacer rollback de la transacción
        $conectar->rollBack();
        error_log("Error al eliminar artículo: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}


function fn_eliminar_movimiento($id_rel_articulo) {
    global $conectar;
    try {
        // Iniciar la transacción
        $conectar->beginTransaction();

        // Obtener el sub_total del movimiento a eliminar
        $orden = $conectar->prepare("SELECT sub_total, venta_id FROM rel_venta_articulo WHERE id = :id_rel_articulo");
        $orden->bindParam(":id_rel_articulo", $id_rel_articulo, PDO::PARAM_INT);
        $orden->execute();

        $movimiento = $orden->fetch(PDO::FETCH_ASSOC);
        $orden->closeCursor();

        if (!$movimiento) {
            throw new Exception("No se encontró el movimiento con el ID: $id_rel_articulo");
        }

        $sub_total = $movimiento['sub_total'];
        $venta_id = $movimiento['venta_id'];

        // Eliminar el movimiento de la tabla rel_venta_articulo
        $orden = $conectar->prepare("DELETE FROM rel_venta_articulo WHERE id = :id_rel_articulo");
        $orden->bindParam(":id_rel_articulo", $id_rel_articulo, PDO::PARAM_INT);
        $orden->execute();
        $orden->closeCursor();

        // Actualizar el total de la venta (restando el sub_total del movimiento eliminado)
        $orden = $conectar->prepare("UPDATE venta SET total = total - :sub_total WHERE id = :venta_id");
        $orden->bindParam(":sub_total", $sub_total, PDO::PARAM_STR);
        $orden->bindParam(":venta_id", $venta_id, PDO::PARAM_INT);
        $orden->execute();
        $orden->closeCursor();

        // Confirmar la transacción si todo salió bien
        $conectar->commit();

        echo json_encode(["success" => true, "message" => "Movimiento eliminado exitosamente."]);

    } catch (\Throwable $th) {
        // Si hay un error, hacer rollback de la transacción
        $conectar->rollBack();
        error_log("Error al eliminar movimiento: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}
