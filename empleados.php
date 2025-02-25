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


    #modalCliente {
        z-index: 1060 !important;
        /* Asegúrate de que sea más alto que el de los demás modales */
    }

    /* Estilo para cambiar el color de fondo y bordes del modal */
    /* Estilo para cambiar el color de fondo y bordes del modal */
    #modalCliente .modal-content {
        background-color: rgb(255, 255, 255);
        /* Color de fondo claro (puedes cambiarlo) */
        border-radius: 10px;
        /* Bordes redondeados */
        border: 2px solid #2a2f5b;
        /* Borde azul para darle más protagonismo */
    }

    /* Agregar una sombra para resaltar más el modal */
    #modalCliente .modal-dialog {
        box-shadow: 0 4px 10px #2a2f5b;
        /* Sombra azul para resaltar el modal */
    }

    /* Título del modal más grande y con un color diferente */
    #modalCliente .modal-header {
        background-color: rgb(255, 255, 255);
        /* Fondo azul */
        color: #2a2f5b;
        /* Texto blanco */
    }

    #modalCliente .btn-close {
        background-color: #f0f8ff;
        /* Botón de cerrar rojo */
    }

    .pagination {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 5px;
        margin: 10px 0;
    }

    .pagination a {
        text-decoration: none;
        padding: 8px 12px;
        border: 1px solid #ddd;
        color: #333;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .pagination a:hover {
        background-color: #f0f0f0;
    }

    .pagination a.active {
        background-color: #007bff;
        color: white;
    }

    /* Hacer que la paginación se ajuste en pantallas pequeñas */
    @media (max-width: 768px) {
        .pagination {
            font-size: 12px;
        }

        .pagination a {
            padding: 6px 10px;
        }

        table {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .pagination {
            font-size: 10px;
        }

        .pagination a {
            padding: 5px 8px;
        }

        table {
            font-size: 12px;
        }
    }
</style>

<div
    class="container">

    <div class="page-inner">
        <div class="card text-start">

            <div class="card-body">


                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">Trabajadores</h4>
                    <button class="btn btn-success rounded-5" id="btnAbrirModalGenerico">Agregar Persona <i class="fas fa-plus"> </i></button>
                </div>
                <hr>
                <div
                    class="row justify-content-center align-items-center md-2">

                    <div class="col-sm-12">
                      
                            <div class="table-responsive">
                                <table
                                    id="multi-filter-select"
                                    class="display table table-striped table-hover">
                                    <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Trabajador</th>
                                                <th>N° de Documento</th>
                                                <th>Condicion</th>
                                                <th>Accion</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
                                            <?php
                                            foreach (listarEmpleados() as $datos) {
                                                $datosJSON = json_encode($datos);
                                            ?>
                                            <tr>
                                                    <td><?php echo $datos["id"] ?></td>
                                                    <td><?php echo $datos["empleado"] ?></td>
                                                    <td><?php echo $datos["numero_documento"] ?></td>
                                                    <td><?php echo $datos["condicion"] ?></td>
                                                    <td>
                                                        <div class="mt-2 text-center">
                                                        <a name="edit" id="edit" class="btn btn-warning btn-round ml-2"
                                                            onclick='fn_editar_usuario(<?php echo $datosJSON; ?>)' role="button">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a name="block" id="block" class="btn btn-dark btn-round ml-2"
                                                                onclick='fn_bloquear_usuario(<?php echo $datosUsuario["id"]; ?>)' role="button">
                                                                <i class="fa fa-lock"></i>
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

<!-- Modal para registrar Cliente -->

<div class="modal fade" id="modalCliente" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="contenidoUsuario">

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
    document.addEventListener("DOMContentLoaded", function() {

        document.getElementById("btnAbrirModalGenerico").addEventListener("click", function() {
            document.getElementById("contenidoUsuario").innerHTML = `

                    <div class="modal-body">
                        <div class="card text-start">
                        <div class="card-body">
                        <h5 class="card-title text-center" id="modalClienteLabel"> <i class="fas fa-user"></i> Registrar Trabajador</h5>
                        <div class="card-sub text-center">
                         Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
                        </div>

                        <div class="tab-content mt-3" id="pills-tabContent">
                            <!-- Formulario Persona -->
                            <div class="tab-pane fade show active" id="pills-persona" role="tabpanel" aria-labelledby="pills-persona-tab">
                                <div class="mb-3">
                                    <label for="numeroDocumentoPersona" class="form-label"><b>Número de Documento  <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="numeroDocumentoPersona" placeholder="Número de Documento">
                                    <div class="invalid-feedback" id="error-numeroDocumentoPersona"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="nombresPersona" class="form-label"><b>Nombres  <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="nombresPersona" placeholder="Nombres">
                                    <div class="invalid-feedback" id="error-nombresPersona"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="apellidosPersona" class="form-label"><b>Apellidos  <b><span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="apellidosPersona" placeholder="Apellidos">
                                    <div class="invalid-feedback" id="error-apellidosPersona"></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><b>Condición <span class="fw-bold text-danger">*</span></b></label>
                                    <input readonly  type="text" class="form-control" id="condicionPersona" value="EMPLEADO">
                                    <div id="error-condicionPersona" class="error-message"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="telefonoPersona" class="form-label"><b>Teléfono Móvil</b></label>
                                    <input type="text" class="form-control" id="telefonoPersona" placeholder="Teléfono Móvil">
                                    <div class="invalid-feedback" id="error-telefonoPersona"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="emailPersona" class="form-label"><b>Email</b></label>
                                    <input type="email" class="form-control" id="emailPersona" placeholder="Email">
                                    <div class="invalid-feedback" id="error-emailPersona"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="direccionPersona" class="form-label"><b>Direccion</b></label>
                                    <input type="text" class="form-control" id="direccionPersona" placeholder="Direccion">
                                    <div class="invalid-feedback" id="error-direccionPersona"></div>
                                </div>

                            </div>

                     </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Salir</button>
                        <button type="button" class="btn btn-success rounded-5" id="btnRegistrarCliente">Registrar</button>
                    </div>
            `;

            const modal = new bootstrap.Modal(document.getElementById("modalCliente"));
            modal.show();


         
            const btnRegistrarCliente = document.getElementById('btnRegistrarCliente');

            // Función para validar los campos
            function validarCamposPersona() {
                let valid = true;

                // Validar el número de documento (solo si tiene datos)
                const numeroDocumentoPersona = document.getElementById('numeroDocumentoPersona');
                const errorNumeroDocumentoPersona = document.getElementById('error-numeroDocumentoPersona');
                if (numeroDocumentoPersona.value.trim() === '') {
                    valid = false;
                    numeroDocumentoPersona.classList.add('is-invalid');
                    errorNumeroDocumentoPersona.textContent = 'El DNI es obligatorio.';
                } else if (!/^\d{8}$/.test(numeroDocumentoPersona.value)) {
                    valid = false;
                    numeroDocumentoPersona.classList.add('is-invalid');
                    errorNumeroDocumentoPersona.textContent = 'Debe ser un DNI válido (9 dígitos).';
                } else {
                    numeroDocumentoPersona.classList.remove('is-invalid');
                    errorNumeroDocumentoPersona.textContent = '';
                }

                // Validar los nombres (solo si tiene datos y sin números)
                const nombresPersona = document.getElementById('nombresPersona');
                const errorNombresPersona = document.getElementById('error-nombresPersona');
                if (nombresPersona.value.trim() == '') {
                    valid = false;
                    nombresPersona.classList.add('is-invalid');
                    errorNombresPersona.textContent = 'Los nombres es obligatorio.';
                } else if (/[^a-zA-Z\s]/.test(nombresPersona.value)) {
                    valid = false;
                    nombresPersona.classList.add('is-invalid');
                    errorNombresPersona.textContent = 'Los nombres no pueden contener números.';
                } else {
                    nombresPersona.classList.remove('is-invalid');
                    errorNombresPersona.textContent = '';
                }

                // Validar los apellidos (solo si tiene datos y sin números)
                const apellidosPersona = document.getElementById('apellidosPersona');
                const errorApellidosPersona = document.getElementById('error-apellidosPersona');
                if (apellidosPersona.value.trim() == '') {
                    valid = false;
                    apellidosPersona.classList.add('is-invalid');
                    errorApellidosPersona.textContent = 'Los apellidos es obligatorio.';
                } else if (/[^a-zA-Z\s]/.test(apellidosPersona.value)) {
                    valid = false;
                    apellidosPersona.classList.add('is-invalid');
                    errorApellidosPersona.textContent = 'Los apellidos no pueden contener números.';
                } else {
                    apellidosPersona.classList.remove('is-invalid');
                    errorApellidosPersona.textContent = '';
                }

                // Validar el teléfono (solo si tiene datos y es un número válido)
                const telefonoPersona = document.getElementById('telefonoPersona');
                const errorTelefonoPersona = document.getElementById('error-telefonoPersona');
                if (telefonoPersona.value.trim() !== '' && !/^\d{9}$/.test(telefonoPersona.value)) {
                    valid = false;
                    telefonoPersona.classList.add('is-invalid');
                    errorTelefonoPersona.textContent = 'El teléfono debe tener 9 dígitos.';
                } else {
                    telefonoPersona.classList.remove('is-invalid');
                    errorTelefonoPersona.textContent = '';
                }

                // Validar el email (solo si tiene datos y es un correo válido)
                const emailPersona = document.getElementById('emailPersona');
                const errorEmailPersona = document.getElementById('error-emailPersona');
                if (emailPersona.value.trim() !== '' && !/\S+@\S+\.\S+/.test(emailPersona.value)) {
                    valid = false;
                    emailPersona.classList.add('is-invalid');
                    errorEmailPersona.textContent = 'Debe ser un correo electrónico válido.';
                } else {
                    emailPersona.classList.remove('is-invalid');
                    errorEmailPersona.textContent = '';
                }

                const condicion = document.getElementById('condicionPersona');
                const errorCondicion = document.getElementById('error-condicionPersona');

                // Verificar si la opción seleccionada es válida
                if (condicion.value === '') {
                    valid = false; // La variable valid debe ser parte de tu lógica de validación general
                    condicion.classList.add('is-invalid');
                    errorCondicion.textContent = 'Debe seleccionar una opción válida.';
                } else {
                    condicion.classList.remove('is-invalid');
                    errorCondicion.textContent = '';
                }


                return valid;
            }


            

            btnRegistrarCliente.addEventListener('click', async function() {
                let datos = {};

                    // Recolectar los datos del formulario Persona
                    if (validarCamposPersona()) {
                        datos = {
                            "numero_documento": document.getElementById('numeroDocumentoPersona').value,
                            "nombres": document.getElementById('nombresPersona').value,
                            "apellidos": document.getElementById('apellidosPersona').value,
                            "telefono_movil": document.getElementById('telefonoPersona').value || null,
                            "email": document.getElementById('emailPersona').value,
                            "direccion": document.getElementById('direccionPersona').value,
                            "condicion": document.getElementById('condicionPersona').value

                        };

                        // Llamar a la función AJAX para registrar la persona
                        console.log(datos);
                        const response = await fnRegistrarPersona(datos);
                        console.log("Persona insertado con éxito:", response);
                        console.log(response.persona_id);

                        if (response.success === true) {
                            swal({
                                title: "Registro con Exito!",
                                text: 'Usuario Registrado  correctamente',
                                icon: "success",
                                buttons: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });

                        } else if (response.error === true) {
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



                    }
            
            });


            function fnRegistrarPersona(datos) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        method: "POST",
                        url: "logica/clssPersona.php", // El archivo PHP donde se maneja el registro de persona
                        data: {
                            "accion": "REGISTRARPERSONA", // Acción que se realiza en el backend
                            "data": JSON.stringify(datos) // Los datos de la persona como JSON
                        }
                    }).done(function(response) {
                        console.log(response);
                        const jsonResponse = JSON.parse(response); // Convertir la respuesta a JSON
                        if (jsonResponse.success) {
                            resolve(jsonResponse); // Resolvemos la promesa en caso de éxito
                        } else {
                            reject(new Error(jsonResponse.mensaje || "Error desconocido")); // Si hay error en la respuesta del servidor
                        }
                    }).fail(function(error) {
                        console.error("Error:", error.responseText);
                        reject(error); // Rechazamos la promesa si ocurre un error en la solicitud AJAX
                    });
                });
            }

         
        });
    });
</script>


<script>
    function fn_editar_usuario(datosUsuario) {
        document.getElementById("contenidoUsuario").innerHTML = `

                    <div class="modal-body">
                        <div class="card text-start">
                            <div class="card-body">
                                    <h5 class="card-title text-center" id="modalClienteLabel"> <i class="fas fa-user"></i> Editar Trabajador</h5>
                                    <div class="card-sub text-center">
                                    Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
                                    </div>


                                <div class="tab-content mt-3" id="pills-tabContent">
                                    <!-- Formulario Persona -->
                                    <div class="tab-pane fade show active" id="pills-persona" role="tabpanel" aria-labelledby="pills-persona-tab">
                                        <div class="mb-3">
                                            <label for="numeroDocumentoPersona" class="form-label"><b>Número de Documento  <span class="fw-bold text-danger">*</span></b></label>
                                            <input type="text" class="form-control" id="numeroDocumentoPersona" placeholder="Número de Documento">
                                            <div class="error-message" id="error-numeroDocumentoPersona"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nombresPersona" class="form-label"><b>Nombres  <span class="fw-bold text-danger">*</span></b></label>
                                            <input type="text" class="form-control" id="nombresPersona" placeholder="Nombres">
                                            <div class="error-message" id="error-nombresPersona"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="apellidosPersona" class="form-label"><b>Apellidos  <span class="fw-bold text-danger">*</span></b></label>
                                            <input type="text" class="form-control" id="apellidosPersona" placeholder="Apellidos">
                                            <div class="error-message" id="error-apellidosPersona"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label"><b>Condición <span class="fw-bold text-danger">*</span></b></label>
                                            <input readonly  type="text" class="form-control" id="condicionPersona" value="EMPLEADO">

                                            <div id="error-condicionPersona" class="error-message"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="telefonoPersona" class="form-label"><b>Teléfono Móvil</b></label>
                                            <input type="text" class="form-control" id="telefonoPersona" placeholder="Teléfono Móvil">
                                        </div>
                                        <div class="mb-3">
                                            <label for="emailPersona" class="form-label"><b>Email</b></label>
                                            <input type="email" class="form-control" id="emailPersona" placeholder="Email">
                                        </div>

                                        <div class="mb-3">
                                            <label for="direccionPersona" class="form-label"><b>Direccion</b></label>
                                            <input type="text" class="form-control" id="direccionPersona" placeholder="Direccion">
                                        </div>

                                    </div>

                                    
                                    <p id="txtcondicion" style="display: none;"></p>
                                </div>

                                <div class="alert alert-light p-3" role="alert">
                                    <p class="mb-0">Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success rounded-5" id="btnEditarCliente">Registrar</button>
                    </div>
            `;

        console.log(datosUsuario);
        // Rellenar el campo de estado según el valor de datosUsuario

        const modal = new bootstrap.Modal(document.getElementById("modalCliente"));
        modal.show();

        obtenerDatosUsuario(datosUsuario.id);

        function obtenerDatosUsuario(id) {
            $.ajax({
                method: "POST",
                url: "logica/clssPersona.php",
                data: {
                    "accion": "OBTENERPERSONA",
                    "id": id
                },
                success: function(response) {
                    console.log(response);
                    var result = JSON.parse(response);
                    console.log(result);

                    // Si la respuesta es exitosa
                    if (result.success === true) {
                        // Rellenar campos comunes
                        const usuario = result.data;
                        document.getElementById("txtcondicion").textContent = usuario.condicion;

                        // Mostrar y ocultar formularios según la condición
                        if (usuario.condicion === "CLIENTE" || usuario.condicion === "EMPLEADO") {
   
                            // Llenar campos de Persona
                            document.getElementById("numeroDocumentoPersona").value = usuario.numero_documento;
                            document.getElementById("nombresPersona").value = usuario.nombres;
                            document.getElementById("apellidosPersona").value = usuario.apellidos;
                            document.getElementById("telefonoPersona").value = usuario.telefonomovil;
                            document.getElementById("emailPersona").value = usuario.email;
                            document.getElementById("direccionPersona").value = usuario.direccion;


                            document.getElementById("numeroDocumentoPersona").classList.add("required");
                            document.getElementById("nombresPersona").classList.add("required");
                            document.getElementById("apellidosPersona").classList.add("required");
                            document.getElementById("condicionPersona").value = usuario.condicion;


                            document.getElementById("pills-empresa-tab").style.display = "none";
                            document.getElementById("pills-empresa").style.display = "none";

                        } 
                    } else {
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }
                },
                error: function(error) {
                    console.error("Error:", error.responseText);
                }
            });
        }

        document.getElementById("btnEditarCliente").addEventListener("click", async function() {
            if (!validarFormulario()) {
                return;
            }

            const condicion = document.getElementById("txtcondicion").textContent
            console.log(condicion)
            if (condicion === "CLIENTE" || condicion === "EMPLEADO") {
                datos = {
                    "id": datosUsuario.id,
                    "numero_documento": document.getElementById('numeroDocumentoPersona').value,
                    "nombres": document.getElementById('nombresPersona').value,
                    "apellidos": document.getElementById('apellidosPersona').value,
                    "telefono_movil": document.getElementById('telefonoPersona').value || null,
                    "email": document.getElementById('emailPersona').value,
                    "direccion": document.getElementById('direccionPersona').value,
                    "condicion": document.getElementById('condicionPersona').value

                };

                // Llamar a la función AJAX para registrar la persona
                console.log(datos);
                fnRegistrarPersona(datos);


            } 


        });

        function validarFormulario() {
            let campos = document.querySelectorAll(".required");
            let formularioValido = true;

            campos.forEach((campo) => {
                let errorMensaje = document.getElementById(`error-${campo.id}`);

                if (campo.value.trim() === "") {
                    campo.classList.add("error-input");
                    errorMensaje.textContent = "Este campo es obligatorio.";
                    formularioValido = false;
                    return false;
                } else {
                    campo.classList.remove("error-input");
                    errorMensaje.textContent = "";
                    return false;
                }
            });

            return true;
        }

        function fnRegistrarPersona(datos) {

            $.ajax({
                method: "POST",
                url: "logica/clssPersona.php", // El archivo PHP donde se maneja el registro de persona
                data: {
                    "accion": "ACTUALIZARPERSONA", // Acción que se realiza en el backend
                    "data": JSON.stringify(datos) // Los datos de la persona como JSON
                }
            }).done(function(response) {
                console.log(response);
                const jsonResponse = JSON.parse(response); // Convertir la respuesta a JSON
                if (jsonResponse.success) {
                    swal({
                        title: "Registro con Exito!",
                        text: 'Persona actualizado  correctamente',
                        icon: "success",
                        buttons: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    }); // Resolvemos la promesa en caso de éxito
                } else {
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

       




    }

    function fn_bloquear_usuario(datosUsuario) {
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
                    url: "logica/clssPersona.php",
                    data: {
                        "accion": "BLOQUEARPERSONA",
                        "id": datosUsuario
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

    function fn_desbloquear_usuario(datosUsuario) {
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
                    url: "logica/clssPersona.php",
                    data: {
                        "accion": "DESBLOQUEARPERSONA",
                        "id": datosUsuario
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

    function fn_eliminar_usuario(datosUsuario) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    method: "POST",
                    url: "logica/clssPersona.php",
                    data: {
                        "accion": "ELIMINARPERSONA",
                        "id": datosUsuario
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