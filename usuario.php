<?php
include("cabecera.php");
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
  
.error-input {
        border: 2px solid red;
    }

    .error-message {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
    }


    #modalCliente {
    z-index: 1060 !important; /* Asegúrate de que sea más alto que el de los demás modales */
}

/* Estilo para cambiar el color de fondo y bordes del modal */
#modalCliente .modal-content {
    background-color: #f0f8ff;  /* Color de fondo claro (puedes cambiarlo) */
    border-radius: 10px;  /* Bordes redondeados */
    border: 2px solid #2a2f5b;  /* Borde azul para darle más protagonismo */
}

/* Agregar una sombra para resaltar más el modal */
#modalCliente .modal-dialog {
    box-shadow: 0 4px 10px #2a2f5b;  /* Sombra azul para resaltar el modal */
}

/* Título del modal más grande y con un color diferente */
#modalCliente .modal-header {
    background-color: #2a2f5b;  /* Fondo azul */
    color: white;  /* Texto blanco */
}
#modalCliente .btn-close {
    background-color: #f0f8ff;  /* Botón de cerrar rojo */
}


   
</style>

<div
    class="container">

    <div class="page-inner">
        <div class="card text-start">

            <div class="card-body">


                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">Usuarios</h4>
                    <button class="btn btn-success rounded-5" id="btnAbrirModalGenerico"> Agregar Usuario <i class="fas fa-plus"> </i></button>
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
                                                <th>USERNAME</th>
                                                <th>ROL</th>
                                                <th>PERSONA</th>
                                                <th>ESTADO</th>
                                                <th>Accion</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>ID</th>
                                                <th>USERNAME</th>
                                                <th>ROL</th>
                                                <th>PERSONA</th>
                                                <th>ESTADO</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>

                                            <?php
                                            foreach (listarUsuarios() as $datosUsuario) {
                                                $datosUsuarioJSON = json_encode($datosUsuario);


                                            ?>
                                                <tr>
                                                    <td><?php echo $datosUsuario["id"] ?></td>
                                                    <td><?php echo $datosUsuario["username"] ?></td>
                                                    <td><?php echo $datosUsuario["rol"] ?></td>
                                                    <td><?php echo $datosUsuario["persona_concatenada"] ?></td>
                                                    <td><?php echo $datosUsuario["estado"] ?></td>
                                                    <th>
                                                        <div class="mt-2 text-center">
                                                            <!-- Botón de Agregar -->

                                                            <!-- Botón de Editar (con ícono amarillo) -->
                                                            <a name="edit" id="edit" class="btn btn-warning btn-round ml-2" 
                                                            onclick='fn_editar_usuario(<?php echo $datosUsuarioJSON; ?>)' role="button">
                                                                <i class="fa fa-edit"></i>
                                                            </a>

                                                            <!-- Botón de Activar/Bloquear -->
                                                            <?php if ($datosUsuario["estado"]  == 'ACTIVO') { ?>
    <!-- Botón para bloquear -->
                                                                <a name="block" id="block" class="btn btn-dark btn-round ml-2"
                                                                    onclick='fn_bloquear_usuario(<?php echo $datosUsuario["id"]; ?>)' role="button">
                                                                    <i class="fa fa-lock"></i> 
                                                                </a>
                                                            <?php } else { ?>
                                                                <!-- Botón para activar -->
                                                                <a name="activate" id="activate" class="btn btn-secondary btn-round ml-2"
                                                                    onclick='fn_desbloquear_usuario(<?php echo $datosUsuario["id"]; ?>)' role="button">
                                                                    <i class="fa fa-unlock"></i> 
                                                                </a>
                                                            <?php } ?>

                                                            <!-- Botón de Eliminar -->
                                                            <a name="delete" id="delete" class="btn btn-danger btn-round ml-2" 
                                                            onclick='fn_eliminar_usuario(<?php echo $datosUsuario["id"]; ?>)' role="button">
                                                                <i class="fa fa-trash"></i> 
                                                            </a>
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

<!-- Modal para registrar Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalClienteLabel">Registrar Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Pils para seleccionar entre Persona y Empresa -->
                <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="pills-persona-tab" data-bs-toggle="pill" data-bs-target="#pills-persona" type="button" role="tab" aria-controls="pills-persona" aria-selected="true">Empleado</button>
                    </li>
                   
                </ul>
                <hr>
                <div class="tab-content mt-3" id="pills-tabContent">
                    <!-- Formulario Persona -->
                    <div class="tab-pane fade show active" id="pills-persona" role="tabpanel" aria-labelledby="pills-persona-tab">
                        <div class="mb-3">
                            <label for="numeroDocumentoPersona" class="form-label">Número de Documento  <span class="fw-bold text-danger">*</span></label>
                            <input type="text" class="form-control" id="numeroDocumentoPersona" placeholder="Número de Documento">
                            <div class="invalid-feedback" id="error-numeroDocumentoPersona"></div>
                        </div>
                        <div class="mb-3">
                            <label for="nombresPersona" class="form-label">Nombres  <span class="fw-bold text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombresPersona" placeholder="Nombres">
                            <div class="invalid-feedback" id="error-nombresPersona"></div>
                        </div>
                        <div class="mb-3">
                            <label for="apellidosPersona" class="form-label">Apellidos  <span class="fw-bold text-danger">*</span></label>
                            <input type="text" class="form-control" id="apellidosPersona" placeholder="Apellidos">
                            <div class="invalid-feedback" id="error-apellidosPersona"></div>
                        </div>
                        <div class="mb-3">
                            <label for="telefonoPersona" class="form-label">Teléfono Móvil</label>
                            <input type="text" class="form-control" id="telefonoPersona" placeholder="Teléfono Móvil">
                            <div class="invalid-feedback" id="error-telefonoPersona"></div>
                        </div>
                        <div class="mb-3">
                            <label for="emailPersona" class="form-label">Email</label>
                            <input type="email" class="form-control" id="emailPersona" placeholder="Email">
                            <div class="invalid-feedback" id="error-emailPersona"></div>
                        </div>
                    </div>

                    <!-- Formulario Empresa -->
                    
                </div>

                <div class="alert alert-light p-3" role="alert">
    <p class="mb-0">Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.</p>
    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="btnRegistrarCliente">Registrar</button>
                </div>
            </div>
        </div>
</div>

<div class="modal fade" id="modalUsuario" tabindex="-1"  data-bs-backdrop="static" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
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
    document.addEventListener("DOMContentLoaded", function () {

        document.getElementById("btnAbrirModalGenerico").addEventListener("click", function () {
            document.getElementById("contenidoUsuario").innerHTML = `
                <div class="modal-header">
                    <h5 class="modal-title mx-auto fw-bold">Registrar Usuario</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Persona <span class="fw-bold text-danger">*</span></label>
                        <span id="idPersona">#</span>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control required" id="nombreCliente" placeholder="AGREGAR EL NOMBRE DEL EMPLEADO O DNI" />
                            <button type="button" class="btn btn-primary ms-2 rounded-5" id="btnAbrirModalCliente">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div id="sugerencias" class="list-group position-absolute w-100"></div>

                        <div id="error-nombreCliente" class="error-message"></div>
                    </div>
                    <div class="row justify-content-center align-items-center sm-2">
                        <div class="col-sm-12 mb-3">
                                <label for="nombreUsuario">Nombre de Usuario <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control required" id="nombreUsuario" placeholder="name@example.com" />
                                <div id="error-nombreUsuario" class="error-message"></div>
                          
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label for="contrasena">Contraseña <span class="fw-bold text-danger">*</span></label>
                            <input type="password" class="form-control required" id="contrasena" placeholder="********" />
                            <div id="error-contrasena" class="error-message"></div>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <div class="mb-3">
                                <label class="form-label">Rol <span class="fw-bold text-danger">*</span></label>
                                <select class="form-select required" id="rol">
                                    <option value="">Seleccione una opción</option>
                                    <option value="1">Administrador</option>
                                    <option value="2">Empleado</option>
                                </select>
                                <div id="error-rol" class="error-message"></div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-light p-3" role="alert">
                        <p class="mb-0">Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success rounded-5" id="btnRegistrarUsuario">Registrar</button>
                </div>
            `;

            const modal = new bootstrap.Modal(document.getElementById("modalUsuario"));
            modal.show();

            // Agregar evento de validación al botón "Registrar"
    
            document.getElementById("btnRegistrarUsuario").addEventListener("click", async function () {
                if(!validarFormulario()){
                    return;
                }

                const username = document.getElementById("nombreUsuario").value
                const contraseña = document.getElementById("contrasena").value
                const rol = document.getElementById("rol").value
                const persona_id = parseInt(document.getElementById("idPersona").textContent)

                const datos = {
                    "username":username,
                    "contraseña":contraseña,
                    "rol":rol,
                    "persona_id":persona_id
                };
                console.log(datos);

                $.ajax({
                    method: "POST",
                    url: "logica/clssUsuario.php",
                    data: {
                        "accion": "REGISTRARUSUARIO",
                        "data": JSON.stringify(datos)
                    }
                }).done(function (response) {

                    var result = JSON.parse(response);
                    console.log(response);

                    // Verificar si el resultado contiene éxito o error
                    if (result.success === true) {
                        swal({
                            title: "Registro con Exito!",
                            text: 'Usuario registrado correctamente',
                            icon: "success",
                            buttons: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });

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

                }).fail(function (error) {
                    console.error("Error:", error.responseText);
                    alert("Error al registrar el usuario.");
                });
                                

            });
    
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
    });

</script>

<script>
    document.addEventListener("input", function (event) {
    if (event.target && event.target.id === "nombreCliente") {
        const nombreCliente = event.target;
        const sugerencias = document.getElementById("sugerencias");
        const persona_id = document.getElementById("idPersona");

        const query = nombreCliente.value.trim();
        console.log(query);

        if (query.length > 0) {
            $.ajax({
                method: "POST",
                url: "logica/clssFiltro.php",
                data: {
                    "accion": "FILTROEMPLEADO",
                    "data": query
                }
            }).done(function (response) {
                try {
                    console.log(response);
                    const resultados = JSON.parse(response);
                    sugerencias.innerHTML = "";

                    if (resultados.length > 0) {
                        resultados.forEach(persona => {
                            const item = document.createElement("div");
                            item.classList.add("list-group-item");
                            item.textContent = persona.persona_concatenada;

                            item.addEventListener("click", function () {
                                nombreCliente.value = persona.persona_concatenada;
                                persona_id.textContent = persona.id;
                                sugerencias.innerHTML = "";
                            });

                            sugerencias.appendChild(item);
                        });
                    } else {
                        const noResults = document.createElement("div");
                        noResults.classList.add("list-group-item", "text-muted");
                        noResults.textContent = "Sin resultados";
                        sugerencias.appendChild(noResults);
                    }
                } catch (e) {
                    console.error("Error al procesar los resultados:", e);
                    sugerencias.innerHTML = "";
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
                sugerencias.innerHTML = "";
            });
        } else {
            sugerencias.innerHTML = "";
        }
    }
    });

    // Para cerrar sugerencias si se hace clic fuera
    document.addEventListener("click", function (e) {
        const nombreCliente = document.getElementById("nombreCliente");
        const sugerencias = document.getElementById("sugerencias");
        
        if (sugerencias && nombreCliente && !nombreCliente.contains(e.target) && !sugerencias.contains(e.target)) {
            sugerencias.innerHTML = "";
        }
    });


</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        console.log("El script ha cargado correctamente");
          // Lógica para borrar los datos cuando se cambia entre los Pills
        const personaTab = document.getElementById("pills-persona-tab");

        // Agrega un evento click para abrir el modal manualmente
        document.body.addEventListener("click", function (event) {
            // Verifica si el clic fue en el botón o en cualquier parte del botón (incluido el icono dentro de él)
            if (event.target && event.target.closest("#btnAbrirModalCliente")) {
                console.log("¡Clic en el botón o icono del botón dinámico!");
                
                // Muestra el modal
                const modalCliente = new bootstrap.Modal(document.getElementById("modalCliente"));
                modalCliente.show();  // Muestra el modal manualmente
            }
        });

        personaTab.addEventListener('click', () => {
            // Limpiar datos de la pestaña Empresa
            document.getElementById('numeroDocumentoEmpresa').value = '';
            document.getElementById('nombreComercial').value = '';
            document.getElementById('razonSocial').value = '';
            document.getElementById('telefonoEmpresa').value = '';
            document.getElementById('emailEmpresa').value = '';
            resetErrors();
        });

       
        function resetErrors() {
            // Limpiar las clases 'is-invalid' y los mensajes de error
            const inputs = document.querySelectorAll('.form-control');
            const errorMessages = document.querySelectorAll('.invalid-feedback');

            inputs.forEach(input => {
                input.classList.remove('is-invalid');
            });

            errorMessages.forEach(message => {
                message.textContent = '';
            });
        }

        function limpiarcampos(){
            document.getElementById('numeroDocumentoPersona').value = '';
            document.getElementById('nombresPersona').value = '';
            document.getElementById('apellidosPersona').value = '';
            document.getElementById('telefonoPersona').value = '';
            document.getElementById('emailPersona').value = '';
        }

        // Seleccionando los elementos de los formularios
        const formPersona = document.getElementById('pills-persona');

        const btnRegistrarCliente = document.getElementById('btnRegistrarCliente');
        console.log("botn",btnRegistrarCliente);

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
                errorNumeroDocumentoPersona.textContent = 'Debe ser un DNI válido (11 dígitos).';
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
            }else if(/[^a-zA-Z\s]/.test(nombresPersona.value)){
                valid = false;
                nombresPersona.classList.add('is-invalid');
                errorNombresPersona.textContent = 'Los nombres no pueden contener números.';
            } 
            else {
                nombresPersona.classList.remove('is-invalid');
                errorNombresPersona.textContent = '';
            }

            // Validar los apellidos (solo si tiene datos y sin números)
            const apellidosPersona = document.getElementById('apellidosPersona');
            const errorApellidosPersona = document.getElementById('error-apellidosPersona');
            if (apellidosPersona.value.trim() == '' ) {
                valid = false;
                apellidosPersona.classList.add('is-invalid');
                errorApellidosPersona.textContent = 'Los apellidos es obligatorio.';
            } else if(/[^a-zA-Z\s]/.test(apellidosPersona.value)){
                valid = false;
                apellidosPersona.classList.add('is-invalid');
                errorApellidosPersona.textContent = 'Los apellidos no pueden contener números.';
            }else {
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

            return valid;
        }

       

        // Registrar cliente
        btnRegistrarCliente.addEventListener('click', async function () {
            let datos = {};
            console.log("click")
            
            if (document.getElementById('pills-persona-tab').classList.contains('active')) {
                // Recolectar los datos del formulario Persona
                if (validarCamposPersona()) {
                    datos = {
                        "numero_documento": document.getElementById('numeroDocumentoPersona').value,
                        "nombres": document.getElementById('nombresPersona').value,
                        "apellidos": document.getElementById('apellidosPersona').value,
                        "telefono_movil": document.getElementById('telefonoPersona').value || null,
                        "email": document.getElementById('emailPersona').value
                    };

                    // Llamar a la función AJAX para registrar la persona
                    console.log(datos);
                    const response = await fnRegistrarPersona(datos);
                    console.log("Persona insertado con éxito:", response);
                    const nombreencadenado = `${document.getElementById('numeroDocumentoPersona').value} - ${document.getElementById('nombresPersona').value} ${document.getElementById('apellidosPersona').value}`;
                    console.log(nombreencadenado);
                    console.log(response.persona_id);


                    
                    enviardatos(response.persona_id,nombreencadenado);
                    limpiarcampos();
                    showNotification("success");


                    modalCliente.hide();

                } else {
                    alert('Por favor, corrige los errores antes de registrar.');
                }
            } 
        });

        function enviardatos(id_persona,nombre,numero_celular,correo){
            document.getElementById('idPersona').textContent = id_persona
            document.getElementById('nombreCliente').value = nombre
           
        }

        function fnRegistrarPersona(datos) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    method: "POST",
                    url: "logica/clssPersona.php",  // El archivo PHP donde se maneja el registro de persona
                    data: {
                        "accion": "REGISTRARPERSONAEMPLEADO",  // Acción que se realiza en el backend
                        "data": JSON.stringify(datos)  // Los datos de la persona como JSON
                    }
                }).done(function (response) {
                    console.log(response);
                    const jsonResponse = JSON.parse(response); // Convertir la respuesta a JSON
                    if (jsonResponse.success) {
                        resolve(jsonResponse);  // Resolvemos la promesa en caso de éxito
                    } else {
                        reject(new Error(jsonResponse.mensaje || "Error desconocido"));  // Si hay error en la respuesta del servidor
                    }
                }).fail(function (error) {
                    console.error("Error:", error.responseText);
                    reject(error);  // Rechazamos la promesa si ocurre un error en la solicitud AJAX
                });
            });
        }

       


        
    });
</script>

<script>
    function fn_editar_usuario(datosUsuario) {
        document.getElementById("contenidoUsuario").innerHTML = `
            <div class="modal-header">
                <h5 class="modal-title mx-auto fw-bold">Editar Usuario</h5>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center align-items-center sm-2">
                    <div class="col-sm-12 mb-3">
                        <label for="nombreUsuario">Nombre de Usuario <span class="fw-bold text-danger">*</span></label>
                        <input type="text" class="form-control required" id="nombreUsuario" placeholder="name@example.com" value="${datosUsuario.username}" />
                        <div id="error-nombreUsuario" class="error-message"></div>
                
                    </div>
                    <div class="col-sm-12 mb-3">
                            <label for="cambiarContrasena">Cambiar Contraseña</label>
                            <input type="password" class="form-control" id="cambiarContrasena" placeholder="********" />
                            <div id="error-cambiarContrasena" class="error-message"></div>
                     
                    </div>
                    <div class="col-sm-12 mb-3">
                        <div class="mb-3">
                            <label class="form-label">Rol <span class="fw-bold text-danger">*</span></label>
                            <select class="form-select required" id="rol">
                                <option value="">Seleccione una opción</option>
                                <option value="1" ${datosUsuario.rol === "ADMINISTRADOR" ? 'selected' : ''}>Administrador</option>
                                <option value="2" ${datosUsuario.rol === "EMPLEADO" ? 'selected' : ''}>Empleado</option>
                            </select>
                            <div id="error-rol" class="error-message"></div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-light p-3" role="alert">
                    <p class="mb-0">Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success rounded-5" id="btnEditarUsuario">Editar</button>
            </div>
        `;

        // Rellenar el campo de estado según el valor de datosUsuario

        const modal = new bootstrap.Modal(document.getElementById("modalUsuario"));
        modal.show();

        document.getElementById("btnEditarUsuario").addEventListener("click", async function () {
            if(!validarFormulario()){
                return;
            }

            const username = document.getElementById("nombreUsuario").value
            const contraseña = document.getElementById("cambiarContrasena").value
            const rol = document.getElementById("rol").value

            const datos = {
                "id": datosUsuario.id,
                "username":username,
                "contraseña":contraseña,
                "rol":rol,
            };
            console.log(datos);

            $.ajax({
                method: "POST",
                url: "logica/clssUsuario.php",
                data: {
                    "accion": "EDITARRUSUARIO",
                    "data": JSON.stringify(datos)
                }
            }).done(function (response) {

                var result = JSON.parse(response);
                console.log(response);

                // Verificar si el resultado contiene éxito o error
                if (result.success === true) {
                    swal({
                        title: "Registro con Exito!",
                        text: 'Usuario actualizado  correctamente',
                        icon: "success",
                        buttons: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });

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

            }).fail(function (error) {
                console.error("Error:", error.responseText);
                alert("Error al registrar el usuario.");
            });
                            

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
    }

    function fn_bloquear_usuario(datosUsuario){
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
                    url: "logica/clssUsuario.php",
                    data: {
                        "accion": "BLOQUEARUSUARIO",
                        "id": datosUsuario
                    }
                }).done(function (response) {

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

                }).fail(function (error) {
                    console.error("Error:", error.responseText);
                    alert("Error al registrar el usuario.");
                });
            }
        });
    }

    function fn_desbloquear_usuario(datosUsuario){
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
                    url: "logica/clssUsuario.php",
                    data: {
                        "accion": "DESBLOQUEARUSUARIO",
                        "id": datosUsuario
                    }
                }).done(function (response) {

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

                }).fail(function (error) {
                    console.error("Error:", error.responseText);
                    alert("Error al registrar el usuario.");
                });
                            
                
            }
        });
    }

    function fn_eliminar_usuario(datosUsuario){
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
                    url: "logica/clssUsuario.php",
                    data: {
                        "accion": "ELIMINARUSUARIO",
                        "id": datosUsuario
                    }
                }).done(function (response) {

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

                }).fail(function (error) {
                    console.error("Error:", error.responseText);
                    alert("Error al registrar el usuario.");
                });
            }
        });
    }

</script>

<?php
include("pie.php");
?>