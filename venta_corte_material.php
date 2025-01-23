<?php
include("cabecera.php");
include("logica/clssVenta.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
}


?>

<style>
    .nav-item .nav-link:hover {
        background-color: #495057 !important;
        /* Cambia el color de fondo al pasar el mouse */
        color: #f8f9fa;
        /* Cambia el color del texto */
        cursor: pointer;
        /* Muestra un cursor de mano para indicar que es clickeable */
    }
</style>
<div
    class="container">
    <div class="page-inner">
        <div
            class="card">

            <div class="card-body">
                <h4 class="card-title">Venta</h4>
                <div class="mb-3">
                    <div class="card-sub">
                        Aquí podrás realizar ventas de cuando un cliente viene a realizar corte y/o compra de materiales.
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="card-title">Artículos</h4>
                                <button type="button" class="btn btn-success" onclick="agregarCorte()">Solo Corte</button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table
                                        id="multi-filter-select"
                                        class="display table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th>Fecha</th>
                                                <th>Hora</th>
                                                <th>Estado</th>
                                                <th>Accion</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            foreach (listarVentaReservaCorte() as $datosReserva) {
                                                $datosReservaJSON = json_encode($datosReserva);


                                            ?>
                                                <tr>
                                                    <td><?php echo $datosReserva["venta_id"] ?></td>
                                                    <td><?php echo $datosReserva["cliente"] ?></td>
                                                    <td><?php echo $datosReserva["fecha"] ?></td>
                                                    <td><?php echo $datosReserva["hora"] ?></td>
                                                    <td><?php echo $datosReserva["estado_venta"] ?></td>
                                                    <th>

                                                        <div class="mt-2 text-center">
                                                            <a
                                                                name=""
                                                                id=""
                                                                class="btn btn-secondary btn-round"

                                                                onclick='fn_consultarVenta(<?php echo $datosReservaJSON; ?>)'
                                                                role="button">Ver</a>
                                                        </div>
                                                    </th>
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
                <div
                    class="card">
                </div>





                <!-- Modal Solo Corte -->
                <div class="modal fade" id="modalSoloCorte" tabindex="-1" aria-labelledby="modalSoloCorteLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalSoloCorteLabel">Corte de Minutos</h5>
                                <button type="button" class="btn_close_solo" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="card-body text-center">
                                    <p class="card-text">Minutos Corte</p>
                                    <div class="row">
                                        <div class="col">
                                            <button id="btn_menos_solocorte" class="btn btn-danger btn-round ms-2" type="button">-</button>
                                        </div>
                                        <div id="cantidad_solocorte" class="col">0</div>
                                        <div class="col">
                                            <button id="btn_mas_solocorte" class="btn btn-success btn-round ms-2" type="button">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" id="btn_agregar_solocorte">Agregar</button>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Modal -->
                <div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h5 class="modal-title" id="miModalLabel">Agregar Corte de Material</h5>
                                <button type="button" id="btn_close" class="btn-close" aria-label="Close">X</button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                                <!-- Acordeones dinámicos -->
                                <div class="accordion" id="acordeonContainer">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                                <!-- Sección global -->
                                <div id="globalContainer" class="mt-3">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" id="btn_no">Cancelar</button>
                                <button type="button" class="btn btn-primary" id="btn_si">Cortar</button>
                            </div>

                        </div>
                    </div>
                </div>





            </div>

        </div>
        <hr>
        <div
            class="row ">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-title">Agrega Más Articulos, Impresiones, Escaneos a la Venta</div>
                        <div>
                            <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link " id="pills-home-tab-nobd" data-bs-toggle="pill" href="#pills-home-nobd" role="tab" aria-controls="pills-home-nobd" aria-selected="true">Materiales y/o Corte</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-profile-tab-nobd" data-bs-toggle="pill" href="#pills-profile-nobd" role="tab" aria-controls="pills-profile-nobd" aria-selected="false">Ploteo</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-contact-tab-nobd" data-bs-toggle="pill" href="#pills-contact-nobd" role="tab" aria-controls="pills-contact-nobd" aria-selected="false">Imprimir</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-contact-tab-nobd" data-bs-toggle="pill" href="#pills-contact-nobd" role="tab" aria-controls="pills-contact-nobd" aria-selected="false">Producto Vitrina</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-contact-tab-nobd" data-bs-toggle="pill" href="#pills-contact-nobd" role="tab" aria-controls="pills-contact-nobd" aria-selected="false">Escaneo</a>
                                </li>
                            </ul>
                        </div>
                    </div>


                    <div class="card-body">

                        <div>
                            <div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                                <div class="tab-pane fade " id="pills-home-nobd" role="tabpanel" aria-labelledby="pills-home-tab-nobd">
                                    <div class="table-responsive">
                                        <table
                                            id="multi-filter-select2"
                                            class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Articulo</th>
                                                    <th>Categoria</th>
                                                    <th>Tipo</th>
                                                    <th>Dimension</th>
                                                    <th>Stock</th>
                                                    <th>Precio de Venta</th>
                                                    <th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Articulo</th>
                                                    <th>Categoria</th>
                                                    <th>Tipo</th>
                                                    <th>Dimension</th>
                                                    <th>Stock</th>
                                                    <th>Precio de Venta</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>

                                                <?php
                                                foreach (listarProductosVenta1() as $datosArticulo) {
                                                    $datosArticuloJSON = json_encode($datosArticulo);


                                                ?>
                                                    <tr>
                                                        <td><?php echo $datosArticulo["articulo"] ?></td>
                                                        <td><?php echo $datosArticulo["categoria"] ?></td>
                                                        <td><?php echo $datosArticulo["tipo"] ?></td>
                                                        <td><?php echo $datosArticulo["dimension"] ?></td>
                                                        <td><?php echo $datosArticulo["stock"] ?></td>
                                                        <td><?php echo $datosArticulo["precio_venta"] ?></td>
                                                        <th>

                                                            <div class="mt-2 text-center">
                                                                <a
                                                                    name=""
                                                                    id=""
                                                                    class="btn btn-secondary btn-round"

                                                                    onclick='fn_agregar_venta(<?php echo $datosArticuloJSON; ?>)'
                                                                    role="button">Agregar</a>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-profile-nobd" role="tabpanel" aria-labelledby="pills-profile-tab-nobd">
                                    <div class="text-center">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="card-title">Servicio de Ploteos</h4>
                                                <div>ID: <span id="id_mov_ploteo">2</span></div>
                                                <div class="card-sub">
                                                    Aquí ingresa los ploteos
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">Cantidad de Ploteos</p>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <button id="btn_mas" class="btn btn-danger btn-round me-2">-</button>
                                                    <input id="input_numero" class="text-center" type="text" value="1" style="width: 40px;" oninput="validarNumero(event)">
                                                    <button id="id_contador" class="btn btn-success btn-round ms-2">+</button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">Monto (S/)</p>
                                                <input type="number" name="" id="" placeholder="Monto (S/)">
                                            </div>
                                            <div class="text-center">
                                                <a class="btn btn-secondary" href="#" role="button">Añadir a la Venta</a>
                                            </div>
                                            <br>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-contact-nobd" role="tabpanel" aria-labelledby="pills-contact-tab-nobd">

                                    <div class="text-center">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="card-title">Servicio de Impresiones</h4>
                                                <div>ID: <span id="id_mov_impresion">3</span></div>
                                                <div class="card-sub">
                                                    Aquí ingresa lo que mandaron a Imprimir
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">Cantidad de Impresiones</p>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <button id="btn_mas_impresion" class="btn btn-danger btn-round me-2">-</button>
                                                    <input id="input_numero_impresion" class="text-center" type="text" value="1" style="width: 40px;" oninput="validarNumero(event)">
                                                    <button id="id_contador_impresion" class="btn btn-success btn-round ms-2">+</button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">Monto (S/)</p>
                                                <input type="number" name="" id="" placeholder="Monto (S/)">
                                            </div>

                                            <div class="text-center">
                                                <a class="btn btn-secondary" href="#" role="button">Añadir a la Venta</a>
                                            </div>
                                            <br>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div
            class="card border-primary">
            <div class="card-body">
                <h4 class="card-title">Detalles de Articulos de Reserva</h4>
                <div class="table-responsive">
                    <table id="tabla_articulos" class="table mt-3">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">MINUTOS</th>
                                <th scope="col">Tarifa</th>
                                <th scope="col">Articulo</th>
                                <th scope="col">Cantidad</th>
                                <th scope="col">Precio Unitario</th>
                                <th scope="col">Sub Total (S/)</th>
                                <th scope="col">Accion</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body text-center">
                        <h5 id="label_total_cortes" class="card-title">Total en Cortes (S/)</h5>
                        <span id="id_subtotal_cortes" style="font-size: 1.3rem;" aria-labelledby="label_total_cortes">xx.xx</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body text-center">
                        <h5 id="label_total_articulos" class="card-title">Total de Artículos (S/)</h5>
                        <span id="id_subtotal_articulos" style="font-size: 1.3rem;" aria-labelledby="label_total_articulos">xx.xx</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-primary card-stats card-round">
                    <div class="card-body text-center">
                        <h5 id="label_total_general" class="card-title">Total (S/)</h5>
                        <span id="id_subtotal_general" style="font-size: 1.3rem;" aria-labelledby="label_total_general">xx.xx</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <button id="btnRealizarPago" type="button" class="btn btn-success btn-block card card-stats card-round">
                    <div class="card-body text-center">
                        <h5 id="label_total_general" class="card-title">Realizar Pago</h5>
                    </div>
                </button>
            </div>

        </div>
    </div>


</div>
<!-- Modal trigger button -->


<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div
    class="modal fade"
    id="modalRealizarPago"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"

    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div
        class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg"
        role="document">
        <div class="modal-content">

            <div class="modal-body">
                <div
                    class="card border-primary">
                    <div class="card-body">

                        <div class="card-body text-center">
                            <h4 class="card-title">Realizar Pago</h4>
                        </div>
                        <!--<div class="card-body text-center">
                            <h1 class="card-title">S/ xx.xx</h1>
                        </div>-->

                        <div class="card-sub">
                            Aquí realiza tus pagos
                        </div>
                        <div>
                            <span>ID Venta: <span id="idVenta">#</span></span> |
                            <span>ID Cliente: <span id="idPersona">#</span></span>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label for="" class="form-label">Cliente</label>
                            <input
                                type="text"
                                class="form-control"
                                name=""
                                id="nombreCliente"
                                aria-describedby="helpId"
                                placeholder="AGREGAR EL NOMBRE DEL CLIENTE" />
                        </div>
                        <!-- Monto Total -->
                        <div class="mb-3">
                            <label for="montoTotal" class="form-label">Monto Total</label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="montoTotal"
                                    placeholder="Monto total de la venta"
                                    readonly />
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-pills nav-secondary  nav-pills-no-bd nav-pills-icons justify-content-center" id="pills-tab-with-icon" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pills-home-tab-icon" data-bs-toggle="pill" href="#pago-directo" role="tab" aria-controls="pago-directo" aria-selected="true">
                                        Pago Directo
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-profile-tab-icon" data-bs-toggle="pill" href="#pago-credito" role="tab" aria-controls="pago-credito" aria-selected="false">
                                        Pago Crédito
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content mt-2 mb-3" id="pills-with-icon-tabContent">
                                <div class="tab-pane fade show active" id="pago-directo" role="tabpanel" aria-labelledby="pills-home-tab-icon">
                                    <div id="panel_forma_pago" class="mb-3">
                                        <div class="card-sub">
                                            <div class="text-center">
                                                Aquí podrás elegir si realizan pagos Directo.
                                            </div>
                                        </div>
                                        <label for="" class="form-label"><strong>Forma de Pago</strong></label>
                                        <!-- Botón de agregar más formas de pago -->
                                        <button id="btnAgregarPago" class="btn btn-secondary btn-round ms-2">+</button>

                                        <div class="d-flex align-items-center">
                                            <!-- Select de formas de pago -->
                                            <select class="form-select form-select-md" name="" id="formaPagoSelect">
                                                <?php
                                                foreach (listarFormaPago() as $datosFormaPago) {
                                                    $datosFormaPagoJSON = json_encode($datosFormaPago);
                                                ?>
                                                    <option value="<?php echo $datosFormaPago["id"] ?>"><?php echo $datosFormaPago["nombre"] ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>

                                            <!-- Caja de texto para monto -->
                                            <input type="number" class="form-control form-control-md ms-2" placeholder="Monto" min="0" id="montoSelect_0">


                                        </div>

                                        <!-- Contenedor para los selects adicionales -->
                                        <div id="contenedorPagos" class="mt-3"></div>

                                    </div>
                                    <hr>
                                    <!-- Monto Total -->
                                    <div
                                        class="row justify-content-center align-items-center g-2">
                                        <div class="col-md-6">
                                            <label for="" class="form-label"><b>Paga Con</b></label>
                                            <div class="input-group">
                                                <span class="input-group-text">S/</span>
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    id=""
                                                    placeholder="" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="" class="form-label"><b>Vuelto (S/)</b></label>
                                            <div class="input-group">
                                                <span class="input-group-text">S/</span>
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    id=""
                                                    placeholder="" />
                                            </div>
                                        </div>

                                    </div>






                                </div>
                                <div class="tab-pane fade" id="pago-credito" role="tabpanel" aria-labelledby="pills-profile-tab-icon">
                                    <div class="card-sub">
                                        <div class="text-center">
                                            Aquí podrás elegir si realizan pagos al Crédito.
                                        </div>
                                    </div>
                                    <!-- Monto Total -->
                                    <div class="mb-3">
                                        <label for="" class="form-label">Monto Inicial (S/)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">S/</span>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id=""
                                                placeholder="Agregue un monto dejado a cuenta" />
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                        <div class="text-center">
                            <a class="btn btn-success" href="#" role="button">Pagar</a>
                        </div>


                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Salir
                </button>

            </div>
        </div>
    </div>
</div>

<!-- Optional: Place to the bottom of scripts -->
<script>
    const myModal = new bootstrap.Modal(
        document.getElementById("modalId"),
        options,
    );
</script>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>


<script>
    $(document).ready(function() {
        // Inicialización de DataTables en español
        $("#basic-datatables").DataTable({
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

        $("#multi-filter-select").DataTable({
            pageLength: 5,
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
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
            },
            initComplete: function() {
                this.api()
                    .columns()
                    .every(function() {
                        var column = this;
                        var select = $('<select class="form-select"><option value=""></option></select>')
                            .appendTo($(column.footer()).empty())
                            .on("change", function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? "^" + val + "$" : "", true, false).draw();
                            });

                        column.data().unique().sort().each(function(d, j) {
                            select.append('<option value="' + d + '">' + d + "</option>");
                        });
                    });
            }
        });

        $("#multi-filter-select2").DataTable({
            pageLength: 5,
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
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
            },
            initComplete: function() {
                this.api()
                    .columns()
                    .every(function() {
                        var column = this;
                        var select = $('<select class="form-select"><option value=""></option></select>')
                            .appendTo($(column.footer()).empty())
                            .on("change", function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? "^" + val + "$" : "", true, false).draw();
                            });

                        column.data().unique().sort().each(function(d, j) {
                            select.append('<option value="' + d + '">' + d + "</option>");
                        });
                    });
            }
        });

        // Agregar fila en español
        $("#add-row").DataTable({
            pageLength: 5,
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
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

        var action =
            '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Editar tarea"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Eliminar"> <i class="fa fa-times"></i> </button> </div> </td>';

        $("#addRowButton").click(function() {
            $("#add-row")
                .dataTable()
                .fnAddData([
                    $("#addName").val(),
                    $("#addPosition").val(),
                    $("#addOffice").val(),
                    action,
                ]);
            $("#addRowModal").modal("hide");
        });
    });
</script>
<script>
    function validarNumero(event) {
        // Eliminar cualquier cosa que no sea un número
        event.target.value = event.target.value.replace(/[^0-9]/g, '');
    }

    const btnMas = document.getElementById('btn_mas');
    const btnContador = document.getElementById('id_contador');
    const inputNumero = document.getElementById('input_numero');

    const btnMasImpresion = document.getElementById('btn_mas_impresion');
    const btnContadorImpresion = document.getElementById('id_contador_impresion');
    const inputNumeroImpresion = document.getElementById('input_numero_impresion');

    btnMas.addEventListener('click', () => {
        let currentValue = parseInt(inputNumero.value);
        if (!isNaN(currentValue) && currentValue > 1) {
            inputNumero.value = currentValue - 1;
        }
    });

    btnMasImpresion.addEventListener('click', () => {
        let currentValue = parseInt(inputNumeroImpresion.value);
        if (!isNaN(currentValue) && currentValue > 1) {
            inputNumeroImpresion.value = currentValue - 1;
        }
    });


    btnContador.addEventListener('click', () => {
        let currentValue = parseInt(inputNumero.value);
        if (!isNaN(currentValue)) {
            inputNumero.value = currentValue + 1;
        }
    });

    btnContadorImpresion.addEventListener('click', () => {
        let currentValue = parseInt(inputNumeroImpresion.value);
        if (!isNaN(currentValue)) {
            inputNumeroImpresion.value = currentValue + 1;
        }
    });
</script>

<script>
    // Variables para manejar los selects y montos adicionales
    const btnAgregarPago = document.getElementById('btnAgregarPago');
    const contenedorPagos = document.getElementById('contenedorPagos');
    let contador = 1; // Para numerar los campos adicionales

    // Evento para agregar más selects con montos
    btnAgregarPago.addEventListener('click', function() {
        // Crear un contenedor para el nuevo select y su campo de monto
        const nuevoContenedor = document.createElement('div');
        nuevoContenedor.classList.add('d-flex', 'align-items-center', 'mb-2');

        // Crear un nuevo select
        const nuevoSelect = document.createElement('select');
        nuevoSelect.classList.add('form-select', 'form-select-md', 'me-2');
        nuevoSelect.innerHTML = `
            <?php
            foreach (listarFormaPago() as $datosFormaPago) {
                echo '<option value="' . $datosFormaPago["id"] . '">' . $datosFormaPago["nombre"] . '</option>';
            }
            ?>
        `;

        // Crear una nueva caja de texto para el monto
        const nuevoInputMonto = document.createElement('input');
        nuevoInputMonto.type = 'number';
        nuevoInputMonto.classList.add('form-control', 'form-control-md', 'ms-2');
        nuevoInputMonto.placeholder = 'Monto';
        nuevoInputMonto.min = '0';
        nuevoInputMonto.id = 'montoSelect_' + contador;

        // Agregar el select y el input al contenedor
        nuevoContenedor.appendChild(nuevoSelect);
        nuevoContenedor.appendChild(nuevoInputMonto);

        // Agregar el contenedor al contenedor principal
        contenedorPagos.appendChild(nuevoContenedor);

        // Incrementar el contador para los nuevos inputs
        contador++;
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Incremento de minutos
        document.getElementById('btn_mas_solocorte').addEventListener('click', function() {
            let cantidad = parseInt(document.getElementById('cantidad_solocorte').textContent);
            if (cantidad > 0) {
                cantidad++;
                document.getElementById('cantidad_solocorte').textContent = cantidad;
            } else {
                document.getElementById('cantidad_solocorte').textContent = 10;

            }

        });

        // Decremento de minutos
        document.getElementById('btn_menos_solocorte').addEventListener('click', function() {
            let cantidad = parseInt(document.getElementById('cantidad_solocorte').textContent);
            if (cantidad > 0) {
                cantidad--;
                document.getElementById('cantidad_solocorte').textContent = cantidad;
            }
        });
        document.getElementById('btn_no').addEventListener('click', function() {
            var modal = bootstrap.Modal.getInstance(document.getElementById('miModal'));
            modal.hide(); // Cierra el modal manualmente

            // Limpiar después de cerrar
            fn_limpiar_modal();
        });

        // Evento para el botón "X"
        document.querySelector('.btn_close').addEventListener('click', function() {
            var modal = bootstrap.Modal.getInstance(document.getElementById('miModal'));
            modal.hide(); // Cierra el modal manualmente

            // Limpiar después de cerrar
            fn_limpiar_modal();
        });

        // Limpiar el modal cuando se cierra con el evento hidden.bs.modal
        $('#miModal').on('hidden.bs.modal', function() {
            fn_limpiar_modal(); // Limpiar el modal cuando se cierra
        });


    })

    function agregarCorte() {
        const modalElement = document.getElementById('modalSoloCorte');
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static', // Evita que se cierre al hacer clic fuera
            keyboard: false // Evita que se cierre con la tecla 'Esc'
        });
        modal.show(); // Muestra el modal

        // Evento para cuando se hace clic en el botón "Agregar"
        const btn_agregar = document.getElementById('btn_agregar_solocorte');
        btn_agregar.addEventListener("click", () => {
            const cantidadMinutos = parseInt(document.getElementById('cantidad_solocorte').textContent) || 0;
            const datosArticulo = {}; // Aquí deberías obtener los datos del artículo (p. ej., desde una variable global o un formulario)

            // Crear el objeto datosCorte
            const datosCorte = [{
                id: 1, // Id del corte
                minutos: cantidadMinutos, // Minutos registrados
                costo: cantidadMinutos * 1.5 // Costo por minuto
            }];

            // Llamar a la función fn_agregar_articulo_tabla
            fn_solo_corte_tabla(datosCorte);

            // Reiniciar los minutos a 0 en la interfaz
            document.getElementById('cantidad_solocorte').textContent = 0;

            // Ocultar el modal
            modal.hide();
        });
    }

    function fn_agregar_articulo_tabla(datosCorte = []) {

        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];


        datosCorte.forEach(corte => {
            let nuevaFila = tabla.insertRow();

            nuevaFila.insertCell(0).textContent = corte.id; // ID
            nuevaFila.insertCell(1).textContent = corte.minutos; // Minutos
            nuevaFila.insertCell(2).textContent = corte.costo; // Costo x Minuto
            nuevaFila.insertCell(3).textContent = 'Corte'; // Artículo
            nuevaFila.insertCell(4).textContent = '-'; // Cantidad fija por corte
            nuevaFila.insertCell(5).textContent = '-'; // Precio unitario
            nuevaFila.insertCell(6).textContent = (corte.costo).toFixed(2); // Subtotal

            let accionCell = nuevaFila.insertCell(7);

            let botonEliminar = document.createElement("button");
            botonEliminar.classList.add("btn", "btn-warning", "btn-round", "ms-2");
            let iconoBasura = document.createElement("i");
            iconoBasura.classList.add("fas", "fa-trash"); // Font Awesome icon for trash
            // Añadir los botones a la celda de acciones
            botonEliminar.appendChild(iconoBasura);
            accionCell.appendChild(botonEliminar);
            // Función para manejar el botón de eliminar
            botonEliminar.addEventListener("click", () => {
                const fila = botonEliminar.closest("tr");
                fila.remove(); // Eliminar la fila
                fn_obtener_total(); // Recalcular los totales después de eliminar
            });

        });



        fn_obtener_total();
    }
</script>

<script>
    let datosArticuloOriginal = [];
    let datosArticuloNuevos = [];



    function fn_limpiar_modal() {
        const acordeonContainer = document.getElementById('acordeonContainer');
        const globalContainer = document.getElementById('globalContainer');

        // Limpiar los contenedores donde se muestran los cortes y cantidades
        acordeonContainer.innerHTML = "";
        globalContainer.innerHTML = "";


    }
    // Función para limpiar el modal


    // Evento para el botón "Cancelar"


    function fn_consultarVenta(datosArticulo) {
        $.ajax({
            method: "POST",
            url: "logica/clssVentaCorte.php",
            data: {
                "accion": "CONSULTARRESERVA",
                "venta_id": datosArticulo['venta_id'],
            }
        }).done(async function(text) {
            var Data = JSON.parse(text);

            datosArticuloOriginal = Data; // Almacena los datos originales
            llenarDatosModal(datosArticulo['venta_id'], datosArticulo['id_persona'], datosArticulo['cliente']);

            console.log(Data);

            // Iterar sobre los datos devueltos (Data) y agregar los artículos a la tabla
            Data.forEach(item => {
                fn_agregar_articulo_tabla(item);
            });
        });
    }

    function fn_agregar_articulo_tabla(datosArticulo) {
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];

        // Insertamos una nueva fila en la tabla
        let nuevaFila = tabla.insertRow();

        // Colocamos los valores de las celdas
        nuevaFila.insertCell(0).textContent = datosArticulo["articulo_id"]; // ID
        nuevaFila.insertCell(1).textContent = datosArticulo["minutos"] || '-'; // Minutos
        nuevaFila.insertCell(2).textContent = datosArticulo["costo_por_minuto"] || '-'; // Costo x Minuto
        nuevaFila.insertCell(3).textContent = datosArticulo["articulo_nombre"]; // Artículo
        nuevaFila.insertCell(4).textContent = datosArticulo["cantidad"]; // Cantidad
        nuevaFila.insertCell(5).textContent = datosArticulo["precio_unitario_articulo"]; // Precio unitario
        nuevaFila.insertCell(6).textContent = parseFloat(datosArticulo["sub_total"]).toFixed(2); // Subtotal

        // Celda para acciones
        let accionCell = nuevaFila.insertCell(7);

        // Si el artículo tiene corte, añadir un botón de tijera
        if (datosArticulo["corte"] === true) {
            let botonCorte = document.createElement("button");
            botonCorte.classList.add("btn", "btn-info", "btn-round", "ms-2");

            let iconoTijera = document.createElement("i");
            iconoTijera.classList.add("fas", "fa-cut"); // Icono de tijeras de FontAwesome

            // Añadir el ícono al botón
            botonCorte.appendChild(iconoTijera);

            // Añadir el botón a la celda de acciones
            accionCell.appendChild(botonCorte);
            botonCorte.addEventListener("click", () => {
                fn_modal_corte(datosArticulo);
            });
        }

        // Agregar un botón de eliminar si es necesario
        let botonEliminar = document.createElement("button");
        botonEliminar.classList.add("btn", "btn-warning", "btn-round", "ms-2");

        let iconoBasura = document.createElement("i");
        iconoBasura.classList.add("fas", "fa-trash"); // Icono de basura

        botonEliminar.appendChild(iconoBasura);
        accionCell.appendChild(botonEliminar);

        // Función para manejar el botón de eliminar
        botonEliminar.addEventListener("click", () => {
            const fila = botonEliminar.closest("tr");
            fila.remove(); // Eliminar la fila
            fn_obtener_total(); // Recalcular los totales después de eliminar
        });

        // Llamamos la función para recalcular los totales si es necesario
        fn_obtener_total();
    }

    function fn_modal_corte(datosArticulo) {
        const modal = new bootstrap.Modal(document.getElementById('miModal'), {
            backdrop: 'static', // Evita que se cierre al hacer clic fuera
            keyboard: false // Evita que se cierre con la tecla 'Esc'
        });

        fn_limpiar_modal();
        modal.show();
        console.log("datos", datosArticulo);
        const btnSi = document.getElementById('btn_si');

        // Crear una copia del botón para eliminar los eventos registrados anteriormente
        const nuevoBtnSi = btnSi.cloneNode(true);

        // Reemplazar el botón antiguo por el nuevo
        btnSi.parentNode.replaceChild(nuevoBtnSi, btnSi);


        // Obtener cantidad de productos
        let cantidad = datosArticulo['cantidad'];

        // Obtener contenedores
        const acordeonContainer = document.getElementById('acordeonContainer');
        const globalContainer = document.getElementById('globalContainer');

        // Limpiar contenido anterior
        acordeonContainer.innerHTML = "";
        globalContainer.innerHTML = "";

        // Mostrar acordeones solo si hay más de 1 producto
        if (cantidad > 1) {
            for (let i = 1; i <= cantidad; i++) {
                const acordeon = `
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading${i}">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#collapse${i}" 
                                        aria-expanded="false" aria-controls="collapse${i}">
                                    ${datosArticulo["articulo_nombre"]} - Corte ${i}
                                </button>
                            </h2>
                            <div id="collapse${i}" class="accordion-collapse collapse" 
                                aria-labelledby="heading${i}" data-bs-parent="#acordeonContainer">
                                <div class="accordion-body text-center">
                                    <p class="card-text">Minutos/Corte</p>
                                    <div class="row">
                                        <div class="col">
                                            <button id="btn_menos_${i}" class="btn btn-danger btn-round ms-2" type="button">-</button>
                                        </div>
                                        <div id="cantidad_${i}" class="col">0</div>
                                        <div class="col">
                                            <button id="btn_mas_${i}" class="btn btn-success btn-round ms-2" type="button">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                acordeonContainer.innerHTML += acordeon;
            }


            document.querySelectorAll('.accordion-button').forEach(button => {
                button.classList.add('collapsed'); // Asegura que esté colapsado
                button.setAttribute('aria-expanded', 'false'); // Marca como cerrado
            });

            // Eventos dinámicos para cada acordeón
            for (let i = 1; i <= cantidad; i++) {
                const btnMas = document.getElementById(`btn_mas_${i}`);
                const btnMenos = document.getElementById(`btn_menos_${i}`);
                const cantidadElemento = document.getElementById(`cantidad_${i}`);
                btnMenos.disabled = true;

                btnMas.addEventListener("click", () => {
                    btnMenos.disabled = false;
                    let valorActual = parseInt(cantidadElemento.innerText) || 0;
                    if (valorActual > 0) {
                        cantidadElemento.innerText = valorActual + 1;
                    } else {
                        cantidadElemento.innerText = 10;
                    }
                });

                btnMenos.addEventListener("click", () => {
                    let valorActual = parseInt(cantidadElemento.innerText) || 0;
                    if (valorActual > 0) {
                        cantidadElemento.innerText = valorActual - 1;
                    }
                    btnMenos.disabled = valorActual === 1;


                });
            }
        }

        // Sección global (siempre visible)
        const globalSection = `
                <div class="card-body text-center">
                    <p class="card-text">Minutos/Corte Todos</p>
                    <div class="row">
                        <div class="col">
                            <button id="btn_menos_global" class="btn btn-danger btn-round ms-2" type="button">-</button>
                        </div>
                        <div id="cantidad_global" class="col">0</div>
                        <div class="col">
                            <button id="btn_mas_global" class="btn btn-success btn-round ms-2" type="button">+</button>
                        </div>
                    </div>
                </div>`;
        globalContainer.innerHTML = globalSection;

        // Eventos para botones globales
        const btnMasGlobal = document.getElementById('btn_mas_global');
        const btnMenosGlobal = document.getElementById('btn_menos_global');
        const cantidadGlobal = document.getElementById('cantidad_global');
        btnMenosGlobal.disabled = true;

        btnMasGlobal.addEventListener("click", () => {
            btnMenosGlobal.disabled = false
            let valorActual = parseInt(cantidadGlobal.innerText) || 0;
            if (valorActual > 0) {
                cantidadGlobal.innerText = valorActual + 1;
            } else {
                cantidadGlobal.innerText = 10;
            }

        });

        btnMenosGlobal.addEventListener("click", () => {
            let valorActual = parseInt(cantidadGlobal.innerText) || 0;
            if (valorActual > 0) {
                cantidadGlobal.innerText = valorActual - 1;
            }
            btnMenosGlobal.disabled = valorActual === 1;
        });

        document.getElementById('btn_si').addEventListener("click", () => {
            // Obtener cantidad global
            const cantidadGlobal = parseInt(document.getElementById('cantidad_global').innerText) || 0;

            // Verificar si se está aplicando corte global
            if (cantidadGlobal > 0) {
                // Realizar corte global

                fn_corte_global(datosArticulo);
            } else {
                // Realizar corte individual
                fn_corte_individual(datosArticulo);
            }

            modal.hide(); // Cierra el modal después de la operación
        });

    }



    function fn_corte_global(datosArticulo) {
        const cantidadGlobal = parseInt(document.getElementById('cantidad_global').innerText) || 0;

        if (cantidadGlobal > 0) {
            // Obtener la tabla de artículos
            const tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
            let filas = Array.from(tabla.rows);

            // Eliminar las filas existentes para ese producto (por articulo_id)
            for (let i = filas.length - 1; i >= 0; i--) {
                let fila = filas[i];
                console.log(datosArticulo['articulo_id']);
                if (fila.cells[0].textContent.trim() === datosArticulo['articulo_id'].toString().trim()) {
                    tabla.deleteRow(i); // Usamos 'i' directamente
                    console.log("Eliminando fila en índice " + i);
                }
            }

            // Crear las nuevas filas basadas en la cantidad de minutos globales (cantidadGlobal)
            const cantidad = datosArticulo['cantidad']; // Cantidad del producto original
            const precioUnitario = datosArticulo['precio_unitario_articulo'] || 0; // Precio unitario del producto
            const costo = 1.5; // Costo por minuto, puede ser un valor fijo o dinámico según tu sistema

            for (let i = 0; i < cantidad; i++) {


                // Calcular el costo total por minuto
                const totalCosto = costo * cantidadGlobal; // Costo por minuto multiplicado por los minutos

                // Calcular el subtotal: (precio_unitario * cantidad) + (costo * minutos)
                const subtotal = (precioUnitario * 1) + totalCosto;



                // Crear la nueva fila y agregarla a la tabla
                const nuevaFila = tabla.insertRow();
                // Crear celdas para cada dato
                const celdaId = nuevaFila.insertCell(0);

                const celdaMinutos = nuevaFila.insertCell(1);
                const celdaCosto = nuevaFila.insertCell(2);

                const celdaNombre = nuevaFila.insertCell(3);
                const celdaCantidad = nuevaFila.insertCell(4);
                const celdaPrecioUnitario = nuevaFila.insertCell(5);
                const celdaSubtotal = nuevaFila.insertCell(6);
                const celdaAcciones = nuevaFila.insertCell(7);

                // Rellenar las celdas con los datos del artículo
                celdaId.textContent = datosArticulo['articulo_id'];
                celdaNombre.textContent = `${datosArticulo["articulo_nombre"]} - Corte ${i}`;
                celdaCantidad.textContent = 1;
                celdaMinutos.textContent = cantidadGlobal;
                celdaCosto.textContent = totalCosto.toFixed(2); // Mostrar el costo total
                celdaPrecioUnitario.textContent = parseFloat(precioUnitario).toFixed(2); // Mostrar el precio unitario
                celdaSubtotal.textContent = subtotal.toFixed(2); // Mostrar el subtotal

                // Agregar el botón de eliminación
                let botonEliminar = document.createElement("button");
                botonEliminar.classList.add("btn", "btn-warning", "btn-round", "ms-2");

                let iconoBasura = document.createElement("i");
                iconoBasura.classList.add("fas", "fa-trash"); // Icono de basura

                botonEliminar.appendChild(iconoBasura);
                celdaAcciones.appendChild(botonEliminar);

                // Función para manejar el botón de eliminar
                botonEliminar.addEventListener("click", () => {
                    const fila = botonEliminar.closest("tr");
                    fila.remove(); // Eliminar la fila
                    fn_obtener_total(); // Recalcular los totales después de eliminar
                });
            }

            // Actualizar el total y mostrarlo
            fn_obtener_total();
        } else {
            alert("Por favor, ingrese un valor mayor a 0 en minutos globales.");
        }
    }



    // Función para manejar el corte individual (actualización de cantidad)
    function fn_corte_individual(datosArticulo) {
        // Obtener la cantidad de cortes individuales
        const cantidad = datosArticulo['cantidad'];

        // Obtener la tabla de artículos
        const tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
        let filas = Array.from(tabla.rows);

        // Para cada corte individual, verificar cuántos se agregan
        for (let i = 1; i <= cantidad; i++) {
            const cantidadtiempo = parseInt(document.getElementById(`cantidad_${i}`).innerText) || 0;
            console.log(i, cantidadtiempo)
            if (cantidadtiempo > 0) {
                // Reducir la cantidad del artículo original en la tabla
                filas.forEach(fila => {
                    // Si el articulo_id en la fila coincide con el articulo_id del artículo original
                    if (fila.cells[0].textContent.trim() === datosArticulo['articulo_id'].toString().trim()) {
                        const cantidadActual = parseInt(fila.cells[4].textContent) || 0; // Leer la cantidad existente
                        const nuevaCantidad = cantidadActual - 1; // Restar la cantidad agregada

                        // Actualizar la cantidad en la tabla
                        fila.cells[4].textContent = nuevaCantidad > 0 ? nuevaCantidad : 0; // Si la cantidad es menor a 0, dejar 0

                        // También actualizar el costo total y el subtotal de esa fila
                        const precioUnitario = parseFloat(fila.cells[5].textContent) || 0;
                        const subtotal = precioUnitario * nuevaCantidad; // Subtotal del corte

                        // Actualizar las celdas de costo y subtotal
                        fila.cells[6].textContent = subtotal.toFixed(2); // Subtotal
                    }
                });

                const precioUnitario = datosArticulo['precio_unitario_articulo'] || 0; // Precio unitario del producto
                const costo = 1.5; // Costo por minuto, puede ser un valor fijo o dinámico según tu sistema

                // Calcular el costo total por minuto
                const totalCosto = costo * cantidadtiempo; // Costo por minuto multiplicado por los minutos

                // Calcular el subtotal: (precio_unitario * cantidad) + (costo * minutos)
                const subtotal = (precioUnitario * 1) + totalCosto;

                // Crear la nueva fila y agregarla a la tabla
                const nuevaFila = tabla.insertRow();
                // Crear celdas para cada dato
                const celdaId = nuevaFila.insertCell(0);

                const celdaMinutos = nuevaFila.insertCell(1);
                const celdaCosto = nuevaFila.insertCell(2);

                const celdaNombre = nuevaFila.insertCell(3);
                const celdaCantidad = nuevaFila.insertCell(4);
                const celdaPrecioUnitario = nuevaFila.insertCell(5);
                const celdaSubtotal = nuevaFila.insertCell(6);
                const celdaAcciones = nuevaFila.insertCell(7);

                // Rellenar las celdas con los datos del artículo
                celdaId.textContent = datosArticulo['articulo_id'];
                celdaNombre.textContent = `${datosArticulo["articulo_nombre"]} - Corte ${i}`;
                celdaCantidad.textContent = 1;
                celdaMinutos.textContent = cantidadtiempo;
                celdaCosto.textContent = totalCosto.toFixed(2); // Mostrar el costo total
                celdaPrecioUnitario.textContent = parseFloat(precioUnitario).toFixed(2); // Mostrar el precio unitario
                celdaSubtotal.textContent = subtotal.toFixed(2); // Mostrar el subtotal

                // Agregar el botón de eliminación
                let botonEliminar = document.createElement("button");
                botonEliminar.classList.add("btn", "btn-warning", "btn-round", "ms-2");

                let iconoBasura = document.createElement("i");
                iconoBasura.classList.add("fas", "fa-trash"); // Icono de basura

                botonEliminar.appendChild(iconoBasura);
                celdaAcciones.appendChild(botonEliminar);

                // Función para manejar el botón de eliminar
                botonEliminar.addEventListener("click", () => {
                    const fila = botonEliminar.closest("tr");
                    fila.remove(); // Eliminar la fila
                    fn_obtener_total(); // Recalcular los totales después de eliminar
                });


            }
        }

        // Actualizar el total después de realizar el corte
        fn_obtener_total();
    }

    function fn_obtener_total() {
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
        var filas = tabla.getElementsByTagName("tr");
        var totalCorte = 0;
        var totalArticulos = 0;
        var total = 0;

        for (var i = 0; i < filas.length; i++) {
            var celdas = filas[i].getElementsByTagName("td");
            totalCorte += parseFloat(celdas[2].innerText) || 0;
            totalArticulos += (parseFloat(celdas[4].innerText) * parseFloat(celdas[5].innerText)) || 0;
            total += parseFloat(celdas[6].innerText) || 0;
        }

        var lbl_subtotal_cortes = document.getElementById("id_subtotal_cortes");
        var lbl_subtotal_articulos = document.getElementById("id_subtotal_articulos");
        var lbl_subtotal_general = document.getElementById("id_subtotal_general");

        lbl_subtotal_cortes.innerText = totalCorte.toFixed(2);
        lbl_subtotal_articulos.innerText = totalArticulos.toFixed(2);
        lbl_subtotal_general.innerText = total.toFixed(2);




    }
</script>


<script>
    document.getElementById("btnRealizarPago").addEventListener("click", function() {


        // Mostrar el modal manualmente
        const modal = new bootstrap.Modal(document.getElementById("modalRealizarPago"));
        modal.show();

        const subtotalGeneral = document.getElementById("id_subtotal_general").textContent;
        document.getElementById("montoTotal").value = subtotalGeneral; // Asignar el monto total

    });

    function llenarDatosModal(idVenta, idPersona, nombreCliente) {
        // Actualizamos el contenido del modal con los datos proporcionados
        document.getElementById("idVenta").textContent = idVenta; // Para el ID de la venta
        document.getElementById("idPersona").textContent = idPersona; // Para el ID del cliente
        document.getElementById("nombreCliente").value = nombreCliente; // Para el nombre del cliente
    }
</script>

<?php
include("pie.php");
?>