<?php
include ("bd.php");
if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    controladorPostreVenta($accion);
}

function controladorPostreVenta($accion){
    switch ($accion) {
        case 'INSERTPOSTREVENTA':
            $datosAddPostreVenta = json_decode($_POST["dataInsertPostreVenta"], true);
            echo "INSERTPOSTREVENTA"+$datosAddPostreVenta;
            //insertPrenda($datosAddPrenda);
            break;
        
        default:
            break;
    }
}

?>