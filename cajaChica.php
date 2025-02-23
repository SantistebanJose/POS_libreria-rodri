<?php
include("cabecera.php");

?>


<div
    class="container">


    <div class="page-inner">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title"> <i class="fas fa-box-open"></i> Caja Chica </h4>
                <hr>
                <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-home-tab-nobd" data-bs-toggle="pill" href="#pills-home-nobd" role="tab" aria-controls="pills-home-nobd" aria-selected="true"> <i class="fas fa-calculator"></i> Caja Chica</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-profile-tab-nobd" data-bs-toggle="pill" href="#pills-profile-nobd" role="tab" aria-controls="pills-profile-nobd" aria-selected="false"> <i class="fas fa-align-left"></i> Historial de Caja</a>
                    </li>
                </ul>


                <div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                    <div class="tab-pane fade show active" id="pills-home-nobd" role="tabpanel" aria-labelledby="pills-home-tab-nobd">

                        <?php
                        if (empty(fnListadoCajaChica())) {
                        ?>
                            <div class="card-sub card-annoucement card-round">
                                <div class="card-body text-center">
                                    <h3 class="card-title"> <strong>Hola! Vysam 🖐️</strong></h3>

                                    <div class="card-desc">
                                        No tienes ninguna <strong>caja aperturada</strong> 😅. Puedes aperturar una caja chica haciendo clic en el botón <strong>Apertura de Caja Chica</strong> 😎.
                                    </div>
                                    <div class="card-detail">
                                        <a
                                            onclick='fnAbrirModalAperturaCaja()'
                                            class="btn btn-secondary btn-round"
                                            role="button">
                                            <i class="fas fa-box-open"> </i> Apertura de Caja Chica
                                        </a>
                                    </div>
                                </div>
                            </div>


                        <?php
                        } else {
                            (fnListadoCajaChica()[0]["monto"] - fnListadoCajaChica()[0]["saldo"]);

                        ?>
                            <div class="row justify-content-center align-items-start g-2">

                                <div class="col-12 col-sm-6 col-md-6 col-xl-4" style="position: sticky; top: 0; z-index: 10;">
                                    <div class="card">
                                        <div class="card-body">

                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4 class="text-dark fw-bold">Caja Chica</h4>
                                                    <h6 class="text-secondary fw-bold">Monto Inicial fue de <?php echo "S/" . fnListadoCajaChica()[0]["monto"] ?> </h6>
                                                    <p class="text-muted">Esta caja se encuentra aperturada</p>
                                                    <p class="text-muted">Aperturada por <strong><?php echo  fnListadoCajaChica()[0]["responsable"] ?></strong></p>
                                                </div>
                                                <h3 class="text-success fw-bold"> <?php echo "S/" . fnListadoCajaChica()[0]["saldo_v2"] ?> </h3>
                                                <div class="dropdown-secondary">
                                                    <button
                                                        class="btn btn-icon btn-clean"
                                                        type="button"
                                                        id="dropdownMenuButton"
                                                        data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <div
                                                        class="dropdown-menu"
                                                        aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="#" onclick="fnAbrirModalRegistroDeEgresoCajaChica()">Registro de Egreso de caja</a>
                                                        <a class="dropdown-item" href="#">Registro de Ingreso de caja</a>
                                                        <a class="dropdown-item" href="#">Cierre de Caja</a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-success"
                                                    role="progressbar"
                                                    aria-valuenow="<?php echo fnListadoCajaChica()[0]["porcentaje"]; ?>"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100"
                                                    style="width: <?php echo fnListadoCajaChica()[0]["porcentaje"]; ?>%;">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-2">
                                                <p class="text-muted mb-0">Porcentaje del gasto realizado en caja chica.</p>
                                                <p class="text-muted mb-0"><strong><?php echo fnListadoCajaChica()[0]["porcentaje"]; ?>%</strong></p>
                                            </div>
                                            <hr>
                                            <div class="row justify-content-center align-items-center sm-2">
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <small id="helpId" class="form-text text-muted">Fecha de Apertura</small>
                                                        <input type="text" disabled class="form-control" value="<?php echo fnListadoCajaChica()[0]["fecha_apertura"]; ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <small id="helpId" class="form-text text-muted">Hora de Apertura</small>
                                                        <input type="text" disabled class="form-control" value="<?php echo fnListadoCajaChica()[0]["hora_apertura"]; ?>" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div><strong>Monto de Apertura de caja: <span style="color: green;">S/ <?php echo fnListadoCajaChica()[0]["monto"]; ?> </span> </strong></div>
                                            <div><strong>Saldo de caja: <span style="color:blue">S/ <?php echo fnListadoCajaChica()[0]["saldo_v2"]; ?></span></strong> </div>
                                            <div><strong>Egresos de Caja: <span style="color: red;">S/ <?php echo (fnListadoCajaChica()[0]["egresos_de_caja"]); ?></span> </strong></div>
                                            <div><strong>Porcentaje de Gasto de caja:</strong> <?php echo (fnListadoCajaChica()[0]["porcentaje"]); ?>% </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- Columna Historial de Caja -->
                                <div class="col-12 col-sm-6 col-md-6 col-xl-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-title" style="display: flex; justify-content: space-between; align-items: center;">
                                                <span>Egresos de Caja Chica</span>
                                            </div>
                                            <ol class="activity-feed">
                                                <?php
                                                $datosDetalleCaja = json_decode(fnListadoCajaChica()[0]["js_detalle_caja"], true);
                                                if (empty($datosDetalleCaja)) {
                                                ?>
                                                    <div>Sin Registros de caja</div>
                                                    <?php
                                                } else {
                                                    foreach ($datosDetalleCaja as $datos) { ?>
                                                        <li class="feed-item feed-item-secondary">
                                                            <time class="date" datetime="9-25"> <?php echo $datos["hora_registro"] ?> </time>
                                                            <div><strong style="color: orange;"><?php echo $datos["tipo_movimiento"] ?></strong> - <?php echo $datos["concepto"] ?> </div>
                                                            <div class="text-secondary">Registrado por: <?php echo "<b>" . $datos["responsable"] . "</b>" ?> </div>
                                                            <div>Monto: <strong>S/ <?php echo number_format($datos["monto"], 2) ?> </strong></div>
                                                        </li>
                                                <?php
                                                    }
                                                }
                                                ?>


                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>



                    </div>
                    <div class="tab-pane fade" id="pills-profile-nobd" role="tabpanel" aria-labelledby="pills-profile-tab-nobd">
                        <div class="card text-start">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table
                                        id="TablaVentaDiaria"
                                        class="dataTable display table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Responsable</th>
                                                <th>dia de semana</th>
                                                <th>Fecha de Apertura</th>
                                                <th>Hora de Apertura</th>
                                                <th>Monto</th>
                                                <th>Hora de Cierre</th>
                                                <th>Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach (fnListadoCajaChicaCerradas() as $datos) {
                                                $datosJSON = json_encode($datos);
                                            ?>
                                                <tr>
                                                    <td><?php echo $datos["id"] ?></td>
                                                    <td><?php echo $datos["responsable"] ?></td>
                                                    <td><?php echo $datos["dia_semana"] ?></td>

                                                    <td><?php echo $datos["fecha_apertura"] ?></td>
                                                    <td><?php echo $datos["hora_apertura"] ?></td>

                                                    <td><?php echo "S/ " . $datos["monto"] ?></td>
                                                    <td><?php echo $datos["hora_cierre"] ?></td>
                                                    <td><?php echo "S/ " . $datos["saldo"] ?></td>

                                                    <td>
                                                        <div class="mt-2 text-center">
                                                            <a
                                                                onclick='abrirDetalle(<?php echo $datosJSON ?>)'
                                                                class="btn btn-secondary btn-round btn-sm"
                                                                role="button">
                                                                <i class="fas fa-external-link-square-alt"></i>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<style>
    /* Tamaño por defecto para pantallas grandes (computadoras) */
    .modal-dialog-custom {
        max-width: 50%;
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
    @media (max-width: 500px) {
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
    id="modalAperturarCaja"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document"> <!-- Usamos la clase personalizada aquí -->
        <div class="modal-content">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-box-open"></i> Apertura de Caja Chica</h4>
                <hr>
                <div class="card-sub text-center">
                    Aquí podrás Registrar la apertura de caja. Ten en cuenta el monto correspondiente.
                </div>
                <div class="row justify-content-center align-items-center sm-2">
                    <div class="col-sm-12">
                        <div class="card text-start">
                            <div class="card-body">
                                <h4 class="card-title">Monto</h4>
                                <div class="mb-3">
                                    <input
                                        type="number"
                                        class="form-control"
                                        name=""
                                        id="idMontoAperturaCajaChica"
                                        aria-describedby="helpId"
                                        placeholder="" />
                                    <small id="helpId" class="form-text text-muted">Este será el monto para tu caja chica.</small>
                                </div>
                                <div class="card-sub text-center">
                                    Recuerda que el monto máximo para cada adquisición con cargo a la Caja Chica no debe exceder del diez por ciento (10%) de una UIT,
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a
                            name=""
                            id=""
                            class="btn btn-success btn-round"
                            onclick='fnRegistrarAperturaDeCaja()'
                            role="button">Aperturar Caja <i class="fas fa-plus"> </i></a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div
    class="modal fade"
    id="modalRegistrarEgresoDeCajaCHica"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document"> <!-- Usamos la clase personalizada aquí -->
        <div class="modal-content">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h6 class="card-title text-center" style="font-size: 20px;"><i class="fas fa-boxes"></i> Registro de Egreso de Caja Chica</h6>
                
                <div class="card-sub text-center">
                    Aquí podrás Registrar la apertura de caja. Ten en cuenta el monto correspondiente.
                </div>
                <div class="row justify-content-center align-items-center sm-2">
                    <div class="col-sm-12">
                        <div class="card text-start">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="" class="form-label">Selccione Concepto de Egreso</label>
                                    <select
                                        class="form-select form-select-sm"
                                        name=""
                                        id="idSelectConceptoEgreso">
                                        <option selected>Seleccione Concepto</option>
    
                                        <option value="">New Delhi</option>
                                        <option value="">Istanbul</option>
                                        <option value="">Jakarta</option>
                                    </select>
                                </div>
                                
                                <div class="card-sub text-center">
                                    Recuerda que el monto máximo para cada adquisición con cargo a la Caja Chica no debe exceder del diez por ciento (10%) de una UIT,
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a
                            name=""
                            id=""
                            class="btn btn-success btn-round"
                            onclick='fnRegistrarAperturaDeCaja()'
                            role="button">Aperturar Caja <i class="fas fa-plus"> </i></a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
include("pie.php")
?>

<script>
    function fnAbrirModalAperturaCaja() {
        $('#modalAperturarCaja').modal('show');
    }

    function fnAbrirModalRegistroDeEgresoCajaChica() {
        $('#modalRegistrarEgresoDeCajaCHica').modal('show');
    }

    function fnRegistrarAperturaDeCaja() {
        var monto = parseFloat(document.getElementById("idMontoAperturaCajaChica").value);

        if (isNaN(monto)) {
            swal("Upps", "Debes de ingresar el monto para aperturar caja 😥", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
        } else {
            var jsDatoCaja = {
                "responsable_id": <?php echo $id_usuario_s; ?>,
                "responsable": "<?php echo $nombre . ", " . $ape_usuario; ?>", // Asegúrate de poner comillas aquí
                "monto": monto
            };

            console.log(jsDatoCaja);
            $.ajax({
                url: 'logica/clssInsertPA.php',
                type: 'POST',
                data: {
                    accion: 'APERTURACAJA',
                    jsDatoCaja: JSON.stringify(jsDatoCaja)
                },
                success: function(response) {

                    console.log("Respuesta del servidor: ", response);

                    try {
                        var result = JSON.parse(response);
                        if (result.estado === true) {
                            swal({
                                title: "Caja Chica Aperturada con Exito!",
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