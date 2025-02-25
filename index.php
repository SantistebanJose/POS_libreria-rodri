<?php include("cabecera.php") ?>

<div class="container">
    <div class="page-inner">
        <div class="card text-start">
            <div class="card-body">
                <h3 class="fw-bold mb-3">Acceso Rápido</h3>
                <div class="card-sub">
                    Accede fácilmente a tu proceso de negocio con el menú de acceso rápido. Solo presiona un botón y serás llevado directamente a la acción.
                </div>
                <div class="row">
                    <div class="col-sm-6 col-md-4">
                        <a href="venta_rapida.php">
                            <button class="btn btn-primary btn-lg w-100">
                                <div class="icon-big text-center">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h6>Venta Rapida</h6>
                            </button>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="venta_reserva_corte.php">
                            <button class="btn btn-info btn-lg w-100">
                                <div class="icon-big text-center">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <h6>Venta Por Reserva</h6>
                            </button>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="venta_corte_material.php">
                            <button class="btn btn-secondary btn-lg w-100">
                                <div class="icon-big text-center">
                                    <i class="fas fa-luggage-cart"></i>
                                </div>
                                <h6>Atender Reserva</h6>
                            </button>
                        </a>

                    </div>
                </div>
                <br>
                <?php if ($rol === '1') { ?>
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <a href="cajaChica.php">
                                <button class="btn btn-success btn-lg w-100">
                                    <div class="icon-big text-center">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <h6>Caja Chica</h6>
                                </button>
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="manejoCaja.php">
                                <button class="btn btn-warning btn-lg w-100">
                                    <div class="icon-big text-center">
                                        <i class="fas fa-toolbox"></i>
                                    </div>
                                    <h6>Manejo de Caja</h6>
                                </button>
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="pagoCredito.php">
                                <button class="btn btn-black btn-lg w-100">
                                    <div class="icon-big text-center">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <h6>Pagos al Credito</h6>
                                </button>
                            </a>

                        </div>

                    </div>
                <?php } ?>



            </div>
        </div>


        <hr>
        <div class="row">
            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div
                                    class="icon-big text-center icon-primary bubble-shadow-small">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Visitors</p>
                                    <h4 class="card-title">1,294</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div
                                    class="icon-big text-center icon-info bubble-shadow-small">
                                    <i class="fas fa-user-check"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Subscribers</p>
                                    <h4 class="card-title">1303</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div
                                    class="icon-big text-center icon-success bubble-shadow-small">
                                    <i class="fas fa-luggage-cart"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Sales</p>
                                    <h4 class="card-title">$ 1,345</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div
                                    class="icon-big text-center icon-secondary bubble-shadow-small">
                                    <i class="far fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Order</p>
                                    <h4 class="card-title">576</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>
</div>

<?php include("pie.php") ?>