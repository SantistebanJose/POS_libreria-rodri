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

