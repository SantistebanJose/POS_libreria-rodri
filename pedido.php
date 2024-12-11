<?php
include("cabecera.php");
include("logica/clssConsultas.php");
?>
<div class="container">
    <div class="page-inner">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Pedidos </h2>
                <p class="card-text">

                    Aquí podrás registras tus PEDIDOS. <b>RECUERDA</b>, debes de haber registrado tus postres a la venta ne la sección de postres.</p>
            </div>
            <a
                onclick="abrirModalInsertPedido()"
                type="button"
                class="btn btn-success btn-md"
                data-bs-toggle="modal"
                data-bs-target="#modal_registro_postre_venta">
                Registrar Pedidos
            </a>
        </div>


        <div class="row">
            <div class="col-md-12">
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
<div
    class="modal fade"
    id="modalInsertPedido"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="card-title">Pedido</div>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row justify-content-center align-items-center g-2">
                        <div class="col-md-4">
                            <div class="card text-start">
                                <div class="card-body">
                                    <h4 class="card-title">Datos del Cliente</h4>
                                    <p class="card-text">Body</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card text-start">
                                <div class="card-body">
                                    <h4 class="card-title">Postres a Comprar</h4>
                                    <p class="card-text">Body</p>
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
                <button type="button" class="btn btn-primary">Guardar</button>
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
    function abrirModalInsertPedido() {
        $("#modalInsertPedido").modal("show");
        console.log("Esoy Aquiii");

    }
</script>


<?php include("pie.php") ?>