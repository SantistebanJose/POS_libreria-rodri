<?php
include("cabecera.php");
?>

<div
    class="container">

    <div class="page-inner">
        <h3 class="fw-bold mb-3"> <i class="fas fa-hands-helping"></i> Manejo de Caja</h3>

        <div class="row">
            <?php foreach (listarFormaPago() as $datos) {
                $datosJSON = json_encode($datos);
            ?>
                <div class="col-12 col-md-3 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-head-row d-flex justify-content-between align-items-center flex-wrap">
                                <div class="card-title d-flex align-items-center">
                                    <i class="<?php echo $datos["icon"]; ?>" style="color: <?php echo $datos["color"]; ?>; font-size: 1.5rem;"></i>
                                    <span style="color: <?php echo $datos["color"]; ?>; font-size: 1rem;" class="ms-2"> <?php echo $datos["nombre"]; ?> </span>
                                </div>

                                <div class="card-tools">
                                    <div class="dropdown">
                                        <button
                                            class="btn btn-sm btn-label-secondary dropdown-toggle btn-round"
                                            type="button"
                                            id="dropdownMenuButton"
                                            data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <button class="dropdown-item" href="#" onclick='fnAbrirSwalRegistroEgreso(<?php echo $datosJSON ?>)'><i class="fas fa-caret-right"></i> Registrar Egreso</button>
                                            <button class="dropdown-item" href="#" onclick='fnAbrirSwalRegistroIngreso(<?php echo $datosJSON ?>)'><i class="fas fa-caret-right"></i> Registrar Ingreso</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-category">Ultimo Movimiento <br> <?php echo $datos["fecha"] . " - " . $datos["hora"] ?></div>
                        </div>
                        <div class="card-body pb-0">
                            <div class="mb-4 mt-2">
                                <h3>S/ <?php echo $datos["monto"] ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>


        <div class="card text-start">

            <div class="card-body">
                <h4 class="card-title"><i class="fas fa-briefcase"></i> Flujo de Caja</h4>
                <hr>
                <div class="card text-start">
                    <div class="card-body">

                        <div class="table-responsive">
                            <table
                                id="TablaVentaDiaria"
                                class="dataTable display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Accionado</th>
                                        <th>Movimiento</th>
                                        <th>concepto</th>
                                        <th>Forma de Pago</th>
                                        <th>dia de semana</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Monto</th>
                                        <th>Nota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach (fnListadoMovimientoCajaGrande() as $datos) {
                                        $datosJSON = json_encode($datos);
                                    ?>
                                        <tr>
                                            <td><?php echo $datos["id"] ?></td>
                                            <td><?php echo $datos["accionado"] ?></td>
                                            <td><?php echo $datos["tipo_movimiento"] ?></td>
                                            <td><?php echo $datos["concepto"] ?></td>
                                            <td><?php echo $datos["forma_pago"] ?></td>
                                            <td><?php echo $datos["dia_semana"] ?></td>

                                            <td><?php echo $datos["fecha"] ?></td>
                                            <td><?php echo $datos["hora"] ?></td>
                                            <td>S/ <?php echo $datos["monto"] ?></td>
                                            <td><?php echo $datos["nota"] ?></td>
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
<div
    class="modal fade"
    id="modalRegistrarEgreso"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="card border-primary">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="card-body">

                        <div class="card-body text-center">
                            <div id="idFormaPago" style="display: none;"></div>
                            <h4 id="idContenidoTitulo"></h4>
                            <h4>S/ <span id="idMontoDisponible"></span></h4>
                        </div>

                        <div class="card-sub text-center">
                            Aquí podrás registrar <strong>EGRESOS de Caja.</strong>
                        </div>
                        <hr>
                        <div class="row justify-content-center align-items-center g-2">
                            <div class="col-md-6">
                                <div class="mb-3">

                                    <label for="idSelectConceptoEgreso" class="form-label"><strong> <i class="fas fa-align-left"></i> Concepto</strong></label>
                                    <select class="form-select form-select-md w-100" aria-label="Default select example" id="idSelectConceptoEgresoGrande">
                                        <option selected>Seleccione Concepto</option>
                                        <?php foreach (fnListadoConceptosEgresos("G") as $datos) { ?>
                                            <option value="<?php echo $datos["id"] ?>"><?php echo $datos["titulo"] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>(S/) Monto </strong></label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        name=""
                                        id="idMontoEgresoCajaGrande"
                                        aria-describedby="helpId"
                                        placeholder="" />
                                </div>
                            </div>
                            <div class="mb-3">

                                <label for="" class="form-label"><strong><i class="fas fa-sticky-note"></i> Nota</strong></label>
                                <textarea class="form-control" name="" id="idDetalleNotaCajaGrande" rows="3" placeholder="Puedes escribir algo como Pago de Luz o Agua por corte, Pasajes Tatiana, etc."></textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <a
                                name=""
                                id=""
                                class="btn btn-success btn-round"
                                onclick='fnRegistrarEgresoDeCajaGrande()'
                                role="button">Registrar Egreso de Caja <i class="fas fa-plus"> </i></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalRegistrarIngreso" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document">
        <div class="modal-content">

            <div class="modal-body">
                <div class="card border-primary">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="card-body">
                        <div class="card-body text-center">
                            <div id="idFormaPagoIngreso" style="display: none;"></div>
                            <h4 id="idContenidoTituloIngreso">Ingreso de Caja</h4>
                            <h4>S/ <span id="idMontoDisponibleIngreso"></span></h4>
                        </div>

                        <div class="card-sub text-center">
                            Aquí podrás registrar <strong>INGRESOS de Caja.</strong>
                        </div>
                        <hr>

                        <div class="row justify-content-center align-items-center g-2">
                            <div class="mb-3">
                                <label for="idMontoIngresoCaja" class="form-label"><strong>(S/) Monto</strong></label>
                                <input type="number" class="form-control" id="idMontoIngresoCaja" placeholder="Ingrese monto" />
                            </div>

                            <div class="mb-3">
                                <label for="idNotaIngresoCaja" class="form-label"><strong><i class="fas fa-sticky-note"></i> Nota</strong></label>
                                <textarea class="form-control" id="idNotaIngresoCaja" rows="3" placeholder="Escribe una nota (Ej. Ingreso por venta de producto)"></textarea>
                            </div>
                        </div>

                        <hr>
                        <div class="text-center">
                            <a name="" id="" class="btn btn-success btn-round" onclick="fnRegistrarIngresoDeCajaGrande()" role="button">
                                Registrar Ingreso de Caja <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<?php
include("pie.php");
?>
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

<script>
    function fnAbrirSwalRegistroEgreso(jsDatos) {
        $('#modalRegistrarEgreso').modal('show');
        //`<span style="color:${jsDatos["color"]}"></sppan> '<i class="${jsDatos["icon"]}"></i> Registrar Egreso en ${jsDatos["nombre"]}</h4>`
        //monto["monto"]
        document.getElementById("idFormaPago").innerText = jsDatos["id"];
        document.getElementById("idContenidoTitulo").innerHTML = `<span style="color:${jsDatos["color"]}"> <i class="${jsDatos["icon"]}"></i> Registrar Egreso en ${jsDatos["nombre"]} </span>`
        document.getElementById("idMontoEgresoCajaGrande").value = "";
        document.getElementById("idMontoDisponible").innerText = jsDatos["monto"];

    }
    //modalRegistrarIngreso
    function fnAbrirSwalRegistroIngreso(jsDatos) {
        $('#modalRegistrarIngreso').modal('show');
        //`<span style="color:${jsDatos["color"]}"></sppan> '<i class="${jsDatos["icon"]}"></i> Registrar Egreso en ${jsDatos["nombre"]}</h4>`
        //monto["monto"]
        document.getElementById("idFormaPagoIngreso").innerText = jsDatos["id"];
        document.getElementById("idContenidoTituloIngreso").innerHTML = `<span style="color:${jsDatos["color"]}"> <i class="${jsDatos["icon"]}"></i> Registrar Egreso en ${jsDatos["nombre"]} </span>`
        document.getElementById("idMontoIngresoCaja").value = "";
        document.getElementById("idMontoDisponibleIngreso").innerText = jsDatos["monto"];

    }

    function fnRegistrarEgresoDeCajaGrande() {
        var conceptoSelect = document.getElementById("idSelectConceptoEgresoGrande");
        var concepto = conceptoSelect.selectedIndex === 0 ? 25 : conceptoSelect.value;
        var concepto_egreso = conceptoSelect.selectedIndex === 0 ? "OTROS EGRESOS" : conceptoSelect.options[conceptoSelect.selectedIndex].text;

        var montoCajaGrande = parseFloat(document.getElementById("idMontoEgresoCajaGrande").value);
        var notaCajaGrande = document.getElementById("idDetalleNotaCajaGrande").value;

        var montoDisponible = parseFloat(document.getElementById("idMontoDisponible").innerText);

        console.log(jsDetalleCajaGrande);
        if (isNaN(montoCajaGrande)) {
            swal("Upps", "Debes de ingresar el monto para registrar el egreso caja 😥", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
        } else if (montoCajaGrande > montoDisponible) {
            swal("Upps", "El monto ingresado supera al saldo de Caja 😥", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
        } else {
            var jsDetalleCajaGrande = {
                "forma_pago_id": parseInt(document.getElementById("idFormaPago").innerText),
                "tipo_movimiento": "EGRESO",
                "responsable_id": <?php echo $id_usuario_s; ?>,
                "responsable": "<?php echo $nombre . ", " . $ape_usuario; ?>",
                "monto_caja_grande": montoCajaGrande,
                "nota_caja_grande": (notaCajaGrande.length) === 0 ? null : notaCajaGrande,
                "concepto_id": parseInt(concepto),
                "concepto_egreso": concepto_egreso
            };
            console.log(jsDetalleCajaGrande);
            $.ajax({
                url: 'logica/clssInsertPA.php',
                type: 'POST',
                data: {
                    accion: 'INSERTDETALLECAJAGRANDE',
                    jsDetalleCajaGrande: JSON.stringify(jsDetalleCajaGrande)
                },
                success: function(response) {

                    console.log("Respuesta del servidor: ", response);

                    try {
                        var result = JSON.parse(response);
                        if (result.estado === true) {
                            swal({
                                title: "Egreso de Caja Registrado con Exito!",
                                text: result.mensaje,
                                icon: "success",
                                buttons: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });;
                        } else {
                            swal("Error", result.mensaje, {
                                icon: "error",
                                buttons: {
                                    confirm: {
                                        className: "btn btn-danger",
                                    },
                                },
                            });
                        }
                    } catch (e) {
                        console.log("Error al parsear el JSON: ", e);
                        swal("Error", "No se pudo procesar la respuesta del servidor.", {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error: " + error);
                    swal("Error", "Hubo un problema con la solicitud.", {
                        icon: "error",
                        buttons: {
                            confirm: {
                                className: "btn btn-danger",
                            },
                        },
                    });
                }
            });
        }
    }


    function fnRegistrarIngresoDeCajaGrande() {

        var montoCajaGrande = parseFloat(document.getElementById("idMontoIngresoCaja").value);
        var notaCajaGrande = document.getElementById("idNotaIngresoCaja").value;


        console.log(jsDetalleCajaGrande);
        if (isNaN(montoCajaGrande)) {
            swal("Upps", "Debes de ingresar el monto para registrar el Ingreso a caja 😥", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
        } else {
            var jsDetalleCajaGrande = {
                "forma_pago_id": parseInt(document.getElementById("idFormaPagoIngreso").innerText),
                "tipo_movimiento": "INGRESO",
                "responsable_id": <?php echo $id_usuario_s; ?>,
                "responsable": "<?php echo $nombre . ", " . $ape_usuario; ?>",
                "monto_caja_grande": montoCajaGrande,
                "nota_caja_grande": (notaCajaGrande.length) === 0 ? null : notaCajaGrande,
                "concepto_id": 1,
                "concepto_egreso": "INGRESO A CAJA"
            };
            console.log(jsDetalleCajaGrande);
            $.ajax({
                url: 'logica/clssInsertPA.php',
                type: 'POST',
                data: {
                    accion: 'INSERTDETALLECAJAGRANDE',
                    jsDetalleCajaGrande: JSON.stringify(jsDetalleCajaGrande)
                },
                success: function(response) {

                    console.log("Respuesta del servidor: ", response);

                    try {
                        var result = JSON.parse(response);
                        if (result.estado === true) {
                            swal({
                                title: "Egreso de Caja Registrado con Exito!",
                                text: result.mensaje,
                                icon: "success",
                                buttons: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });;
                        } else {
                            swal("Error", result.mensaje, {
                                icon: "error",
                                buttons: {
                                    confirm: {
                                        className: "btn btn-danger",
                                    },
                                },
                            });
                        }
                    } catch (e) {
                        console.log("Error al parsear el JSON: ", e);
                        swal("Error", "No se pudo procesar la respuesta del servidor.", {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error: " + error);
                    swal("Error", "Hubo un problema con la solicitud.", {
                        icon: "error",
                        buttons: {
                            confirm: {
                                className: "btn btn-danger",
                            },
                        },
                    });
                }
            });
        }
    }
</script>