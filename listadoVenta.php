<?php
include("cabecera.php");
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}
?>
<div class="container">
    <div class="page-inner">
        <div class="card text-start">

            <div class="card-body">
                <h4 class="card-title">Listado de Ventas</h4>
                <div class="card-sub">
                    Selecciona de acuerdo a las ventas que necesites :)
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="ventaDiaria" data-bs-toggle="pill" href="#pills-ventaDiaria" role="tab" aria-controls="pills-ventaDiaria" aria-selected="true">Ventas del Día</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ventaSemanal" data-bs-toggle="pill" href="#pills-ventaSemanal" role="tab" aria-controls="pills-ventaSemanal" aria-selected="false">Ventas de la Semana</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-contact-tab-nobd" data-bs-toggle="pill" href="#pills-contact-nobd" role="tab" aria-controls="pills-contact-nobd" aria-selected="false">Todas las Ventas</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                        <div class="tab-pane fade show active" id="pills-ventaDiaria" role="tabpanel" aria-labelledby="ventaDiaria">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table
                                            id="TablaVentaDiaria"
                                            class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Cliente</th>
                                                    <th>DÍA</th>
                                                    <th>FECHA</th>
                                                    <th>HORA</th>
                                                    <th>TOTAL(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th>
                                                    <th>pérdida</th>
                                                    <th>ESTADO</th>
                                                    <th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach (fnListForVentasDiarias() as $datos) {
                                                    $datos['accion_ajax'] = 'DETALLEVENTA_VENTA_ID';
                                                    $datosJSON = json_encode($datos);
                                                ?>
                                                    <tr>
                                                        <td><?php echo $datos["venta_id"] ?></td>
                                                        <td><?php echo $datos["cliente"] ?></td>
                                                        <td><?php echo $datos["dia_nombre"] ?></td>
                                                        <td><?php echo $datos["fecha"] ?></td>
                                                        <td><?php echo $datos["hora"] ?></td>
                                                        <td><?php echo "S/ " . $datos["total"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_final"] ?></td>
                                                        <td><?php echo "S/ " . $datos["perdida_utilidad"] ?></td>
                                                        <td><?php echo $datos["estado_pago"] ?></td>
                                                        <td>
                                                            <div class="mt-2 text-center">
                                                                <a
                                                                    name=""
                                                                    id=""
                                                                    onclick='abrirModalDetalle(<?php echo $datosJSON ?>)'
                                                                    class="btn btn-success btn-round"
                                                                    role="button">Ver</a>
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
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-ventaSemanal" role="tabpanel" aria-labelledby="pills-profile-tab-nobd">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table
                                            id="TablaVentaSemanal"
                                            class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Cliente</th>
                                                    <th>DÍA</th>
                                                    <th>FECHA</th>
                                                    <th>HORA</th>
                                                    <th>TOTAL(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th>
                                                    <th>pérdida</th>
                                                    <th>ESTADO</th>
                                                    <th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach (fnListForVentasSemanales() as $datos) {
                                                    $datos['accion_ajax'] = 'DETALLEVENTA_VENTA_ID';
                                                    $datosJSON = json_encode($datos);
                                                ?>
                                                    <tr>
                                                        <td><?php echo $datos["venta_id"] ?></td>
                                                        <td><?php echo $datos["cliente"] ?></td>
                                                        <td><?php echo $datos["dia_nombre"] ?></td>
                                                        <td><?php echo $datos["fecha"] ?></td>
                                                        <td><?php echo $datos["hora"] ?></td>
                                                        <td><?php echo "S/ " . $datos["total"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_final"] ?></td>
                                                        <td><?php echo "S/ " . $datos["perdida_utilidad"] ?></td>
                                                        <td><?php echo $datos["estado_pago"] ?></td>
                                                        <td>
                                                            <div class="mt-2 text-center">
                                                                <a
                                                                    name=""
                                                                    id=""
                                                                    class="btn btn-success btn-round"
                                                                    onclick='abrirModalDetalle(<?php echo $datosJSON ?>)'
                                                                    role="button">Ver</a>
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
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-contact-nobd" role="tabpanel" aria-labelledby="pills-contact-tab-nobd">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table
                                            id="TablaVentaDiaria"
                                            class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Cliente</th>
                                                    <th>DÍA</th>
                                                    <th>FECHA</th>
                                                    <th>HORA</th>
                                                    <th>TOTAL(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th>
                                                    <th>pérdida</th>
                                                    <th>ESTADO</th>
                                                    <th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach (fnListForVentasTodasLasVentas() as $datos) {
                                                    $datos['accion_ajax'] = 'DETALLEVENTA_VENTA_ID';
                                                    $datosJSON = json_encode($datos);
                                                ?>
                                                    <tr>
                                                        <td><?php echo $datos["venta_id"] ?></td>
                                                        <td><?php echo $datos["cliente"] ?></td>
                                                        <td><?php echo $datos["dia_nombre"] ?></td>
                                                        <td><?php echo $datos["fecha"] ?></td>
                                                        <td><?php echo $datos["hora"] ?></td>
                                                        <td><?php echo "S/ " . $datos["total"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_final"] ?></td>
                                                        <td><?php echo "S/ " . $datos["perdida_utilidad"] ?></td>
                                                        <td><?php echo $datos["estado_pago"] ?></td>
                                                        <td>
                                                            <div class="mt-2 text-center">
                                                                <a
                                                                    name=""
                                                                    id=""
                                                                    onclick='abrirModalDetalle(<?php echo $datosJSON ?>)'
                                                                    class="btn btn-success btn-round"
                                                                    role="button">Ver</a>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Button trigger modal -->


<!-- Modal Detalle Venta Articulo -->
<style>
    /* Tamaño por defecto para pantallas grandes (computadoras) */
    .modal-dialog-custom {
        max-width: 900px;
        /* Este sería el tamaño 'normal' para computadoras */
        margin: 0 auto;
        /* Centra el modal */
    }

    /* Tamaño para pantallas medianas (tabletas) */
    @media (max-width: 768px) {
        .modal-dialog-custom {
            max-width: 80%;
            /* 80% del ancho de la pantalla en tabletas */
        }
    }

    /* Tamaño para pantallas pequeñas (teléfonos móviles) */
    @media (max-width: 576px) {
        .modal-dialog-custom {
            width: 100%;
            /* Asegura que el modal ocupe todo el ancho disponible en móviles */
            margin: 0 10px;
            /* Da un poco de espacio a los lados en móviles */
            max-width: 100%;
            /* No permite que el modal se haga más grande que el 100% */
        }
    }

    /* Asegura que el contenido del modal no se desborde */
    .modal-content {
        padding: 15px;
        /* Espaciado dentro del modal para que el contenido no esté pegado a los bordes */
    }

    .dataTable {
        overflow-x: auto;
        /* Para permitir desplazamiento horizontal si es necesario */
    }
</style>



<div
    class="modal fade"
    id="modalDetalleVenta"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document"> <!-- Usamos la clase personalizada aquí -->
        <div class="modal-content">


            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h4 class="card-title text-center" style="font-size: 28px;">Venta de S/ <strong id="idMontoVenta"></strong></h4>
                <hr>
                <p class="card-text text-center" id="idUtilidad">ssss</p>
                <div class="card-sub text-center">
                    Aquí podrás revisar los datos de la venta.
                </div>
                <div class="row justify-content-center align-items-center sm-2">
                    <div class="col-sm-6">
                        <div class="card text-start">
                            <div class="card-body" style="color:indigo">
                                <h4 class="card-title" style="color:indigo"><i class="fas fa-user"> </i> Cliente</h4>
                                <p class="card-text" id="nombreCliente"></p>
                                <hr>
                                <div><strong>N° DOCUMENTO:</strong> <span id="docCliente"></span></div>
                                <div><strong>Número de Celular:</strong> <span id="numCelCliente"></span></div>
                                <div><strong>Correo:</strong> <span id="emailCliente"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card text-start">
                            <div class="card-body">
                                <h4 class="card-text" style="color:green"><i class="fas fa-credit-card"> </i> Monto Final: S/ <strong id="idMontoFinalVenta"></strong> </h4>
                                <p>La venta real fue de <strong id="idTotalOriginal"></strong></p>
                                <div><strong>Atendido Por: </strong> <span id="idUsuario">3- FRANCO RODRIGO VALDIVIESO FIGUEROA</span></div>
                                <div><strong>Fecha:</strong> <span id="idFechaVenta"></span></div>
                                <div><strong>Hora:</strong> <span id="idHoraVenta">19:00:00</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">Detalle de Venta</div>
                        <div class="card-sub">
                            Revisa el detalle de la venta :)
                        </div>
                        <div class="table-responsive">
                            <table
                                id="tablaDetalle"
                                class="table table-head-bg-secondary mt-4">
                                <thead>
                                    <tr>
                                        <th scope="col">descripción</th>
                                        <th scope="col">corte</th>
                                        <th scope="col">Cant</th>
                                        <th scope="col">P.Uni</th>
                                        <th scope="col">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody id="">

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<script>

</script>

<script>
    function abrirModalDetalle(datosJsonVenta) {
        console.log("DATOS JSON: ", datosJsonVenta);
        $('#modalDetalleVenta').modal('show');
        document.getElementById("nombreCliente").innerText = datosJsonVenta.cliente;
        document.getElementById("docCliente").innerText = datosJsonVenta.numero_doc_cliente;
        document.getElementById("numCelCliente").innerText = datosJsonVenta.telefonomovil_cliente;
        document.getElementById("emailCliente").innerText = datosJsonVenta.email_cliente;

        document.getElementById("idMontoVenta").innerText = datosJsonVenta.monto_venta_final;
        document.getElementById("idMontoFinalVenta").innerText = datosJsonVenta.monto_venta_final;
        document.getElementById("idTotalOriginal").innerText = "S/ " + datosJsonVenta.total;
        document.getElementById("idFechaVenta").innerText = datosJsonVenta.fecha;
        document.getElementById("idHoraVenta").innerText = datosJsonVenta.hora;
        document.getElementById("idUsuario").innerText = datosJsonVenta.usuario;

        // Utilidad
        if (datosJsonVenta.perdida_utilidad < 0) {

            document.getElementById("idUtilidad").innerHTML = "<span style='color:red'> En esta venta, PERDISTE un margen de utilidad de <strong>S/" + (parseFloat(datosJsonVenta.perdida_utilidad) * -1.00).toFixed(2) + ".</strong> </span>";


        } else {
            if (datosJsonVenta.estado_pago === "CREDITO" && datosJsonVenta.estado_final === "VENTA REALIZADA AL CREDITO - AUN DEBE") {
                //acumulado_deuda
                document.getElementById("idUtilidad").innerHTML = "<b>" + datosJsonVenta.estado_final + "</b><br> <span style='color:green'>  En esta venta fue realizada a CRÉDITO. Tiene abonado S/ " + datosJsonVenta.acumulado_deuda + " </span> <span style='color:orange'><br><strong>Para más información, Revisar en la seccion de Crédito [Historial Clientes] </span>";
            } else if (datosJsonVenta.estado_pago === "CREDITO" && datosJsonVenta.estado_final === "PAGADO - CREDTIO") {
                document.getElementById("idUtilidad").innerHTML = "<b>" + datosJsonVenta.estado_final + "</b><br><span style='color:orange'> En esta venta fue realizada a CRÉDITO. <strong style = 'color:green'>Pagó su DEUDA</span>";
            } else {
                //document.getElementById("idUtilidad").innerHTML = "<span style='color:red'> En esta venta, PERDISTE un margen de utilidad de <strong>S/" + (parseFloat(datosJsonVenta.perdida_utilidad) * -1.00).toFixed(2) + ".</strong> </span>";
                document.getElementById("idUtilidad").innerHTML = "<span style='color:green'> <b> En esta venta, no hiciste rebajas :)</b> </span>";
            }


        }

        $.ajax({
            url: 'logica/clssConsultas.php',
            type: 'POST',
            data: {
                accion: datosJsonVenta.accion_ajax,
                venta_id: datosJsonVenta.venta_id
            },
            dataType: 'json',
            success: function(datosArticulo) {
                console.log("Detalles de venta: ", datosArticulo);
                var tabla = document.getElementById("tablaDetalle").getElementsByTagName("tbody")[0];
                tabla.innerHTML = '';

                for (let i = 0; i < datosArticulo.length; i++) {
                    let articulo = datosArticulo[i];
                    let nuevaFila = tabla.insertRow();
                    console.log(articulo);
                    let min = articulo["minutos"] !== null ? articulo["minutos"] : '';


                    //nuevaFila.insertCell(0).textContent = articulo["minutos"] !== null ? articulo["minutos"] : '-'; // Minutos            
                    //nuevaFila.insertCell(1).textContent = articulo["costo_por_minuto"] !== null ? articulo["costo_por_minuto"] : '-'; // Costo x Minuto
                    let totalCorte = (articulo["minutos"] === null && articulo["costo_por_minuto"] === null) ?
                        '-' : // Si ambos son null, mostramos una línea
                        (articulo["minutos"] && articulo["costo_por_minuto"]) ?
                        (articulo["costo_por_minuto"] * articulo["minutos"]) : articulo["sub_total"] || '-';

                    let totalCorteRedondeado = (totalCorte !== '-') ? "S/ " + (totalCorte.toFixed(2)) : totalCorte;

                    let texto = "";
                    if (articulo["minutos"] !== null || articulo["costo_por_minuto"] !== null) {
                        texto = articulo["descripcion"] + "\n" + "<span style='color:blue'> <b>[" + min + " Minutos X " + articulo["costo_por_minuto"] + " = " + totalCorte.toFixed(2) + "]</b></span>";

                    } else {
                        texto = articulo["descripcion"];
                    }
                    texto = articulo["descripcion"];

                    nuevaFila.insertCell(0).innerHTML = texto;
                    nuevaFila.insertCell(1).textContent = totalCorteRedondeado;
                    nuevaFila.insertCell(2).textContent = articulo["cantidad"] || '-';
                    nuevaFila.insertCell(3).textContent = articulo["precio_unitario_articulo"] != null ? "S/ " + articulo["precio_unitario_articulo"] : '-';
                    nuevaFila.insertCell(4).textContent = "S/ " + articulo["sub_total"] || '-';
                }

            },
            error: function(xhr, status, error) {
                console.error("Error al obtener los detalles de la venta:", error);
            }
        });
    }
</script>




<!-- Incluir el CSS de DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">

<!-- Incluir jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Incluir el JS de DataTables -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {

        fnDataTables();
    });

    function fnDataTables() {
        $(".dataTable").DataTable({
            "order": [
                [0, 'desc']
            ],
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sPrevious": "Anterior",
                    "sNext": "Siguiente",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        });
    }
</script>



<?php
include("pie.php");
?>