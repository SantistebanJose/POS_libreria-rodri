<?php
include("./bd.php");


function listarMovimientos(): array {
    global $conectar;
    try {
        $orden = $conectar->prepare("
            SELECT 
            *
            FROM
            movimiento
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