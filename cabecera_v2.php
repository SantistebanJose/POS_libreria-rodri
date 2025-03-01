<?php
include('logica/clssConsultas.php');
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$flagRespuesta = fnVerificarUsarioSession($_SESSION['id']);
if ($flagRespuesta == 0) {
    $ape_usuario = $_SESSION['ape'];
    $nombre = $_SESSION['nombre'];
    $usuario = $_SESSION['usuario'];
    echo '<div style="text-align: center; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 20px; border-radius: 10px; font-size: 18px; font-weight: bold;">
        <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i> 
        Usuario BLOQUEADO - ' . strtoupper($nombre) . ' ' . strtoupper($ape_usuario) . ' [' . strtoupper($usuario) . '] 😞 ❌
      </div>
      <br>
      <div style="text-align: center;">
        <img src="assets/img/mebloqueaste.png" alt="Usuario Bloqueado" />
      </div>
      <br>
      <div style="text-align: center;"> <b>Comunícate con los dueños para que te den acceso</b> </div>';
    exit();
} else {
    $nombre = $_SESSION['nombre'];
    $correo = $_SESSION['correo'];
    $rol = $_SESSION['rol'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>VYSAM</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" href="assets/img/kaiadmin/favicon.ico" type="image/x-icon">

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["assets/css/fonts.min.css"],
            },
            active: function () {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- Bootstrap & Custom CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css">
    <link rel="stylesheet" href="assets/css/stylePerzo.css">
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar sidebar-style-2" data-background-color="dark">
            <div class="sidebar-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="index.php" class="logo">
                        <img src="assets/img/kaiadmin/logo_light.svg" alt="Logo" class="navbar-brand" height="20">
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="gg-menu-right"></i>
                        </button>
                        <button class="btn btn-toggle sidenav-toggler">
                            <i class="gg-menu-left"></i>
                        </button>
                    </div>
                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- End Sidebar -->

        <!-- Main Content -->
        <div class="main-panel">
            <!-- Header Navbar -->
            <div class="main-header">
                <nav class="navbar navbar-header navbar-expand-lg border-bottom">
                    <div class="container-fluid">
                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <li class="nav-item">
                                <a class="btn btn-outline-light" href="index.php">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="quickAccessDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-layer-group"></i> Accesos Rápidos
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="venta_rapida.php"><i class="fas fa-shopping-cart"></i> Venta Rápida</a></li>
                                    <li><a class="dropdown-item" href="venta_reserva_corte.php"><i class="fas fa-calendar-check"></i> Venta por Reserva</a></li>
                                    <li><a class="dropdown-item" href="venta_corte_material.php"><i class="fas fa-cut"></i> Atender Reserva</a></li>
                                    <?php if ($rol === '1') { ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="pagoCredito.php"><i class="fas fa-credit-card"></i> Pagos Crédito</a></li>
                                        <li><a class="dropdown-item" href="manejoCaja.php"><i class="fas fa-cash-register"></i> Manejo de Caja</a></li>
                                        <li><a class="dropdown-item" href="cajaChica.php"><i class="fas fa-box"></i> Caja Chica</a></li>
                                    <?php } ?>
                                </ul>
                            </li>

                            <!-- Usuario -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                    <div class="avatar-sm me-2">
                                        <img src="assets/img/usuario.png" alt="Perfil" class="avatar-img rounded-circle">
                                    </div>
                                    <span class="d-none d-md-inline">Hola, <?php echo $nombre; ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Mi Perfil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="logica/logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
            <!-- Fin Header Navbar -->

            <!-- Contenido -->
            <div class="container">
                <h1 class="mt-4">Bienvenido, <?php echo $nombre; ?> 👋</h1>
                <p>Panel de administración</p>
            </div>
        </div>
        <!-- Fin Main Content -->
    </div>

    <!-- Scripts -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
