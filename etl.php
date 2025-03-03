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
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function fn_abrir_swal() {
        $.ajax({
            url: 'logica/clssConsultas.php', // Ruta del archivo PHP que maneja el proceso
            type: 'POST',
            data: {
                accion: "EJECUTARETL", // Acción que identifica el proceso ETL
                EJECUTARETL: "EJECUTARETL" // Cualquier parámetro adicional que necesites
            },
            dataType: 'json',
            success: function(data) {
                // Si la respuesta es 'REALIZADO', mostramos éxito
                if (data.respuesta === "REALIZADO") {
                    Swal.fire({
                        title: '¡Proceso completado!',
                        text: 'Los datos fueron migrados exitosamente.',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    });
                }
                // Si la respuesta es 'ERROR', mostramos error
                else if (data.respuesta === "ERROR") {
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un error al ejecutar el proceso.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function(xhr, status, error) {
                // Si la petición AJAX falla (error en la conexión)
                Swal.fire({
                    title: 'Error de Conexión',
                    text: 'Hubo un error al intentar conectar con el servidor.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
        let timerInterval;

        // Abrir el modal de SweetAlert2
        Swal.fire({
            title: "Ejecutando proceso...",
            html: "Por favor, espere. El proceso se está ejecutando. <b></b> ms restantes.",
            timer: 5000, // Ajusta el tiempo total para el proceso
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");

                // Actualizar el temporizador cada 100ms
                timerInterval = setInterval(() => {
                    timer.textContent = `${Swal.getTimerLeft()}`;
                }, 100);
            },
            willClose: () => {
                // Detener el temporizador cuando el modal se cierre
                clearInterval(timerInterval);
            }
        }).then((result) => {
            // Aquí iniciamos la validación antes de continuar con la ejecución del proceso
            let validacionExitosa = validarProceso();

            if (!validacionExitosa) {
                // Si la validación falla, mostramos un error y detenemos el proceso
                Swal.fire({
                    title: 'Error',
                    text: 'No se puede ejecutar el proceso en este momento. Verifique los datos.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                // Si la validación es exitosa, hacemos la petición AJAX para ejecutar el proceso

            }
        });
    }
</script>

<?php
include("pie.php");
?>