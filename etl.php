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
    // Mostrar el loading inmediatamente
    Swal.fire({
        title: "Ejecutando proceso...",
        html: "Por favor, espere. El proceso se está ejecutando.",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Hacer la petición AJAX
    $.ajax({
        url: 'logica/clssConsultas.php',
        type: 'POST',
        data: {
            accion: "EJECUTARETL",
            EJECUTARETL: "EJECUTARETL"
        },
        dataType: 'json',
        success: function(data) {
            // Cerrar el loading y mostrar el resultado
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
            // Cerrar el loading y mostrar error de conexión
            Swal.fire({
                title: 'Error de Conexión',
                text: 'Hubo un error al intentar conectar con el servidor.',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

function fnCtmreETL() {
    // Mostrar el loading inmediatamente
    Swal.fire({
        title: "Ejecutando proceso...",
        html: "Por favor, espere. El proceso se está ejecutando.",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Hacer la petición AJAX
    $.ajax({
        url: 'logica/clssConsultas.php',
        type: 'POST',
        data: {
            accion: "EJECUTARETLARTICULOSNUBE",
            EJECUTARETLARTICULOSNUBE: "EJECUTARETLARTICULOSNUBE"
        },
        dataType: 'json',
        success: function(data) {
            console.log(data);
            // Cerrar el loading y mostrar el resultado
            if (data.respuesta) {
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
            // Cerrar el loading y mostrar error de conexión
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