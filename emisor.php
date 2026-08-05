<?php
include("cabecera.php");

?>

<div class="container">
    <div class="page-inner">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title" id="modalClienteLabel"> <i class="fab fa-staylinked"></i> Datos del Emisor del Facturardor SUNAT</h5>
                <div class="card-sub">
                    Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
                </div>

                <div class="tab-content mt-3" id="pills-tabContent">
                    <!-- Formulario Persona -->
                    <div class="tab-pane fade show active" id="pills-persona" role="tabpanel" aria-labelledby="pills-persona-tab">
                        <div class="row justify-content-center align-items-center g-2">

                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>RUC <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="idRuc" value="<?php echo fnListadoDeEmisor()[0]["ruc"] ?>" disabled>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Razón Social <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="idRazonSocial" value="<?php echo fnListadoDeEmisor()[0]["razon_social"] ?>" disabled>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Nombre Comercial <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="idNombreComercial" value="<?php echo fnListadoDeEmisor()[0]["nombre_comercial"] ?>" disabled>
                                </div>
                            </div>

                            <hr>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="horasPersona" class="form-label"><b>Usuario SOL <span class="fw-bold text-danger">*</span></b></label>
                                    <div class="input-group">
                                        <!-- Input de Usuario SOL -->
                                        <input type="password" class="form-control" id="idUsuarioSol" value="<?php echo fnListadoDeEmisor()[0]["usuario_sol"] ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="diasPersona" class="form-label"><b>Clave Sol <span class="fw-bold text-danger">*</span></b></label>
                                    <div class="input-group">
                                        <!-- Input de Clave Sol -->
                                        <input type="password" class="form-control" id="idClaveSol" value="<?php echo fnListadoDeEmisor()[0]["clave_sol"] ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón para alternar entre mostrar y ocultar las contraseñas -->
                            <button type="button" id="togglePassword" class="btn btn-link mt-2">Mostrar</button>

                            <script>
                                // Obtenemos los campos de las contraseñas
                                const passwordFields = document.querySelectorAll("#idUsuarioSol, #idClaveSol");
                                const togglePasswordButton = document.getElementById("togglePassword");

                                togglePasswordButton.addEventListener("click", function() {
                                    // Iteramos por ambos campos de contraseña y cambiamos el tipo
                                    passwordFields.forEach(function(field) {
                                        const type = field.type === "password" ? "text" : "password";
                                        field.type = type;
                                    });

                                    // Cambiamos el texto del botón entre "Mostrar" y "Ocultar"
                                    togglePasswordButton.textContent = togglePasswordButton.textContent === "Mostrar" ? "Ocultar" : "Mostrar";
                                });
                            </script>

                            <hr>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Departamento <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="idDepartamento" value="<?php echo fnListadoDeEmisor()[0]["departamento"] ?>" disabled>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label"><b>Provincia <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="idProvincia" value="<?php echo fnListadoDeEmisor()[0]["provincia"] ?>" disabled>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Distrito</b></label>
                                    <input type="text" class="form-control" id="idDistrito" value="<?php echo fnListadoDeEmisor()[0]["distrito"] ?>" disabled>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Ubigeo</b></label>
                                    <input type="number" class="form-control" id="idUbigeo" value="<?php echo fnListadoDeEmisor()[0]["ubigeo"] ?>" disabled>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Dirección Fiscal <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="idDireccion" value="<?php echo fnListadoDeEmisor()[0]["direccion"] ?>" disabled>
                                </div>
                            </div>

                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="mb-3">
                                <label for="" class="form-label"><b>Teléfono</b></label>
                                <input type="text" class="form-control" id="idTelefono" value="<?php echo fnListadoDeEmisor()[0]["telefono"] ?>" disabled>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="mb-3">
                                <label for="" class="form-label"><b>Correo Electrónico</b></label>
                                <input type="email" class="form-control" id="idCorreo" value="<?php echo fnListadoDeEmisor()[0]["correo_electronico"] ?>" disabled>
                            </div>
                        </div>

                        <br>
                        <div class="col-12 text-center">
                            <div class="d-flex justify-content-center gap-2">

                                <button id="idBtnHabilitar" class="btn btn-warning btn-round text" onclick="habilitarCampos()" role="button">Habilitar Cambios</button>


                                <a style="display: none;" name="" id="idBtneGuardar" class="btn btn-success btn-round text" onclick="fn_guardar_cambios()" role="button"> <i class="fas fa-save"></i> Guardar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include("pie.php");
?>

<script>
    function habilitarCampos() {
        // Obtener todos los inputs del formulario
        const inputs = document.querySelectorAll("#pills-persona input");
        document.getElementById("idBtneGuardar").style.display = "block";

        // Recorrer todos los inputs y quitar el atributo "disabled"
        inputs.forEach(input => {
            input.disabled = false;
        });
    }

    function fn_guardar_cambios() {
        var datos_emisor = {
            "ruc": document.getElementById("idRuc").value,
            "razon_social": document.getElementById("idRazonSocial").value,
            "nombre_comercial": document.getElementById("idNombreComercial").value,
            "usuario_sol": document.getElementById("idUsuarioSol").value,
            "clave_sol": document.getElementById("idClaveSol").value,
            "departamento": document.getElementById("idDepartamento").value,
            "provincia": document.getElementById("idProvincia").value,
            "distrito": document.getElementById("idDistrito").value,
            "ubigeo": document.getElementById("idUbigeo").value,
            "direccion": document.getElementById("idDireccion").value,
            "telefono": document.getElementById("idTelefono").value,        // 👈 nuevo
            "correo_electronico": document.getElementById("idCorreo").value  // 👈 nuevo
        }
        console.log(datos_emisor)
        $.ajax({
            url: 'logica/clssInsertPA.php',
            type: 'POST',
            data: {
                accion: 'EDITAR_EMISOR',
                jsDatos: JSON.stringify(datos_emisor)
            },
            success: function(response) {
                console.log("Respuesta del servidor: ", response);
                try {
                    var result = JSON.parse(response);
                    if (result.estado === true) {
                        swal({
                            title: "Emisor SUNAT con Exito!",
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
        });

    }
</script>