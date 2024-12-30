<?php
include("bd.php");
function listarInsumosCompra(): array
{
    global $conectar;
    try {
        $orden = $conectar->prepare("
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
        ");
        
        $orden->execute();
        $lista = $orden->fetchAll(PDO::FETCH_ASSOC);
        $orden->closeCursor();
    } catch (PDOException $e) {
        
        $lista = array(); 
    }
    
    return $lista;
}
function listarPostres(): array
{
    global $conectar;
    try {
        $orden = $conectar->prepare(query: "
            SELECT id,nombre,descripcion FROM postre WHERE deleted_at IS NULL;
        ");
        $orden -> execute();
        $datos = $orden->fetchAll(PDO::FETCH_ASSOC);
        $orden->closeCursor();

    } catch (\Throwable $th) {
        $datos = array(); 
    }
    return $datos;

}

function listarMovimientos(): array
{
    global $conectar;
    try {
        $orden = $conectar->prepare(query: "
            SELECT id, descripcion,ruta_php from movimiento WHERE deleted_at IS NULL order by 1;
        ");
        $orden -> execute();
        $datos = $orden->fetchAll(PDO::FETCH_ASSOC);
        $orden->closeCursor();

    } catch (\Throwable $th) {
        $datos = array(); 
    }
    return $datos;

}
function listarProductosVenta1(): array
{
    global $conectar;
    try {
        $orden = $conectar->prepare(query: "
            Select * from view_articulos;
        ");
        $orden -> execute();
        $datos = $orden->fetchAll(PDO::FETCH_ASSOC);
        $orden->closeCursor();

    } catch (\Throwable $th) {
        $datos = array(); 
    }
    return $datos;

}

?>