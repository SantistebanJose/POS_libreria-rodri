<?php
include("cabecera.php");
?>

<div
    class="modal fade"
    id="modalDetalleReservaWeb"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 90%; height: 80%;" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card border-primary">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="card-body">
                        <div class="card-body text-center">
                            <h4><i class="fas fa-cloud"></i> Detalle de Reserva Web</h4>
                            <div class="card-sub text-center">
                                Aquí podrás revisar todas las reservas <strong>WEB.</strong>
                            </div>
                        </div>

                        <!-- Aquí vamos a agregar la lista de detalles de la reserva -->
                        <div id="detalleReservaList" class="row justify-content-center align-items-center g-2">
                            <!-- Los detalles se cargarán aquí dinámicamente -->
                        </div>
                        <hr>
                        <div class="text-center">
                            <a
                                name=""
                                id=""
                                href=""
                                class="btn btn-secondary btn-round"
                                onclick=''
                                role="button"><i class="far fa-check-circle"></i> Reserva Realizada</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div
    class="container">

    <div class="page-inner">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <h4 class="card-title"><i class="fas fa-align-left"></i> Listado de Reservas Web</h4>
                    <div class="card-tools">
                        <ul
                            class="nav nav-pills nav-secondary nav-pills-no-bd nav-sm"
                            id="pills-tab"
                            role="tablist">
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    id="pills-today"
                                    data-bs-toggle="pill"
                                    href="#pills-today"
                                    role="tab"
                                    aria-selected="true">Today</a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link active"
                                    id="pills-week"
                                    data-bs-toggle="pill"
                                    href="#pills-week"
                                    role="tab"
                                    aria-selected="false">Week</a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    id="pills-month"
                                    data-bs-toggle="pill"
                                    href="#pills-month"
                                    role="tab"
                                    aria-selected="false">Month</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php foreach (fnListadoDeReservasWeb() as $datosReserva) {
                    $datosReservaJSON = json_encode($datosReserva);
                    $jsonCliente = json_decode($datosReserva["json_cliente"], true);
                    $color = "";
                    if ($datosReserva["estado"] === "P") {
                        $color = "warning";
                    } else if ($datosReserva["estado"] === "R") {
                        $color = "success";
                    } else if ($datosReserva["estado"] === "A") {
                        $color = "danger";
                    } else {
                        $color = "dark";
                    }
                ?>
                    <div class="d-flex" data-bs-toggle="modal" onclick='fn_abrir_modal_detalle(<?php echo $datosReservaJSON ?>)'
                        data-nombre-cliente="<?php echo $jsonCliente["nombres"] . " " . $jsonCliente["apellidos"]; ?>"
                        data-estado="<?php echo $datosReserva["estado_v2"]; ?>"
                        data-fecha="<?php echo date("d/m/Y H:i:s", strtotime($datosReserva["created_at"])); ?>">
                        <div class="avatar avatar-online">
                            <span class="avatar-title rounded-circle border border-white bg-info"><?php echo substr($jsonCliente["nombres"], 0, 1) ?></span>
                        </div>
                        <div class="flex-1 ms-3 pt-1">
                            <h6 class="text-uppercase fw-bold mb-1">
                                <?php echo  $jsonCliente["nombres"] . " " . $jsonCliente["apellidos"] ?>
                                <span class="text-<?php echo $color ?> ps-3"><?php echo $datosReserva["estado_v2"] ?></span>
                            </h6>
                        </div>
                        <div class="float-end pt-1">
                            <small class="text-muted">
                                <?php echo date("d/m/Y H:i:s", strtotime($datosReserva["created_at"])); ?>
                            </small>
                        </div>
                    </div>
                    <div class="separator-dashed"></div>
                <?php } ?>
            </div>
        </div>
        <script>
            function fn_abrir_modal_detalle(jsdatos) {
                $('#modalDetalleReservaWeb').modal('show');
                console.log(jsdatos);
                var reserva = JSON.parse(jsdatos["json_detalle"]);

                var detalleReservaList = document.getElementById('detalleReservaList');
                detalleReservaList.innerHTML = '';

                // Crear la estructura de dos columnas
                var acordeonHtml = `
                    <div class="row">
                        <div class="col-md-3">
                            <!-- Detalles de la reserva -->
                            <div class="accordion" id="accordionDetalleReserva">
                `;

                // Crear una columna para el iframe
                var iframeHtml = `
                    </div>
                        </div>
                        <div class="col-md-9">
                            <div class="card-sub">
                                    Aquí podrás viualizar el contenido del documento.
                             </div>
                            <div id="iframeContainer">
                                <iframe id="iframeViewer" class="w-100" style="height: 400px;" frameborder="0"></iframe>
                            </div>
                        </div>
                    </div>
                `;

                reserva.forEach(function(detalle, index) {
                    var itemId = 'item' + index;
                    var collapseId = 'collapse' + index;

                    // Construir el acordeón con detalles
                    acordeonHtml += `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="${itemId}">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="true" aria-controls="${collapseId}">
                            Tipo Hoja: ${detalle.tipo_hoja}
                        </button>
                    </h2>
                    <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${itemId}" data-bs-parent="#accordionDetalleReserva">
                        <div class="accordion-body">
                            <div><strong>Cantidad Impresiones:</strong> ${detalle.cantidad_impresiones}</div>
                            <div><strong>Indicaciones:</strong> ${detalle.indicaciones}</div>
                            <a href="javascript:void(0)" onclick="cargarIframe('${detalle.link_archivo}')">Ver Archivo</a>
                        </div>
                    </div>
                </div>
            `;
                });

                // Agregar las columnas al contenedor
                acordeonHtml += iframeHtml;

                // Insertar el acordeón dentro de la página
                detalleReservaList.innerHTML = acordeonHtml;
            }

            // Función para cargar el archivo en el iframe
            function cargarIframe(link) {
                var iframe = document.getElementById('iframeViewer');
                iframe.src = link;
            }
        </script>
    </div>
</div>

<?php
include("pie.php");
?>