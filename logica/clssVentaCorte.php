<?php
include("bd.php");

if (isset($_POST["accion"])){
    $accion = $_POST["accion"];
    controladorVentaCorte($accion);
}

function controladorVentaCorte($accion){
    switch($accion){
        case 'CONSULTARRESERVA':
            $venta_id = $_POST["venta_id"];
           
            consultarDetalleReserva($venta_id);
            break;
        case 'REGISTRARRESERVA':
            $data = $_POST["data"];
            registrar_reserva($data);
            break;
        case 'CAMBIARCONTRASEÑA':
            break;

    }
}

function consultarDetalleReserva($venta_id){
    global $conectar;

    try{
        $orden = $conectar ->prepare("SELECT 
                rva.id AS rel_venta_articulo_id,
                rva.venta_id,
                rva.articulo_id,
                CONCAT(ar.nombre, ' (', dim.medida, ')') AS articulo_nombre,
                rva.cantidad,
                rva.precio_unitario_articulo,
                rva.minutos,
                rva.costo_por_minuto,
                rva.sub_total,
                ar.corte
            FROM 
                rel_venta_articulo AS rva
            INNER JOIN 
                articulo AS ar 
                ON rva.articulo_id = ar.id
            INNER JOIN 
                dimension AS dim 
                ON ar.dimension_id = dim.id
            WHERE 
                rva.venta_id = :id;");
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

        $orden = $conectar->prepare("INSERT INTO venta(usuario_id, cliente_id, estado_pago, estado_venta) 
                                     VALUES (:usuario_id, :cliente_id, 'P', 'R');");
        $orden->bindParam(":usuario_id", $datos['usuario_id']);
        $orden->bindParam(":cliente_id", $datos['cliente_id']);
        $orden->execute();
        $venta_id = $conectar->lastInsertId(); // Obtener el ID de la venta recién creada
        $orden->closeCursor();

        // Insertar en la tabla rel_venta_articulo y descontar stock
        foreach ($datos['articulos'] as $articulo) {
            $orden = $conectar->prepare("INSERT INTO rel_venta_articulo(venta_id, articulo_id, precio_unitario_articulo, cantidad, sub_total) 
                                         VALUES (:venta_id, :articulo_id, :precio_unitario, :cantidad, :sub_total);");
            $orden->bindParam(":venta_id", $venta_id);
            $orden->bindParam(":articulo_id", $articulo['articulo_id']);
            $orden->bindParam(":precio_unitario", $articulo['precio_unitario']);
            $orden->bindParam(":cantidad", $articulo['cantidad']);
            $orden->bindParam(":sub_total", $articulo['sub_total']);
            $orden->execute();
            $orden->closeCursor();

            $orden = $conectar->prepare("UPDATE articulo 
                                         SET stock = stock - :cantidad 
                                         WHERE articulo_id = :articulo_id;");
            $orden->bindParam(":cantidad", $articulo['cantidad']);
            $orden->bindParam(":articulo_id", $articulo['articulo_id']);
            $orden->execute();
            $orden->closeCursor();
        }

        $conectar->commit();
        echo json_encode(["success" => true, "venta_id" => $venta_id]);
    } catch (\Throwable $th) {
        $conectar->rollBack();
        echo json_encode(["error" => $th->getMessage()]);
    }
}