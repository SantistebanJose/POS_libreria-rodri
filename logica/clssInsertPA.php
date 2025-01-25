<?php
include("bd.php");

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorClssInsertPA($accion);
}

function controladorClssInsertPA($accion)
{
    switch ($accion) {
        case 'FINALIZARVENTA':
            if (isset($_POST["jsDatosVenta"])) {
                $jsDatosVenta = $_POST["jsDatosVenta"];
                finalizarVentaReserva($jsDatosVenta);
            }
            break;
        case 'CAMBIARCONTRASEÑA':
            // Otros casos si los necesitas
            break;
    }
}

function finalizarVentaReserva($jsDatosVenta)
{
    global $conectar;

    
    $data = json_decode($jsDatosVenta, true);

    
    $venta_id = $data['venta_id'];
    $atencion_final_usuario = $data['atencion_final_usuario'];
    $numUpdateTelefonoPersona = $data['numUpdateTelefonoPersona'];
    $monto_original = $data['monto_original'];
    $monto_venta_final = $data['monto_venta_final'];

    try {
        
        $sql = "SELECT fn_finalizar_venta(:venta_id, :atencion_final_usuario, :numUpdateTelefonoPersona, :monto_original, :monto_venta_final)";
        $stmt = $conectar->prepare($sql);
        
        
        $stmt->bindParam(':venta_id', $venta_id);
        $stmt->bindParam(':atencion_final_usuario', $atencion_final_usuario);
        $stmt->bindParam(':numUpdateTelefonoPersona', $numUpdateTelefonoPersona);
        $stmt->bindParam(':monto_original', $monto_original);
        $stmt->bindParam(':monto_venta_final', $monto_venta_final);

        
        $stmt->execute();

        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_finalizar_venta']; 

        
        $response = json_decode($jsonResponse, true);

        
        echo json_encode($response);  
    } catch (Exception $e) {
        
        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la venta']);
    }
}


?>
