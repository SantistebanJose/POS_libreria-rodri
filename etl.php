<?php
include("cabecera.php");
?>

<div class="container">
    <div class="page-inner">
        <div class="card text-start">
            <div class="card-body">
                <h4 class="card-title text-center"><i class="fas fa-file-powerpoint"></i> Migrar Datos para ser visualizados en Power BI</h4>
                <div class="card-sub text-center">
                    Darle click al botón de Ejecutar, y esperar a que le muestre el mensaje de ejecutado. Una vez ir a Power BI para actualizar sus datos y visualizar sus DashBoards.
                </div>
                <div class="text-center">
                    <a class="btn btn-success btn-round" onclick='fn_abrir_swal()' role="button">
                        <i class="fas fa-file-upload"></i> Ejecutar
                    </a>
                </div>

                <br>

                <div class="text-center">
                    <a class="btn btn-success btn-round" onclick='fnCtmreETL()' role="button">
                        <i class="fas fa-file-upload"></i> update nube
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    /////

    function fn_abrir_swal() {
        Swal.fire({
            title: "Ejecutando proceso...",
            html: "Por favor, espere. El proceso se está ejecutando.",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'logica/clssConsultas.php',
            type: 'POST',
            data: {
                accion: "EJECUTARETL",
                EJECUTARETL: "EJECUTARETL"
            },
            dataType: 'json',
            success: function(data) {
                console.log("RAW:", data, typeof data.estado, data.estado);
                if (data.respuesta === "REALIZADO") {
                    Swal.fire({
                        title: '¡Proceso completado!',
                        text: 'Los datos fueron migrados exitosamente.',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    });
                } else if (data.respuesta === "ERROR") {
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un error al ejecutar el proceso.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    title: 'Error de Conexión',
                    text: 'Hubo un error al intentar conectar con el servidor.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    }

    // ------------------------------------------------------------------
    // Ahora apunta al archivo dedicado: logica/clsEtlArticulosNube.php
    // ------------------------------------------------------------------
    function fnCtmreETL() {
        Swal.fire({
            title: "Ejecutando proceso...",
            html: "Por favor, espere. El proceso se está ejecutando.",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'logica/clsEtlArticulosNube.php',
            type: 'POST',
            data: {
                accion: "EJECUTARETLARTICULOSNUBE"
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if (data.estado === true) {
                    Swal.fire({
                        title: '¡Proceso completado!',
                        text: data.mensaje || 'Los datos fueron migrados exitosamente.',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.mensaje || 'Hubo un error al ejecutar el proceso.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
                Swal.fire({
                    title: 'Error de Conexión',
                    text: 'Hubo un error al intentar conectar con el servidor.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    }
</script>

<?php
include("pie.php");
?>