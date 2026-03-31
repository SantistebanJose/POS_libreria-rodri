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
    $id_usuario_s = $_SESSION['id'];
    $rol = $_SESSION['rol'];
    $usuario = $_SESSION['usuario'];
    $nombre = $_SESSION['nombre'];
    $correo = $_SESSION['correo'];
    echo '<div style="text-align: center; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 20px; border-radius: 10px; font-size: 18px; font-weight: bold;">
        <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i> 
        Usuario BLOQUEADO - ' . strtoupper($nombre) . ' ' . strtoupper($ape_usuario) . ' [' . strtoupper($usuario) . '] 😞 ❌
      </div>
      <br>
      <div style="text-align: center;">
        <img src="assets/img/mebloqueaste.png" alt="Usuario Bloqueado" />
      </div>
      <br>
      <div style="text-align: center;"> <b>Comunicate con los dueños para que te den acceso</b> </div>
      ';
    exit();
} else {
    $ape_usuario = $_SESSION['ape'];
    $id_usuario_s = $_SESSION['id'];
    $rol = $_SESSION['rol'];
    $nombre = $_SESSION['nombre'];
    $correo = $_SESSION['correo'];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>LB Rodri — Sistema POS</title>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/caracoles.png" type="image/x-icon" />

    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: ["Font Awesome 5 Solid","Font Awesome 5 Regular","Font Awesome 5 Brands","simple-line-icons"],
                urls: ["assets/css/fonts.min.css"],
            },
            active: function() { sessionStorage.fonts = true; },
        });
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@700;900&family=Syne:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
    <link rel="stylesheet" href="assets/css/stylePerzo.css" />

    <style>
        :root {
            --blue:      #2251a3;
            --blue-deep: #0d2a5e;
            --yellow:    #fef97c;
            --yellow-dim: rgba(254,249,124,.15);
            --font-body: 'Syne', 'Public Sans', sans-serif;
        }

        body { font-family: var(--font-body) !important; }
        h1,h2,h3,h4,h5,h6,
        p, span.text, span.sub-item, span.title, span.subtitle,
        span.profile-username, .nav-section-label, .sidebar-footer,
        .brand-names, .brand-names *, a p { font-family: var(--font-body) !important; }

        /* ══ SIDEBAR ══ */
        .sidebar {
            background: var(--blue-deep) !important;
            border-right: none !important;
            box-shadow: 4px 0 24px rgba(13,26,62,.35) !important;
        }

        .logo-header[data-background-color="dark"] {
            background: var(--blue-deep) !important;
            border-bottom: 1px solid rgba(254,249,124,.15) !important;
            padding: 0 1rem !important;
        }

        .sidebar .logo-header { height: 64px; display: flex; align-items: center; }

        .sidebar-brand {
            display: flex; align-items: center; gap: .65rem;
            text-decoration: none; padding: .5rem 0;
        }
        .sidebar-brand .brand-icon-wrap {
            width: 36px; height: 36px; background: var(--yellow);
            border-radius: 10px; display: grid; place-items: center; flex-shrink: 0;
        }
        .sidebar-brand .brand-icon-wrap i { color: var(--blue-deep); font-size: 1rem; }
        .sidebar-brand .brand-names { line-height: 1.15; }
        .sidebar-brand .brand-names .main-name {
            display: block; font-family: 'Fraunces', serif !important;
            font-size: .88rem; font-weight: 900; color: #fff; white-space: nowrap;
        }
        .sidebar-brand .brand-names .sub-name {
            display: block; font-size: .62rem; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
            color: var(--yellow); opacity: .85;
        }

        .nav-section-label {
            padding: 1.2rem 1.2rem .4rem; font-size: .6rem; font-weight: 700;
            letter-spacing: .14em; text-transform: uppercase; color: rgba(255,255,255,.28);
        }

        .sidebar-wrapper .nav-secondary > .nav-item > a {
            display: flex; align-items: center; gap: .75rem;
            padding: .65rem 1.2rem !important; margin: .1rem .7rem;
            border-radius: 10px; color: rgba(255,255,255,.65) !important;
            font-size: .82rem; font-weight: 500;
            transition: background .18s, color .18s !important; position: relative;
        }

        .sidebar-wrapper .nav-secondary > .nav-item > a i.fas,
        .sidebar-wrapper .nav-secondary > .nav-item > a i.fab,
        .sidebar-wrapper .nav-secondary > .nav-item > a i.far {
            width: 32px; height: 32px; background: rgba(255,255,255,.07);
            border-radius: 8px; display: flex; align-items: center;
            justify-content: center; font-size: .85rem; flex-shrink: 0;
            transition: background .18s, color .18s;
        }

        .sidebar-wrapper .nav-secondary > .nav-item > a:hover,
        .sidebar-wrapper .nav-secondary > .nav-item.active > a {
            background: var(--yellow-dim) !important; color: #fff !important;
        }

        .sidebar-wrapper .nav-secondary > .nav-item > a:hover i.fas,
        .sidebar-wrapper .nav-secondary > .nav-item > a:hover i.fab,
        .sidebar-wrapper .nav-secondary > .nav-item > a:hover i.far,
        .sidebar-wrapper .nav-secondary > .nav-item.active > a i.fas,
        .sidebar-wrapper .nav-secondary > .nav-item.active > a i.fab,
        .sidebar-wrapper .nav-secondary > .nav-item.active > a i.far {
            background: var(--yellow) !important; color: var(--blue-deep) !important;
        }

        .sidebar-wrapper .nav-secondary > .nav-item.active > a::before {
            content: ''; position: absolute; left: -.7rem; top: 50%;
            transform: translateY(-50%); width: 3px; height: 24px;
            background: var(--yellow); border-radius: 0 3px 3px 0;
        }

        .sidebar-wrapper .nav-secondary > .nav-item > a .caret {
            margin-left: auto; font-size: .65rem; opacity: .5;
        }

        .nav-collapse .nav-item a, .nav-collapse li a {
            padding: .42rem 1rem .42rem 3.2rem !important; font-size: .78rem;
            color: rgba(255,255,255,.5) !important; border-radius: 8px;
            margin: .05rem .7rem; display: block; transition: color .15s, background .15s;
        }
        .nav-collapse .nav-item a:hover, .nav-collapse li a:hover {
            color: var(--yellow) !important; background: rgba(254,249,124,.06) !important;
        }
        .sub-item { font-size: .78rem; }

        .sidebar-footer {
            position: sticky; bottom: 0; padding: .8rem 1.2rem;
            border-top: 1px solid rgba(255,255,255,.07);
            background: var(--blue-deep);
            display: flex; align-items: center; justify-content: space-between;
        }
        .sidebar-footer .sf-label { font-size: .62rem; color: rgba(255,255,255,.25); font-weight: 500; }
        .sidebar-footer .sf-captain { font-size: .62rem; font-weight: 700; color: var(--yellow); opacity: .7; }

        /* ══ NAVBAR — WHITE ══ */
        .main-header .navbar {
            background: #fff !important;
            border-bottom: 2px solid #eaeff8 !important;
            padding: 0 1.5rem !important;
            height: 64px;
            box-shadow: 0 2px 16px rgba(13,26,62,.08) !important;
        }

        .main-header-logo .logo-header {
            background: var(--blue-deep) !important;
            height: 64px;
        }

        .quick-actions-header {
            background: linear-gradient(135deg, var(--blue-deep) 0%, var(--blue) 100%) !important;
            border-radius: 10px 10px 0 0;
        }

        /* ── Info chips ── */
        .info-chips {
            display: flex; align-items: center; gap: .5rem; flex-wrap: nowrap;
        }

        .info-chips .chip {
            display: inline-flex; align-items: center; gap: .35rem;
            font-size: .78rem; font-weight: 600; border-radius: 8px;
            padding: .28rem .75rem; white-space: nowrap; text-decoration: none;
            background: #f0f2f5;
            border: 1.5px solid #c8cdd8;
            color: #2b3a5a;
            transition: background .18s, border-color .18s, transform .15s;
        }
        .info-chips .chip i { font-size: .75rem; color: var(--blue); }
        .info-chips .chip:hover {
            background: #e2e8f4;
            border-color: var(--blue);
            transform: translateY(-1px);
        }

        /* WhatsApp chip — keep green */
        .info-chips .c-wa {
            background: #eafaf2;
            border-color: #a3dfc0;
            color: #1a7a45;
        }
        .info-chips .c-wa i { color: #25d366; }
        .info-chips .c-wa:hover { background: #d4f4e4; border-color: #25d366; }

        /* Tienda Online chip */
        .info-chips .c-store {
            background: var(--blue);
            border-color: var(--blue-deep);
            color: var(--yellow);
            font-weight: 700;
        }
        .info-chips .c-store i { color: var(--yellow); }
        .info-chips .c-store:hover { background: var(--blue-deep); border-color: #000; }

        /* divider */
        .info-chips .chip-div {
            width: 1px; height: 22px;
            background: #c8cdd8; margin: 0 .15rem; flex-shrink: 0;
        }

        /* social buttons */
        .info-chips .s-btn {
            width: 30px; height: 30px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: .76rem; text-decoration: none; flex-shrink: 0;
            border: 1.5px solid transparent;
            transition: transform .15s, filter .15s, box-shadow .15s;
        }
        .info-chips .s-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,.2); }
        .info-chips .s-btn.fb { background: #1877f2; color: #fff; border-color: #1255b3; }
        .info-chips .s-btn.ig { background: linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); color: #fff; }
        .info-chips .s-btn.wa { background: #25d366; color: #fff; border-color: #1da851; }
        .info-chips .s-btn.tk { background: #010101; color: #fff; border-color: #333; }

        /* ── User pill ── */
        .topbar-user .dropdown-toggle.profile-pic {
            display: flex; align-items: center; gap: .55rem;
            background: #f4f6fb; border: 1.5px solid #e2e8f4;
            border-radius: 999px; padding: .3rem .85rem .3rem .3rem !important;
            transition: border-color .2s, box-shadow .2s;
        }
        .topbar-user .dropdown-toggle.profile-pic:hover {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(34,81,163,.1);
        }
        .topbar-user .avatar-sm {
            width: 34px; height: 34px; border-radius: 50%;
            background: #e8edf8; border: 2px solid var(--blue);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; flex-shrink: 0;
        }
        .topbar-user .avatar-sm img { width: 100%; height: 100%; object-fit: cover; }
        .topbar-user .profile-username .op-7 {
            color: #9aa3be; font-size: .73rem; display: block; line-height: 1;
        }
        .topbar-user .profile-username .fw-bold {
            color: var(--blue-deep); font-size: .85rem; font-weight: 700;
        }

        /* ── Action buttons ── */
        .topbar-nav-btn {
            width: 36px; height: 36px;
            background: #f4f6fb; border: 1.5px solid #e2e8f4;
            border-radius: 10px; display: grid; place-items: center;
            padding: 0; text-decoration: none;
            transition: background .2s, border-color .2s;
        }
        .topbar-nav-btn:hover { background: var(--blue); border-color: var(--blue); }
        .topbar-nav-btn i { color: var(--blue); font-size: .95rem; }
        .topbar-nav-btn:hover i { color: #fff; }

        /* user dropdown */
        .dropdown-user .user-box {
            padding: 1rem 1.2rem; display: flex; gap: .8rem; align-items: center;
            background: linear-gradient(135deg, var(--blue-deep), var(--blue));
            border-radius: 10px 10px 0 0;
        }
        .dropdown-user .user-box h4 { color: #fff; font-size: .9rem; margin: 0 0 .2rem; font-weight: 700; }
        .dropdown-user .user-box p  { color: var(--yellow) !important; font-size: .72rem; margin: 0; word-break: break-all; }
        .dropdown-user .avatar-lg img { width: 48px; height: 48px; border-radius: 12px; border: 2px solid var(--yellow); }

        .btn-toggle { color: #fff !important; }

        /* scrollbar */
        .sidebar-wrapper::-webkit-scrollbar { width: 4px; }
        .sidebar-wrapper::-webkit-scrollbar-track { background: transparent; }
        .sidebar-wrapper::-webkit-scrollbar-thumb { background: rgba(254,249,124,.2); border-radius: 4px; }

    </style>
</head>

<body>
<div class="wrapper">

    <!-- ════════ SIDEBAR ════════ -->
    <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
            <div class="logo-header" data-background-color="dark">
                <a href="index.php" class="logo sidebar-brand">
                    <div class="brand-icon-wrap"><i class="fas fa-book-open"></i></div>
                    <div class="brand-names">
                        <span class="main-name">LB Rodri</span>
                        <span class="sub-name">Sistema POS</span>
                    </div>
                </a>
                <div class="nav-toggle" style="margin-left:auto;">
                    <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                    <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
                </div>
                <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
            </div>
        </div>

        <div class="sidebar-wrapper scrollbar scrollbar-inner">
            <div class="sidebar-content">
                <ul class="nav nav-secondary">

                    <?php if ($rol === '1') { ?>
                    <div class="nav-section-label">Administración</div>

                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#dashboard" class="collapsed" aria-expanded="false">
                            <i class="fas fa-cog"></i><p>Administrador</p><span class="caret"></span>
                        </a>
                        <div class="collapse" id="dashboard">
                            <ul class="nav nav-collapse">
                                <li><a href="Empleados.php"><span class="sub-item">Trabajadores</span></a></li>
                                <li><a href="usuario.php"><span class="sub-item">Usuarios</span></a></li>
                                <li><a href="persona.php"><span class="sub-item">Personas</span></a></li>
                                <li><a href="articulos.php"><span class="sub-item">Artículos</span></a></li>
                                <li><a href="mantenimiento.php"><span class="sub-item">Mantenimientos</span></a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#compras" class="collapsed" aria-expanded="false">
                            <i class="fas fa-store-alt"></i><p>Negocio</p><span class="caret"></span>
                        </a>
                        <div class="collapse" id="compras">
                            <ul class="nav nav-collapse">
                                <li><a href="compra.php"><span class="sub-item">Gestionar de Compras</span></a></li>
                                <li><a href="cajaChica.php"><span class="sub-item">Caja Chica</span></a></li>
                                <li><a href="manejoCaja.php"><span class="sub-item">Manejo de Caja</span></a></li>
                                <li><a href="deudas_proveedores.php"><span class="sub-item" style="color:#fca5a5;">💳 Deudas a Proveedores</span></a></li>

                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#facturador_sunat" class="collapsed" aria-expanded="false">
                            <i class="fab fa-stripe-s"></i><p>Facturador SUNAT</p><span class="caret"></span>
                        </a>
                        <div class="collapse" id="facturador_sunat">
                            <ul class="nav nav-collapse">
                                <li><a href="emisor.php"><span class="sub-item">Datos de Emisor</span></a></li>
                                <li><a href="listVentasForPagosSunat.php"><span class="sub-item">Declarar Comprobantes a SUNAT</span></a></li>
                                <li><a href="listComprobantesDeclarados.php"><span class="sub-item">Comprobantes Declarados</span></a></li>
                                <li><a href="comprobantes_no_declarados.php"><span class="sub-item" style="color:#ff6b6b;">Comprobantes NO Declarados</span></a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#etl" class="collapsed" aria-expanded="false">
                            <i class="fas fa-file-powerpoint"></i><p>Datos</p><span class="caret"></span>
                        </a>
                        <div class="collapse" id="etl">
                            <ul class="nav nav-collapse">
                                <li><a href="etl.php"><span class="sub-item">ETL para Power BI</span></a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#deuda" class="collapsed" aria-expanded="false">
                            <i class="fas fa-user-lock"></i><p>Crédito</p><span class="caret"></span>
                        </a>
                        <div class="collapse" id="deuda">
                            <ul class="nav nav-collapse">
                                <li><a href="pagoCredito.php"><span class="sub-item">Realizar Abono a Crédito</span></a></li>
                                <li><a href="historialClientes.php"><span class="sub-item">Historial de Clientes</span></a></li>
                            </ul>
                        </div>
                    </li>
                    <?php } ?>

                    <div class="nav-section-label">Operaciones</div>

                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#reserva" class="collapsed" aria-expanded="false">
                            <i class="fas fa-toolbox"></i><p>Reserva</p><span class="caret"></span>
                        </a>
                        <div class="collapse" id="reserva">
                            <ul class="nav nav-collapse">
                                <li><a href="venta_reserva_corte.php"><span class="sub-item">Materiales / Corte / Ploteo / Impresión / Escaneo</span></a></li>
                                <li><a href="venta_corte_material.php"><span class="sub-item">Atención de reservas</span></a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#reservaweb" class="collapsed" aria-expanded="false">
                            <i class="fas fa-cloud-download-alt"></i><p>Reserva WEB</p><span class="caret"></span>
                        </a>
                        <div class="collapse" id="reservaweb">
                            <ul class="nav nav-collapse">
                                <li><a href="listadoWeb.php"><span class="sub-item">Listado de Reserva por la Web</span></a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#venta" class="collapsed" aria-expanded="false">
                            <i class="fas fa-cart-plus"></i><p>Venta</p><span class="caret"></span>
                        </a>
                        <div class="collapse" id="venta">
                            <ul class="nav nav-collapse">
                                <li><a href="venta_rapida_v2.php"><span class="sub-item">Punto de Venta Rápida</span></a></li>
                                <li><a href="listadoVenta.php"><span class="sub-item">Listado de Ventas</span></a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#pago" class="collapsed" aria-expanded="false">
                            <i class="fas fa-credit-card"></i><p>Pago</p><span class="caret"></span>
                        </a>
                        <div class="collapse" id="pago">
                            <ul class="nav nav-collapse">
                                <li><a href="listadoPagos.php"><span class="sub-item">Listado de Pagos</span></a></li>
                            </ul>
                        </div>
                    </li>

                </ul>

                <div class="sidebar-footer">
                    <span class="sf-label">© LB Rodri 2023</span>
                    <span class="sf-captain">⚓ Captain</span>
                </div>
            </div>
        </div>
    </div>
    <!-- End Sidebar -->

    <!-- ════════ MAIN PANEL ════════ -->
    <div class="main-panel">
        <div class="main-header">

            <div class="main-header-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="index.php" class="logo sidebar-brand" style="gap:.5rem;">
                        <div class="brand-icon-wrap" style="width:30px;height:30px;background:var(--yellow);border-radius:8px;display:grid;place-items:center;">
                            <i class="fas fa-book-open" style="color:var(--blue-deep);font-size:.8rem;"></i>
                        </div>
                        <span style="color:#fff;font-size:.82rem;font-weight:700;">LB Rodri</span>
                    </a>
                    <div class="nav-toggle" style="margin-left:auto;">
                        <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                        <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
                    </div>
                    <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                </div>
            </div>

            <!-- ── TOP NAVBAR (WHITE) ── -->
            <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                <div class="container-fluid">

                    <!-- LEFT: info chips -->
                    <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                        <div class="info-chips">
                            <span class="chip c-hora"><i class="fas fa-clock"></i> 6:20 A.M. – 8:00 P.M.</span>
                            <span class="chip c-dir"><i class="fas fa-map-marker-alt"></i> La Curva de Tranca Sasape, Mórrope</span>
                            <a href="mailto:libreriabazarrodri@gmail.com" class="chip c-mail"><i class="fas fa-envelope"></i> libreriabazarrodri@gmail.com</a>
                            <a href="https://wa.me/51917428886" target="_blank" class="chip c-wa"><i class="fab fa-whatsapp"></i> +51 917 428 886</a>
                            <a href="https://libreria-rodri-store.onrender.com/" target="_blank" class="chip c-store"><i class="fas fa-store"></i> Tienda Online</a>
                            <div class="chip-div"></div>
                            <a href="https://www.facebook.com/profile.php?id=61557382597979" target="_blank" class="s-btn fb" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://www.instagram.com/libreria.bazar.rodri" target="_blank" class="s-btn ig" title="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://wa.me/51917428886" target="_blank" class="s-btn wa" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                            <a href="https://www.tiktok.com/@libreria.bazar.rodri" target="_blank" class="s-btn tk" title="TikTok"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </nav>

                    <!-- RIGHT: actions + user -->
                    <ul class="navbar-nav topbar-nav ms-md-auto align-items-center" style="gap:.5rem;">

                        <li class="nav-item">
                            <a class="topbar-nav-btn" href="index.php" title="Inicio">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>

                        <li class="nav-item topbar-icon dropdown hidden-caret">
                            <a class="nav-link topbar-nav-btn" data-bs-toggle="dropdown" href="" aria-expanded="false">
                                <i class="fas fa-layer-group"></i>
                            </a>
                            <div class="dropdown-menu quick-actions animated fadeIn">
                                <div class="quick-actions-header">
                                    <span class="title mb-1">Accesos Rápidos</span>
                                    <span class="subtitle op-7">Todo en un solo click.</span>
                                </div>
                                <div class="quick-actions-scroll scrollbar-outer">
                                    <div class="quick-actions-items">
                                        <div class="row m-0">
                                            <a class="col-6 col-md-4 p-0" href="venta_rapida_v2.php">
                                                <div class="quick-actions-item">
                                                    <div class="avatar-item bg-primary rounded-circle"><i class="fas fa-users"></i></div>
                                                    <span class="text">Venta Rápida</span>
                                                </div>
                                            </a>
                                            <a class="col-6 col-md-4 p-0" href="venta_reserva_corte.php">
                                                <div class="quick-actions-item">
                                                    <div class="avatar-item bg-success rounded-circle"><i class="fab fa-whatsapp"></i></div>
                                                    <span class="text">Venta Por Reserva</span>
                                                </div>
                                            </a>
                                            <a class="col-6 col-md-4 p-0" href="venta_corte_material.php">
                                                <div class="quick-actions-item">
                                                    <div class="avatar-item bg-secondary rounded-circle"><i class="fas fa-luggage-cart"></i></div>
                                                    <span class="text">Atender Reserva</span>
                                                </div>
                                            </a>
                                            <?php if ($rol === '1') { ?>
                                            <a class="col-6 col-md-4 p-0" href="pagoCredito.php">
                                                <div class="quick-actions-item">
                                                    <div class="avatar-item bg-dark rounded-circle"><i class="fas fa-credit-card"></i></div>
                                                    <span class="text">Pagos al Crédito</span>
                                                </div>
                                            </a>
                                            <a class="col-6 col-md-4 p-0" href="manejoCaja.php">
                                                <div class="quick-actions-item">
                                                    <div class="avatar-item bg-warning rounded-circle"><i class="fas fa-toolbox"></i></div>
                                                    <span class="text">Manejo de Caja</span>
                                                </div>
                                            </a>
                                            <a class="col-6 col-md-4 p-0" href="cajaChica.php">
                                                <div class="quick-actions-item">
                                                    <div class="avatar-item bg-info rounded-circle"><i class="fas fa-box-open"></i></div>
                                                    <span class="text">Caja Chica</span>
                                                </div>
                                            </a>
                                            <a class="col-6 col-md-4 p-0" href="deudas_proveedores.php">
                                                <div class="quick-actions-item">
                                                    <div class="avatar-item bg-danger rounded-circle"><i class="fas fa-credit-card"></i></div>
                                                    <span class="text">Deudas Proveedores</span>
                                                </div>
                                            </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item topbar-user dropdown hidden-caret">
                            <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                <div class="avatar-sm">
                                    <img src="assets/img/usuario.png" alt="..." class="avatar-img rounded-circle" />
                                </div>
                                <span class="profile-username">
                                    <span class="op-7">Hola,</span>
                                    <span class="fw-bold"><?php echo $nombre ? $nombre : 'Error'; ?></span>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-user animated fadeIn">
                                <div class="dropdown-user-scroll scrollbar-outer">
                                    <li>
                                        <div class="user-box">
                                            <div class="avatar-lg">
                                                <img src="assets/img/usuario.png" alt="profile" class="avatar-img rounded" />
                                            </div>
                                            <div class="u-text">
                                                <h4><?php echo $nombre; ?></h4>
                                                <p class="text-muted"><?php echo $correo ? $correo : 'Sin correo'; ?></p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="logica/logout.php">
                                            <i class="fas fa-sign-out-alt me-2" style="color:var(--blue);"></i> Salir
                                        </a>
                                    </li>
                                </div>
                            </ul>
                        </li>

                    </ul>
                </div>
            </nav>
            <!-- End Navbar -->
        </div>

        <br>

        <!-- ══ CONTENT GOES HERE ══ -->

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let menuItems = document.querySelectorAll(".nav-item a");
                let currentPath = window.location.pathname.split("/").pop();
                menuItems.forEach(item => {
                    let menuPath = item.getAttribute("href") ? item.getAttribute("href").split("/").pop() : "";
                    if (currentPath.includes(menuPath) && menuPath !== "") {
                        document.querySelectorAll(".nav-item").forEach(nav => nav.classList.remove("active"));
                        item.closest(".nav-item").classList.add("active");
                    }
                });
            });
        </script>