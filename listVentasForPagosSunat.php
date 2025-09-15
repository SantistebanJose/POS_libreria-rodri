<?php
include("cabecera.php");

?>

<div
    class="container">
    <div class="page-inner">
        <div class="card text-start">

            <div class="card-body">

                <h4 class="card-title"> <i class="fab fa-staylinked"></i> Ventas Pagadas Para Declarar a SUNAT </h4>
                <div class="card-sub">
                    Marque <strong>en el boton verder</strong> a los comprobantes que desea <strong>declarar a SUNAT.</strong>
                </div>

                <div class="tablita-responsive">
                    <div class="table-responsive">
                        <table id="tabla_boletas" class="table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marcado como</th>
                                    <th>N° Documento</th>
                                    <th>CLIENTE</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Enviar SUNAT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach (listarVentasPagadasParaComprobantes() as $datos) {
                                    $datosJSON = ($datos);
                                    $js_detalle = ($datos["js_detalle_venta"]);
                                    $datosEmisorJSON = (fnListarEmisor()[0]);

                                    $datax_completo = array("datos_query" => $datosJSON, "js_detalle" => $js_detalle, "emisor" => $datosEmisorJSON);

                                    $datosFunctionEvio = json_encode($datax_completo);
                                ?>
                                    <tr>
                                        <td><?php echo $datos["venta_id"] ?></td>
                                        <td><?php echo $datos["tipo_comprobante"] ?></td>
                                        <td><?php echo $datos["ca_cliente_numero_documento_sunat"] ?></td>
                                        <td><?php echo $datos["ca_cliente_cliente_sunat"] ?></td>
                                        <td><?php echo "S/ " . number_format($datos["monto_venta_final"], 2) ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($datos["fecha"])) ?></td>
                                        <td>
                                            <div class="mt-2 text-center d-flex justify-content-center">
                                                <a
                                                    href="javascript:void(0);"
                                                    onclick='fn_enviar_sunat_por_fila(<?php echo $datosFunctionEvio ?>)'
                                                    class="btn btn-success btn-round btn-sm mx-1"
                                                    role="button" aria-label="Enviar a SUNAT">
                                                    <i class="fas fa-hand-holding-usd"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <br>
                <!-- 
                <a
                    name=""
                    id=""
                    onclick='fn_enviar_sunat(<?php echo $datosEmisorJSON ?>)'
                    class="btn btn-secondary btn-round btn-md mx-1"
                    role="button">
                    <i class="fas fa-paper-plane"></i> Enivar a SUNAT
                </a>
                -->

            </div>
        </div>
    </div>
</div>

<script>
    function fn_enviar_sunat_por_fila(jsDatosDeMrd) {
        console.log(jsDatosDeMrd);
        var js_ya_arreglado = (jsDatosDeMrd);
        var jsDatosEmisor = jsDatosDeMrd["emisor"];
        var jsonDetalleArray = JSON.parse(jsDatosDeMrd["js_detalle"]);


        var operaciones_gravadas = 0;
        var igv = 0;
        var item_cont = 0;

        let valorTotalVentas = 0;
        jsonDetalleArray.forEach((item) => {
            valorTotalVentas += parseFloat(item["pu_sin_igv"]) * item["cantidad_sunat"];
        });


        var datos_query = js_ya_arreglado["datos_query"];
        var descuentoTotal = parseFloat(datos_query["descuento"]);

        var js_detalles = [];
        jsonDetalleArray.forEach((item, index) => {
            item_cont = item_cont + 1;

            let sutTotalDetalle = (parseFloat(item["pu_sin_igv"]) * item["cantidad_sunat"]);
            let descuentoPorArticulo = (descuentoTotal * (parseFloat(item["pu_sin_igv"]) * item["cantidad_sunat"])) / valorTotalVentas;
            let valorUnitarioConDescuento = parseFloat(item["pu_sin_igv"] / item["cantidad_sunat"]) - descuentoPorArticulo;
            valorUnitarioConDescuento = (valorUnitarioConDescuento);

            var detalle_ = {
                "item": item_cont,
                "cantidad": item["cantidad_sunat"],
                "unidad": "NIU",
                "nombre": item["descripcion_articulo"],
                "valor_unitario": parseFloat(valorUnitarioConDescuento).toFixed(2),
                "precio_lista": parseFloat(valorUnitarioConDescuento * 1.18).toFixed(2),
                "pu_con_igv": parseFloat(valorUnitarioConDescuento * 1.18).toFixed(2),
                "pu_sin_igv": parseFloat(valorUnitarioConDescuento).toFixed(2),
                "total_impuestos": parseFloat((valorUnitarioConDescuento * 1.18 - valorUnitarioConDescuento) * item["cantidad_sunat"]).toFixed(2),
                "igv": parseFloat((valorUnitarioConDescuento * 0.18)).toFixed(2),
                "valor_total": parseFloat((valorUnitarioConDescuento * item["cantidad_sunat"])).toFixed(2)
            };

            js_detalles.push(detalle_);
            operaciones_gravadas += (valorUnitarioConDescuento * item["cantidad_sunat"]);
            igv += parseFloat(item["IGV"]);
        });

        let flagTipoDocumento;
        if (datos_query["ca_cliente_numero_documento_sunat"].length <= 10) {
            flagTipoDocumento = "1"
        } else {
            flagTipoDocumento = "6"
        }
        const datos_cliente = {
            "tipo_documento": datos_query["ca_cliente_tipo_documento_sunat"],
            "numero_doc_cliente": datos_query["ca_cliente_numero_documento_sunat"],
            "cliente": datos_query["ca_cliente_cliente_sunat"],
            "direccion": datos_query["ca_cliente_direccion_sunat"]
        };

        const tipo_comprobante_ref = datos_query["tipo_comprobante"] === "BOLETA" ? "03" : "01";

        const datos_cabecera = {
            "venta_id": datos_query["venta_id"],
            "ruc_emisor": jsDatosEmisor["ruc"],
            "numero_doc_cliente": datos_query["ca_cliente_numero_documento_sunat"],
            "tipo_operacion": "0101",
            "tipo_comprobante": tipo_comprobante_ref,
            "moneda": "PEN",
            "serie": datos_query["serie"],
            "forma_pago": "Contado",
            "total_op_gravadas": parseFloat(operaciones_gravadas).toFixed(2),
            "igv": parseFloat(igv).toFixed(2),
            "icbper": 0,
            "total_op_exoneradas": 0.0,
            "total_op_inafectas": 0.0,
            "total_antes_impuestos": parseFloat(operaciones_gravadas).toFixed(2),
            "total_impuestos": parseFloat(igv).toFixed(2),
            "total_despues_impuestos": (parseFloat(operaciones_gravadas) + parseFloat(igv)).toFixed(2),
            "total_a_pagar": (parseFloat(operaciones_gravadas) + parseFloat(igv)).toFixed(2),
            "total_pagado_en_caja": parseFloat(datos_query["monto_venta_final"]).toFixed(2),
            "tipo_comp_ref": "03",
            "serie_correletaivo_ref": datos_query["serie_correltavio_referencial"],
            "fecha_emision": datos_query["fecha"],
            "fecha_vencimiento": datos_query["fecha"],
            "hora_emision": datos_query["hora"],
            "descuento": parseFloat(datos_query["descuento"]).toFixed(2)
        };


        var jsDatos = {
            "emisor": jsDatosEmisor,
            "cliente": datos_cliente,
            "cabecera": datos_cabecera,
            "detalles": js_detalles
        };

        let ajaxRequest = $.ajax({
            url: 'logica/clssComprobante.php',
            type: 'POST',
            data: {
                accion: 'REGISTROCOMPROBANTESBD',
                jsComprobantes: JSON.stringify(jsDatos)
            },
            success: function(response) {
                swal({
                    title: "Comprobantes Enviados",
                    text: "Comprobantes declarados a SUNAT con éxito!",
                    icon: "success",
                    buttons: false,
                    timer: 1500
                }).then(() => {
                    console.log(response);
                    location.reload();
                });;

            },
            error: function(xhr, status, error) {
                console.log('Error:', error);
                console.log('Detalles del error:', xhr.responseText);
            }
        });

        ajaxRequests.push(ajaxRequest); // Agregar la promesa de la solicitud AJAX al array


    }

    function fn_enviar_sunat(jsDatosEmisor) {
        console.log(jsDatosEmisor);

        const filas = document.querySelectorAll("#tabla_boletas tbody tr");
        let ajaxRequests = []; // Array para guardar las promesas de AJAX
        let datosSeleccionados = []; // Array para guardar los datos de las filas seleccionadas

        filas.forEach(fila => {
            const checkbox = fila.querySelector("td input[type='checkbox']");

            if (checkbox && checkbox.checked) {
                const jsonDetalleArray = JSON.parse(fila.cells[8].innerText);
                var operaciones_gravadas = 0;
                var igv = 0;
                var item_cont = 0;

                let valorTotalVentas = 0;
                jsonDetalleArray.forEach((item) => {
                    valorTotalVentas += parseFloat(item["pu_sin_igv"]) * item["cantidad_sunat"];
                });

                var datos_query = JSON.parse(fila.cells[7].innerText);
                var descuentoTotal = parseFloat(datos_query["descuento"]);

                var js_detalles = [];
                jsonDetalleArray.forEach((item, index) => {
                    item_cont = item_cont + 1;

                    let sutTotalDetalle = (parseFloat(item["pu_sin_igv"]) * item["cantidad_sunat"]);
                    let descuentoPorArticulo = (descuentoTotal * (parseFloat(item["pu_sin_igv"]) * item["cantidad_sunat"])) / valorTotalVentas;
                    let valorUnitarioConDescuento = parseFloat(item["pu_sin_igv"] / item["cantidad_sunat"]) - descuentoPorArticulo;
                    valorUnitarioConDescuento = (valorUnitarioConDescuento);

                    var detalle_ = {
                        "item": item_cont,
                        "cantidad": item["cantidad_sunat"],
                        "unidad": "NIU",
                        "nombre": item["descripcion_articulo"],
                        "valor_unitario": parseFloat(valorUnitarioConDescuento).toFixed(2),
                        "precio_lista": parseFloat(valorUnitarioConDescuento * 1.18).toFixed(2),
                        "pu_con_igv": parseFloat(valorUnitarioConDescuento * 1.18).toFixed(2),
                        "pu_sin_igv": parseFloat(valorUnitarioConDescuento).toFixed(2),
                        "total_impuestos": parseFloat((valorUnitarioConDescuento * 1.18 - valorUnitarioConDescuento) * item["cantidad_sunat"]).toFixed(2),
                        "igv": parseFloat((valorUnitarioConDescuento * 0.18)).toFixed(2),
                        "valor_total": parseFloat((valorUnitarioConDescuento * item["cantidad_sunat"])).toFixed(2)
                    };

                    js_detalles.push(detalle_);
                    operaciones_gravadas += (valorUnitarioConDescuento * item["cantidad_sunat"]);
                    igv += parseFloat(item["IGV"]);
                });

                const datos_cliente = {
                    "tipo_documento": "01",
                    "numero_doc_cliente": datos_query["ca_cliente_numero_documento_sunat"],
                    "cliente": fila.cells[4].innerText,
                    "direccion": ""
                };

                const tipo_comprobante_ref = datos_query["tipo_comprobante"] === "BOLETA" ? "03" : "01";

                const datos_cabecera = {
                    "venta_id": datos_query["venta_id"],
                    "ruc_emisor": jsDatosEmisor["ruc"],
                    "numero_doc_cliente": datos_query["ca_cliente_numero_documento_sunat"],
                    "tipo_operacion": "0101",
                    "tipo_comprobante": tipo_comprobante_ref,
                    "moneda": "PEN",
                    "serie": datos_query["serie"],
                    "forma_pago": "Contado",
                    "total_op_gravadas": parseFloat(operaciones_gravadas).toFixed(2),
                    "igv": parseFloat(igv).toFixed(2),
                    "icbper": 0,
                    "total_op_exoneradas": 0.0,
                    "total_op_inafectas": 0.0,
                    "total_antes_impuestos": parseFloat(operaciones_gravadas).toFixed(2),
                    "total_impuestos": parseFloat(igv).toFixed(2),
                    "total_despues_impuestos": (parseFloat(operaciones_gravadas) + parseFloat(igv)).toFixed(2),
                    "total_a_pagar": (parseFloat(operaciones_gravadas) + parseFloat(igv)).toFixed(2),
                    "total_pagado_en_caja": parseFloat(datos_query["monto_venta_final"]).toFixed(2),
                    "tipo_comp_ref": "03",
                    "serie_correletaivo_ref": datos_query["serie_correltavio_referencial"],
                    "fecha_emision": datos_query["fecha"],
                    "fecha_vencimiento": datos_query["fecha"],
                    "hora_emision": datos_query["hora"],
                    "descuento": parseFloat(datos_query["descuento"]).toFixed(2)
                };

                const datosFila = {
                    venta_id: fila.cells[1].innerText,
                    tipo_comprobante: fila.cells[2].innerText,
                    numero_documento: fila.cells[3].innerText,
                    cliente: fila.cells[4].innerText,
                    monto: fila.cells[5].innerText,
                    fecha: fila.cells[6].innerText,
                    hora: fila.cells[7].innerText,
                    datos_query: JSON.parse(fila.cells[7].innerText),
                    js_detalle: JSON.parse(fila.cells[8].innerText)
                };

                var jsDatos = {
                    "emisor": jsDatosEmisor,
                    "cliente": datos_cliente,
                    "cabecera": datos_cabecera,
                    "detalles": js_detalles
                };

                let ajaxRequest = $.ajax({
                    url: 'logica/clssComprobante.php',
                    type: 'POST',
                    data: {
                        accion: 'REGISTROCOMPROBANTESBD',
                        jsComprobantes: JSON.stringify(jsDatos)
                    },
                    success: function(response) {
                        swal({
                            title: "Comprobantes Enviados",
                            text: "Comprobantes declarados a SUNAT con éxito!",
                            icon: "success",
                            buttons: false,
                            timer: 1500
                        });
                        console.log(response);
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                        console.log('Detalles del error:', xhr.responseText);
                    }
                });

                ajaxRequests.push(ajaxRequest); // Agregar la promesa de la solicitud AJAX al array
                datosSeleccionados.push(datosFila);
            }
        });

        // Usamos Promise.all para esperar que todas las solicitudes AJAX se completen
        Promise.all(ajaxRequests).then(() => {
            console.log("Todos los comprobantes han sido enviados.");
            console.log(datosSeleccionados);
        });
    }
</script>

<?php
include("pie.php");
?>