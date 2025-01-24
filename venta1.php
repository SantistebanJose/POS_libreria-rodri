<?php
include("cabecera.php");
include("logica/clssVenta.php");
session_start(); // O el nombre de la variable que contiene el ID


if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

?>
<style>
    #sugerencias {
        max-height: 200px;
        overflow-y: auto;
        z-index: 1050; /* Para asegurar que esté sobre otros elementos */
    }

    #sugerencias .list-group-item {
        cursor: pointer;
    }
    #tabla_articulos th:nth-child(1),
#tabla_articulos td:nth-child(1) {
    display: none;
}
</style>

<div
    class="container">
    <div class="page-inner">
        <div
            class="card"
        ">

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
                            <button type="button" class="btn btn-success" onclick="agregarCorte()" >Solo Corte</button>
                        </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table
                                        id="multi-filter-select"
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

                    <!-- 
                        <label for="" class="form-label">Movimiento</label>
                        <select
                        class="form-select form-select-md"
                        name=""
                        id="">
                        <option selected>Seleccione</option>
                        
                        <?php
                        /**
                         foreach (listarMovimientos2() as $movimiento): ?>
                            <option value="<?php echo htmlspecialchars($movimiento['id']); ?>">
                                <?php echo htmlspecialchars($movimiento['descripcion']); ?>
                            </option>
                        <?php endforeach  
                         */
                        ?>

                    </select>
                    -->

                </div>
                <div
                    class="card"
                ">
                </div>





<!-- Modal Solo Corte -->
<div class="modal fade" id="modalSoloCorte" tabindex="-1" aria-labelledby="modalSoloCorteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSoloCorteLabel">Corte de Minutos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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







<!-- Modal -->
    <div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="miModalLabel">Agregar Corte de Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_no">No</button>
            <button type="button" class="btn btn-primary" id="btn_si">Sí</button>
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
                            <div class="card-header">
                                <div class="card-title">Detalle Materiales / Corte</div>
                            </div>
                            <div class="card-body" > 
                                <div class="card-sub">
                                    Aquí la venta de los materiales
                                </div>
                                <div class="table-responsive">
                                <table id="tabla_articulos" class="table mt-3">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">MINUTOS</th>
                                            <th scope="col">Tarifa</th>
                                            <th scope="col">Total Corte</th>
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
                    </div>
                </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body text-center">
                        <h5 id="label_total_cortes" class="card-title">Total Cortes S/:</h5>
                        <span id="id_subtotal_cortes" style="font-size: 1.3rem;" aria-labelledby="label_total_cortes">xx.xx</span>
                    </div>
                </div>  
            </div>
            <div class="col-md-3">
                    <div class="card card-stats card-round">
                    <div class="card-body text-center">
                        <h5 id="label_total_articulos" class="card-title">Total Artículos S/:</h5>
                        <span id="id_subtotal_articulos" style="font-size: 1.3rem;"  aria-labelledby="label_total_articulos">xx.xx</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                    <div class="card card-primary card-stats card-round">
                    <div class="card-body text-center">
                        <h5 id="label_total_general" class="card-title">Total S/:</h5>
                        <span id="id_subtotal_general" style="font-size: 1.3rem;" aria-labelledby="label_total_general">xx.xx</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <button id="btnRealizarReserva" type="button" class="btn btn-success btn-block card card-stats card-round">
                    <div class="card-body text-center">
                        <h5 id="label_total_general" class="card-title">Realizar Reserva</h5>
                    </div>
                </button>
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
                            <h4 class="card-title">Realizar Reserva</h4>
                        </div>
                       <!--<div class="card-body text-center">
                            <h1 class="card-title">S/ xx.xx</h1>
                        </div>-->

                        <div class="card-sub">
                            Aquí realiza tus pagos
                        </div>
                        <div>
                            <span>ID Cliente: <span id="idPersona">#</span></span>
                        </div>
                        <hr>
                        <div class="mb-3 position-relative">
                            <label for="nombreCliente" class="form-label">Cliente</label>
                            <input
                                type="text"
                                class="form-control"
                                id="nombreCliente"
                                placeholder="AGREGAR EL NOMBRE DEL CLIENTE O DNI" />
                            <!-- Contenedor para las sugerencias -->
                            <div id="sugerencias" class="list-group position-absolute w-100"></div>
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

                      
                        <div class="text-center">
                            <a class="btn btn-success" id="Reservar" role="button">Reservar</a>
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
     document.addEventListener('DOMContentLoaded', function() {
         // Incremento de minutos
        document.getElementById('btn_mas_solocorte').addEventListener('click', function() {
            let cantidad = parseInt(document.getElementById('cantidad_solocorte').textContent);
            if(cantidad >0){
                cantidad++;
                document.getElementById('cantidad_solocorte').textContent = cantidad;
            }else{
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

     })
   

</script>

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
                document.getElementById('btnConfirmarCantidad').onclick = () => {
                    datosArticulo.cantidad = parseInt(inputCantidad.value, 10);
                    datosArticulo.minutos = parseInt(cantidadCorte.value, 10) || '-';
                    datosArticulo.costo_por_minuto = parseFloat(precioCorte.value, 10) || '-';

                    modalCantidad.hide();
                    fn_agregar_articulo_tabla(datosArticulo);
                };

                // Mostrar el modal
                modalCantidad.show();
            }
        //}





    function fn_agregar_articulo_tabla(datosArticulo) {
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
        nuevaFila.insertCell(5).textContent = datosArticulo["cantidad"]; // Cantidad
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

<script>
    document.getElementById("btnRealizarReserva").addEventListener("click", function () {
  

        // Mostrar el modal manualmente
        const modal = new bootstrap.Modal(document.getElementById("modalRealizarPago"));
        modal.show();

        const subtotalGeneral = document.getElementById("id_subtotal_general").textContent;
        document.getElementById("montoTotal").value = subtotalGeneral; // Asignar el monto total

    });

    document.addEventListener("DOMContentLoaded", function () {
        const nombreCliente = document.getElementById("nombreCliente");
        const sugerencias = document.getElementById("sugerencias");
        const persona_id = document.getElementById("idPersona");
        nombreCliente.addEventListener("input", function () {
            const query = nombreCliente.value.trim();
            console.log(query)
            if (query.length > 0) {
                // Realiza la solicitud AJAX con jQuery
                $.ajax({
                    method: "POST",
                    url: "logica/clssFiltro.php",
                    data: {
                        "accion": "FILTROPERSONA",
                        "data": query
                    }
                }).done(function (response) {
                    try {
                        // Parsear la respuesta como JSON
                        console.log(response)
                        const resultados = JSON.parse(response);

                        // Limpiar las sugerencias actuales
                        sugerencias.innerHTML = "";

                        // Verificar si hay resultados
                        if (resultados.length > 0) {
                            resultados.forEach(persona => {
                                // Crear un elemento de lista para cada resultado
                                const item = document.createElement("div");
                                item.classList.add("list-group-item");
                                item.textContent = persona.persona_concatenada;

                                // Acción al seleccionar un resultado
                                item.addEventListener("click", function () {
                                    // Establecer el valor del input con el nombre seleccionado
                                    nombreCliente.value = persona.persona_concatenada;
                                    persona_id.textContent = persona.id
 

                                    // Limpiar las sugerencias
                                    sugerencias.innerHTML = "";
                                });

                                // Agregar el elemento a la lista de sugerencias
                                sugerencias.appendChild(item);
                            });
                        } else {
                            // Mostrar un mensaje si no hay resultados
                            const noResults = document.createElement("div");
                            noResults.classList.add("list-group-item", "text-muted");
                            noResults.textContent = "Sin resultados";
                            sugerencias.appendChild(noResults);
                        }
                    } catch (e) {
                        console.error("Error al procesar los resultados:", e);
                        sugerencias.innerHTML = ""; // Limpiar las sugerencias en caso de error
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
                    sugerencias.innerHTML = ""; // Limpiar las sugerencias en caso de fallo
                });
            } else {
                // Limpiar las sugerencias si no hay texto
                sugerencias.innerHTML = "";
            }
        });

        // Cerrar las sugerencias si se hace clic fuera del input o sugerencias
        document.addEventListener("click", function (e) {
            if (!nombreCliente.contains(e.target) && !sugerencias.contains(e.target)) {
                sugerencias.innerHTML = "";
            }
        });
    });


   

   
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    // Evento para el botón "Reservar"
    document.getElementById("Reservar").addEventListener("click", function () {
        var idCliente = document.getElementById('idPersona').textContent.trim();
        var total = document.getElementById("montoTotal").value;

        const userId = <?php echo $_SESSION['id']; ?>;
        console.log(idCliente);
        console.log(userId);

        const datos = {
            "usuario_id": userId,  // Puedes cambiar este valor dinámicamente si es necesario
            "cliente_id": idCliente,  // También este valor puede ser dinámico
            "total": total,
            "articulos": []
        };

        // Obtener todas las filas de la tabla (excepto el encabezado)
        const rows = document.querySelectorAll("#tabla_articulos tbody tr");

        // Recorrer todas las filas y obtener los datos de cada columna
        rows.forEach(function(row) {
            const articulo = {
                "articulo_id": row.cells[0].textContent,  // El ID del artículo
                "minutos": row.cells[1].textContent, 
                "costoxminuto": row.cells[2].textContent, 
                "precio_unitario": parseFloat(row.cells[6].textContent),  // Precio Unitario
                "cantidad": parseInt(row.cells[5].textContent),  // Cantidad
                "sub_total": parseFloat(row.cells[7].textContent)  // Subtotal
            };

            // Agregar el artículo al array
            datos.articulos.push(articulo);
        });

        // Mostrar los datos en la consola para verificar
        console.log(JSON.stringify(datos));

        $.ajax({
            method: "POST",
            url: "logica/clssVentaCorte.php",
            data: {
                "accion": "REGISTRARRESERVA",
                "data": JSON.stringify(datos)
            }
        }).done(function (response) {
            console.log(response);
            if(response.success){
                alert("Reserva registrada correctamente.");
            }
           
        }).fail(function (error) {
            console.error("Error:", error.responseText);
            alert("Error al registrar la reserva.");
        });
    });


    });
</script>



<?php
include("pie.php");
?>