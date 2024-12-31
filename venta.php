<?php
include("cabecera.php");
include("logica/clssVenta.php");


if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

?>


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
                            <div class="card-header">
                                <h4 class="card-title">Articulos</h4>
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
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <button id="rest_<?php echo $datosArticulo["id"] ?>" class="btn btn-danger btn-round me-2">-</button>
                                                            <span id="cantidad_<?php echo $datosArticulo["id"] ?>" class="mx-2">1</span>
                                                            <button id="add_<?php echo $datosArticulo["id"] ?>" class="btn btn-success btn-round ms-2">+</button>
                                                        </div>
                                                        <div class="mt-2 text-center">
                                                            <a
                                                                name=""
                                                                id=""
                                                                class="btn btn-secondary btn-round"

                                                                onclick='fn_preguntar_corte(<?php echo $datosArticuloJSON; ?>)'
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
        <div class="mb-3 row">
          <div class="col-md-12">
            <div class="card text-start">   
              <div class="card-body text-center">
                <p class="card-text">Minutos Corte</p>
                <div class="row">
                  <div class="col">
                    <button name="" id="btn_menos" class="btn btn-danger btn-round ms-2" type="button" role="button">-</button>
                  </div>
                  <div id="cantidad" class="col">10</div>
                  <div class="col">
                    <button name="" id="btn_mas" type="button" class="btn btn-success btn-round ms-2" role="button">+</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
                <hr>
                <div
                    class="row ">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Detalle Corte</div>
                            </div>
                            <div class="card-body">
                                <div class="card-sub">
                                    Aquí la venta por minutos en corte de MAQUINA
                                </div>
                                <table id="tabla_minutos_corte" class="table mt-3">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">MINUTOS</th>
                                            <th scope="col">COSTO x MINUTO</th>

                                        </tr>
                                    </thead>
                                    <tbody>             
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Detalle Materiales</div>
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
                                            <th scope="col">Articulo</th>
                                            <th scope="col">Cantidad</th>
                                            <th scope="col">Precio Unitario</th>
                                            <th scope="col">Sub Total (S/)</th>
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

                <div class="card-title">Total S/ <span id="id_subtotal_materiales">xx.xx</span></div>

            </div>
        </div>
    </div>


</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>


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

document.addEventListener("DOMContentLoaded", () => {
    const btn_mas = document.getElementById('btn_mas');
    const btn_menos = document.getElementById('btn_menos');
    const cantidad = document.getElementById('cantidad');
    const btn_add_minutos = document.getElementById('btn_add_minutos');
    
    
    cantidad.innerText = 0;
    btn_menos.disabled = true;

    // Evento para incrementar
    btn_mas.addEventListener("click", () => {
        btn_menos.disabled = false;
        let valorActual = parseInt(cantidad.innerText) || 0; 
        if (valorActual > 0){
            cantidad.innerText = valorActual + 1; 
        }else{
            cantidad.innerText = 10;
        }
    });

    // Evento para decrementar
    btn_menos.addEventListener("click", () => {
        let valorActual = parseInt(cantidad.innerText) || 0;
        if (valorActual > 0) { 
            cantidad.innerText = valorActual - 1; 
            if (parseInt(cantidad.innerText) === 0) {
                btn_menos.disabled = true;
            }
        }
    });

   

});

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

    function fn_agregar_corte_minuto(id){
        const tabla = document.getElementById('tabla_minutos_corte').getElementsByTagName('tbody')[0];
        const costo = 1.5;
        const cantidad = document.getElementById('cantidad');


        let minutos = parseInt(cantidad.innerText) || 0;
        
        if (minutos > 0) {
            // Crear una nueva fila
            var nuevaFila = tabla.insertRow();

            // Crear las celdas
            var celdaId = nuevaFila.insertCell(0);
            var celdaMinutos = nuevaFila.insertCell(1);
            var celdaCosto = nuevaFila.insertCell(2);

            // Asignar el contenido a las celdas
            celdaId.innerText = id; 
            celdaMinutos.innerText = minutos;       
            celdaCosto.innerText = costo * minutos ;           
           
            cantidad.innerText = 0;
        }
    }

    function fn_preguntar_corte(datosArticulo) {
        const modal = new bootstrap.Modal(document.getElementById('miModal'), {
    backdrop: 'static', // Evita que se cierre al hacer clic fuera
    keyboard: false     // Opcional: Evita que se cierre con la tecla 'Esc'
});
    modal.show();

    // Obtener botones
    const btnSi = document.getElementById('btn_si');
    const btnNo = document.getElementById('btn_no');

    // Eliminar todos los event listeners previos
    btnSi.replaceWith(btnSi.cloneNode(true));
    btnNo.replaceWith(btnNo.cloneNode(true));

    // Actualizar las referencias a los botones clonados
    const btnSiNuevo = document.getElementById('btn_si');
    const btnNoNuevo = document.getElementById('btn_no');

    // Evento para el botón "Sí"
    btnSiNuevo.addEventListener("click", () => {
        fn_agregar_corte_minuto(datosArticulo["id"]); // Agregar minutos
        modal.hide(); // Cerrar modal
        fn_agregar_articulo_tabla(datosArticulo); // Agregar el artículo
    });

    // Evento para el botón "No"
    btnNoNuevo.addEventListener("click", () => {
        modal.hide(); // Cerrar modal
        fn_agregar_articulo_tabla(datosArticulo); // Solo agregar el artículo
    });
}

    function fn_agregar_articulo_tabla(datosArticulo) {
        
        console.log(datosArticulo);
        console.log("ID ARTICULO: ", datosArticulo["id"])
        var cantidad = document.getElementById("cantidad_" + datosArticulo["id"]).textContent
        console.log("CANTIDAD DE MRD:", cantidad)
        ///////////////////////////////////////////////////////////////////////////////////////////////////
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
        var nuevaFila = tabla.insertRow();

        var celdaArticulo = nuevaFila.insertCell(0);
        celdaArticulo.textContent = datosArticulo["id"];

        var celdaArticulo = nuevaFila.insertCell(1);
        celdaArticulo.textContent = datosArticulo["articulo"];


        var celdaCantidad = nuevaFila.insertCell(2);
        celdaCantidad.textContent = cantidad;



        var celdaPrecio = nuevaFila.insertCell(3);
        celdaPrecio.textContent = datosArticulo["precio_venta"];

        var celdaSubTotal = nuevaFila.insertCell(4);
        celdaSubTotal.textContent = cantidad * parseFloat(datosArticulo["precio_venta"]);
       
    }

    
</script>




<?php
include("pie.php");
?>