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
        case 'FINALIZARVENTARAPIDO':
            if (isset($_POST["jsDatosVenta"])) {
                $jsDatosVenta = $_POST["jsDatosVenta"];
                $js_articulos = $_POST["js_articulos"];
                $js_detalle_pago = $_POST["js_detalle_pago"];
                finalizarVentaReservaRapido($jsDatosVenta, $js_articulos, $js_detalle_pago);
            }
            break;
        case 'FINALIZARVENTACREDITO':
            if (isset($_POST["jsDatosVenta"])) {
                $jsDatosVenta = $_POST["jsDatosVenta"];
                finalizarVentaReservaCredito($jsDatosVenta);
            }
            break;
        case 'FINALIZARVENTACREDITORAPIDO':
            if (isset($_POST["jsDatosVenta"])) {
                $jsDatosVenta = $_POST["jsDatosVenta"];
                $js_articulos = $_POST["js_articulos"];
                $js_detalle_deuda = $_POST["js_detalle_deuda"];
                finalizarVentaReservaCreditoRapido($jsDatosVenta, $js_articulos, $js_detalle_deuda);
            }
            break;
        case 'ABONARDEUDACLIENTE':
            if (isset($_POST["jsDatosAbono"])) {
                $jsDatos = $_POST["jsDatosAbono"];
                abonarDeuda($jsDatos);
            }
            break;

        case 'REGISTAR_ARTICULO':
            if (isset($_POST["jsDatosArticulo"])) {
                $jsDatos = $_POST["jsDatosArticulo"];
                paRegistrarArticulo($jsDatos);
            }
            break;
        case 'REGISTRAR_COMPRA':
            if (isset($_POST["jsDatosCompra"])) {
                $jsDatos = $_POST["jsDatosCompra"];
                paRegistrarCompra($jsDatos);
            }
            break;
        case 'APERTURACAJA':
            //fnAperturaDeCajaChica
            // Otros casos si los necesitas
            if (isset($_POST["jsDatoCaja"])) {
                $jsDatos = $_POST["jsDatoCaja"];
                fnAperturaDeCajaChica($jsDatos);
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
    $json_pagos = json_encode($data['js_detalle_pagos']);

    try {

        $sql = "SELECT fn_finalizar_venta_directa(:venta_id, :atencion_final_usuario, :numUpdateTelefonoPersona, :monto_original, :monto_venta_final,:js_pagos)";
        $stmt = $conectar->prepare($sql);


        $stmt->bindParam(':venta_id', $venta_id);
        $stmt->bindParam(':atencion_final_usuario', $atencion_final_usuario);
        $stmt->bindParam(':numUpdateTelefonoPersona', $numUpdateTelefonoPersona);
        $stmt->bindParam(':monto_original', $monto_original);
        $stmt->bindParam(':monto_venta_final', $monto_venta_final);
        $stmt->bindParam(':js_pagos', $json_pagos);

        $stmt->execute();


        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_finalizar_venta_directa'];


        $response = json_decode($jsonResponse, true);


        echo json_encode($response);
    } catch (Exception $e) {

        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la venta. Consultar con el Administrador de Sistemas']);
    }
}

function finalizarVentaReservaRapido($jsDatosVenta, $js_articulos, $js_detalle_pago)
{
    global $conectar;

    try {

        $sql = "SELECT fn_finalizar_venta_directa_rapida(:json_venta,:json_detalles,:json_pagos)";
        $stmt = $conectar->prepare($sql);


        $stmt->bindParam(':json_venta', $jsDatosVenta);
        $stmt->bindParam(':json_detalles', $js_articulos);
        $stmt->bindParam(':json_pagos', $js_detalle_pago);


        $stmt->execute();


        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_finalizar_venta_directa_rapida'];


        $response = json_decode($jsonResponse, true);


        echo json_encode($response);
    } catch (Exception $e) {

        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la venta. Consultar con el Administrador de Sistemas']);
    }
}

function finalizarVentaReservaCreditoRapido($jsDatosVenta, $js_articulos, $js_detalle_deuda)
{
    global $conectar;


    try {
        $sql = "SELECT fn_finalizar_venta_credito_rapida(:json_venta,:json_detalles, :json_deudas)";
        $stmt = $conectar->prepare($sql);

        if ($js_detalle_deuda === null || $js_detalle_deuda === "null") {
            $js_detalle_deuda = "[]"; // Reemplaza null por un array vacío en JSON
        }

        $stmt->bindParam(':json_venta', $jsDatosVenta);
        $stmt->bindParam(':json_detalles', $js_articulos);
        $stmt->bindParam(':json_deudas', $js_detalle_deuda);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_finalizar_venta_credito_rapida'];

        $response = json_decode($jsonResponse, true);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la venta. Consultar con el Administrador de Sistemas.' . $e]);
    }
}

function finalizarVentaReservaCredito($jsDatosVenta)
{
    global $conectar;

    $data = json_decode($jsDatosVenta, true);

    $venta_id = $data['venta_id'];
    $atencion_final_usuario = $data['atencion_final_usuario'];
    $numUpdateTelefonoPersona = $data['numUpdateTelefonoPersona'];
    $monto_original = $data['monto_original'];
    $monto_venta_final = $data['monto_venta_final'];
    $monto_inicial_deuda = $data['monto_inicial'];
    $json_deuda = $data['js_detalle_deuda'];


    if (is_null($json_deuda) || empty($json_deuda)) {

        $json_deuda = null;
    } else {

        $json_deuda = json_encode($json_deuda);
    }

    try {
        $sql = "SELECT fn_finalizar_venta_credito(:venta_id, :atencion_final_usuario, :numUpdateTelefonoPersona, :monto_original, :monto_venta_final,:monto_ini, :js_deudas)";
        $stmt = $conectar->prepare($sql);

        $stmt->bindParam(':venta_id', $venta_id);
        $stmt->bindParam(':atencion_final_usuario', $atencion_final_usuario);
        $stmt->bindParam(':numUpdateTelefonoPersona', $numUpdateTelefonoPersona);
        $stmt->bindParam(':monto_original', $monto_original);
        $stmt->bindParam(':monto_venta_final', $monto_venta_final);
        $stmt->bindParam(':monto_ini', $monto_inicial_deuda);
        $stmt->bindParam(':js_deudas', $json_deuda);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_finalizar_venta_credito'];

        $response = json_decode($jsonResponse, true);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la venta. Consultar con el Administrador de Sistemas.' . $e]);
    }
}

function paRegistrarArticulo($jsDatosArticulo)
{
    global $conectar;

    $data = json_decode($jsDatosArticulo, true);

    $categoria_id = $data['categoria_id'];
    $color = $data['color'];
    $corte = false;
    $dimension_id = $data['dimension_id'];
    $escala_id = $data['escala_id'];
    $marca = $data['marca'];
    $nombre = $data['nombre'];
    $tipo_id = $data['tipo_id'];

    try {
        $sql = "SELECT fn_registrar_articulo(:nombre, :categoria_id, :tipo_id, :dimension_id, :escala_id, :corte, :color, :marca)";

        $stmt = $conectar->prepare($sql);

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':categoria_id', $categoria_id);
        $stmt->bindParam(':tipo_id', $tipo_id);
        $stmt->bindParam(':dimension_id', $dimension_id);
        $stmt->bindParam(':escala_id', $escala_id);
        $stmt->bindParam(':corte', $corte, PDO::PARAM_BOOL); // Usando PDO::PARAM_BOOL para asegurarse que se trata como booleano
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':marca', $marca);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_registrar_articulo'];

        $response = json_decode($jsonResponse, true);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar el Registro. Consultar con el Administrador de Sistemas.' . $e, 'jsonEntrada' => $data, 'SQL' => $stmt]);
    }
}


function abonarDeuda($jsDatosAbono)
{
    global $conectar;


    $data = json_decode($jsDatosAbono, true);


    $cliente_id = $data['cliente_id'];
    $usuario_id = $data['usuario_id'];
    $montoAbono = $data['montoAbono'];
    $json_pagos_abono = json_encode($data['js_detalle_pagos']);


    try {

        $sql = "SELECT fn_pagar_deuda(:p_cliente_id,:usuario_id_p,:monto_abono_p,:json_pagos_p);";
        $stmt = $conectar->prepare($sql);


        $stmt->bindParam(':p_cliente_id', $cliente_id);
        $stmt->bindParam(':usuario_id_p', $usuario_id);
        $stmt->bindParam(':monto_abono_p', $montoAbono);
        $stmt->bindParam(':json_pagos_p', $json_pagos_abono);

        $stmt->execute();


        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_pagar_deuda'];

        $response = json_decode($jsonResponse, true);

        echo json_encode($response);
    } catch (Exception $e) {

        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la venta. Consultar con el Administrador de Sistemas']);
    }
}

function paRegistrarCompra($jsDatosCompra)
{
    global $conectar;

    $data = json_decode($jsDatosCompra, true);

    try {

        $sql = "SELECT fn_registrar_compra(:jsDatosCompra);";
        $stmt = $conectar->prepare($sql);


        $stmt->bindParam(':jsDatosCompra', $jsDatosCompra);

        $stmt->execute();


        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_registrar_compra'];

        $response = json_decode($jsonResponse, true);

        echo json_encode($response);
    } catch (Exception $e) {

        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la venta. Consultar con el Administrador de Sistemas']);
    }
}
function fnAperturaDeCajaChica($jsDatoCaja)
{
    global $conectar;

    $data = json_decode($jsDatoCaja, true);

    $responsable_id = $data['responsable_id'];
    $responsable = $data['responsable'];
    $monto = $data['monto'];

    try {
        $sql = "SELECT fn_apertura_caja(:responsable_id, :responsable, :monto)";
        $stmt = $conectar->prepare($sql);

        $stmt->bindParam(':responsable_id', $responsable_id);
        $stmt->bindParam(':responsable', $responsable);
        $stmt->bindParam(':monto', $monto);


        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_apertura_caja'];

        $response = json_decode($jsonResponse, true);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la venta. Consultar con el Administrador de Sistemas.' . $e]);
    }
}
