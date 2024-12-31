<?php
include("cabecera.php");
include("logica/clssVenta.php");


if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

?>

<style>
    @media (min-width: 768px) and (max-width: 1500px) {
        #multi-filter-select_wrapper > div:nth-of-type(3) {
            display: flex; 
            flex-direction: column; 
        }
        #multi-filter-select_wrapper > div:nth-of-type(3)> div:last-child {
            width: 100%;
        }
        #multi-filter-select_wrapper > div:nth-of-type(3)> div:last-child > div {
            width: 100%;
        }
        #multi-filter-select_wrapper > div:nth-of-type(3)> div:last-child > div > ul{
            justify-content: space-between;
        }
    }
</style>

<div
    class="container">
    <div class="page-inner">
        <div
            class="card"
        >

            <div class="card-body">
                <h4 class="card-title">Venta Simple De Materiales</h4>
                <div class="mb-3">
                    <div class="card-sub">
                        Aquí podrás realizar ventas de TODOS tus articulos.
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="articulos">Articulos</h4>
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
                                                            <span id="cantidad_<?php echo $datosArticulo["id"] ?>" class="mx-2 text-center" style="width: 20px;">1</span>
                                                            <button id="add_<?php echo $datosArticulo["id"] ?>" class="btn btn-success btn-round ms-2">+</button>
                                                        </div>
                                                        <div class="mt-2 text-center">
                                                            <a
                                                                name=""
                                                                id=""
                                                                class="btn btn-secondary btn-round"
                                                                href=""
                                                                onclick='fn_agregar_articulo_tabla(event, <?php echo $datosArticuloJSON; ?>)'
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
                >
                </div>
                <hr>
                <div
                    class="row ">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div id="detalle-venta-materiales" class="card-title">Detalle de Venta de Materiales</div>
                            </div>
                            <div class="card-body">
                                <div class="card-sub">
                                    Aquí la venta de los materiales
                                </div>
                                <table id="tabla_articulos" class="table mt-3">
                                    <thead>
                                        <tr>
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

                <div class="d-flex justify-content-between">
                    <div class="card-title">Total S/ <span id="id_subtotal_materiales">00.00</span></div>
                    <button class="btn btn-success" onclick="fn_concretar_venta()">Concretar venta</button>
                </div>

            </div>
        </div>
    </div>


</div>
<!-- Cargar jQuery desde el CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Cargar DataTables desde el CDN -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- Asegúrate de cargar cualquier otro script necesario -->
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

    //Array que almacena los articulos agregados
    rel_venta_articulo = [];

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

    function fn_agregar_articulo_tabla(e, datosArticulo) {
        e.preventDefault();
        console.log(datosArticulo);
        console.log("ID ARTICULO: ", datosArticulo["id"])
        var cantidad = document.getElementById("cantidad_" + datosArticulo["id"]).textContent
        console.log("CANTIDAD DE MRD:", cantidad)
        ///////////////////////////////////////////////////////////////////////////////////////////////////
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
        var nuevaFila = tabla.insertRow();

        var celdaArticulo = nuevaFila.insertCell(0);
        celdaArticulo.textContent = datosArticulo["articulo"];


        var celdaCantidad = nuevaFila.insertCell(1);
        celdaCantidad.textContent = cantidad;



        var celdaPrecio = nuevaFila.insertCell(2);
        celdaPrecio.textContent = datosArticulo["precio_venta"];

        var celdaSubTotal = nuevaFila.insertCell(3);
        celdaSubTotal.textContent = cantidad * parseFloat(datosArticulo["precio_venta"]);

        //Agregamos cantidad al datoArticulo
        datosArticulo = {
            ...datosArticulo, 
            cantidad: parseInt(cantidad),
            subtotal: parseInt(cantidad)*parseFloat(datosArticulo["precio_venta"])
        }

        //Guardar datos articulo para enviada posterior a la base de datos
        rel_venta_articulo.push(datosArticulo);

        fn_sumar_subtotal();

        document.getElementById("detalle-venta-materiales").scrollIntoView({behavior:"smooth"});
    }


    function fn_concretar_venta() {

        //Verificamos que se hayan agregado articulos
        if(rel_venta_articulo.length == 0) {
            swal({
                title: "No se han agregado articulos",
                icon: "info",
                text: ""
            })
            .then(value => {
                if(value == null) return
                document.getElementById("articulos").scrollIntoView({
                    behavior: "smooth"
                });
            })
            return;
        }

        const venta = {
            usuarioId: <?php echo $_SESSION["id"]; ?>,
            movimientoId: <?php echo $id; ?>,
            clienteId: 0,
            total: parseFloat(document.getElementById("id_subtotal_materiales").textContent),
            detalleVenta: rel_venta_articulo
        }

        swal({
            title: "Confirmación",
            text:"¿Estas seguro de proceder con la venta?",
            icon: "info",
            buttons:["Cancelar", true]
        }).then((value) => {
            if(!value) throw null;

            return fetch("logica/clssVenta.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(venta)
                });
        }).then((result) => {
            return result.json();
        }).then(json => {
            return swal("Éxito",json["mensaje"], "success");
        }).then(value => {
            window.location.href = "/venta_simple.php?id=5"
        }).catch(error => {
            if(!error) {
                swal.stopLoading();
                swal.close();
            }
        });
    }

    function fn_sumar_subtotal() {
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
        var subtotal = 0;
        for (var i = 0; i < tabla.rows.length; i++) {

            var fila = tabla.rows[i];

            var precio = parseFloat(fila.cells[3].textContent);

            if (!isNaN(precio)) {
                subtotal += precio;
            }
        }
        console.log("Subtotal: ", subtotal);
        document.getElementById("id_subtotal_materiales").textContent = subtotal.toFixed(2);
    }
</script>


<?php
include("pie.php");
?>