<?php
include("cabecera.php");
?>

<style>
    #sugerencias {
        max-height: 200px;
        overflow-y: auto;
        z-index: 1050;
        /* Para asegurar que esté sobre otros elementos */
    }

    #sugerencias .list-group-item {
        cursor: pointer;
    }

    .error-input {
        border: 2px solid red;
    }

    .error-message {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
    }
 
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
    class="container">

    <div class="page-inner">
        <div class="card text-start">

            <div class="card-body">


                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">Articulos</h4>
                    <button class="btn btn-success rounded-5" id="btnAbrirModalGenerico"> Agregar Articulo <i class="fas fa-plus"> </i></button>
                </div>
                <hr>
                <div
                    class="row justify-content-center align-items-center md-2">

                    <div class="col-sm-12">
                    <div class="table-filters mb-4">
                                    <div class="row justify-content-center align-items-center g-2">
                                        <div class="col-md-3">
                                            <select id="filterCategoria" class="form-select" style="border-radius: 25px; border: 2px solid #6861ce;">
                                                <option value="">Filtrar por Categoría</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select id="filterTipo" class="form-select" style="border-radius: 25px; border: 2px solid #6861ce;">
                                                <option value="">Filtrar por Tipo</option>

                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select id="filterDimension" class="form-select" style="border-radius: 25px; border: 2px solid #6861ce;">
                                                <option value="">Filtrar por Dimensión</option>

                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <button
                                                name=""
                                                id="clearFilters"
                                                class="btn btn-secondary btn-round btn-round btn-md"
                                                href="#"
                                                role="button"><i class="fas fa-broom"></i> Limpiar Filtros</b>
                                        </div>
                                    </div>

                                </div>
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
                                       
                                        <tbody>

                                            <?php
                                            foreach (listarArticuloSinview() as $datosArticulo) {
                                                $datosArticuloJSON = json_encode($datosArticulo);


                                            ?>
                                                <tr>
                                                    <td><?php echo $datosArticulo["articulo"] ?></td>
                                                    <td><?php echo $datosArticulo["categoria"] ?? '-'; ?></td>
                                                    <td><?php echo $datosArticulo["tipo"] ?? '-'; ?></td>
                                                    <td><?php echo $datosArticulo["dimension"] ?? '-'; ?></td>
                                                    <td><?php echo $datosArticulo["stock"] ?></td>
                                                    <td><?php echo $datosArticulo["precio_venta"] ?></td>
                                                    <th>
                                                       
                                                        <div class="mt-2 text-center">
                                                            <!-- Botón de Editar (con ícono amarillo) -->
                                                    <a name="edit" id="edit" class="btn btn-warning btn-round ml-2"
                                                        onclick='fn_editar_articulo(<?php echo $datosArticuloJSON; ?>)' role="button">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    <!-- Botón de Activar/Bloquear -->
                                                    <?php if (is_null($datosArticulo["deleted_at"])) { ?>
                                                        <!-- Botón para bloquear -->
                                                        <a name="block" id="block" class="btn btn-dark btn-round ml-2"
                                                            onclick='fn_bloquear_articulo(<?php echo $datosArticulo["id"]; ?>)' role="button">
                                                            <i class="fa fa-lock"></i>
                                                        </a>
                                                    <?php } else { ?>
                                                        <!-- Botón para activar -->
                                                        <a name="activate" id="activate" class="btn btn-secondary btn-round ml-2"
                                                            onclick='fn_desbloquear_articulo(<?php echo $datosArticulo["id"]; ?>)' role="button">
                                                            <i class="fa fa-unlock"></i>
                                                        </a>
                                                    <?php } ?>
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



<div class="modal fade" id="modalArticulo" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalArticuloLabel" aria-hidden="true">
    
    <div  class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document">


        <div class="modal-content" id="contenidoArticulo">
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script src="assets/js/scriptNotify.js"></script>


<script>
    $(document).ready(function() {
        var table = $("#multi-filter-select").DataTable({
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

        // Llenar el filtro de Categoría con valores únicos
        table.column(1).data().unique().sort().each(function(d, j) {
            if (d !== "") {
                $('#filterCategoria').append('<option value="' + d + '">' + d + '</option>');
            }
        });

        // Llenar el filtro de Dimensión con valores únicos
        table.column(3).data().unique().sort().each(function(d, j) {
            if (d !== "") {
                $('#filterDimension').append('<option value="' + d + '">' + d + '</option>');
            }
        });

        // Llenar el filtro de Tipo con valores únicos
        table.column(2).data().unique().sort().each(function(d, j) {
            if (d !== "") {
                $('#filterTipo').append('<option value="' + d + '">' + d + '</option>');
            }
        });

        // Filtrar por Categoría
        $('#filterCategoria').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(1).search(val ? "^" + val + "$" : "", true, false).draw();
        });

        // Filtrar por Tipo
        $('#filterTipo').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(2).search(val ? "^" + val + "$" : "", true, false).draw();
        });

        // Filtrar por Dimensión
        $('#filterDimension').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(3).search(val ? "^" + val + "$" : "", true, false).draw();
        });

        // Limpiar los filtros al hacer clic en el botón
        $('#clearFilters').on('click', function() {
            // Limpiar las selecciones de los filtros
            $('#filterCategoria').val('');
            $('#filterTipo').val('');
            $('#filterDimension').val('');

            // Restablecer los filtros de la tabla
            table.columns().search('').draw();
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        document.getElementById("btnAbrirModalGenerico").addEventListener("click", function() {
            document.getElementById("contenidoArticulo").innerHTML = `
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>

                
                <div class="card-body">
                      <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-shopping-bag"></i> Registro de Articulos</h4>
                <div class="card-sub text-center">
                    Aquí podrás <strong>registrar</strong> los Artículos <strong>NUEVOS.</strong>
                </div>
                <div class="card-sub text-center">
                    Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
                </div>
                <div class="card text-start">

                    <div class="card-body">
                        <div
                            class="row justify-content-center align-items-center g-2">

                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Ingrese Nombre de Articulo</strong></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="idRegistroNombreArticulo"
                                        id="idRegistroNombreArticulo"
                                        aria-describedby="helpId"
                                        placeholder="Articulo 1" />
                                </div>

                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Categoría</strong></label>
                                    <select
                                        class="form-select form-select-sm"
                                        name="idRegistoCategoria"
                                        id="idRegistoCategoria">
                                        <option selected>Selccione Categoría</option>
                                        <?php foreach (listarCategoria() as $datos) {
                                        ?>
                                            <option value="<?php echo $datos["id"] ?>"><?php echo $datos["abreviatura"] ?></option>

                                        <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Tipo de Artículo</strong></label>
                                    <select
                                        class="form-select form-select-sm"
                                        name="idRegistoTipo"
                                        id="idRegistoTipo">
                                        <option selected>Selccione Tipo de Articulo</option>
                                        <?php foreach (listarTipoArticulos() as $datos) {
                                        ?>
                                            <option value="<?php echo $datos["id"] ?>"><?php echo $datos["abreviatura"] ?></option>

                                        <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Dimensión</strong></label>
                                    <select
                                        class="form-select form-select-sm"
                                        name="idRegistroDimension"
                                        id="idRegistroDimension">
                                        <option selected>Selccione Dimensión</option>
                                        <?php foreach (listarDimension() as $datos) {
                                        ?>
                                            <option value="<?php echo $datos["id"] ?>"><?php echo $datos["medida"] ?></option>

                                        <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Escala</strong></label>
                                    <select
                                        class="form-select form-select-sm"
                                        name="idRegistroEscala"
                                        id="idRegistroEscala">
                                        <option selected>Selccione Escala</option>
                                        <?php foreach (listarEscala() as $datos) {
                                        ?>
                                            <option value="<?php echo $datos["id"] ?>"><?php echo $datos["abreviatura"] ?></option>

                                        <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Marca de Articulo</strong></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="idRegistroMarca"
                                        id="idRegistroMarca"
                                        aria-describedby="helpId"
                                        placeholder="Ejemplo: Artesco" />
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"> <strong>Color</strong></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="idRegistroColor"
                                        id="idRegistroColor"
                                        aria-describedby="helpId"
                                        placeholder="Rojo, verde, azul, Etc." />
                                </div>

                            </div>

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"> <strong>Stock</strong></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="idRegistrarStock"
                                        id="idRegistrarStock"
                                        aria-describedby="helpId"
                                        placeholder="00" />
                                </div>

                            </div>

                               <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"> <strong>Precio Venta</strong></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="idRegistrarPrecioVenta"
                                        id="idRegistrarPrecioVenta"
                                        aria-describedby="helpId"
                                        placeholder="00.00" />
                                </div>

                            </div>


                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="" class="form-label"><strong>Requiere Corte</strong></label>
                                    <div class="d-flex">
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="flexRadioDefault"
                                                id="flexRadioDefault1"
                                                value="Si" />
                                            <label
                                                class="form-check-label"
                                                for="flexRadioDefault1">
                                                Si
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="flexRadioDefault"
                                                id="flexRadioDefault2"
                                                value="No"
                                                checked />
                                            <label
                                                class="form-check-label"
                                                for="flexRadioDefault2">
                                                No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">

                            </div>
                            <div class="text-center">
                                <a
                                    name=""
                                    id="btnRegistrarArticulo"
                                    class="btn btn-success btn-round"

                                    role="button">Registrar <i class="fas fa-check"> </i></a>
                            </div>
                        </div>
                    </div>


                </div>
                </div>
                    
                    
                    
           
            `;

            const modal = new bootstrap.Modal(document.getElementById("modalArticulo"));
            modal.show();

            // Agregar evento de validación al botón "Registrar"

            document.getElementById("btnRegistrarArticulo").addEventListener("click", async function() {
                if ((document.getElementById("idRegistroNombreArticulo").value).length > 0) {

                    let categoriaSelect = document.getElementById("idRegistoCategoria");
                    let categoria = categoriaSelect.selectedIndex === 0 ? null : categoriaSelect.value;
                    //////////////////////////////
                    let tipoSelect = document.getElementById("idRegistoTipo");
                    let tipo = tipoSelect.selectedIndex === 0 ? null : tipoSelect.value;
                    /////////////////////////////
                    let dimensionSelect = document.getElementById("idRegistroDimension");
                    let dimension = dimensionSelect.selectedIndex === 0 ? null : dimensionSelect.value;

                    /////////////
                    let escalaSelect = document.getElementById("idRegistroEscala");
                    let escala = escalaSelect.selectedIndex === 0 ? null : escalaSelect.value;

                    /////////////////////////////////////////
                    let radios = document.getElementsByName("flexRadioDefault");
                    let selectedValue = "";

                    for (let i = 0; i < radios.length; i++) {
                        if (radios[i].checked) {
                            selectedValue = radios[i].value;
                            break;
                        }
                    }
                    let corte = selectedValue === "Si" ? true : false;

                    let colorEscrito = document.getElementById("idRegistroColor").value;
                    let color = (colorEscrito).length > 0 ? colorEscrito : null;
                    ///////
                    let marcaEscrita = document.getElementById("idRegistroMarca").value;
                    let marca = (marcaEscrita).length > 0 ? marcaEscrita : null;

                    let stockEscrito = document.getElementById("idRegistrarStock").value.trim();

                    // Si es un número entero positivo, lo convierte a número; si no, asigna 0
                    let stock = stockEscrito !== "" && /^\d+$/.test(stockEscrito) ? parseInt(stockEscrito, 10) : 0;
                    
                    let precioventaEscrito = document.getElementById("idRegistrarStock").value.trim();

                    // Si es un número entero positivo, lo convierte a número; si no, asigna 0
                    let precioventa = precioventaEscrito !== "" && /^\d+$/.test(precioventaEscrito) ? parseFloat(precioventaEscrito, 10) : 0;

                    var jsArticulo = {
                        "nombre": document.getElementById("idRegistroNombreArticulo").value,
                        "categoria_id": categoria,
                        "tipo_id": tipo,
                        "dimension_id": dimension,
                        "escala_id": escala,
                        "corte": corte,
                        "color": color,
                        "stock": stock,
                        "precio_venta":precioventa,
                        "marca": document.getElementById("idRegistroMarca").value
                    };
                    console.log(jsArticulo);

                    $.ajax({
                        url: 'logica/clssInsertPA.php',
                        type: 'POST',
                        data: {
                            accion: 'REGISTAR_ARTICULO_COMPLETO',
                            jsDatosArticulo: JSON.stringify(jsArticulo)
                        },
                        success: function(response) {
                            console.log("Respuesta del servidor PA articulo: ", response);
                            try {
                                var result = JSON.parse(response);
                                if (result.estado === true) {
                                    swal({
                                        title: "Registrado con Exito!",
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


                    } else {
                    swal("Ups!, Debes de ingresar el nombre del Articulo 😩", {
                        icon: "error",
                        buttons: {
                            confirm: {
                                className: "btn btn-danger",
                            },
                        },
                    });
                    }


            });

        });

       

    });
</script>


<script>
    function fn_editar_articulo(datosArticulo) {
        console.log(datosArticulo);
        document.getElementById("contenidoArticulo").innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>

           <div class="card-body">
                      <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-shopping-bag"></i> Registro de Articulos</h4>
                <div class="card-sub text-center">
                    Aquí podrás <strong>registrar</strong> los Artículos <strong>NUEVOS.</strong>
                </div>
                <div class="card-sub text-center">
                    Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
                </div>
                <div class="card text-start">

                    <div class="card-body">
                        <div
                            class="row justify-content-center align-items-center g-2">

                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Ingrese Nombre de Articulo</strong></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="idRegistroNombreArticulo"
                                        id="idRegistroNombreArticulo"
                                        aria-describedby="helpId"
                                        placeholder="Articulo 1" />
                                </div>

                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Categoría</strong></label>
                                    <select
                                        class="form-select form-select-sm"
                                        name="idRegistoCategoria"
                                        id="idRegistoCategoria">
                                        <option selected>Selccione Categoría</option>
                                        <?php foreach (listarCategoria() as $datos) {
                                        ?>
                                            <option value="<?php echo $datos["id"] ?>"><?php echo $datos["abreviatura"] ?></option>

                                        <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Tipo de Artículo</strong></label>
                                    <select
                                        class="form-select form-select-sm"
                                        name="idRegistoTipo"
                                        id="idRegistoTipo">
                                        <option selected>Selccione Tipo de Articulo</option>
                                        <?php foreach (listarTipoArticulos() as $datos) {
                                        ?>
                                            <option value="<?php echo $datos["id"] ?>"><?php echo $datos["abreviatura"] ?></option>

                                        <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Dimensión</strong></label>
                                    <select
                                        class="form-select form-select-sm"
                                        name="idRegistroDimension"
                                        id="idRegistroDimension">
                                        <option selected>Selccione Dimensión</option>
                                        <?php foreach (listarDimension() as $datos) {
                                        ?>
                                            <option value="<?php echo $datos["id"] ?>"><?php echo $datos["medida"] ?></option>

                                        <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Escala</strong></label>
                                    <select
                                        class="form-select form-select-sm"
                                        name="idRegistroEscala"
                                        id="idRegistroEscala">
                                        <option selected>Selccione Escala</option>
                                        <?php foreach (listarEscala() as $datos) {
                                        ?>
                                            <option value="<?php echo $datos["id"] ?>"><?php echo $datos["abreviatura"] ?></option>

                                        <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Marca de Articulo</strong></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="idRegistroMarca"
                                        id="idRegistroMarca"
                                        aria-describedby="helpId"
                                        placeholder="Ejemplo: Artesco" />
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"> <strong>Color</strong></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="idRegistroColor"
                                        id="idRegistroColor"
                                        aria-describedby="helpId"
                                        placeholder="Rojo, verde, azul, Etc." />
                                </div>

                            </div>

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"> <strong>Stock</strong></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="idRegistrarStock"
                                        id="idRegistrarStock"
                                        aria-describedby="helpId"
                                        placeholder="00" />
                                </div>

                            </div>

                               <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"> <strong>Precio Venta</strong></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="idRegistrarPrecioVenta"
                                        id="idRegistrarPrecioVenta"
                                        aria-describedby="helpId"
                                        placeholder="00.00" />
                                </div>

                            </div>


                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="" class="form-label"><strong>Requiere Corte</strong></label>
                                    <div class="d-flex">
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="flexRadioDefault"
                                                id="flexRadioDefault1"
                                                value="Si" />
                                            <label
                                                class="form-check-label"
                                                for="flexRadioDefault1">
                                                Si
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="flexRadioDefault"
                                                id="flexRadioDefault2"
                                                value="No"
                                                checked />
                                            <label
                                                class="form-check-label"
                                                for="flexRadioDefault2">
                                                No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">

                            </div>
                            <div class="text-center">
                                <a
                                    name=""
                                    id="btnEditarArticulo"
                                    class="btn btn-success btn-round"

                                    role="button">Registrar <i class="fas fa-check"> </i></a>
                            </div>
                        </div>
                    </div>


                </div>
                </div>
            
        `;
        const setSelectValue = (elementId, text) => {
            const select = document.getElementById(elementId);
            let optionFound = false;
            
            if (text === null) {
                // Seleccionar la primera opción si el texto es null
                select.selectedIndex = 0;
            } else {
                // Recorrer las opciones del select y seleccionar la que tenga el texto correcto
                for (let option of select.options) {
                    if (option.text.trim() === text.trim()) {
                        option.selected = true;
                        optionFound = true;
                        break;
                    }
                }
                // Si no se encuentra la opción con el texto, seleccionamos la primera opción
                if (!optionFound) {
                    select.selectedIndex = 0;
                }
            }
        }

        setSelectValue("idRegistoCategoria", datosArticulo.categoria);
        setSelectValue("idRegistoTipo", datosArticulo.tipo);
        setSelectValue("idRegistroDimension", datosArticulo.dimension);
        setSelectValue("idRegistroEscala", datosArticulo.escala);
        // Rellenar el campo de estado según el valor de datosUsuario
        document.getElementById("idRegistroNombreArticulo").value = datosArticulo.articulo || '';
        document.getElementById("idRegistroMarca").value = datosArticulo.marca || '';
        document.getElementById("idRegistroColor").value = datosArticulo.color || '';
        document.getElementById("idRegistrarStock").value = datosArticulo.stock || '';
        document.getElementById("idRegistrarPrecioVenta").value = datosArticulo.precio_venta || '';

        // Para el campo 'corte' (radio buttons)
        if (datosArticulo.corte) {
            document.getElementById("flexRadioDefault1").checked = true;
        } else {
            document.getElementById("flexRadioDefault2").checked = true;
        }
        const modal = new bootstrap.Modal(document.getElementById("modalArticulo"));
        modal.show();

        document.getElementById("btnEditarArticulo").addEventListener("click", async function() {
            if ((document.getElementById("idRegistroNombreArticulo").value).length > 0) {

            let categoriaSelect = document.getElementById("idRegistoCategoria");
            let categoria = categoriaSelect.selectedIndex === 0 ? null : categoriaSelect.value;
            //////////////////////////////
            let tipoSelect = document.getElementById("idRegistoTipo");
            let tipo = tipoSelect.selectedIndex === 0 ? null : tipoSelect.value;
            /////////////////////////////
            let dimensionSelect = document.getElementById("idRegistroDimension");
            let dimension = dimensionSelect.selectedIndex === 0 ? null : dimensionSelect.value;

            /////////////
            let escalaSelect = document.getElementById("idRegistroEscala");
            let escala = escalaSelect.selectedIndex === 0 ? null : escalaSelect.value;

            /////////////////////////////////////////
            let radios = document.getElementsByName("flexRadioDefault");
            let selectedValue = "";

            for (let i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    selectedValue = radios[i].value;
                    break;
                }
            }
            let corte = selectedValue === "Si" ? true : false;

            let colorEscrito = document.getElementById("idRegistroColor").value;
            let color = (colorEscrito).length > 0 ? colorEscrito : null;
            ///////
            let marcaEscrita = document.getElementById("idRegistroMarca").value;
            let marca = (marcaEscrita).length > 0 ? marcaEscrita : null;

            let stockEscrito = document.getElementById("idRegistrarStock").value.trim();

            // Si es un número entero positivo, lo convierte a número; si no, asigna 0
            let stock = stockEscrito !== "" && /^\d+$/.test(stockEscrito) ? parseInt(stockEscrito, 10) : 0;

            let precioventaEscrito = document.getElementById("idRegistrarStock").value.trim();

            // Si es un número entero positivo, lo convierte a número; si no, asigna 0
            let precioventa = precioventaEscrito !== "" && /^\d+$/.test(precioventaEscrito) ? parseFloat(precioventaEscrito, 10) : 0;

            var jsArticulo = {
                "id": datosArticulo.id,
                "nombre": document.getElementById("idRegistroNombreArticulo").value,
                "categoria_id": categoria,
                "tipo_id": tipo,
                "dimension_id": dimension,
                "escala_id": escala,
                "corte": corte,
                "color": color,
                "stock": stock,
                "precio_venta":precioventa,
                "marca": document.getElementById("idRegistroMarca").value
            };
            console.log(jsArticulo);

            $.ajax({
                url: 'logica/clssInsertPA.php',
                type: 'POST',
                data: {
                    accion: 'EDITAR_ARTICULO_COMPLETO',
                    jsDatosArticulo: JSON.stringify(jsArticulo)
                },
                success: function(response) {
                    console.log("Respuesta del servidor PA articulo: ", response);
                    try {
                        var result = JSON.parse(response);
                        if (result.estado === true) {
                            swal({
                                title: "Registrado con Exito!",
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


            } else {
            swal("Ups!, Debes de ingresar el nombre del Articulo 😩", {
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

    function fn_bloquear_articulo(datosArticulo) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, bloquear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    method: "POST",
                    url: "logica/clssInsertPA.php",
                    data: {
                        "accion": "BLOQUEAR_ARTICULO",
                        "id": datosArticulo
                    }
                }).done(function(response) {

                    var result = JSON.parse(response);
                    console.log(response);

                    // Verificar si el resultado contiene éxito o error
                    if (result.success === true) {
                        location.reload();
                    } else if (result.error === true) {
                        // Si existe un error, mostrar el mensaje devuelto por el servidor
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }

                }).fail(function(error) {
                    console.error("Error:", error.responseText);
                });
            }
        });
    }

    function fn_desbloquear_articulo(datosArticulo) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, desbloquear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "logica/clssInsertPA.php",
                    data: {
                        "accion": "DESBLOQUEAR_ARTICULO",
                        "id": datosArticulo
                    }
                }).done(function(response) {

                    var result = JSON.parse(response);
                    console.log(response);

                    // Verificar si el resultado contiene éxito o error
                    if (result.success === true) {
                        location.reload();
                    } else if (result.error === true) {
                        // Si existe un error, mostrar el mensaje devuelto por el servidor
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }

                }).fail(function(error) {
                    console.error("Error:", error.responseText);
                });


            }
        });
    }

</script>

<?php
include("pie.php");
?>