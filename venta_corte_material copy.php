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
    
    #tabla_articulos th:nth-child(1),
    #tabla_articulos td:nth-child(1),
    #tabla_articulos th:nth-child(10),
    #tabla_articulos td:nth-child(10),
    #tabla_articulos th:nth-child(11),
    #tabla_articulos td:nth-child(11) {
        display: none !important;
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
                                                                href="#panelDetalles"
                                                                class="btn btn-success btn-round"

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
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="modalSoloCorteLabel">Opciones de Corte</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="col-12 p-4 bg-light rounded">
                            <h6 class="fw-bold text-center mb-4">Opciones de Corte</h6>
                            <div class="mb-4">
                                <!-- Minutos Corte -->
                                <div class="text-center" style="flex: 1;">
                                <p class="mb-1">Minutos Corte</p>
                                <div class="d-flex justify-content-center align-items-center mb-2">
                                    <button id="btnRestarSoloCorte" class="btn btn-danger btn-round">-</button>
                                    <input id="cantidad_solocorte" type="number" class="form-control text-center mx-2" value="0" style="width: 80px; font-size: 1.2rem;" />
                                    <button id="btnSumarSoloCorte" class="btn btn-success btn-round">+</button>
                                </div>
                                </div>
                                
                                <!-- Línea divisoria -->
                                <hr>
                                
                                <!-- Precio Corte -->
                                <div class="text-center" style="flex: 1;">
                                <p class="mb-1">Precio Corte</p>
                                <div class="w-100 d-flex justify-content-center mb-1">
                                    <input id="precioSoloCorte" type="number" class="form-control text-center mx-2" value="1.5" style="width: 90px; font-size: 1.2rem;" />
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button id="btnIncremento05SoloCorte" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+0.5</button>
                                    <button id="btnIncremento1SoloCorte" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+1</button>
                                    <button id="btnIncremento2SoloCorte" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+2</button>
                                    <button id="btnIncremento5SoloCorte" class="btn btn-outline-primary btn-sm" style="font-size: 0.9rem;">+5</button>
                                </div>
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
        <div class="row " id="panelAdicionarMas" style="display:none;">
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
                                    <button class="nav-link" id="btnAbrirModalPloteo" >Ploteo</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="btnAbrirModalImprimir">Imprimir</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="btnAbrirModalEscaneo">Escaneo</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="btnAbrirModalSolo" role="tab" aria-controls="pills-contact-nobd" aria-selected="false">Solo Corte</button>
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
                               
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div id="panelDetalles" class="card border-primary" style="display:none;">
            <div class="card-body">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-center align-items-center md-2">
                            <h4 class="card-title">Reserva</h4>

                            <div class="card-sub">
                                Aquí podras el visualizar datos del cliente y de quien realizó la reserva
                            </div>
                            <div class="col-md-6">
                                <div class="card-body">
                                    CLIENTE
                                    <div class="card-title" id="idClienteReservaDetalle"></div>
                                    <hr>
                                    <div><strong>Número de Celular:</strong> <span id="idNumCelClienteReserva">942781324</span> </div>
                                    <div><strong>Correo Cliente:</strong> <span id="idCorreoClienteReserva">frvf2000@gmail.com</span> </div>
                                    <div><strong>N° de Documento:</strong> <span id="idNumDocClienteReserva">73578005</span> </div>
                                </div>
                            </div>

                            <div class="col-md-6" style="border-left: 0.5px solid #6e6e6e; padding-left: 10px;">
                                <div class="card-body">
                                    ATENDIDO POR
                                    <h4 class="card-title" id="idUsuarioReservaDetalle"></h4>
                                    <div>ID VENTA: <span id="idVentaReserva"></span></div>
                                    <hr>
                                    <div class="mb-3">
                                        <input
                                            type="date"
                                            class="form-control form-control-sm text-center"
                                            name=""
                                            id="idFechaReservaDetalle"
                                            aria-describedby="helpId"
                                            readonly />
                                    </div>
                                    <div class="mb-3">
                                        <input
                                            type="text"
                                            class="form-control form-control-sm text-center"
                                            name=""
                                            id="idHoraReservaDetalle"
                                            aria-describedby="helpId"
                                            readonly />

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card text-start">
                    <div class="card-body">
                        <h4 class="card-title">Detalle de Venta</h4>
                        <div class="table-responsive">
                            <table id="tabla_articulos" class="table table-sm mt-3">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">MINUTOS</th>
                                        <th scope="col">Tarifa</th>
                                        <th scope="col">Total corte</th>
                                        <th scope="col">Articulo</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Precio Unitario</th>
                                        <th scope="col">Sub Total (S/)</th>
                                        <th scope="col">Accion</th>
                                        <th scope="col">IDMOVIMIENTO</th>
                                        <th scope="col">IDRELARTICULO</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-stats card-round">
                            <div class="card-body text-center">
                                <h5 id="label_total_cortes" class="card-title">Cortes (S/)</h5>
                                <span id="id_subtotal_cortes" style="font-size: 1.3rem;" aria-labelledby="label_total_cortes">xx.xx</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-stats card-round">
                            <div class="card-body text-center">
                                <h5 id="label_total_articulos" class="card-title">Artículos (S/)</h5>
                                <span id="id_subtotal_articulos" style="font-size: 1.3rem;" aria-labelledby="label_total_articulos">xx.xx</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-primary card-stats card-round">
                            <div class="card-body text-center">
                                <h5 id="label_total_general" class="card-title">Total Venta (S/)</h5>
                                <span id="id_subtotal_general" style="font-size: 1.3rem;" aria-labelledby="label_total_general">xx.xx</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <button id="btnRealizarPago" type="button" class="btn btn-success" style="width: 80%;">
                            Realizar Pago
                        </button>
                    </div>

                </div>
            </div>
        </div>


    </div>


</div>
<!-- Modal Unificado -->
<div class="modal fade " data-bs-backdrop="static" id="modalCantidad" tabindex="-1" aria-labelledby="modalCantidadCorteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCantidadCorteLabel">Configurar Cantidad y Corte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <!-- Sección de cantidad -->
                    <div class="row mb-3">
                        <div class="col-12 p-3 bg-light rounded">
                            <h6 id="nombreArticulo" class="fw-bold text-center mb-3">Nombre del artículo</h6>
                            <div class="d-flex justify-content-center align-items-center">
                                <button id="btnRestarCantidad" class="btn btn-danger btn-round" >-</button>
                                <input id="inputCantidad" type="number" class="form-control text-center mx-2 " value="1" style="width: 80px; font-size: 1.2rem;" />
                                <button id="btnSumarCantidad" class="btn btn-success btn-round" >+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de corte (solo visible si cantidad = 1) -->
                    <div id="seccionCorte" class="row mb-3" style="display: none;">
                        <div class="col-12 p-4 bg-light rounded">
                            <h6 class="fw-bold text-center mb-4">Opciones de Corte</h6>
                            <div class="mb-4">
                                <div class="text-center" style="flex: 1;">
                                    <p class="mb-1">Minutos Corte</p>
                                    <div class="d-flex justify-content-center align-items-center mb-2">
                                        <button id="btnRestarCorte" class="btn btn-danger btn-round" >-</button>
                                        <input id="cantidadCorte" type="number" class="form-control text-center mx-2 " value="0" style="width: 80px; font-size: 1.2rem;" />
                                        <button id="btnSumarCorte" class="btn btn-success btn-round" >+</button>
                                    </div>
                                </div>
                                
                                <!-- Línea divisoria -->
                                <hr>
                                
                                <div class="text-center" style="flex: 1;">
                                    <p class="mb-1">Precio Corte</p>
                                    <div class="w-100 d-flex justify-content-center mb-1">
                                        <input id="precioCorte" type="number" class="form-control text-center mx-2 " value="1.5" style="width: 90px; font-size: 1.2rem;" />
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button id="btnIncremento05" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+0.5</button>
                                        <button id="btnIncremento1" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+1</button>
                                        <button id="btnIncremento2" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+2</button>
                                        <button id="btnIncremento5" class="btn btn-outline-primary btn-sm" style="font-size: 0.9rem;">+5</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <!-- Botón Confirmar a la izquierda -->
                <button id="btnConfirmarCantidad" class="btn btn-primary" style="width: 120px;">Confirmar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal trigger button -->
<!-- Modal  -->
<div class="modal fade" id="modalGenerico" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalGenericoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalGenericoLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalContent">
        <!-- Contenido dinámico se cargará aquí -->
      </div>

    </div>
  </div>
</div>

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
                            <h4 class="card-title fs-1 fw-bold">Realizar Pago </h4>
                            <h1 class="card-title fw-bold" style="font-size: 3rem;"> S/ <span id="idMontoVentaTitulo">#</span></h1>
                        </div>
                        <!--<div class="card-body text-center">
                            <h1 class="card-title">S/ xx.xx</h1>
                        </div>-->

                        <div class="card-sub">
                            Aquí realiza tus pagos
                        </div>
                        <div>
                            <span>ID Venta: <span id="idVenta">#</span></span> |
                            <span>ID Cliente: <span id="idPersona">#</span></span> |
                            <span>ID Usuario Reserva: <span id="idUsuario">#</span>
                                <br>
                                <span><strong>Atendiendo la Transacción:</strong> <span id="idAtencionFinal"><?php echo $id_usuario_s . "-" . $nombre . ", " . $ape_usuario ?></span></span>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label for="" class="form-label"><strong>Cliente</strong></label>
                            <input
                                type="text"
                                class="form-control"
                                name=""
                                id="nombreCliente"
                                aria-describedby="helpId"
                                placeholder="AGREGAR EL NOMBRE DEL CLIENTE" readonly />
                        </div>
                        <div class="row justify-content-center align-items-center g-2">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Número de Telefono</strong></label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        name=""
                                        id="idUpdateNumTelefonoCliente"
                                        aria-describedby="helpId"
                                        placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Correo Electronico</strong></label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        name=""
                                        id="idUpdateCorreoCliente"
                                        aria-describedby="helpId"
                                        placeholder="" />
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- Monto Total -->
                        <div
                            class="row justify-content-center align-items-center md-2">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="montoTotal" class="form-label"><strong>Monto Original de Venta</strong> </label>
                                    <div class="input-group">
                                        <span class="input-group-text">S/</span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="montoTotal"
                                            readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="montoVentaFinal" class="form-label">Monto Final de Venta</label>
                                    <div class="input-group">
                                        <span class="input-group-text">S/</span>
                                        <input
                                            type="number"
                                            class="form-control"
                                            id="montoTotalFinal"
                                            placeholder="Monto con descuento a clientes" />
                                    </div>
                                </div>
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
                                    <form id="form-pago-directo">
                                        <div id="panel_forma_pago" class="mb-3">
                                            <div class="card-sub">
                                                <div class="text-center">
                                                    Aquí podrás elegir si realizan pagos Directo.
                                                </div>
                                            </div>

                                            <label for="" class="form-label"><strong>Forma de Pago</strong></label>
                                            <!-- Botón de agregar más formas de pago -->
                                            <button id="btnAgregarPago" class="btn btn-secondary btn-round ms-2" type="button">+</button>

                                            <div class="d-flex align-items-center">
                                                <!-- Select de formas de pago -->
                                                <select class="form-select form-select-md" name="formaPago" id="formaPagoSelect">
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
                                                <input type="number" class="form-control form-control-md ms-2" placeholder="Monto" min="0" name="monto" id="montoSelect_0">
                                            </div>

                                            <!-- Contenedor para los selects adicionales -->
                                            <div id="contenedorPagos" class="mt-3"></div>
                                        </div>

                                        <hr>

                                        <!-- Monto Total -->
                                        <!--
                                        <div id="panelMontos" class="row justify-content-center align-items-center g-2">
                                            <div class="col-md-4">
                                                <label for="" class="form-label"><b>Falta Pagar</b></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">S/</span>
                                                    <input type="number" class="form-control" name="faltaPagar" placeholder="" readonly />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="" class="form-label"><b>Paga Con</b></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">S/</span>
                                                    <input type="number" class="form-control" name="pagaCon" placeholder="" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="" class="form-label"><b>Vuelto (S/)</b></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">S/</span>
                                                    <input type="number" class="form-control" name="vuelto" placeholder="" />
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                        -->
                                    </form>
                                    <div class="text-center">
                                        <a class="btn btn-success" href="#" role="button" onclick="fn_pagar_directo()">Pagar</a>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="pago-credito" role="tabpanel" aria-labelledby="pills-profile-tab-icon">
                                    <div class="card-sub">
                                        <div class="text-center">
                                            Aquí podrás elegir si realizan pagos al Crédito.
                                        </div>
                                    </div>

                                    <div class="card-sub">
                                        <div class="text-center">

                                            Si un cliente te deja pagado algo de la venta, <strong>REGISTRALO</strong>.
                                            <br>
                                            Si no, deja en blanco y darle click al boton Realizar <br><strong>Pago a Credito</strong>
                                        </div>
                                    </div>



                                    <!-- Formulario para el pago a crédito -->
                                    <form id="form-pago-credito">
                                        <!-- Monto Total -->
                                        <label for="" class="form-label"><strong>Forma de Pago</strong></label>

                                        <!-- Botón de agregar más formas de pago -->
                                        <button id="btnAgregarPagoCredito" class="btn btn-secondary btn-round ms-2" type="button">+</button>

                                        <!-- Primer campo de pago -->
                                        <div class="d-flex align-items-center" id="pagoCredito_0">
                                            <!-- Select de formas de pago -->
                                            <select class="form-select form-select-md" name="formaPagoCredito[]" id="formaPagoCreditoSelect_0">
                                                <?php
                                                foreach (listarFormaPago() as $datosFormaPago) {
                                                    echo '<option value="' . $datosFormaPago["id"] . '">' . $datosFormaPago["nombre"] . '</option>';
                                                }
                                                ?>
                                            </select>

                                            <!-- Caja de texto para monto -->
                                            <input type="number" class="form-control form-control-md ms-2" placeholder="Monto" min="0" name="montoCredito[]" id="montoSelectCredito_0">
                                        </div>

                                        <!-- Contenedor para los selects adicionales -->
                                        <div id="contenedorPagosCredito" class="mt-3"></div>

                                        <br>
                                        <!-- Botón para realizar el pago -->
                                        <div class="text-center">
                                            <a class="btn btn-secondary" href="#" role="button" onclick="fn_pagar_credito()">Realizar Pago a Credito</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
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

<!--FRANCO -->
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
<!--FRANCO -->
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
        nuevoSelect.name = 'formaPago_' + contador; // Agregar nombre dinámico
        nuevoSelect.innerHTML = `<?php
                                    foreach (listarFormaPago() as $datosFormaPago) {
                                        echo '<option value="' . $datosFormaPago["id"] . '">' . $datosFormaPago["nombre"] . '</option>';
                                    }
                                    ?>`;

        // Crear una nueva caja de texto para el monto
        const nuevoInputMonto = document.createElement('input');
        nuevoInputMonto.type = 'number';
        nuevoInputMonto.classList.add('form-control', 'form-control-md', 'ms-2');
        nuevoInputMonto.placeholder = 'Monto';
        nuevoInputMonto.min = '0';
        nuevoInputMonto.name = 'monto_' + contador; // Agregar nombre dinámico
        nuevoInputMonto.id = 'montoSelect_' + contador;

        // Crear un botón de eliminación pequeño
        const btnEliminar = document.createElement('button');
        btnEliminar.type = 'button';
        btnEliminar.classList.add('btn', 'btn-danger', 'btn-sm', 'ms-2'); // Clase btn-sm para hacerlo más pequeño
        btnEliminar.textContent = '-'; // Texto del botón
        btnEliminar.addEventListener('click', function() {
            contenedorPagos.removeChild(nuevoContenedor); // Eliminar el contenedor
        });

        // Agregar el select, el input y el botón de eliminación al contenedor
        nuevoContenedor.appendChild(nuevoSelect);
        nuevoContenedor.appendChild(nuevoInputMonto);
        nuevoContenedor.appendChild(btnEliminar);

        // Agregar el contenedor al contenedor principal
        contenedorPagos.appendChild(nuevoContenedor);

        // Incrementar el contador para los nuevos inputs
        contador++;
    });

    // Variables para manejar los selects y montos adicionales de pago a crédito
    const btnAgregarPagoCredito = document.getElementById('btnAgregarPagoCredito');
    const contenedorPagosCredito = document.getElementById('contenedorPagosCredito');
    let contadorCredito = 1; // Para numerar los campos adicionales de pago a crédito

    // Evento para agregar más selects con montos de pago a crédito
    btnAgregarPagoCredito.addEventListener('click', function() {
        // Crear un contenedor para el nuevo select y su campo de monto
        const nuevoContenedorCredito = document.createElement('div');
        nuevoContenedorCredito.classList.add('d-flex', 'align-items-center', 'mb-2');
        nuevoContenedorCredito.id = 'pagoCredito_' + contadorCredito; // ID único para cada contenedor

        // Crear un nuevo select para el pago a crédito
        const nuevoSelectCredito = document.createElement('select');
        nuevoSelectCredito.classList.add('form-select', 'form-select-md', 'me-2');
        nuevoSelectCredito.name = 'formaPagoCredito[]'; // Nombre único para el array
        nuevoSelectCredito.id = 'formaPagoCreditoSelect_' + contadorCredito; // ID único para el select
        nuevoSelectCredito.innerHTML = `<?php
                                        foreach (listarFormaPago() as $datosFormaPago) {
                                            echo '<option value="' . $datosFormaPago["id"] . '">' . $datosFormaPago["nombre"] . '</option>';
                                        }
                                        ?>`;

        // Crear una nueva caja de texto para el monto de pago a crédito
        const nuevoInputMontoCredito = document.createElement('input');
        nuevoInputMontoCredito.type = 'number';
        nuevoInputMontoCredito.classList.add('form-control', 'form-control-md', 'ms-2');
        nuevoInputMontoCredito.placeholder = 'Monto';
        nuevoInputMontoCredito.min = '0';
        nuevoInputMontoCredito.name = 'montoCredito[]'; // Nombre único para el array
        nuevoInputMontoCredito.id = 'montoSelectCredito_' + contadorCredito; // ID único para el campo de monto

        // Crear un botón de eliminación pequeño
        const btnEliminarCredito = document.createElement('button');
        btnEliminarCredito.type = 'button';
        btnEliminarCredito.classList.add('btn', 'btn-danger', 'btn-sm', 'ms-2'); // Clase btn-sm para hacerlo más pequeño
        btnEliminarCredito.textContent = '-'; // Texto del botón
        btnEliminarCredito.addEventListener('click', function() {
            contenedorPagosCredito.removeChild(nuevoContenedorCredito); // Eliminar el contenedor
        });

        // Agregar el select, el input y el botón de eliminación al contenedor
        nuevoContenedorCredito.appendChild(nuevoSelectCredito);
        nuevoContenedorCredito.appendChild(nuevoInputMontoCredito);
        nuevoContenedorCredito.appendChild(btnEliminarCredito);

        // Agregar el contenedor al contenedor principal
        contenedorPagosCredito.appendChild(nuevoContenedorCredito);

        // Incrementar el contador para los nuevos inputs
        contadorCredito++;
    });
</script>

<!-- FRANCO -->
<script>
    function fn_pagar_directo() {
        var datosSerializados = $('#form-pago-directo').serializeArray();

        console.log(datosSerializados); // Ver los datos serializados como un array de objetos

        //////////////////////////////////////////////////////
        var numTelefonoUpdate = document.getElementById('idUpdateNumTelefonoCliente').value;
        //////////////////////////////////////////////////////////////////////////
        var idVenta = document.getElementById('idVenta').textContent;
        var idPersona = document.getElementById('idPersona').textContent;
        var idUsuario = document.getElementById('idUsuario').textContent;
        var idAtencionFinal = document.getElementById('idAtencionFinal').textContent;
        var numUpdateTelefonoPersona = document.getElementById('idUpdateNumTelefonoCliente').value;
        ////

        var montoOriginal = parseFloat(document.getElementById('montoTotal').value);
        var montoFinal = parseFloat(document.getElementById('montoTotalFinal').value);

        if (isNaN(montoFinal)) {
            montoFinal = montoOriginal;
        };



        ///////////////////////////////////////////////////////

        var js_detalle_pago = [];

        var formaPago = null;
        var monto = null;
        var acumMontos = 0;
        for (var i = 0; i < datosSerializados.length; i++) {
            var dato = datosSerializados[i];

            if (dato.name.startsWith('formaPago')) {
                formaPago = dato.value;
            }

            if (dato.name.startsWith('monto')) {
                monto = parseFloat(dato.value);
                acumMontos = acumMontos + monto;
            }
            if (formaPago && monto) {
                js_detalle_pago.push({
                    "venta_id": idVenta,
                    "id_forma_pago": formaPago,
                    "monto_forma_pago": monto
                });
                formaPago = null;
                monto = null;
            }
        };
        var js_venta = {
            "venta_id": idVenta,
            "atencion_final_usuario": idAtencionFinal,
            "numUpdateTelefonoPersona": numUpdateTelefonoPersona,
            "monto_original": montoOriginal,
            "monto_venta_final": montoFinal,
            "js_detalle_pagos": js_detalle_pago
        };

        var js_for_pago = {
            "venta_id": idVenta,
            "monto_original": montoOriginal,
            "monto_venta_final": montoFinal,
            "comentario": ""
        };
        //monto_forma_pago
        if (js_detalle_pago.length === 0) {
            swal("Ups!, Falta Agregar los monto de acuerdo a forma de Pago", "Agrega los montos :)", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
            console.log("Falta Agregar los Metodos de Pago");
        } else if (acumMontos > montoFinal) {
            swal("Ups!, Los montos ingresados son MAYORES al Monto final de la venta", "Agrega correctamente los montos :)", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });

        } else if (acumMontos < montoFinal) {
            swal("Ups!, Los montos ingresados son MENORES al Monto final de la venta", "Agrega correctamente los montos :)", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });

        } else {
            console.log("js_detalle_pago", js_detalle_pago);
            console.log("CELULAR UPDATE", numTelefonoUpdate);

            console.log("js_detalle_pago final: ", js_detalle_pago);

            $.ajax({
                url: 'logica/clssInsertPA.php',
                type: 'POST',
                data: {
                    accion: 'FINALIZARVENTA',
                    jsDatosVenta: JSON.stringify(js_venta)
                },
                success: function(response) {

                    console.log("Respuesta del servidor: ", response);

                    try {
                        var result = JSON.parse(response);
                        if (result.estado === true) {
                            swal({
                                title: "Pagado con Exito!",
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

    function fn_pagar_credito() {


        //////////////////////////////////////////////////////
        var numTelefonoUpdate = document.getElementById('idUpdateNumTelefonoCliente').value;
        //////////////////////////////////////////////////////////////////////////
        var idVenta = document.getElementById('idVenta').textContent;
        var idPersona = document.getElementById('idPersona').textContent;
        var idUsuario = document.getElementById('idUsuario').textContent;
        var idAtencionFinal = document.getElementById('idAtencionFinal').textContent;
        var numUpdateTelefonoPersona = document.getElementById('idUpdateNumTelefonoCliente').value;
        ////

        var montoOriginal = parseFloat(document.getElementById('montoTotal').value);
        var montoFinal = parseFloat(document.getElementById('montoTotalFinal').value);

        if (isNaN(montoFinal)) {
            montoFinal = montoOriginal;
        };

        var datosSerializadosCredito = $('#form-pago-credito').serializeArray();
        console.log(datosSerializadosCredito);

        var js_detalle_deuda = [];

        var formaPagoCredito = null;
        var montoCredito = null;
        var acumMontos = 0;
        for (var i = 0; i < datosSerializadosCredito.length; i++) {
            var dato = datosSerializadosCredito[i];

            if (dato.name.startsWith('formaPagoCredito[]')) {
                formaPagoCredito = dato.value;
            }

            if (dato.name.startsWith('montoCredito[]')) {
                montoCredito = parseFloat(dato.value);
                acumMontos = acumMontos + montoCredito;
            }
            if (formaPagoCredito && montoCredito) {
                js_detalle_deuda.push({
                    "venta_id": idVenta,
                    "id_forma_pago": formaPagoCredito,
                    "monto_forma_pago": montoCredito
                });
                formaPagoCredito = null;
                montoCredito = null;
            }
        };
        if (isNaN(acumMontos)) {
            acumMontos = 0;
        }
        if (js_detalle_deuda.length === 0) {
            js_detalle_deuda = null;
        }

        var js_venta = {
            "venta_id": idVenta,
            "atencion_final_usuario": idAtencionFinal,
            "numUpdateTelefonoPersona": numUpdateTelefonoPersona,
            "monto_original": montoOriginal,
            "monto_venta_final": montoFinal,
            "monto_inicial": acumMontos,
            "js_detalle_deuda": js_detalle_deuda
        };
        console.log(js_venta);
        $.ajax({
            url: 'logica/clssInsertPA.php',
            type: 'POST',
            data: {
                accion: 'FINALIZARVENTACREDITO',
                jsDatosVenta: JSON.stringify(js_venta)
            },
            success: function(response) {

                console.log("Respuesta del servidor: ", response);

                try {
                    var result = JSON.parse(response);
                    if (result.estado === true) {
                        swal({
                            title: "Pagado con Exito!",
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
</script>

<!-- SOLO CORTE -->
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Registrar eventos para los botones de incremento y decremento (fuera del modal)
        document.getElementById("btnSumarSoloCorte").addEventListener("click", function() {
            let cantidad = parseInt(document.getElementById("cantidad_solocorte").value);
            if(cantidad == 0){
                document.getElementById("cantidad_solocorte").value = 10;
            }else{
                document.getElementById("cantidad_solocorte").value = cantidad + 1;
            }
        });

        document.getElementById("btnRestarSoloCorte").addEventListener("click", function() {
            let cantidad = parseInt(document.getElementById("cantidad_solocorte").value);
            if (cantidad > 0) {
                document.getElementById("cantidad_solocorte").value = cantidad - 1;
            }
        });

        // Para los incrementos en el precio
        document.getElementById("btnIncremento05SoloCorte").addEventListener("click", function() {
            let precio = parseFloat(document.getElementById("precioSoloCorte").value);
            document.getElementById("precioSoloCorte").value = (precio + 0.5).toFixed(2);
        });

        document.getElementById("btnIncremento1SoloCorte").addEventListener("click", function() {
            let precio = parseFloat(document.getElementById("precioSoloCorte").value);
            document.getElementById("precioSoloCorte").value = (precio + 1).toFixed(2);
        });

        document.getElementById("btnIncremento2SoloCorte").addEventListener("click", function() {
            let precio = parseFloat(document.getElementById("precioSoloCorte").value);
            document.getElementById("precioSoloCorte").value = (precio + 2).toFixed(2);
        });

        document.getElementById("btnIncremento5SoloCorte").addEventListener("click", function() {
            let precio = parseFloat(document.getElementById("precioSoloCorte").value);
            document.getElementById("precioSoloCorte").value = (precio + 5).toFixed(2);
        });

        // Abrir el modal y manejar el evento de agregar corte
        document.getElementById('btnAbrirModalSolo').addEventListener('click', function(event) {
            event.preventDefault();  // Prevenir el comportamiento por defecto del botón
            let navLinks = document.querySelectorAll(".nav-link");

            // Remover la clase 'active' de todas las pestañas
            navLinks.forEach(function(link) {
                link.classList.remove("active");
            });

            // Desactivar todos los panes (contenido de las pestañas)
            let tabPanes = document.querySelectorAll(".tab-pane");
            tabPanes.forEach(function(pane) {
                pane.classList.remove("show", "active");
            });
            // Mostrar el modal de Solo Corte
            const modalElement = document.getElementById('modalSoloCorte');
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static', // Evita que se cierre al hacer clic fuera
                keyboard: false // Evita que se cierre con la tecla 'Esc'
            });

            // Seleccionar el botón "Agregar"
            const btn_agregar = document.getElementById('btn_agregar_solocorte');

            // Eliminar cualquier escucha de eventos previa
            btn_agregar.removeEventListener("click", agregarDatosCorte);

            // Agregar una nueva escucha de eventos para agregar un corte
            btn_agregar.addEventListener("click", agregarDatosCorte);
            modal.show(); // Muestra el modal
        });

        // Función que maneja el evento de agregar datos
        async function agregarDatosCorte() {
            const cantidadMinutos = parseInt(document.getElementById('cantidad_solocorte').value) || 0;
            const tarifa = parseFloat(document.getElementById('precioSoloCorte').value) || 0;

            // Crear el objeto datosCorte
            const datosCorte = [{
                id: '0', // Id del corte
                minutos: cantidadMinutos, // Minutos registrados
                costo_por_minuto: tarifa, // Costo por minuto
                costo: cantidadMinutos * tarifa,
                articulo: 'CORTE MATERIAL',
                id_movimiento: 6,
                precio_venta: null,
                cantidad: null,
            }];

            const corte = datosCorte[0];
            let venta_id = document.getElementById('idVentaReserva').textContent;

            try {
                // Espera a que fn_insert_movimiento se complete
                const response = await fn_adicionar_articulo(venta_id, corte);
                console.log("Movimiento insertado con éxito:", response);

                // Si tiene éxito, continúa con el resto del proceso
                fn_solo_corte_tabla(datosCorte);
                
                document.getElementById('cantidad_solocorte').value = '0';
                document.getElementById('precioSoloCorte').value = '1.5'; // Valor inicial

                // Ocultar el modal
                const modalElement = document.getElementById('modalSoloCorte');
                const modal = bootstrap.Modal.getInstance(modalElement);
                modal.hide();
            } catch (error) {
                // Manejar el error
                console.error("Error al insertar movimiento:", error.message);
                alert("Error al procesar el movimiento: " + error.message);
            }

           
        }

        function fn_solo_corte_tabla(datosCorte) {
            var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];

            datosCorte.forEach(corte => {
                let nuevaFila = tabla.insertRow();

                nuevaFila.insertCell(0).textContent = corte.id; // ID
                nuevaFila.insertCell(1).textContent = corte.minutos; // Minutos
                nuevaFila.insertCell(2).textContent = corte.costo_por_minuto; // Costo x Minuto
                nuevaFila.insertCell(3).textContent = (corte.costo).toFixed(2); // Costo x Minuto
                nuevaFila.insertCell(4).textContent = corte.articulo; // Artículo
                nuevaFila.insertCell(5).textContent = '-'; // Cantidad fija por corte
                nuevaFila.insertCell(6).textContent = '-'; // Precio unitario
                nuevaFila.insertCell(7).textContent = (corte.costo).toFixed(2); // Subtotal

                let accionCell = nuevaFila.insertCell(8);
                nuevaFila.insertCell(9).textContent = corte.id_movimiento; // Subtotal
                nuevaFila.insertCell(10).textContent = corte.id_rel_articulo;

                // 1. Botón de Editar
                let botonEditar = document.createElement("button");
                botonEditar.classList.add("btn", "btn-warning", "btn-round", "ms-2", "text-white", "px-3", "py-2");
                botonEditar.innerHTML = '<i class="fas fa-edit"></i>'; // Ícono de editar con texto

                // Agregar el botón de editar a la celda de acciones
                accionCell.appendChild(botonEditar);

                // 2. Botón de Eliminar
                let botonEliminar = document.createElement("button");
                botonEliminar.classList.add("btn", "btn-danger", "btn-round", "ms-2", "px-3", "py-2");
                botonEliminar.innerHTML = '<i class="fas fa-trash"></i>'; // Ícono de eliminar con texto

                accionCell.appendChild(botonEliminar);

                botonEditar.addEventListener("click", () => {
                    // Llenamos el modal con los datos del corte
                    document.getElementById("cantidad_solocorte").value = corte.minutos || 0; // Minutos corte
                    document.getElementById("precioSoloCorte").value = corte.tarifa || 1.5; // Precio corte

                    // Mostrar el modal
                    const modalElement = document.getElementById('modalSoloCorte');
                    const modal = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });

                    modal.show();

                    // El botón de agregar se convierte en "Actualizar" para modificar los valores
                    const btn_agregar = document.getElementById('btn_agregar_solocorte');
                    btn_agregar.textContent = 'Actualizar'; // Cambiar texto del botón
                    btn_agregar.removeEventListener("click", agregarDatosCorte);

                    // Actualizar el corte en la tabla cuando se presiona "Actualizar"
                    btn_agregar.addEventListener("click", function() {
                        corte.minutos = parseInt(document.getElementById("cantidad_solocorte").value) || 0;
                        corte.tarifa = parseFloat(document.getElementById("precioSoloCorte").value) || 1.5;
                        corte.costo = corte.minutos * corte.tarifa; // Recalcular el costo

                        // Actualizar las celdas de la fila con los nuevos valores
                        nuevaFila.cells[1].textContent = corte.minutos; // Minutos
                        nuevaFila.cells[2].textContent = corte.tarifa; // Costo x Minuto
                        nuevaFila.cells[3].textContent = corte.costo.toFixed(2); // Costo total

                        // Recalcular el subtotal
                        nuevaFila.cells[7].textContent = corte.costo.toFixed(2); // Subtotal

                        // Cerrar el modal
                        modal.hide();
                        fn_obtener_total(); // Recalcular los totales después de editar
                    });
                });

                // Función para manejar el botón de eliminar
                botonEliminar.addEventListener("click", () => {
                    const fila = botonEliminar.closest("tr");
                    fila.remove(); // Eliminar la fila
                    fn_obtener_total(); // Recalcular los totales después de eliminar
                });
            });

            fn_obtener_total();
        }

    });
</script>

<!--Agregar desde tabla Modal-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
            const botonesSumar = document.querySelectorAll('[id^="add_"]');
            const botonesRestar = document.querySelectorAll('[id^="rest_"]');

            botonesSumar.forEach(boton => {
                boton.addEventListener('click', function() {
                    const id = boton.id.split('_')[1];
                    const spanCantidad = document.getElementById(`cantidad_${id}`);
                    let cantidad = parseInt(spanCantidad.textContent);
                    cantidad++;
                    spanCantidad.textContent = cantidad;
                });
            });


            botonesRestar.forEach(boton => {
                boton.addEventListener('click', function() {
                    const id = boton.id.split('_')[1];
                    const spanCantidad = document.getElementById(`cantidad_${id}`);
                    let cantidad = parseInt(spanCantidad.textContent);
                    if (cantidad > 1) {
                        cantidad--;
                        spanCantidad.textContent = cantidad;
                    }
                });
            });
        });

        function fn_agregar_venta(datosArticulo) {
            //if (verificarSiArticuloExiste(datosArticulo['id'])) {
               // Swal.fire({
                //    icon: 'info',
                  //  title: '¡Artículo ya registrado!',
                    //text: 'Este artículo ya está en la tabla.',
                    //confirmButtonText: 'Aceptar'
                //});
            //} else {
                const modalCantidad = new bootstrap.Modal(document.getElementById('modalCantidad'));

                // Configurar el nombre del artículo
                const nombreArticulo = document.getElementById('nombreArticulo');
                nombreArticulo.textContent = `Artículo: ${datosArticulo.articulo || "Sin nombre"}`;

                // Resetear valores del modal
                const inputCantidad = document.getElementById('inputCantidad');
                const seccionCorte = document.getElementById('seccionCorte');
                const cantidadCorte = document.getElementById('cantidadCorte');
                const precioCorte = document.getElementById('precioCorte');

                inputCantidad.value = 1; // Cantidad por defecto
                cantidadCorte.value = 0; // Resetear minutos corte
                precioCorte.value = 0; // Precio por defecto

                // Mostrar u ocultar la sección de corte según datosArticulo.corte
                if (datosArticulo.corte) {
                    precioCorte.value = 1.5;
                    seccionCorte.style.display = 'block';
                } else {
                    seccionCorte.style.display = 'none';
                }

                // Configurar botones de cantidad
                document.getElementById('btnRestarCantidad').onclick = () => {
                    let cantidad = parseInt(inputCantidad.value, 10);
                    
                    // Restar si la cantidad es mayor a 1
                    if (cantidad > 1) {
                        inputCantidad.value = cantidad - 1;  // Restar cantidad
                    }

                    // Verificar si la cantidad es 1 y el artículo tiene corte
                    if (inputCantidad.value == 1 && datosArticulo.corte) {
                        precioCorte.value = 1.5;
                        seccionCorte.style.display = 'block';  // Mostrar sección de corte
                        
                    } else if (inputCantidad.value > 1) {
                        // Ocultar sección de corte si la cantidad es mayor a 1
                        precioCorte.value = 0;
                        seccionCorte.style.display = 'none';
                    }
                };

                document.getElementById('btnSumarCantidad').onclick = () => {
                    let cantidad = parseInt(inputCantidad.value, 10);
                    inputCantidad.value = cantidad + 1;
                    if (cantidad + 1 === 1 && datosArticulo.corte) {
                        precioCorte.value = 1.5;
                        seccionCorte.style.display = 'block';
                    } else {
                        precioCorte.value = 0;
                        seccionCorte.style.display = 'none';
                    }
                };

                // Configurar botones de corte
                document.getElementById('btnRestarCorte').onclick = () => {
                    let corte = parseInt(cantidadCorte.value, 10); // Cambié textContent por value
                    if (corte > 0) cantidadCorte.value = corte - 1; // Cambié textContent por value
                };

                document.getElementById('btnSumarCorte').onclick = () => {
                    let corte = parseInt(cantidadCorte.value, 10); // Cambié textContent por value
                    if(corte == 0){
                        cantidadCorte.value = 10; // Cambié textContent por value
                    } else {
                        cantidadCorte.value = corte + 1; // Cambié textContent por value
                    }
                };

                // Botones para modificar precio
                document.getElementById('btnIncremento05').onclick = () => {
                    precioCorte.value = (parseFloat(precioCorte.value) + 0.5).toFixed(2);
                };
                document.getElementById('btnIncremento1').onclick = () => {
                    precioCorte.value = (parseFloat(precioCorte.value) + 1).toFixed(2);
                };
                document.getElementById('btnIncremento2').onclick = () => {
                    precioCorte.value = (parseFloat(precioCorte.value) + 2).toFixed(2);
                };
                document.getElementById('btnIncremento5').onclick = () => {
                    precioCorte.value = (parseFloat(precioCorte.value) + 5).toFixed(2);
                };

                // Confirmar cantidad y agregar a la tabla
                document.getElementById('btnConfirmarCantidad').onclick = async () => {
                    try {
                        datosArticulo.cantidad = parseInt(inputCantidad.value, 10);
                        datosArticulo.minutos = parseInt(cantidadCorte.value, 10) || '-';
                        datosArticulo.costo_por_minuto = parseFloat(precioCorte.value, 10) || '-';
                        datosArticulo.id_movimiento = 1;

                        let venta_id = document.getElementById('idVentaReserva').textContent;

                        const response = await fn_adicionar_articulo(venta_id, datosArticulo);
                        console.log("Movimiento insertado con éxito:", response);
                        
                        modalCantidad.hide();
                        fn_agregar_articulo_tabla_Modal(datosArticulo);
                        
                    }catch (error) {
                        // Manejo de errores
                        console.error("Error al confirmar cantidad:", error);
                        alert("Ocurrió un error al agregar el artículo.");
                    }
                    
                };

                // Mostrar el modal
                modalCantidad.show();
            }
        //}





    function fn_agregar_articulo_tabla_Modal(datosArticulo) {
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];

        // Insertamos una nueva fila en la tabla
        let nuevaFila = tabla.insertRow();
        console.log(datosArticulo);
        // Colocamos los valores de las celdas
        nuevaFila.insertCell(0).textContent = datosArticulo["id"]; // ID
        nuevaFila.insertCell(1).textContent = datosArticulo["minutos"] || '-'; // Minutos
        nuevaFila.insertCell(2).textContent = datosArticulo["costo_por_minuto"] || '-'; // Costo x Minuto
        nuevaFila.insertCell(3).textContent = datosArticulo["costo_por_minuto"] * datosArticulo["minutos"] || '-'; // Costo x Minuto
        nuevaFila.insertCell(4).textContent = datosArticulo["articulo"]; // Artículo
        nuevaFila.insertCell(5).textContent = datosArticulo["cantidad"] || '-'; // Cantidad
        nuevaFila.insertCell(6).textContent = datosArticulo["precio_venta"]; // Precio unitario

        let totalCorte = (datosArticulo["costo_por_minuto"] * datosArticulo["minutos"]) || 0;
        // Cálculo base del subtotal: cantidad * precio de venta
        let subtotal = datosArticulo["cantidad"] * datosArticulo["precio_venta"];

        // Sumar el "total corte" al subtotal si existe
        subtotal += totalCorte;

        // Asignamos el subtotal a la celda 7
        nuevaFila.insertCell(7).textContent = subtotal.toFixed(2); // Subtotal con 2 decimales
        // Celda para acciones
        let accionCell = nuevaFila.insertCell(8);
        // 3. Botón de Corte (si aplica)
        
        // 1. Botón de Editar
        let botonEditar = document.createElement("button");
        botonEditar.classList.add("btn", "btn-warning", "btn-round", "ms-2", "text-white", "px-3", "py-2");
        botonEditar.innerHTML = '<i class="fas fa-edit"></i>'; // Ícono de editar con texto

        // Agregar el botón de editar a la celda de acciones
        accionCell.appendChild(botonEditar);
        nuevaFila.insertCell(9).textContent = datosArticulo["id_movimiento"]; // Precio unitario
        nuevaFila.insertCell(10).textContent = datosArticulo["id_rel_articulo"]; // Precio unitario

        // Función para manejar el botón de editar
        botonEditar.addEventListener("click", () => {
            // Abrir el modal con la cantidad actual, nombre del artículo, y datos adicionales de corte
            document.getElementById("nombreArticulo").textContent = datosArticulo["articulo"];
            document.getElementById("inputCantidad").value = datosArticulo["cantidad"];
            
            // Mostrar los valores actuales de corte si es que existen
            

            // Mostrar u ocultar la sección de corte según datosArticulo.corte (solo se muestra si corte es true)
            const seccionCorte = document.getElementById("seccionCorte");
            if (datosArticulo["corte"] && datosArticulo["cantidad"] == 1) {
                document.getElementById("cantidadCorte").value = 
                datosArticulo["minutos"] === '-' ? 0 : (datosArticulo["minutos"] || 0);

                document.getElementById("precioCorte").value = 
                    datosArticulo["costo_por_minuto"] === '-' ? 1.5 : (datosArticulo["costo_por_minuto"] || 1.5);
                seccionCorte.style.display = "block";
            } else {
                document.getElementById("cantidadCorte").value = 
                datosArticulo["minutos"] === '-' ? 0 : (datosArticulo["minutos"] || 0);
                seccionCorte.style.display = "none";
            }

            // Guardar el artículo actual para hacer la modificación posteriormente
            document.getElementById("btnConfirmarCantidad").onclick = function() {
                // Actualizamos la cantidad, minutos de corte y precio de corte en el objeto datosArticulo
                datosArticulo["cantidad"] = parseInt(document.getElementById("inputCantidad").value);
                datosArticulo["minutos"] = parseInt(document.getElementById("cantidadCorte").value) || '-';
                datosArticulo["costo_por_minuto"] = parseFloat(document.getElementById("precioCorte").value) || '-';

                // Actualizamos la celda de cantidad y subtotal en la tabla
                nuevaFila.cells[5].textContent = datosArticulo["cantidad"];
                nuevaFila.cells[1].textContent = datosArticulo["minutos"] || '-';
                nuevaFila.cells[2].textContent = datosArticulo["costo_por_minuto"] || '-';
                nuevaFila.cells[3].textContent = datosArticulo["minutos"] * datosArticulo["costo_por_minuto"] || '-';

                
                // Recalcular el subtotal considerando el precio de corte y minutos de corte
                let subtotal = datosArticulo["cantidad"] * datosArticulo["precio_venta"];
                subtotal += (datosArticulo["costo_por_minuto"] * datosArticulo["minutos"]) || 0;
                subtotal += (datosArticulo["minutosCorte"] * datosArticulo["precioCorte"]) || 0;  // Considerar precio de corte

                nuevaFila.cells[7].textContent = subtotal.toFixed(2);

                // Cerramos el modal
                $('#modalCantidad').modal('hide');
                fn_obtener_total(); // Recalcular los totales después de editar
            };

            // Mostrar el modal
            $('#modalCantidad').modal('show');
        });
        // 2. Botón de Eliminar
        let botonEliminar = document.createElement("button");
        botonEliminar.classList.add("btn", "btn-danger", "btn-round", "ms-2", "px-3", "py-2");
        botonEliminar.innerHTML = '<i class="fas fa-trash"></i>'; // Ícono de eliminar con texto

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



    function fn_limpiar_modal() {
        const acordeonContainer = document.getElementById('acordeonContainer');
        const globalContainer = document.getElementById('globalContainer');

        // Limpiar los contenedores donde se muestran los cortes y cantidades
        acordeonContainer.innerHTML = "";
        globalContainer.innerHTML = "";


    }


    


    function fn_obtener_total () {
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
        var filas = tabla.getElementsByTagName("tr");
        var totalCorte = 0;
        var totalArticulos = 0;
        var total = 0;

        for (var i = 0; i < filas.length; i++) {
            var celdas = filas[i].getElementsByTagName("td");
            totalCorte += parseFloat(celdas[3].innerText) || 0;
            totalArticulos += (parseFloat(celdas[5].innerText) * parseFloat(celdas[6].innerText)) || 0;
            total += parseFloat(celdas[7].innerText) || 0;
        }

        var lbl_subtotal_cortes = document.getElementById("id_subtotal_cortes");
        var lbl_subtotal_articulos= document.getElementById("id_subtotal_articulos");
        var lbl_subtotal_general = document.getElementById("id_subtotal_general");

        lbl_subtotal_cortes.innerText = totalCorte.toFixed(2);
        lbl_subtotal_articulos.innerText = totalArticulos.toFixed(2);
        lbl_subtotal_general.innerText = total.toFixed(2);


   

    }

    function verificarSiArticuloExiste(idArticulo) {
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
        var filas = tabla.getElementsByTagName("tr");

        for (var i = 0; i < filas.length; i++) {
            var celdas = filas[i].getElementsByTagName("td");
            var idFila = celdas[0].textContent; // Suponiendo que el ID está en la primera celda

            if (idFila == idArticulo) {
                return true; // Si se encuentra una coincidencia, retorna true
            }
        }
        return false; // Si no se encuentra ninguna coincidencia, retorna false
    }
    
</script>

<!--Cargar Datos tabla-->
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





    function fn_consultarVenta(datosArticulo) {
        // Limpiar Tabla
        var tabla = document.getElementById("tabla_articulos");
        var tbody = tabla.getElementsByTagName("tbody")[0];
        tbody.innerHTML = '';
        /////////////////////////////////////////////////////
        var panelDetalle = document.getElementById("panelDetalles");
        var panelAgregar = document.getElementById("panelAdicionarMas");
        panelDetalle.style.display = "block";
        panelAgregar.style.display = "block";
        ///////////////////// ///////////////////// ///////////////////// /////////////////////
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
            llenarDatosModal(
                datosArticulo['venta_id'],
                datosArticulo['id_persona'],
                datosArticulo['cliente'],
                datosArticulo['usuario_id'],
                datosArticulo['telefonomovil_cliente'],
                datosArticulo['email_cliente']
            );
            llenarDatosPanelCliente(
                datosArticulo['venta_id'],
                datosArticulo['cliente'],
                datosArticulo['fecha'],
                datosArticulo['hora'],
                datosArticulo['usuario'],
                datosArticulo['telefonomovil_cliente'],
                datosArticulo['email_cliente'],
                datosArticulo['numero_doc_cliente'],
            );

            console.log(Data);

            // Iterar sobre los datos devueltos (Data) y agregar los artículos a la tabla
            Data.forEach(item => {
                fn_agregar_articulo_tabla(item);
            });
        });
    }

    function fn_agregar_articulo_tabla(datosArticulo) {
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
        //tabla.innerHTML = "";

        // Insertamos una nueva fila en la tabla
        let nuevaFila = tabla.insertRow();

        // Colocamos los valores de las celdas
        nuevaFila.insertCell(0).textContent = datosArticulo["articulo_id"]; // ID
        nuevaFila.insertCell(1).textContent = datosArticulo["minutos"] || '-'; // Minutos
        nuevaFila.insertCell(2).textContent = datosArticulo["costo_por_minuto"] || '-'; // Costo x Minuto
        nuevaFila.insertCell(3).textContent = datosArticulo["costo_por_minuto"] * datosArticulo["minutos"] || '-'; // Costo x Minuto
        nuevaFila.insertCell(4).textContent = datosArticulo["articulo_nombre"]; // Artículo
        nuevaFila.insertCell(5).textContent = datosArticulo["cantidad"] || '-'; // Cantidad
        nuevaFila.insertCell(6).textContent = datosArticulo["precio_unitario_articulo"] || '-';; // Precio unitario
        nuevaFila.insertCell(7).textContent = parseFloat(datosArticulo["sub_total"]).toFixed(2); // Subtotal

        // Celda para acciones
        let accionCell = nuevaFila.insertCell(8);
        nuevaFila.insertCell(9).textContent = datosArticulo["movimiento_id"]; // Subtotal
        nuevaFila.insertCell(10).textContent = datosArticulo["rel_venta_articulo_id"]; // Precio unitario

        let botonEditar = document.createElement("button");
        botonEditar.classList.add("btn", "btn-warning", "btn-round", "ms-2", "text-white", "px-3", "py-2");
        botonEditar.innerHTML = '<i class="fas fa-edit"></i>'; // Ícono de editar con texto

        // Agregar el botón de editar a la celda de acciones
        accionCell.appendChild(botonEditar);

        botonEditar.addEventListener("click", () => {
            // Abrir el modal con la cantidad actual, nombre del artículo, y datos adicionales de corte
            document.getElementById("nombreArticulo").textContent = datosArticulo["articulo_nombre"];
            document.getElementById("inputCantidad").value = datosArticulo["cantidad"];
            
            // Mostrar los valores actuales de corte si es que existen
            document.getElementById('btnRestarCantidad').onclick = () => {
                    let cantidad = parseInt(inputCantidad.value, 10);
                    
                    // Restar si la cantidad es mayor a 1
                    if (cantidad > 1) {
                        inputCantidad.value = cantidad - 1;  // Restar cantidad
                    }

                    // Verificar si la cantidad es 1 y el artículo tiene corte
                    if (inputCantidad.value == 1 && datosArticulo.corte) {
                        precioCorte.value = 1.5;
                        seccionCorte.style.display = 'block';  // Mostrar sección de corte
                        
                    } else if (inputCantidad.value > 1) {
                        // Ocultar sección de corte si la cantidad es mayor a 1
                        precioCorte.value = 0;
                        seccionCorte.style.display = 'none';
                    }
                };

                document.getElementById('btnSumarCantidad').onclick = () => {
                    let cantidad = parseInt(inputCantidad.value, 10);
                    inputCantidad.value = cantidad + 1;
                    if (cantidad + 1 === 1 && datosArticulo.corte) {
                        precioCorte.value = 1.5;
                        seccionCorte.style.display = 'block';
                    } else {
                        precioCorte.value = 0;
                        seccionCorte.style.display = 'none';
                    }
                };

                // Configurar botones de corte
                document.getElementById('btnRestarCorte').onclick = () => {
                    let corte = parseInt(cantidadCorte.value, 10); // Cambié textContent por value
                    if (corte > 0) cantidadCorte.value = corte - 1; // Cambié textContent por value
                };

                document.getElementById('btnSumarCorte').onclick = () => {
                    let corte = parseInt(cantidadCorte.value, 10); // Cambié textContent por value
                    if(corte == 0){
                        cantidadCorte.value = 10; // Cambié textContent por value
                    } else {
                        cantidadCorte.value = corte + 1; // Cambié textContent por value
                    }
                };

                // Botones para modificar precio
                document.getElementById('btnIncremento05').onclick = () => {
                    precioCorte.value = (parseFloat(precioCorte.value) + 0.5).toFixed(2);
                };
                document.getElementById('btnIncremento1').onclick = () => {
                    precioCorte.value = (parseFloat(precioCorte.value) + 1).toFixed(2);
                };
                document.getElementById('btnIncremento2').onclick = () => {
                    precioCorte.value = (parseFloat(precioCorte.value) + 2).toFixed(2);
                };
                document.getElementById('btnIncremento5').onclick = () => {
                    precioCorte.value = (parseFloat(precioCorte.value) + 5).toFixed(2);
                };


            // Mostrar u ocultar la sección de corte según datosArticulo.corte (solo se muestra si corte es true)
            const seccionCorte = document.getElementById("seccionCorte");
            if (datosArticulo["corte"] && datosArticulo["cantidad"] == 1) {
                document.getElementById("cantidadCorte").value = 
                datosArticulo["minutos"] === '-' ? 0 : (datosArticulo["minutos"] || 0);

                document.getElementById("precioCorte").value = 
                    datosArticulo["costo_por_minuto"] === '-' ? 1.5 : (datosArticulo["costo_por_minuto"] || 1.5);
                seccionCorte.style.display = "block";
            } else {
                document.getElementById("cantidadCorte").value = 
                datosArticulo["minutos"] === '-' ? 0 : (datosArticulo["minutos"] || 0);
                document.getElementById("precioCorte").value = 
                datosArticulo["costo_por_minuto"] === '-' ? 0 : (datosArticulo["costo_por_minuto"] || 0);
                seccionCorte.style.display = "none";
            }

            // Guardar el artículo actual para hacer la modificación posteriormente
            document.getElementById("btnConfirmarCantidad").onclick = function() {
                // Actualizamos la cantidad, minutos de corte y precio de corte en el objeto datosArticulo
                datosArticulo["cantidad"] = parseInt(document.getElementById("inputCantidad").value);
                datosArticulo["minutos"] = parseInt(document.getElementById("cantidadCorte").value) || '-';
                datosArticulo["costo_por_minuto"] = parseFloat(document.getElementById("precioCorte").value) || '-';

                // Actualizamos la celda de cantidad y subtotal en la tabla
                nuevaFila.cells[5].textContent = datosArticulo["cantidad"];
                nuevaFila.cells[1].textContent = datosArticulo["minutos"] || '-';
                nuevaFila.cells[2].textContent = datosArticulo["costo_por_minuto"] || '-';
                nuevaFila.cells[3].textContent = datosArticulo["minutos"] * datosArticulo["costo_por_minuto"] || '-';

                
                // Recalcular el subtotal considerando el precio de corte y minutos de corte
                let subtotal = datosArticulo["cantidad"] * datosArticulo["precio_unitario_articulo"];
                subtotal += (datosArticulo["costo_por_minuto"] * datosArticulo["minutos"]) || 0;
                subtotal += (datosArticulo["minutosCorte"] * datosArticulo["precioCorte"]) || 0;  // Considerar precio de corte

                nuevaFila.cells[7].textContent = subtotal.toFixed(2);

                // Cerramos el modal
                $('#modalCantidad').modal('hide');
                fn_obtener_total(); // Recalcular los totales después de editar
            };

            // Mostrar el modal
            $('#modalCantidad').modal('show');
        });

        let botonEliminar = document.createElement("button");
        botonEliminar.classList.add("btn", "btn-danger", "btn-round", "ms-2", "px-3", "py-2");
        botonEliminar.innerHTML = '<i class="fas fa-trash"></i>'; // Ícono de eliminar con texto

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
</script>

<!--Ploteo-->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let ploteoEditando = null; // Variable para guardar el ploteo que se está editando
        document.getElementById('btnAbrirModalPloteo').addEventListener('click', function () {
            document.getElementById('modalGenericoLabel').textContent = 'Servicio de Ploteo';
            document.getElementById('modalContent').innerHTML = `
                <div class="text-center">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Servicio de Ploteo</h4>
                            <div>ID: <span id="id_mov_escaneo">4</span></div>
                            <div class="card-sub">Aquí ingresa lo que mandaron a Ploteo</div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Cantidad de Ploteo</p>
                            <div class="d-flex align-items-center justify-content-center">
                                <button id="btn_menos_ploteo" class="btn btn-danger btn-round me-2">-</button>
                                <input id="input_cantidad_ploteo" class="text-center" type="text" value="1" style="width: 40px;" oninput="validarNumero(event)">
                                <button id="btn_mas_ploteo" class="btn btn-success btn-round ms-2">+</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Monto (S/)</p>
                            <input type="number" id="monto_ploteo" class="form-control" placeholder="Monto (S/)">
                        </div>
                        <div class="text-center">
                            <button class="btn btn-secondary" id="btnAgregarPloteo" role="button">Añadir a la Venta</button>
                        </div>
                        <br>
                    </div>
                </div>
            `;

            // 1. Manejar Incremento y Decremento de Ploteos
            document.getElementById("btn_mas_ploteo").addEventListener("click", function () {
                let cantidad = parseInt(document.getElementById("input_cantidad_ploteo").value);
                document.getElementById("input_cantidad_ploteo").value = cantidad + 1;
            });

            document.getElementById("btn_menos_ploteo").addEventListener("click", function () {
                let cantidad = parseInt(document.getElementById("input_cantidad_ploteo").value);
                if (cantidad > 1) {
                    document.getElementById("input_cantidad_ploteo").value = cantidad - 1;
                }
            });

            // Funcionalidad para Añadir Escaneo a la Tabla
            document.getElementById('btnAgregarPloteo').addEventListener('click', async function () {
                const cantidadPloteos = parseInt(document.getElementById('input_cantidad_ploteo').value) || 1;
                const montoPloteo = parseFloat(document.getElementById('monto_ploteo').value) || 0;


                // Si no estamos editando, agregar un nuevo ploteo
                const datosPloteo = [{
                    id: '0', // ID del ploteo
                    cantidad: cantidadPloteos, // Cantidad de ploteos
                    monto: '-', // Monto
                    subtotal: montoPloteo, // Subtotal
                    articulo: 'PLOTEO',
                    idmovimiento: 2,
                }];

                const ploteo = datosPloteo[0];
                let venta_id = document.getElementById('idVentaReserva').textContent;

                try {
                    // Espera a que fn_insert_movimiento se complete
                    const response = await fn_insert_movimiento(venta_id, ploteo.idmovimiento, ploteo.cantidad, ploteo.subtotal);
                    console.log("Movimiento insertado con éxito:", response);

                    // Si tiene éxito, continúa con el resto del proceso
                    fn_ploteo_tabla(datosPloteo);
                    document.getElementById('input_cantidad_ploteo').value = 0; // Reset cantidad
                    document.getElementById('monto_ploteo').value = ''; // Reset monto
                        // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                    if (modal) modal.hide();
                } catch (error) {
                    // Manejar el error
                    console.error("Error al insertar movimiento:", error.message);
                    alert("Error al procesar el movimiento: " + error.message);
                }

               
            });

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
            modal.show();
        });
       

    

        // 3. Función para Agregar a la Tabla de Ploteos
        function fn_ploteo_tabla(datosPloteo) {
            var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];

            datosPloteo.forEach(ploteo => {
                let nuevaFila = tabla.insertRow();

                // Agregar celdas para los datos de ploteo
                nuevaFila.insertCell(0).textContent = ploteo.id; // ID
                nuevaFila.insertCell(1).textContent = '-'; // Cantidad de Ploteos
                nuevaFila.insertCell(2).textContent = '-'; // Monto unitario
                nuevaFila.insertCell(3).textContent = '-'; // Subtotal
                nuevaFila.insertCell(4).textContent = ploteo.articulo; // Artículo (Ploteo)
                nuevaFila.insertCell(5).textContent = ploteo.cantidad; // Se puede agregar más detalles si se requiere
                nuevaFila.insertCell(6).textContent = ploteo.monto; // Otro dato
                nuevaFila.insertCell(7).textContent = ploteo.subtotal.toFixed(2); // Subtotal (multiplied)

                let accionCell = nuevaFila.insertCell(8);
                nuevaFila.insertCell(9).textContent = ploteo.idmovimiento; // Subtotal (multiplied)
                nuevaFila.insertCell(10).textContent = ploteo.id_rel_articulo;

                // 1. Botón de Editar
                let botonEditar = document.createElement("button");
                botonEditar.classList.add("btn", "btn-warning", "btn-round", "ms-2", "text-white", "px-3", "py-2");
                botonEditar.innerHTML = '<i class="fas fa-edit"></i>';

                accionCell.appendChild(botonEditar);

                // 2. Botón de Eliminar
                let botonEliminar = document.createElement("button");
                botonEliminar.classList.add("btn", "btn-danger", "btn-round", "ms-2", "px-3", "py-2");
                botonEliminar.innerHTML = '<i class="fas fa-trash"></i>';

                accionCell.appendChild(botonEliminar);

                botonEditar.addEventListener("click", () => {

                    document.getElementById('modalGenericoLabel').textContent='Editar Ploteo';
                    document.getElementById('modalContent').innerHTML = `
                        <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Servicio de Ploteo</h4>
                            <div>ID: <span id="id_mov_escaneoEditar">${ploteo.idmovimiento}</span></div>
                            <div class="card-sub">Aquí ingresa lo que mandaron a Ploteo</div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Cantidad de Ploteo</p>
                            <div class="d-flex align-items-center justify-content-center">
                            <button id="btn_menos_ploteoEditar" class="btn btn-danger btn-round me-2">-</button>
                            <input id="input_cantidad_ploteoEditar" class="text-center" type="text" value="${ploteo.cantidad}" style="width: 40px;" oninput="validarNumero(event)">
                            <button id="btn_mas_ploteoEditar" class="btn btn-success btn-round ms-2">+</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Monto (S/)</p>
                            <input type="number" id="monto_ploteoeditar" class="form-control" value="${ploteo.subtotal}">
                        </div>
                        <div class="text-center">
                            <button class="btn btn-secondary" id="btnAgregarploteoEditar" role="button">Actualizar</button>
                        </div>
                        </div>
                    `;

                    document.getElementById('btn_menos_ploteoEditar').addEventListener('click', () => {
                        let cantidad = parseInt(document.getElementById('input_cantidad_ploteoEditar').value);
                        if (cantidad > 1) document.getElementById('input_cantidad_ploteoEditar').value = cantidad - 1;
                    });

                    document.getElementById('btn_mas_ploteoEditar').addEventListener('click', () => {
                        let cantidad = parseInt(document.getElementById('input_cantidad_ploteoEditar').value);
                        document.getElementById('input_cantidad_ploteoEditar').value = cantidad + 1;
                    });

                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
                    modal.show();
                    
                    // Rellenar los campos con los valores actuales del ploteo
                    document.getElementById("input_cantidad_ploteoEditar").value = ploteo.cantidad;
                    document.getElementById("monto_ploteoeditar").value = ploteo.subtotal;


                    // Guardar la referencia a la fila del ploteo
                    ploteo.fila = nuevaFila;  // Guardar referencia a la fila
                    ploteoEditando = ploteo;  // Guardar referencia al ploteo que se está editando


                    document.getElementById('btnAgregarploteoEditar').addEventListener('click', function () {
                        const cantidadPloteos = parseInt(document.getElementById('input_cantidad_ploteoEditar').value) || 1;
                        const montoPloteo = parseFloat(document.getElementById('monto_ploteoeditar').value) || 0;
                        // Actualizamos los valores de la fila existente
                        ploteoEditando.cantidad = cantidadPloteos;
                        ploteoEditando.subtotal = montoPloteo;

                        // Actualizamos la fila de la tabla
                        ploteoEditando.fila.cells[5].textContent = ploteoEditando.cantidad; // Cantidad
                        ploteoEditando.fila.cells[7].textContent = ploteoEditando.subtotal.toFixed(2); // Subtotal

                        // Limpiar los campos
                        document.getElementById('input_cantidad_ploteoEditar').value = 0;
                        document.getElementById('monto_ploteoeditar').value = 0;

                        // Resetear el botón y quitar la referencia al ploteo editado
                        ploteoEditando = null; // Reiniciar la referencia
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                        if (modal) modal.hide(); // Cierra el modal si existe
                    });

                });

                // Función de eliminar
                botonEliminar.addEventListener("click", () => {
                    const fila = botonEliminar.closest("tr");
                    fila.remove();
                    fn_obtener_total(); // Recalcular los totales
                });
            });

            fn_obtener_total(); // Recalcular totales
        }
    });




</script>

<!--Impresion-->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let impresionEditando = null; // Variable para guardar la impresión que se está editando

        document.getElementById('btnAbrirModalImprimir').addEventListener('click', function () {
            document.getElementById('modalGenericoLabel').textContent = 'Servicio de Impresión';
            document.getElementById('modalContent').innerHTML = `
                <div class="text-center">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Servicio de Impresión</h4>
                            <div>ID: <span id="id_mov_escaneo">4</span></div>
                            <div class="card-sub">Aquí ingresa lo que mandaron a Imprimir</div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Cantidad a Imprimir</p>
                            <div class="d-flex align-items-center justify-content-center">
                                <button id="btn_menos_impresion" class="btn btn-danger btn-round me-2">-</button>
                                <input id="input_numero_impresion" class="text-center" type="text" value="1" style="width: 40px;" oninput="validarNumero(event)">
                                <button id="btn_mas_impresion" class="btn btn-success btn-round ms-2">+</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Monto (S/)</p>
                            <input type="number" id="monto_impresion" class="form-control" placeholder="Monto (S/)">
                        </div>
                        <div class="text-center">
                            <button class="btn btn-secondary" id="btnAgregarImpresion" role="button">Añadir a la Venta</button>
                        </div>
                        <br>
                    </div>
                </div>
            `;

            // 1. Manejar Incremento y Decremento de Impresiones
            document.getElementById("btn_mas_impresion").addEventListener("click", function () {
                let cantidad = parseInt(document.getElementById("input_numero_impresion").value);
                document.getElementById("input_numero_impresion").value = cantidad + 1;
            });

            document.getElementById("btn_menos_impresion").addEventListener("click", function () {
                let cantidad = parseInt(document.getElementById("input_numero_impresion").value);
                if (cantidad > 1) {
                    document.getElementById("input_numero_impresion").value = cantidad - 1;
                }
            });

            // Funcionalidad para Añadir Escaneo a la Tabla
            document.getElementById('btnAgregarImpresion').addEventListener('click',async function () {
                const cantidadImpresiones = parseInt(document.getElementById('input_numero_impresion').value) || 1;
                const montoImpresion = parseFloat(document.getElementById('monto_impresion').value) || 0;
                // Si no estamos editando, agregar una nueva impresión
                const datosImpresion = [{
                    id: '0', // ID de la impresión
                    cantidad: cantidadImpresiones, // Cantidad de impresiones
                    monto: '-', // Monto
                    subtotal: montoImpresion, // Subtotal
                    articulo: 'IMPRESIÓN',
                    idmovimiento: 3, // ID movimiento para impresión
                }];


                const impresion = datosImpresion[0];
                let venta_id = document.getElementById('idVentaReserva').textContent;

                try {
                    // Espera a que fn_insert_movimiento se complete
                    const response = await fn_insert_movimiento(venta_id, impresion.idmovimiento, impresion.cantidad, impresion.subtotal);
                    console.log("Movimiento insertado con éxito:", response);

                    // Si tiene éxito, continúa con el resto del proceso
                    fn_impresion_tabla(datosImpresion);

                    document.getElementById('input_numero_impresion').value = 1; // Reset cantidad
                    document.getElementById('monto_impresion').value = ''; // Reset monto

                    // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                    if (modal) modal.hide();
                } catch (error) {
                    // Manejar el error
                    console.error("Error al insertar movimiento:", error.message);
                    alert("Error al procesar el movimiento: " + error.message);
                }


              
            });

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
            modal.show();
        });

        // 3. Función para Agregar a la Tabla de Impresiones
        function fn_impresion_tabla(datosImpresion) {
            var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];

            datosImpresion.forEach(impresion => {
                let nuevaFila = tabla.insertRow();

                // Agregar celdas para los datos de la impresión
                nuevaFila.insertCell(0).textContent = impresion.id; // ID
                nuevaFila.insertCell(1).textContent = '-'; // Cantidad de Impresiones
                nuevaFila.insertCell(2).textContent = '-'; // Monto unitario
                nuevaFila.insertCell(3).textContent = '-'; // Subtotal
                nuevaFila.insertCell(4).textContent = impresion.articulo; // Artículo (Impresión)
                nuevaFila.insertCell(5).textContent = impresion.cantidad; // Cantidad
                nuevaFila.insertCell(6).textContent = impresion.monto; // Monto
                nuevaFila.insertCell(7).textContent = impresion.subtotal.toFixed(2); // Subtotal

                let accionCell = nuevaFila.insertCell(8);
                nuevaFila.insertCell(9).textContent = impresion.idmovimiento; // ID de movimiento
                nuevaFila.insertCell(10).textContent = impresion.id_rel_articulo;

                // 1. Botón de Editar
                let botonEditar = document.createElement("button");
                botonEditar.classList.add("btn", "btn-warning", "btn-round", "ms-2", "text-white", "px-3", "py-2");
                botonEditar.innerHTML = '<i class="fas fa-edit"></i>';

                accionCell.appendChild(botonEditar);

                // 2. Botón de Eliminar
                let botonEliminar = document.createElement("button");
                botonEliminar.classList.add("btn", "btn-danger", "btn-round", "ms-2", "px-3", "py-2");
                botonEliminar.innerHTML = '<i class="fas fa-trash"></i>';

                accionCell.appendChild(botonEliminar);

                botonEditar.addEventListener("click", () => {             
                    document.getElementById('modalGenericoLabel').textContent='Editar Impresión';
                    document.getElementById('modalContent').innerHTML = `
                        <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Servicio de Impresión</h4>
                            <div>ID: <span id="id_mov_escaneoEditar">${impresion.idmovimiento}</span></div>
                            <div class="card-sub">Aquí ingresa lo que mandaron a Imprimir</div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Cantidad a Imprimir</p>
                            <div class="d-flex align-items-center justify-content-center">
                            <button id="btn_menos_impresionEditar" class="btn btn-danger btn-round me-2">-</button>
                            <input id="input_numero_impresionEditar" class="text-center" type="text" value="${impresion.cantidad}" style="width: 40px;" oninput="validarNumero(event)">
                            <button id="btn_mas_impresionEditar" class="btn btn-success btn-round ms-2">+</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Monto (S/)</p>
                            <input type="number" id="monto_impresionEditar" class="form-control" value="${impresion.subtotal}">
                        </div>
                        <div class="text-center">
                            <button class="btn btn-secondary" id="btnAgregarimpresionEditar" role="button">Actualizar</button>
                        </div>
                        </div>
                    `;

                    document.getElementById('btn_menos_impresionEditar').addEventListener('click', () => {
                        let cantidad = parseInt(document.getElementById('input_numero_impresionEditar').value);
                        if (cantidad > 1) document.getElementById('input_numero_impresionEditar').value = cantidad - 1;
                    });

                    document.getElementById('btn_mas_impresionEditar').addEventListener('click', () => {
                        let cantidad = parseInt(document.getElementById('input_numero_impresionEditar').value);
                        document.getElementById('input_numero_impresionEditar').value = cantidad + 1;
                    });

                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
                    modal.show();
                    // Rellenar los campos con los valores actuales de la impresión
                    document.getElementById("input_numero_impresionEditar").value = impresion.cantidad;
                    document.getElementById("monto_impresionEditar").value = impresion.subtotal;

                    // Guardar la referencia a la fila de la impresión
                    impresion.fila = nuevaFila;  // Guardar referencia a la fila
                    impresionEditando = impresion;  // Guardar referencia a la impresión que se está editando

                    document.getElementById('btnAgregarimpresionEditar').addEventListener('click', function () {
                        const cantidadImpresion = parseInt(document.getElementById('input_numero_impresionEditar').value) || 1;
                        const montoImpresion = parseFloat(document.getElementById('monto_impresionEditar').value) || 0;

                        // Actualizamos los valores de la fila existente
                        impresionEditando.cantidad = cantidadImpresion;
                        impresionEditando.subtotal = montoImpresion;

                        // Actualizamos la fila de la tabla
                        impresionEditando.fila.cells[5].textContent = impresionEditando.cantidad; // Cantidad
                        impresionEditando.fila.cells[7].textContent = impresionEditando.subtotal.toFixed(2); // Subtotal

                        // Limpiar los campos
                        document.getElementById('input_numero_impresionEditar').value = 1; // Reset cantidad
                        document.getElementById('monto_impresionEditar').value = 0; // Reset monto

                        impresionEditando = null; // Reiniciar la referencia
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                        if (modal) modal.hide(); // Cierra el modal si existe
                    });

                });

                // Función de eliminar
                botonEliminar.addEventListener("click", () => {
                    const fila = botonEliminar.closest("tr");
                    fila.remove();
                    fn_obtener_total(); // Recalcular los totales
                });
            });

            fn_obtener_total(); // Recalcular totales
        }
    });

    
</script>

<!--Escaneo-->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let escaneoEditando = null; // Variable para guardar el escaneo que se está editando

         // Abrir modal con el contenido específico para Escaneo
         document.getElementById('btnAbrirModalEscaneo').addEventListener('click', function () {
            document.getElementById('modalGenericoLabel').textContent = 'Servicio de Escaneo';
            document.getElementById('modalContent').innerHTML = `
                <div class="text-center">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Servicio de Escaneo</h4>
                            <div>ID: <span id="id_mov_escaneo">4</span></div>
                            <div class="card-sub">Aquí ingresa lo que mandaron a Escanear</div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Cantidad de Escaneo</p>
                            <div class="d-flex align-items-center justify-content-center">
                                <button id="btn_menos_escaneo" class="btn btn-danger btn-round me-2">-</button>
                                <input id="input_numero_escaneo" class="text-center" type="text" value="1" style="width: 40px;" oninput="validarNumero(event)">
                                <button id="btn_mas_escaneo" class="btn btn-success btn-round ms-2">+</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Monto (S/)</p>
                            <input type="number" id="monto_escaneo" class="form-control" placeholder="Monto (S/)">
                        </div>
                        <div class="text-center">
                            <button class="btn btn-secondary" id="btnAgregarescaneo" role="button">Añadir a la Venta</button>
                        </div>
                        <br>
                    </div>
                </div>
            `;

            // Funcionalidades de Incremento y Decremento
            document.getElementById("btn_mas_escaneo").addEventListener("click", function () {
                let cantidad = parseInt(document.getElementById("input_numero_escaneo").value);
                document.getElementById("input_numero_escaneo").value = cantidad + 1;
            });

            document.getElementById("btn_menos_escaneo").addEventListener("click", function () {
                let cantidad = parseInt(document.getElementById("input_numero_escaneo").value);
                if (cantidad > 1) {
                    document.getElementById("input_numero_escaneo").value = cantidad - 1;
                }
            });

            // Funcionalidad para Añadir Escaneo a la Tabla
            document.getElementById('btnAgregarescaneo').addEventListener('click',async  function () {
                const cantidadEscaneos = parseInt(document.getElementById('input_numero_escaneo').value) || 1;
                const montoEscaneo = parseFloat(document.getElementById('monto_escaneo').value) || 0;

                const datosEscaneo = [{
                    id: '0', // ID del escaneo
                    cantidad: cantidadEscaneos,
                    monto: '-', // Monto unitario
                    subtotal: montoEscaneo,
                    articulo: 'ESCANEO',
                    idmovimiento: 5, // ID movimiento
                }];
                const movimiento = datosEscaneo[0];
                let venta_id = document.getElementById('idVentaReserva').textContent;

                try {
                    // Espera a que fn_insert_movimiento se complete
                    const response = await fn_insert_movimiento(venta_id, movimiento.idmovimiento, movimiento.cantidad, movimiento.subtotal);
                    console.log("Movimiento insertado con éxito:", response);

                    // Si tiene éxito, continúa con el resto del proceso
                    fn_escaneo_tabla(datosEscaneo);

                    document.getElementById('input_numero_escaneo').value = 1;
                    document.getElementById('monto_escaneo').value = '';

                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                    if (modal) modal.hide();
                } catch (error) {
                    // Manejar el error
                    console.error("Error al insertar movimiento:", error.message);
                    alert("Error al procesar el movimiento: " + error.message);
                }

            });

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
            modal.show();
        });

        
        // 3. Función para Agregar a la Tabla de Escaneos
        function fn_escaneo_tabla(datosEscaneo) {
            var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];

            datosEscaneo.forEach(escaneo => {
                let nuevaFila = tabla.insertRow();

                // Agregar celdas para los datos del escaneo
                nuevaFila.insertCell(0).textContent = escaneo.id; // ID
                nuevaFila.insertCell(1).textContent = '-'; // Cantidad de Escaneos
                nuevaFila.insertCell(2).textContent = '-'; // Monto unitario
                nuevaFila.insertCell(3).textContent = '-'; // Subtotal
                nuevaFila.insertCell(4).textContent = escaneo.articulo; // Artículo (Escaneo)
                nuevaFila.insertCell(5).textContent = escaneo.cantidad; // Cantidad
                nuevaFila.insertCell(6).textContent = escaneo.monto; // Monto
                nuevaFila.insertCell(7).textContent = escaneo.subtotal.toFixed(2); // Subtotal

                let accionCell = nuevaFila.insertCell(8);
                nuevaFila.insertCell(9).textContent = escaneo.idmovimiento; // ID de movimiento
                nuevaFila.insertCell(10).textContent = escaneo.id_rel_articulo;

                // 1. Botón de Editar
                let botonEditar = document.createElement("button");
                botonEditar.classList.add("btn", "btn-warning", "btn-round", "ms-2", "text-white", "px-3", "py-2");
                botonEditar.innerHTML = '<i class="fas fa-edit"></i>';

                accionCell.appendChild(botonEditar);

                // 2. Botón de Eliminar
                let botonEliminar = document.createElement("button");
                botonEliminar.classList.add("btn", "btn-danger", "btn-round", "ms-2", "px-3", "py-2");
                botonEliminar.innerHTML = '<i class="fas fa-trash"></i>';

                accionCell.appendChild(botonEliminar);

                botonEditar.addEventListener("click", () => {
                    
                    document.getElementById('modalGenericoLabel').textContent='Editar Escaneo';
                    document.getElementById('modalContent').innerHTML = `
                        <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Servicio de Escaneo</h4>
                            <div>ID: <span id="id_mov_escaneoEditar">${escaneo.idmovimiento}</span></div>
                            <div class="card-sub">Aquí ingresa lo que mandaron a Escanear</div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Cantidad de Escaneo</p>
                            <div class="d-flex align-items-center justify-content-center">
                            <button id="btn_menos_escaneoEditar" class="btn btn-danger btn-round me-2">-</button>
                            <input id="input_numero_escaneoEditar" class="text-center" type="text" value="${escaneo.cantidad}" style="width: 40px;" oninput="validarNumero(event)">
                            <button id="btn_mas_escaneoEditar" class="btn btn-success btn-round ms-2">+</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Monto (S/)</p>
                            <input type="number" id="monto_escaneoEditar" class="form-control" value="${escaneo.subtotal}">
                        </div>
                        <div class="text-center">
                            <button class="btn btn-secondary" id="btnAgregarescaneoEditar" role="button">Actualizar</button>
                        </div>
                        </div>
                    `;
                    document.getElementById('btn_menos_escaneoEditar').addEventListener('click', () => {
                        let cantidad = parseInt(document.getElementById('input_numero_escaneoEditar').value);
                        if (cantidad > 1) document.getElementById('input_numero_escaneoEditar').value = cantidad - 1;
                    });

                    document.getElementById('btn_mas_escaneoEditar').addEventListener('click', () => {
                        let cantidad = parseInt(document.getElementById('input_numero_escaneoEditar').value);
                        document.getElementById('input_numero_escaneoEditar').value = cantidad + 1;
                    });

                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
                    modal.show();
                    // Rellenar los campos con los valores actuales del escaneo
                    document.getElementById("input_numero_escaneoEditar").value = escaneo.cantidad;
                    document.getElementById("monto_escaneoEditar").value = escaneo.subtotal;

               


                    // Guardar la referencia a la fila del escaneo
                    escaneo.fila = nuevaFila;  // Guardar referencia a la fila
                    escaneoEditando = escaneo;  // Guardar referencia al escaneo que se está editando
                    
                    document.getElementById('btnAgregarescaneoEditar').addEventListener('click', function () {
                        const cantidadEscaneos = parseInt(document.getElementById('input_numero_escaneoEditar').value) || 1;
                        const montoEscaneo = parseFloat(document.getElementById('monto_escaneoEditar').value) || 0;

                        // Actualizamos los valores de la fila existente
                        escaneoEditando.cantidad = cantidadEscaneos;
                        escaneoEditando.subtotal = montoEscaneo;

                        // Actualizamos la fila de la tabla
                        escaneoEditando.fila.cells[5].textContent = escaneoEditando.cantidad; // Cantidad
                        escaneoEditando.fila.cells[7].textContent = escaneoEditando.subtotal.toFixed(2); // Subtotal

                        // Limpiar los campos
                        document.getElementById('input_numero_escaneoEditar').value = 1; // Reset cantidad
                        document.getElementById('monto_escaneoEditar').value = 0; // Reset monto

                        escaneoEditando = null; // Reiniciar la referencia
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                        if (modal) modal.hide(); // Cierra el modal si existe
                    });
                });

                // Función de eliminar
                botonEliminar.addEventListener("click", () => {
                    const fila = botonEliminar.closest("tr");
                    fila.remove();
                    fn_obtener_total(); // Recalcular los totales
                });
            });

            fn_obtener_total(); // Recalcular totales
        }
    });


</script>

<!--Fn Insert, Update y Delete-->
<script>

    function fn_insert_movimiento(venta_id, movimiento_id, cantidad, sub_total) {
            return new Promise((resolve, reject) => {
                try {
                    const datos = {
                        "venta_id": venta_id,
                        "movimiento_id": movimiento_id,
                        "cantidad": cantidad,
                        "sub_total": sub_total
                    };

                    $.ajax({
                        method: "POST",
                        url: "logica/clssVentaCorte.php",
                        data: {
                            "accion": "INSERTMOVIMIENTO",
                            "data": JSON.stringify(datos)
                        }
                    })
                    .done(function (response) {
                        const jsonResponse = JSON.parse(response); // Si el servidor devuelve un JSON
                        if (jsonResponse.success) {
                            resolve(jsonResponse); // Éxito: Resuelve la promesa
                        } else {
                            reject(new Error(jsonResponse.mensaje || "Error desconocido")); // Error del servidor
                        }
                    })
                    .fail(function (error) {
                        reject(new Error(error.responseText || "Error en la solicitud AJAX")); // Error en la solicitud
                    });
                } catch (error) {
                    reject(new Error(error.message)); // Error inesperado
                }
        });
    }

    function fn_adicionar_articulo(venta_id, datosArticulo) {
        return new Promise((resolve, reject) => {
            const datos = {
                "venta_id": venta_id,  // Puedes cambiar este valor dinámicamente si es necesario
                "articulo_id": datosArticulo['id'],  // También este valor puede ser dinámico
                "cantidad": datosArticulo['cantidad'] ,
                "sub_total": calcularSubTotal(datosArticulo),
                "minutos": datosArticulo['minutos'],
                "precio_unitario": datosArticulo['precio_venta'] ,
                "costoxminuto": datosArticulo['costo_por_minuto'],
                "movimiento_id": datosArticulo['id_movimiento'],

            };

            console.log(datos);
            $.ajax({
                method: "POST",
                url: "logica/clssVentaCorte.php",
                data: {
                    "accion": "ADICIONARARTICULO",
                    "data": JSON.stringify(datos)
                }
            }).done(function (response) {
                console.log(response);
                // Resolvemos la promesa con la respuesta de la solicitud
                resolve(response);
            }).fail(function (error) {
                console.error("Error:", error.responseText);
                // Rechazamos la promesa si hay un error
                reject(error);
            });
        });
    }

    function calcularSubTotal(datosArticulo) {
        let cantidad = datosArticulo['cantidad'] === '-' || datosArticulo['cantidad'] === null ? 0 : parseInt(datosArticulo['cantidad']);;
        let precio_venta = datosArticulo['precio_venta'] === '-' || datosArticulo['precio_venta'] === null ? 0 : parseFloat(datosArticulo['precio_venta']);
        let minutos = datosArticulo['minutos'] === '-' || datosArticulo['minutos'] === null ? 0 : parseInt(datosArticulo['minutos']);
        let costo_por_minuto = datosArticulo['costo_por_minuto'] === '-' || datosArticulo['costo_por_minuto'] === null ? 0 : parseFloat(datosArticulo['costo_por_minuto']);
        console.log(cantidad);
        console.log(precio_venta);

        console.log(minutos);

        console.log(costo_por_minuto);


        // Calcular subtotal: cantidad * precio_venta + minutos * costo_por_minuto
        return (cantidad * precio_venta) + (minutos * costo_por_minuto);
    }

    function fn_eliminar_articulo(id_rel_articulo) {
        return new Promise((resolve, reject) => {
            $.ajax({
                method: "POST",
                url: "logica/clssVentaCorte.php",
                data: {
                    "accion": "ELIMINARARTICULO", // Acción que se realizará en PHP
                    "id_rel_articulo": id_rel_articulo
                }
            }).done(function (response) {
                console.log(response);
                // Resolvemos la promesa con la respuesta de la solicitud
                resolve(response);
            }).fail(function (error) {
                console.error("Error:", error.responseText);
                // Rechazamos la promesa si hay un error
                reject(error);
            });
        });
    }

    function fn_eliminar_movimiento(id_rel_articulo) {
        return new Promise((resolve, reject) => {
            $.ajax({
                method: "POST",
                url: "logica/clssVentaCorte.php",
                data: {
                    "accion": "ELIMINARMOVIMIENTO", // Acción que se realizará en PHP
                    "id_rel_articulo": id_rel_articulo
                }
            }).done(function (response) {
                console.log(response);
                // Resolvemos la promesa con la respuesta de la solicitud
                resolve(response);
            }).fail(function (error) {
                console.error("Error:", error.responseText);
                // Rechazamos la promesa si hay un error
                reject(error);
            });
        });
    }

</script>

<script>
    document.getElementById("btnRealizarPago").addEventListener("click", function() {


        // Mostrar el modal manualmente
        const modal = new bootstrap.Modal(document.getElementById("modalRealizarPago"));
        modal.show();

        const subtotalGeneral = document.getElementById("id_subtotal_general").textContent;
        document.getElementById("montoTotal").value = subtotalGeneral; // Asignar el monto total
        document.getElementById("idMontoVentaTitulo").textContent = subtotalGeneral;

    });

    function llenarDatosModal(idVenta, idPersona, nombreCliente, idUsuario, numeroCelular, email) {
        // Actualizamos el contenido del modal con los datos proporcionados
        document.getElementById("idUsuario").textContent = idUsuario; // Para el ID de la venta
        document.getElementById("idVenta").textContent = idVenta; // Para el ID de la venta
        document.getElementById("idPersona").textContent = idPersona; // Para el ID del cliente
        document.getElementById("nombreCliente").value = nombreCliente; // Para el nombre del cliente

        document.getElementById("idUpdateNumTelefonoCliente").value = numeroCelular;
        document.getElementById("idUpdateCorreoCliente").value = email;

    }

    function llenarDatosPanelCliente(idVenta,cliente, fechaReserva, horaReserva, usuario, telefonomovil, email, numeroDoc) {
        // Actualizamos el contenido del modal con los datos proporcionados
        document.getElementById("idVentaReserva").textContent = idVenta;

        document.getElementById("idClienteReservaDetalle").textContent = cliente;

        document.getElementById("idNumCelClienteReserva").textContent = telefonomovil;
        document.getElementById("idCorreoClienteReserva").textContent = email;
        document.getElementById("idNumDocClienteReserva").textContent = numeroDoc;


        document.getElementById("idClienteReservaDetalle").textContent = cliente;
        document.getElementById("idFechaReservaDetalle").value = fechaReserva;
        document.getElementById("idHoraReservaDetalle").value = horaReserva;
        document.getElementById("idUsuarioReservaDetalle").textContent = usuario;
    }
</script>


<?php
include("pie.php");
?>