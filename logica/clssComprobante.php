<?php
include("clssConsultas.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controlador_clss_comprobante($accion);
}

function controlador_clss_comprobante($accion)
{
    switch ($accion) {
        case 'REGISTROCOMPROBANTESBD':
            $jsComprobantes = $_POST["jsComprobantes"];
            fn_envio_prueba_comprobante($jsComprobantes);  // Llamada correcta
            break;
        case 'CAMBIARCONTRASEÑA':
            // Otros casos si los necesitas
            break;
    }
}

function fnEnviarBoleta($JsDatos)
{
    /*
     $data = json_decode($JsDatos, true);

    // Aquí se desglosan los datos
    $emisor = $data["emisor"];
    $dataCabecera = $data["cabecera"];
    $datosCliente = $data["cliente"];
    $cliente = array(
        "tipo_documento" => $datosCliente["tipo_documento"],
        "ruc" => $datosCliente["numero_doc_cliente"],
        "razon_social" => $datosCliente["cliente"],
        "direccion" => ""
    );
    $cabecera = array(
        "forma_pago" => "Contado",
        "tipo_operacion"    => $dataCabecera["tipo_operacion"],
        "tipo_comprobante" => $dataCabecera["tipo_comprobante"],
        "moneda"             => $dataCabecera["moneda"],
        "serie"                => $dataCabecera["serie"],
        "correlativo"        => $dataCabecera["correlativo_texto"],
        "total_op_gravadas" => $dataCabecera["total_op_gravadas"],
        "igv"                => $dataCabecera["igv"],
        "icbper"            => $dataCabecera["icbper"],
        "total_op_exoneradas" => $dataCabecera["total_op_exoneradas"],
        "total_op_inafectas" => $dataCabecera["total_op_inafectas"],
        "total_antes_impuestos" => $dataCabecera["total_antes_impuestos"],
        "total_impuestos"        => $dataCabecera["total_impuestos"],
        "total_despues_impuestos" => $dataCabecera["total_despues_impuestos"],
        "total_a_pagar"        => $dataCabecera["total_a_pagar"],
        "fecha_emision"        => $dataCabecera["fecha_emision"],
        "hora_emision"        => $dataCabecera["hora_emision"],
        "fecha_vencimiento" => $dataCabecera["fecha_vencimiento"]
    );

    $detalles = $data["detalles"];
    $items = array();
    
    foreach ($detalles as $item) {
        $items[] = array(
            "item"   => $item["item"],
            "cantidad"   => $item["cantidad"],
            "unidad"   => $item["unidad"],
            "nombre" => $item["nombre"],
            "valor_unitario" => $item["valor_unitario"],
            "precio_lista" => $item["precio_lista"],
            "valor_total" => $item["valor_total"],
            "igv"     => $item["igv"],
            "icbper"     => 0,
            "factor_icbper"    => 0.30,
            "total_antes_impuestos" => $item["pu_sin_igv"],
            "total_impuestos" => $item["total_impuestos"],
            "codigos" => array("S", "10", "1000", "IGV", "VAT")
        );
     */


    try {

        $emisor = array(
            "tipo_documento" => 6,
            "ruc"    => "20607599727",
            "razon_social" => "INSTITUTO INTERNACIONAL DE SOFTWARE S.A.C.",
            "nombre_comercial" => "ACADEMIA DE SOFTWARE",
            "departamento" => "LAMBAYEQUE",
            "provincia" => "CHICLAYO",
            "distrito" => "CHICLAYO",
            "direccion" => "CALLE OCHO DE OCTUBRE 123",
            "ubigeo" => "140101",
            "usuario_sol" => "MODDATOS",
            "clave_sol" => "MODDATOS",
            "clave_firma_digital" => "prueba1"
        );

        $cliente = array(
            "tipo_documento" => "",
            "ruc" => "",
            "razon_social" => "CLIENTE VARIOS",
            "direccion" => ""
        );

        $cabecera = array(
            "tipo_operacion"    => "0101",
            "tipo_comprobante" => "03", //boleta
            "moneda"             => "PEN",
            "serie"                => "B002",
            "correlativo"        => 123,
            "total_op_gravadas" => 50.17,
            "igv"                => 9.03,
            "icbper"            => 0.30,
            "total_op_exoneradas" => 140.00,
            "total_op_inafectas" => 270.00,
            "total_antes_impuestos" => 460.17,
            "total_impuestos"        => 9.33,
            "total_despues_impuestos" => 469.50,
            "total_a_pagar"        => 469.50,
            "fecha_emision"        => "2021-08-24",
            "hora_emision"        => "19:43:00",
            "fecha_vencimiento" => "2021-08-24"
        );

        $items = array();

        $items[] = array(
            "item"   => 1,
            "cantidad"   => 1,
            "unidad"   => "NIU",
            "nombre" => "MOCHILA",
            "valor_unitario" => 50.00,
            "precio_lista" => 59.00,
            "valor_total" => 50.00,
            "igv"     => 9.00,
            "icbper"     => 0.00,
            "factor_icbper"    => 0.30,
            "total_antes_impuestos" => 50.00,
            "total_impuestos" => 9.00,
            "codigos" => array("S", "10", "1000", "IGV", "VAT")
        );

        $items[] = array(
            "item"   => 2,
            "cantidad"   => 2,
            "unidad"   => "NIU",
            "nombre" => "LIBRO COQUITO",
            "valor_unitario" => 70.00,
            "precio_lista" => 70.00,
            "valor_total" => 140.00,
            "igv"     => 0.00,
            "icbper"     => 0.00,
            "factor_icbper"    => 0.30,
            "total_antes_impuestos" => 140.00,
            "total_impuestos" => 0.00,
            "codigos" => array("E", "20", "9997", "EXO", "VAT")
        );


        $items[] = array(
            "item"   => 3,
            "cantidad"   => 3,
            "unidad"   => "NIU",
            "nombre" => "MANZANA",
            "valor_unitario" => 90.00,
            "precio_lista" => 90.00,
            "valor_total" => 270.00,
            "igv"     => 0.00,
            "icbper"     => 0.00,
            "factor_icbper"    => 0.30,
            "total_antes_impuestos" => 270.00,
            "total_impuestos" => 0.00,
            "codigos" => array("E", "30", "9998", "INA", "FRE")
        );


        $items[] = array(
            "item"   => 4,
            "cantidad"   => 1,
            "unidad"   => "NIU",
            "nombre" => "BOLSA PLÁSTICA",
            "valor_unitario" => 0.17,
            "precio_lista" => 0.50,
            "valor_total" => 0.17,
            "igv"     => 0.03,
            "icbper"     => 0.30,
            "factor_icbper"    => 0.30,
            "total_antes_impuestos" => 0.17,
            "total_impuestos" => 0.33,
            "codigos" => array("S", "10", "1000", "IGV", "VAT")
        );

        //fn_enviar_boleta($emisor, $cliente, $cabecera, $items);
        //fn_enviar_boleta($emisor, $cliente, $cabecera, $items);  // Llamada a la función
        //echo json_encode(['estado' => true, 'datos_detalle' => $items]);

    } catch (\Throwable $th) {
        echo json_encode(['estado' => false, 'error' => $th->getMessage(), 'code' => $th->getCode()]);
    }
}

function fn_envio_prueba_comprobante($jsDatos)
{

    $dataJs = json_decode($jsDatos, true);
    ///////////////////
    $emisor = $dataJs["emisor"];
    $cabecera = $dataJs["cabecera"];
    $cliente = $dataJs["cliente"];
    $detalles = $dataJs["detalles"];

    $correlativo = fnSiguienteCorrelativo($cabecera["tipo_comprobante"])[0]["correlativo_siguiente"];
    $correlativo_texto = fnSiguienteCorrelativo($cabecera["tipo_comprobante"])[0]["correlativo_texto"];
    //////////////////////////////////////
    $items = array();
    foreach ($detalles as $item) {
        $items[] = array(
            "item"   => $item["item"],
            "cantidad"   => $item["cantidad"],
            "unidad"   => $item["unidad"],
            "nombre" => $item["nombre"],
            "valor_unitario" => $item["valor_unitario"],
            "precio_lista" => $item["precio_lista"],
            "valor_total" => $item["valor_total"],
            "igv"     => $item["igv"],
            "icbper"     => 0,
            "factor_icbper"    => 0.30,
            "total_antes_impuestos" => $item["pu_sin_igv"],
            "total_impuestos" => $item["total_impuestos"],
            "codigos" => array("S", "10", "1000", "IGV", "VAT")
        );
    }
    //echo json_encode($cabecera);

    $data = array(
        "emisor" => array(
            "tipo_documento" => 6,
            "ruc" => $emisor["ruc"],
            "razon_social" => $emisor["razon_social"],
            "nombre_comercial" => $emisor["nombre_comercial"],
            "departamento" => $emisor["departamento"],
            "provincia" => $emisor["provincia"],
            "distrito" => $emisor["distrito"],
            "direccion" => $emisor["direccion"],
            "ubigeo" => $emisor["ubigeo"],
            "usuario_sol" => $emisor["usuario_sol"],
            "clave_sol" => $emisor["clave_sol"],
            "clave_firma_digital" => $emisor["contraseña_firma_digital"]
        ),
        "cliente" => array(
            "tipo_documento" => $cliente["tipo_documento"],
            "ruc" => $cliente["numero_doc_cliente"],
            "razon_social" => $cliente["cliente"],
            "direccion" => ""
        ),

        "cabecera" => array(
            "tipo_operacion" => $cabecera["tipo_operacion"],
            "tipo_comprobante" => $cabecera["tipo_comprobante"],
            "moneda" => $cabecera["moneda"],
            "serie" => $cabecera["serie"],
            "correlativo" => $correlativo_texto,
            "forma_pago" => $cabecera["forma_pago"],		
            "total_op_gravadas" => $cabecera["total_op_gravadas"],
            "igv" => $cabecera["igv"],
            "icbper" => $cabecera["icbper"],
            "total_op_exoneradas" => $cabecera["total_op_exoneradas"],
            "total_op_inafectas" => $cabecera["total_op_inafectas"],
            "total_antes_impuestos" => $cabecera["total_antes_impuestos"],
            "total_impuestos" => $cabecera["total_impuestos"],
            "total_despues_impuestos" => $cabecera["total_despues_impuestos"],
            "total_a_pagar" => $cabecera["total_a_pagar"],
            "fecha_emision" => $cabecera["fecha_emision"],
            "hora_emision" => $cabecera["hora_emision"],
            "fecha_vencimiento" => $cabecera["fecha_vencimiento"]
        ),
        "items" => $items
    );

    // Convertir los datos a JSON
    $jsonData = json_encode($data);

    // URL de la API que vas a invocar
    //http://192.168.18.162/
    $apiUrl = "http://localhost/";
    //$ip = "http://192.168.18.162/";
    $ip = "http://localhost/";
    $comprobante = "";
    if($cabecera["tipo_comprobante"]=== "03"){
        $comprobante = "BOLETA";
        $apiUrl = $ip.'tallerfe/clssEnviarBoleta.php';
    }else{
        $comprobante = "FACTURA";
        $apiUrl = $ip.'http://localhost/tallerfe/clssEnviarFactura.php';
    }
    

    // Inicializar cURL
    $ch = curl_init();

    // Configurar la solicitud cURL
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ));

    // Ejecutar la solicitud y obtener la respuesta
    $response = curl_exec($ch);

    // Verificar si hubo algún error con la solicitud cURL
    if (curl_errno($ch)) {
        echo 'Error en cURL: ' . curl_error($ch);
    } else {

        $respuesta_api = json_decode($response, true);
        ////////////////////////////////////////////////


        global $conectar;

        $sql = "
        INSERT INTO comprobante (
            ruc_emisor,
            tipo_comprobante,
            serie,
            correlativo,
            correlativo_texto,
            forma_pago,
            fecha_emision,
            fecha_vencimiento,
            moneda,
            op_gravadas,
            op_exoneradas,
            op_inafectas,
            igv,
            total,
            numero_doc_cliente,
            tipo_comp_ref,
            serie_correletaivo_ref,
            codmotivo,
            nombrexml,
            xmlbase64,
            hash,
            codigo_sunat,
            mensaje_sunat,
            estado_comprobante,
            estado_envio,
            comprobante,
            venta_id
        ) VALUES (
            :ruc_emisor,
            :tipo_comprobante,
            :serie,
            :correlativo,
            :correlativo_texto,
            :forma_pago,
            :fecha_emision,
            :fecha_vencimiento,
            :moneda,
            :op_gravadas,
            :op_exoneradas,
            :op_inafectas,
            :igv,
            :total,
            :numero_doc_cliente,
            :tipo_comp_ref,
            :serie_correletaivo_ref,
            :codmotivo,
            :nombrexml,
            :xmlbase64,
            :hash,
            :codigo_sunat,
            :mensaje_sunat,
            :estado_comprobante,
            :estado_envio,
            :comprobante,
            :venta_id
            );
        ";
        $stmt = $conectar->prepare($sql);



        $stmt->bindParam(':ruc_emisor', $emisor['ruc']);
        $stmt->bindParam(':tipo_comprobante', $cabecera['tipo_comprobante']);
        $stmt->bindParam(':serie', $cabecera['serie']);
        $stmt->bindParam(':correlativo', $correlativo);
        $stmt->bindParam(':correlativo_texto', $correlativo_texto);
        $stmt->bindParam(':forma_pago', $cabecera["forma_pago"]);
        $stmt->bindParam(':fecha_emision', $cabecera['fecha_emision']);
        $stmt->bindParam(':fecha_vencimiento', $cabecera['fecha_vencimiento']);
        $stmt->bindParam(':moneda', $cabecera['moneda']);
        $stmt->bindParam(':op_gravadas', $cabecera['total_op_gravadas']);
        $stmt->bindParam(':op_exoneradas', $cabecera['total_op_exoneradas']);
        $stmt->bindParam(':op_inafectas', $cabecera['total_op_inafectas']);
        $stmt->bindParam(':igv', $cabecera['igv']);
        $stmt->bindParam(':total', $cabecera['total_despues_impuestos']);
        $stmt->bindParam(':numero_doc_cliente', $cliente['numero_doc_cliente']);
        $stmt->bindParam(':tipo_comp_ref', $cabecera["tipo_comp_ref"]);
        $stmt->bindParam(':serie_correletaivo_ref', $cabecera["serie_correletaivo_ref"]);
        $codMotivo = "";
        $stmt->bindParam(':codmotivo', $codMotivo);
        $stmt->bindParam(':nombrexml', $respuesta_api["CDR"]);
        $stmt->bindParam(':xmlbase64', $respuesta_api["xml"]);
        $stmt->bindParam(':hash', $respuesta_api["CDR"]);
        $stmt->bindParam(':codigo_sunat', $respuesta_api["codigo"]);
        $stmt->bindParam(':mensaje_sunat', $respuesta_api["mensaje_sunat"]);
        $estado_comprobante = ($respuesta_api["estado"] === false) ? "0" : "1";
        $stmt->bindParam(':estado_comprobante', $estado_comprobante);
        $stmt->bindParam(':estado_envio', $respuesta_api["estado"], PDO::PARAM_BOOL);
        
        $stmt->bindParam(':comprobante', $comprobante);
        $stmt->bindParam(':venta_id', $cabecera["venta_id"]);
        
        try {

            $stmt->execute();
            echo "Comprobante insertado correctamente.";
        } catch (Exception $e) {
            echo "Error al insertar el comprobante: " . $e->getMessage();
        }


        echo 'Respuesta de la API: ' . $response;
    }

    // Cerrar la conexión cURL
    curl_close($ch);
}

// Llamar a la función
//fn_envio_prueba_boleta();
