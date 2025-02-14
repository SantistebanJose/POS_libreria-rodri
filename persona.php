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
/* Estilo para cambiar el color de fondo y bordes del modal */
#modalCliente .modal-content {
    background-color:rgb(255, 255, 255);  /* Color de fondo claro (puedes cambiarlo) */
    border-radius: 10px;  /* Bordes redondeados */
    border: 2px solid #2a2f5b;  /* Borde azul para darle más protagonismo */
}

/* Agregar una sombra para resaltar más el modal */
#modalCliente .modal-dialog {
    box-shadow: 0 4px 10px #2a2f5b;  /* Sombra azul para resaltar el modal */
}

/* Título del modal más grande y con un color diferente */
#modalCliente .modal-header {
    background-color:rgb(255, 255, 255);  /* Fondo azul */
    color: #2a2f5b; /* Texto blanco */
}
#modalCliente .btn-close {
    background-color: #f0f8ff;  /* Botón de cerrar rojo */
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
                    <h4 class="card-title">Personas</h4>
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
                                                <th>N° Documento</th>
                                                <th>Nombre</th>
                                                <th>CONDICION</th>
                                                <th>N° TELEFONO</th>
                                                <th>Accion</th>
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
    </div>
</div>

<!-- Modal para registrar Cliente -->

<div class="modal fade" id="modalCliente" tabindex="-1"  data-bs-backdrop="static" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
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
            processing: true, // Muestra un indicador de carga
            serverSide: true, // Activa el procesamiento en el servidor
            ajax: {
                "url": "logica/listar_personas.php", // Archivo PHP que maneja la carga de datos
                "type": "POST"
            },
            columns: [
                { "data": "id" },
                { "data": "numero_documento" },
                { "data": "nombre" },
                { "data": "condicion" },
                { "data": "telefono" },
                { "data": "acciones", "orderable": false, "searchable": false }
            ],
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
                        <h5 class="modal-title mx-auto fw-bold" id="modalClienteLabel">Registrar Persona</h5>
                    </div>
                    <div class="modal-body">
                        <!-- Pils para seleccionar entre Persona y Empresa -->
                        <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="pills-persona-tab" data-bs-toggle="pill" data-bs-target="#pills-persona" type="button" role="tab" aria-controls="pills-persona" aria-selected="true">Cliente | Empleado</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="pills-empresa-tab" data-bs-toggle="pill" data-bs-target="#pills-empresa" type="button" role="tab" aria-controls="pills-empresa" aria-selected="false">Empresa | Proveedor</button>
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
                                    <label class="form-label">Condición <span class="fw-bold text-danger">*</span></label>
                                    <select class="form-select required" id="condicionPersona">
                                        <option value="">Seleccione una opción</option>
                                        <option value="CLIENTE">CLIENTE</option>
                                        <option value="EMPLEADO">EMPLEADO</option>
                                    </select>
                                    <div id="error-condicionPersona" class="error-message"></div>
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

                                <div class="mb-3">
                                    <label for="direccionPersona" class="form-label">Direccion</label>
                                    <input type="text" class="form-control" id="direccionPersona" placeholder="Direccion">
                                    <div class="invalid-feedback" id="error-direccionPersona"></div>
                                </div>

                            </div>

                            <!-- Formulario Empresa -->
                            <div class="tab-pane fade" id="pills-empresa" role="tabpanel" aria-labelledby="pills-empresa-tab">
                                <div class="mb-3">
                                    <label for="numeroDocumentoEmpresa" class="form-label">Número de Ruc  <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="numeroDocumentoEmpresa" placeholder="Número de Documento">
                                    <div class="invalid-feedback" id="error-numeroDocumentoEmpresa"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="nombreComercial" class="form-label">Nombre Comercial  <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombreComercial" placeholder="Nombre Comercial">
                                    <div class="invalid-feedback" id="error-nombreComercial"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="razonSocial" class="form-label">Razón Social  <span class="fw-bold text-danger">*</span> </label>
                                    <input type="text" class="form-control" id="razonSocial" placeholder="Razón Social">
                                    <div class="invalid-feedback" id="error-razonSocial"></div>
                                </div>

                                 <div class="mb-3">
                                    <label class="form-label">Condición <span class="fw-bold text-danger">*</span></label>
                                    <select class="form-select required" id="condicionEmpresa">
                                        <option value="">Seleccione una opción</option>
                                        <option value="EMPRESA">EMPRESA</option>
                                        <option value="PROVEEDOR">PROVEEDOR</option>
                                    </select>
                                    <div id="error-condicionEmpresa" class="error-message"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="emailEmpresa" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="emailEmpresa" placeholder="Email">
                                    <div class="invalid-feedback" id="error-emailEmpresa"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="telefonoEmpresa" class="form-label">Teléfono Móvil</label>
                                    <input type="text" class="form-control" id="telefonoEmpresa" placeholder="Teléfono Móvil">
                                    <div class="invalid-feedback" id="error-telefonoEmpresa"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="direccionEmpresa" class="form-label">Direccion</label>
                                    <input type="text" class="form-control" id="direccionEmpresa" placeholder="Direccion">
                                    <div class="invalid-feedback" id="error-direccionEmpresa"></div>
                                </div>

                               

                            </div>
                        </div>

                        <div class="alert alert-light p-3" role="alert">
                            <p class="mb-0">Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Salir</button>
                        <button type="button" class="btn btn-success rounded-5" id="btnRegistrarCliente">Registrar</button>
                    </div>
            `;

            const modal = new bootstrap.Modal(document.getElementById("modalCliente"));
            modal.show();

            const personaTab = document.getElementById("pills-persona-tab");
            const empresaTab = document.getElementById("pills-empresa-tab");

            personaTab.addEventListener('click', () => {
                // Limpiar datos de la pestaña Empresa
                document.getElementById('numeroDocumentoEmpresa').value = '';
                document.getElementById('nombreComercial').value = '';
                document.getElementById('razonSocial').value = '';
                document.getElementById('telefonoEmpresa').value = '';
                document.getElementById('emailEmpresa').value = '';
                document.getElementById('direccionEmpresa').value = '';
                document.getElementById('condicionEmpresa').selectedIndex = 0;

                resetErrors();
            });

            empresaTab.addEventListener('click', () => {
                // Limpiar datos de la pestaña Persona
                document.getElementById('numeroDocumentoPersona').value = '';
                document.getElementById('nombresPersona').value = '';
                document.getElementById('apellidosPersona').value = '';
                document.getElementById('telefonoPersona').value = '';
                document.getElementById('emailPersona').value = '';
                document.getElementById('direccionPersona').value = '';
                document.getElementById('condicionPersona').selectedIndex = 0;

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
                document.getElementById('numeroDocumentoEmpresa').value = '';
                document.getElementById('nombreComercial').value = '';
                document.getElementById('razonSocial').value = '';
                document.getElementById('telefonoEmpresa').value = '';
                document.getElementById('emailEmpresa').value = '';
                document.getElementById('numeroDocumentoPersona').value = '';
                document.getElementById('nombresPersona').value = '';
                document.getElementById('apellidosPersona').value = '';
                document.getElementById('telefonoPersona').value = '';
                document.getElementById('emailPersona').value = '';
            }

            // Seleccionando los elementos de los formularios
            const formPersona = document.getElementById('pills-persona');
            const formEmpresa = document.getElementById('pills-empresa');

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

                const condicion = document.getElementById('condicionPersona');
                const errorCondicion = document.getElementById('error-condicionPersona');

                // Verificar si la opción seleccionada es válida
                if (condicion.value === '') {
                    valid = false;  // La variable valid debe ser parte de tu lógica de validación general
                    condicion.classList.add('is-invalid');
                    errorCondicion.textContent = 'Debe seleccionar una opción válida.';
                } else {
                    condicion.classList.remove('is-invalid');
                    errorCondicion.textContent = '';
                }


                return valid;
            }


            function validarCamposEmpresa() {
                let valid = true;

                // Validar RUC (solo si tiene datos)
                const numeroDocumentoEmpresa = document.getElementById('numeroDocumentoEmpresa');
                const errorNumeroDocumentoEmpresa = document.getElementById('error-numeroDocumentoEmpresa');
                if (numeroDocumentoEmpresa.value.trim() === '') {
                    valid = false;
                    numeroDocumentoEmpresa.classList.add('is-invalid');
                    errorNumeroDocumentoEmpresa.textContent = 'El RUC es obligatorio.';
                } else if (!/^\d{11}$/.test(numeroDocumentoEmpresa.value)) {
                    valid = false;
                    numeroDocumentoEmpresa.classList.add('is-invalid');
                    errorNumeroDocumentoEmpresa.textContent = 'Debe ser un RUC válido (11 dígitos).';
                } else {
                    numeroDocumentoEmpresa.classList.remove('is-invalid');
                    errorNumeroDocumentoEmpresa.textContent = '';
                }

                // Validar nombre comercial (solo si tiene datos)
                const nombreComercial = document.getElementById('nombreComercial');
                const errorNombreComercial = document.getElementById('error-nombreComercial');
                if (nombreComercial.value.trim() == '' ) {
                    valid = false;
                    nombreComercial.classList.add('is-invalid');
                    errorNombreComercial.textContent = 'Este campo es obligatorio.';
                } else {
                    nombreComercial.classList.remove('is-invalid');
                    errorNombreComercial.textContent = '';
                }

                // Validar razón social (solo si tiene datos)
                const razonSocial = document.getElementById('razonSocial');
                const errorRazonSocial = document.getElementById('error-razonSocial');
                if (razonSocial.value.trim() == '' ) {
                    valid = false;
                    razonSocial.classList.add('is-invalid');
                    errorRazonSocial.textContent = 'Este campo es obligatorio.';
                } else {
                    razonSocial.classList.remove('is-invalid');
                    errorRazonSocial.textContent = '';
                }

                // Validar teléfono (solo si tiene datos)
                const telefonoEmpresa = document.getElementById('telefonoEmpresa');
                const errorTelefonoEmpresa = document.getElementById('error-telefonoEmpresa');
                if (telefonoEmpresa.value.trim() !== '' && !/^\d{9}$/.test(telefonoEmpresa.value)) {
                    valid = false;
                    telefonoEmpresa.classList.add('is-invalid');
                    errorTelefonoEmpresa.textContent = 'El teléfono debe tener 9 dígitos.';
                } else {
                    telefonoEmpresa.classList.remove('is-invalid');
                    errorTelefonoEmpresa.textContent = '';
                }

                // Validar email (solo si tiene datos)
                const emailEmpresa = document.getElementById('emailEmpresa');
                const errorEmailEmpresa = document.getElementById('error-emailEmpresa');
                if (emailEmpresa.value.trim() !== '' && !/\S+@\S+\.\S+/.test(emailEmpresa.value)) {
                    valid = false;
                    emailEmpresa.classList.add('is-invalid');
                    errorEmailEmpresa.textContent = 'Debe ser un correo electrónico válido.';
                } else {
                    emailEmpresa.classList.remove('is-invalid');
                    errorEmailEmpresa.textContent = '';
                }

                const condicion = document.getElementById('condicionEmpresa');
                const errorCondicion = document.getElementById('error-condicionEmpresa');

                // Verificar si la opción seleccionada es válida
                if (condicion.value === '') {
                    valid = false;  // La variable valid debe ser parte de tu lógica de validación general
                    condicion.classList.add('is-invalid');
                    errorCondicion.textContent = 'Debe seleccionar una opción válida.';
                } else {
                    condicion.classList.remove('is-invalid');
                    errorCondicion.textContent = '';
                }

                return valid;
            }

            btnRegistrarCliente.addEventListener('click', async function () {
                let datos = {};
                
                if (document.getElementById('pills-persona-tab').classList.contains('active')) {
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
                                text: 'Usuario actualizado  correctamente',
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
                } else if (document.getElementById('pills-empresa-tab').classList.contains('active')) {
                    // Recolectar los datos del formulario Empresa
                    if (validarCamposEmpresa()) {
                        datos = {
                            "numero_documento": document.getElementById('numeroDocumentoEmpresa').value,
                            "nombre_comercial": document.getElementById('nombreComercial').value,
                            "razon_social": document.getElementById('razonSocial').value,
                            "telefono_movil": document.getElementById('telefonoEmpresa').value,
                            "email": document.getElementById('emailEmpresa').value,
                            "condicion": document.getElementById('condicionEmpresa').value
                               
                        };

                        console.log(datos);
                        // Llamar a la función AJAX para registrar la empresa
                        const response = await fnRegistrarEmpresa(datos);
                        console.log("Empresa insertado con éxito:", response);
                        if (response.success === true) {
                            swal({
                                title: "Registro con Exito!",
                                text: 'Usuario actualizado  correctamente',
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
                }
            });


            function fnRegistrarPersona(datos) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        method: "POST",
                        url: "logica/clssPersona.php",  // El archivo PHP donde se maneja el registro de persona
                        data: {
                            "accion": "REGISTRARPERSONA",  // Acción que se realiza en el backend
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

            function fnRegistrarEmpresa(datos) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        method: "POST",
                        url: "logica/clssPersona.php",  // El archivo PHP donde se maneja el registro de empresa
                        data: {
                            "accion": "REGISTRARPERSONA",  // Acción que se realiza en el backend
                            "data": JSON.stringify(datos)  // Los datos de la empresa como JSON
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
    });

</script>


<script>
    function fn_editar_usuario(datosUsuario) {
        document.getElementById("contenidoUsuario").innerHTML = `
           <div class="modal-header">
                        <h5 class="modal-title mx-auto fw-bold" id="modalClienteLabel">Editar Persona</h5>
                    </div>
                    <div class="modal-body">
                        <!-- Pils para seleccionar entre Persona y Empresa -->
                        <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="pills-persona-tab" data-bs-toggle="pill" data-bs-target="#pills-persona" type="button" role="tab" aria-controls="pills-persona" aria-selected="true">Cliente | Empleado</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="pills-empresa-tab" data-bs-toggle="pill" data-bs-target="#pills-empresa" type="button" role="tab" aria-controls="pills-empresa" aria-selected="false">Empresa | Proveedor</button>
                            </li>
                        </ul>
                        <hr>
                        <div class="tab-content mt-3" id="pills-tabContent">
                            <!-- Formulario Persona -->
                            <div class="tab-pane fade show active" id="pills-persona" role="tabpanel" aria-labelledby="pills-persona-tab">
                                <div class="mb-3">
                                    <label for="numeroDocumentoPersona" class="form-label">Número de Documento  <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="numeroDocumentoPersona" placeholder="Número de Documento">
                                    <div class="error-message" id="error-numeroDocumentoPersona"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="nombresPersona" class="form-label">Nombres  <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombresPersona" placeholder="Nombres">
                                    <div class="error-message" id="error-nombresPersona"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="apellidosPersona" class="form-label">Apellidos  <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="apellidosPersona" placeholder="Apellidos">
                                    <div class="error-message" id="error-apellidosPersona"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Condición <span class="fw-bold text-danger">*</span></label>
                                    <select class="form-select required" id="condicionPersona">
                                        <option value="">Seleccione una opción</option>
                                        <option value="CLIENTE">CLIENTE</option>
                                        <option value="EMPLEADO">EMPLEADO</option>
                                    </select>
                                    <div id="error-condicionPersona" class="error-message"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="telefonoPersona" class="form-label">Teléfono Móvil</label>
                                    <input type="text" class="form-control" id="telefonoPersona" placeholder="Teléfono Móvil">
                                </div>
                                <div class="mb-3">
                                    <label for="emailPersona" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="emailPersona" placeholder="Email">
                                </div>

                                <div class="mb-3">
                                    <label for="direccionPersona" class="form-label">Direccion</label>
                                    <input type="text" class="form-control" id="direccionPersona" placeholder="Direccion">
                                </div>

                            </div>

                            <!-- Formulario Empresa -->
                            <div class="tab-pane fade" id="pills-empresa" role="tabpanel" aria-labelledby="pills-empresa-tab">
                                <div class="mb-3">
                                    <label for="numeroDocumentoEmpresa" class="form-label">Número de Ruc  <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="numeroDocumentoEmpresa" placeholder="Número de Documento">
                                    <div class="error-message" id="error-numeroDocumentoEmpresa"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="nombreComercial" class="form-label">Nombre Comercial  <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombreComercial" placeholder="Nombre Comercial">
                                    <div class="error-message" id="error-nombreComercial"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="razonSocial" class="form-label">Razón Social  <span class="fw-bold text-danger">*</span> </label>
                                    <input type="text" class="form-control" id="razonSocial" placeholder="Razón Social">
                                    <div class="error-message" id="error-razonSocial"></div>
                                </div>

                                 <div class="mb-3">
                                    <label class="form-label">Condición <span class="fw-bold text-danger">*</span></label>
                                    <select class="form-select required" id="condicionEmpresa">
                                        <option value="">Seleccione una opción</option>
                                        <option value="EMPRESA">EMPRESA</option>
                                        <option value="PROVEEDOR">PROVEEDOR</option>
                                    </select>
                                    <div id="error-condicionEmpresa" class="error-message"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="emailEmpresa" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="emailEmpresa" placeholder="Email">
                                </div>
                                <div class="mb-3">
                                    <label for="telefonoEmpresa" class="form-label">Teléfono Móvil</label>
                                    <input type="text" class="form-control" id="telefonoEmpresa" placeholder="Teléfono Móvil">
                                </div>
                                <div class="mb-3">
                                    <label for="direccionEmpresa" class="form-label">Direccion</label>
                                    <input type="text" class="form-control" id="direccionEmpresa" placeholder="Direccion">

                                </div>

                               

                            </div>
                            <p id="txtcondicion" style="display: none;"></p>
                        </div>

                        <div class="alert alert-light p-3" role="alert">
                            <p class="mb-0">Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.</p>
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
                        if (usuario.condicion === "CLIENTE" || usuario.condicion === "EMPLEADO" ) {
                            // Mostrar pestaña de Persona
                            document.getElementById("pills-persona-tab").classList.add("active");
                            document.getElementById("pills-empresa-tab").classList.remove("active");
                            document.getElementById("pills-persona").classList.add("show", "active");
                            document.getElementById("pills-empresa").classList.remove("show", "active");

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

                        } else if (usuario.condicion === "PROVEEDOR" || usuario.condicion === "EMPRESA") {
                            // Mostrar pestaña de Empresa
                            document.getElementById("pills-empresa-tab").classList.add("active");
                            document.getElementById("pills-persona-tab").classList.remove("active");
                            document.getElementById("pills-empresa").classList.add("show", "active");
                            document.getElementById("pills-persona").classList.remove("show", "active");

                            // Llenar campos de Empresa
                            document.getElementById("numeroDocumentoEmpresa").value = usuario.numero_documento;
                            document.getElementById("nombreComercial").value = usuario.nombre_comercial;
                            document.getElementById("razonSocial").value = usuario.razon_social;
                            document.getElementById("emailEmpresa").value = usuario.email;
                            document.getElementById("telefonoEmpresa").value = usuario.telefonomovil;
                            document.getElementById("direccionEmpresa").value = usuario.direccion;
                            document.getElementById("condicionEmpresa").value = usuario.condicion;

                            document.getElementById("numeroDocumentoEmpresa").classList.add("required");
                            document.getElementById("nombreComercial").classList.add("required");
                            document.getElementById("razonSocial").classList.add("required");


                            document.getElementById("pills-persona-tab").style.display = "none";
                            document.getElementById("pills-persona").style.display = "none";

                            // Mostrar el botón para cambiar a la pestaña de Empresa
                            document.getElementById("pills-empresa-tab").style.display = "block";
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
                    alert("Error al obtener los datos del usuario.");
                }
            });
        }

        document.getElementById("btnEditarCliente").addEventListener("click", async function () {
            if(!validarFormulario()){
                return;
            }

            const condicion = document.getElementById("txtcondicion").textContent
            console.log(condicion)
            if (condicion === "CLIENTE" || condicion === "EMPLEADO" ) {
                datos = {
                    "id":datosUsuario.id,
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

                
            }else if(condicion === "PROVEEDOR" || condicion === "EMPRESA"){
                datos = {
                    "id":datosUsuario.id,
                    "numero_documento": document.getElementById('numeroDocumentoEmpresa').value,
                    "nombre_comercial": document.getElementById('nombreComercial').value,
                    "razon_social": document.getElementById('razonSocial').value,
                    "telefono_movil": document.getElementById('telefonoEmpresa').value,
                    "email": document.getElementById('emailEmpresa').value,
                    "direccion": document.getElementById('direccionEmpresa').value,
                    "condicion": document.getElementById('condicionEmpresa').value


                };

                console.log(datos);
                // Llamar a la función AJAX para registrar la empresa
                fnRegistrarEmpresa(datos);


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
                url: "logica/clssPersona.php",  // El archivo PHP donde se maneja el registro de persona
                data: {
                    "accion": "ACTUALIZARPERSONA",  // Acción que se realiza en el backend
                    "data": JSON.stringify(datos)  // Los datos de la persona como JSON
                }
            }).done(function (response) {
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
                    });  // Resolvemos la promesa en caso de éxito
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
            }).fail(function (error) {
                console.error("Error:", error.responseText);
                alert("Error al registrar el usuario.");
            });
           
        }

        function fnRegistrarEmpresa(datos) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    method: "POST",
                    url: "logica/clssPersona.php",  // El archivo PHP donde se maneja el registro de empresa
                    data: {
                        "accion": "ACTUALIZARPERSONA",  // Acción que se realiza en el backend
                        "data": JSON.stringify(datos)  // Los datos de la empresa como JSON
                    }
                }).done(function (response) {
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
                        });  // Resolvemos la promesa en caso de éxito
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
                }).fail(function (error) {
                    console.error("Error:", error.responseText);
                    alert("Error al registrar el usuario.");
                });
            });
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
                    url: "logica/clssPersona.php",
                    data: {
                        "accion": "BLOQUEARPERSONA",
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
                    url: "logica/clssPersona.php",
                    data: {
                        "accion": "DESBLOQUEARPERSONA",
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
                    url: "logica/clssPersona.php",
                    data: {
                        "accion": "ELIMINARPERSONA",
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