<?php
include("cabecera.php");
include("logica/clssConsultas.php");
?>
<?php
// Simular una lista de insumos generada desde PHP
$lista = listarInsumosCompra();
$lista_json = json_encode($lista);
$json_insumo_compra = json_decode($lista_json, true);
$lista_postres = listarPostres();
$insumos = $json_insumo_compra;

// Convertir el array de insumos a JSON para usarlo en JavaScript
$insumos_json = json_encode($insumos);
//print_r($insumos_json);

?>

<div class="container">
    <div class="page-inner">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Postres a la venta </h2>
                <p class="card-text">

                    Aquí podrás registras tus postres para la venta. <b>RECUERDA</b>, debes de haber registrado tus compras de insumos para realizar esta opción.</p>
            </div>
            <a
                onclick="abrirModalInsert()"
                type="button"
                class="btn btn-primary btn-md"
                data-bs-toggle="modal"
                data-bs-target="#modal_registro_postre_venta">
                Registrar Postre Para Venta
            </a>
        </div>


        <div class="row">
            <div class="col-md-4">
                <div class="card card-round">
                    <div class="card-body">
                        <div class="card-title fw-mediumbold">Postres</div>
                        <div class="card-list">
                            <?php
                            // Aca tiene que ir el Listado de postres (select * from producto)
                            foreach ($lista_postres as $datax_postre2) {
                            ?>
                                <div class="item-list" data-id="<?php echo ($datax_postre2["id"]); ?>">
                                    <div class="avatar">
                                        <img
                                            src=""
                                            alt="..."
                                            class="avatar-img rounded-circle" />
                                    </div>
                                    <div class="info-user ms-3">
                                        <div class="username"><?php echo htmlspecialchars($datax_postre2["nombre"], ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div class="status"><?php echo $datax_postre2["descripcion"] ?></div>
                                    </div>
                                    <div class="btn-group dropend">
                                        <button
                                            type="button"
                                            class="btn btn-success btn-round dropdown-toggle"
                                            data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="handleAction('darBaja', '<?php echo ($datax_postre2["id"]); ?>')">Dar de Baja</a>
                                                <a class="dropdown-item" href="#" onclick="handleAction('modificar', '<?php echo ($datax_postre2["id"]); ?>')">Modificar</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="#" onclick="handleAction('otros', '<?php echo ($datax_postre2["id"]); ?>')">Otros</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="row g-2 justify-content-center align-items-center">
                    <?php
                    for ($i = 0; $i < 10; $i++) {
                    ?>
                        <div class="col-6 col-sm-4 col-md-2">
                            <div class="card">
                                <div class="card-body p-3 text-center">
                                    <div class="h1 m-0">43</div>
                                    <div class="text-muted mb-3">New Tickets</div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- EL MODAL DE REGISTRO DE POSTRES A LA VENTA -->
<!-- Button trigger modal -->



<!-- Modal -->
<div
    class="modal fade"
    id="modalInsertPostre"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="mb-3">
                    <label for="" class="form-label">Postre a la Venta</label>
                    <select
                        class="form-select form-select-md"
                        name=""
                        id="">
                        <?php
                        foreach ($lista_postres as $datax_postre) {
                        ?>
                            <option value="<?php echo $datax_postre["id"] ?>"><?php echo $datax_postre["nombre"]; ?></option>
                        <?php
                        }
                        ?>
                    </select>



                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row" id="id_contenedor">

                        <div class="col-md-12">
                            <div class="card card-round">
                                <div class="card-body">
                                    <h4 class="card-title">Selecciona tus Insumos para: Envase | Relleno | Cobertura | Envase</h4>

                                    <!-- Input para búsqueda -->
                                    <input id="search-input" class="form-control mb-3" placeholder="Buscar insumos..." />

                                    <!-- Contenedor para mostrar insumos filtrados -->
                                    <div id="insumos-filtrados">
                                        <ul id="insumos-lista" class="list-unstyled">
                                            <!-- Los insumos filtrados aparecerán aquí -->
                                        </ul>
                                    </div>
                                    <hr>
                                    <h4>Total del Producto: S/ <span id="id_total"> 0</span></h4>
                                    <span>Cantidad de Postres: </span><input id="id_total_postres_unidad" type="number" value="4" min="1"></input>
                                    <hr>
                                    <div class="row justify-content-center align-items-center g-2">
                                        <div class="col">Postre Por Unidad (S/): <span id="id_respuesta_v_u">0</span></div>
                                        <div class="col"><span>Precio de Venta: </span><span><input id="id_precio_venta" type="number"></span></div>
                                        <div class="col">Ganancia: S/ <span id="id_ganancia">0</span></div>
                                    </div>

                                    <hr>
                                    <!-- Lista de insumos seleccionados -->
                                    <h4 class="mt-4">Insumos seleccionados</h4>
                                    <table class="table table-striped" id="selected-items-table">
                                        <thead>
                                            <tr>
                                                <th>Insumo</th>
                                                <th>Cantidad</th>
                                                <th>Unidad</th>
                                                <th>Parte</th>
                                                <th>Subtotal (S/)</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="selected-items">
                                            <!-- Aquí se agregarán los insumos seleccionados -->
                                        </tbody>
                                    </table>

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
                <button type="button" class="btn btn-primary" onclick="fn_guardar_datos_venta_postre()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

<!-- CSS de Bootstrap -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- JS de Bootstrap -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>


<script>
    var modalId = document.getElementById('modalId');

    modalId.addEventListener('show.bs.modal', function(event) {
        // Button that triggered the modal
        let button = event.relatedTarget;
        // Extract info from data-bs-* attributes
        let recipient = button.getAttribute('data-bs-whatever');

        // Use above variables to manipulate the DOM
    });
</script>


<script>
    function abrirModalInsert() {
        $("#modalInsertPostre").modal("show");
        console.log("Esoy Aquiii");

    }
</script>



<!-- jQuery para manejar eventos -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    // Obtener los insumos desde PHP (convertido a JSON)
    var insumos = <?php echo $insumos_json; ?>;
    console.log(insumos);

    // Función para mostrar los insumos filtrados
    function fn_sumar_datos_table() {
        var tabla = document.getElementById("selected-items-table");
        var total = 0;
        ////////////////////////////
        var etiqueta_total = document.getElementById("id_total");

        var etiqueta_total_postre_unidad = document.getElementById("id_total_postres_unidad");
        ////////////////////////////
        console.log(etiqueta_total_postre_unidad.value)


        for (var i = 1; i < tabla.rows.length; i++) {
            var valor = parseFloat(tabla.rows[i].cells[4].innerText); // Convertir a número
            console.log("valor: " + tabla.rows[i].cells[4].innerText);
            total += valor; // Sumar al total
        }
        etiqueta_total.innerText = total;
        /////////////////7

        var valor_unidad_postre = document.getElementById("id_respuesta_v_u");
        valor_unidad_postre.innerText = total / etiqueta_total_postre_unidad.value;
    }

    document.getElementById("id_total_postres_unidad").addEventListener("input", fn_sumar_datos_table);

    function fn_calcular_ganancia() {
        var postre_por_unidad_element = document.getElementById("id_respuesta_v_u");
        var postre_por_unidad = parseFloat(postre_por_unidad_element.innerText); // Asegúrate de convertir a número

        console.log("Precio POSTRE_UNIDAD: " + postre_por_unidad);

        if (postre_por_unidad != 0) {
            var ganancia = document.getElementById("id_ganancia");
            var precio_venta = parseFloat(document.getElementById("id_precio_venta").value);

            var resp = precio_venta - postre_por_unidad;
            console.log("Precio VENTA: " + precio_venta);
            console.log("Precio POSTRE_UNIDAD: " + postre_por_unidad);

            var tentativo = document.getElementById("id_precio_tentativo");

            ganancia.innerText = resp;
        }
    }


    document.getElementById("id_precio_venta").addEventListener("input", fn_calcular_ganancia);

    function mostrarInsumosFiltrados(filtro) {
        // Filtrar los insumos basados en el input
        var insumosFiltrados = insumos.filter(function(insumo) {
            return insumo.nombre.toLowerCase().includes(filtro.toLowerCase());
        });

        // Mostrar los insumos filtrados en la lista
        var listaHTML = '';
        insumosFiltrados.forEach(function(insumo) {
            var precio_por_unidad = insumo.precio / insumo.cantidad;


            listaHTML += `
                <li class="mb-3">
                    <h5>${insumo.fecha} | <b>${insumo.nombre}</b> (${insumo.cantidad} - ${insumo.medida}) - S/ ${insumo.precio} <br>| Precio por Unidad: ${precio_por_unidad.toFixed(2)}</h5>
                    <div class="input-group mb-3">
                        <input type="number" class="form-control quantity" placeholder="Cantidad (${insumo.medida})" min="1" id="cantidad-${insumo.id}">
                        <button class="btn btn-primary agregar-insumo" data-id="${insumo.id}" data-nombre="${insumo.nombre}" data-medida="${insumo.medida}" data-precio_unidad="${precio_por_unidad}" data-parte_postre = "${insumo.parte_postre}">Agregar</button>
                    </div>
                </li>
            `;
        });

        $('#insumos-lista').html(listaHTML);
    }

    // Manejar la búsqueda en tiempo real
    $('#search-input').on('input', function() {
        var filtro = $(this).val();
        mostrarInsumosFiltrados(filtro);
        if (filtro) {
            $('#insumos-filtrados').show();
        } else {
            $('#insumos-filtrados').hide();
        }
    });

    // Inicializar la lista con todos los insumos al cargar la página
    // mostrarInsumosFiltrados('');

    $(document).on('click', '.agregar-insumo', function() {
        var insumoId = $(this).data('id');
        var insumoNombre = $(this).data('nombre');
        var insumoUnidad = $(this).data('medida');
        var insumoPrecio = $(this).data('precio_unidad');
        var partePostre = $(this).data('parte_postre');
        console.log("PARTE POSTREEEEEEEE:" + partePostre);

        // Colores definidos
        var js_color = {
            "BASE": "#F4A300", // Un tono naranja cálido para la base
            "RELLENO": "#F0C419", // Un amarillo suave para el relleno
            "COBERTURA": "#D85B5B", // Un rojo suave para la cobertura
            "ENVASE": "#4A90E2", // Un azul claro para el envase
        };

        // Verificar si el partePostre existe como una clave en el objeto js_color
        var color = js_color[partePostre.toUpperCase()] || "#FFFFFF"; // Si no existe, usa blanco por defecto

        ///////////////////////////////////////////////////////
        var cantidad = $('#cantidad-' + insumoId).val();
        ////////////////////////////////////////////////////////

        // Verificar si se ingresó una cantidad válida
        if (cantidad && cantidad > 0) {
            var subtotal = (cantidad * insumoPrecio);
            // Agregar el insumo a la tabla de seleccionados
            $('#selected-items').append(
                '<tr id="' + insumoId + '">' +
                '<td>' + insumoNombre + '</td>' +
                '<td>' + cantidad + '</td>' +
                '<td>' + insumoUnidad + '</td>' +
                '<td> <span style="background-color: ' + color + '; color: white;">' + partePostre + '</span></td>' +
                '<td><strong>' + subtotal.toFixed(2) + '</strong></td>' +
                '<td><button class="btn btn-danger btn-sm eliminar-insumo" data-id="' + insumoId + '">Eliminar</button></td>' +
                '</tr>'
            );
            // Limpiar el campo de cantidad
            $('#cantidad-' + insumoId).val('');
            fn_sumar_datos_table();

        } else {
            alert('Por favor, ingresa una cantidad válida.');
        }
    });


    // Manejar el evento de eliminar insumos
    $(document).on('click', '.eliminar-insumo', function() {
        var insumoId = $(this).data('id');
        $('#insumo-' + insumoId).remove(); // Elimina la fila de la tabla
        fn_sumar_datos_table();
    });


    function fn_guardar_datos_venta_postre() {
        let selectedItems = [];

        
        $('#selected-items tr').each(function() {
        
            let id = $(this).attr('id');

            let nombre = $(this).find('td:nth-child(1)').text().trim();
            let cantidad = $(this).find('td:nth-child(2)').text().trim();
            let unidad = $(this).find('td:nth-child(3)').text().trim();
            let partePostre = $(this).find('td:nth-child(4)').text().trim();
            let subtotal = $(this).find('td:nth-child(5)').text().trim();

        
            selectedItems.push({
                id: id,
                nombre: nombre,
                cantidad: cantidad,
                unidad: unidad,
                partePostre: partePostre,
                subtotal: parseFloat(subtotal)
            });
        });

        
        console.log("DATAX REL_VENTA_POSTRE: "+selectedItems);
        console.log('DATAX REL_VENTA_POSTRE:', JSON.stringify(selectedItems, null, 2));
    }
</script>


<?php include("pie.php") ?>