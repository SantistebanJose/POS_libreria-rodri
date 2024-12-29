<?php
include("bd.php");


function listarMovimientos(): array {
    global $conectar;
    try {
        $orden = $conectar->prepare("
            SELECT 
            id, descripcion
            FROM
            movimiento
            where deleted_at is null
            ORDER BY id
        ");
        
        $orden->execute();
        $lista = $orden->fetchAll(PDO::FETCH_ASSOC);
        $orden->closeCursor();
    } catch (PDOException $e) {
        
        $lista = array(); 
    }
    
    return $lista;
}

?>