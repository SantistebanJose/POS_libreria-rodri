<?php include("cabecera.php") ?>

<style>
    /* Fondo navideño con degradado */
    .christmas-bg {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 50%, #fff9c4 100%);
        position: relative;
        overflow: hidden;
    }

    /* Efecto de nieve cayendo */
    @keyframes snowfall {
        0% {
            transform: translateY(-100vh) translateX(0);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) translateX(100px);
            opacity: 0.3;
        }
    }

    .snowflake {
        position: fixed;
        top: -10px;
        color: #b0bec5;
        font-size: 1em;
        opacity: 0.4;
        pointer-events: none;
        animation: snowfall linear infinite;
        z-index: 1;
    }

    /* Tarjeta navideña */
    .christmas-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        border: 4px solid #c41e3a;
        position: relative;
        overflow: visible;
        z-index: 10;
    }

    .christmas-card::before {
        content: "";
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        height: 8px;
        background: repeating-linear-gradient(
            90deg,
            #c41e3a 0px,
            #c41e3a 20px,
            #fff 20px,
            #fff 40px,
            #0f8a5f 40px,
            #0f8a5f 60px
        );
    }

    /* Título navideño */
    .christmas-title {
        color: #c41e3a;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        font-family: 'Georgia', serif;
        font-size: 2rem;
        font-weight: bold;
    }

    .christmas-title i {
        color: #0f8a5f;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    /* Botones navideños */
    .christmas-btn {
        position: relative;
        overflow: hidden;
        border-radius: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        margin-bottom: 15px;
        font-weight: 600;
    }

    .christmas-btn:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
    }

    .christmas-btn h6 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 10px 0;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
    }

    .christmas-btn::before {
        content: "✨";
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 1.5em;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .christmas-btn:hover::before {
        opacity: 1;
    }

    /* Colores navideños para botones */
    .btn-christmas-red {
        background: linear-gradient(135deg, #c41e3a 0%, #a01729 100%);
        border: none;
        color: white;
    }

    .btn-christmas-green {
        background: linear-gradient(135deg, #0f8a5f 0%, #0d6b4a 100%);
        border: none;
        color: white;
    }

    .btn-christmas-gold {
        background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
        border: none;
        color: white;
    }

    .btn-christmas-blue {
        background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%);
        border: none;
        color: white;
    }

    /* Iconos grandes con brillo */
    .icon-christmas {
        font-size: 3em;
        margin: 15px 0;
        filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.5));
    }

    /* Texto descriptivo navideño */
    .christmas-subtitle {
        color: #2e7d32;
        font-style: italic;
        margin-bottom: 30px;
        font-size: 1.1rem;
        font-weight: 500;
    }

    /* Decoraciones navideñas */
    .decoration {
        position: absolute;
        font-size: 3em;
        opacity: 0.15;
        pointer-events: none;
    }

    .decoration-1 { top: 20px; left: 20px; }
    .decoration-2 { top: 20px; right: 20px; }
    .decoration-3 { bottom: 20px; left: 20px; }
    .decoration-4 { bottom: 20px; right: 20px; }

    /* Animación de brillo */
    @keyframes sparkle {
        0%, 100% { opacity: 0.15; }
        50% { opacity: 0.25; }
    }

    .decoration {
        animation: sparkle 3s ease-in-out infinite;
    }

    /* Luces navideñas */
    .christmas-lights {
        display: flex;
        justify-content: space-around;
        margin-bottom: 20px;
    }

    .light {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        animation: blink 1.5s infinite;
    }

    .light:nth-child(1) { background: #ff0000; animation-delay: 0s; }
    .light:nth-child(2) { background: #00ff00; animation-delay: 0.3s; }
    .light:nth-child(3) { background: #ffff00; animation-delay: 0.6s; }
    .light:nth-child(4) { background: #0000ff; animation-delay: 0.9s; }
    .light:nth-child(5) { background: #ff00ff; animation-delay: 1.2s; }

    @keyframes blink {
        0%, 50%, 100% { opacity: 1; box-shadow: 0 0 10px currentColor; }
        25%, 75% { opacity: 0.3; box-shadow: none; }
    }
</style>

<div class="container christmas-bg" style="min-height: 100vh; padding: 30px 15px;">
    <div class="page-inner">
        <div class="card text-start christmas-card">
            <div class="card-body" style="padding: 40px; position: relative;">
                
                <!-- Decoraciones navideñas -->
                <span class="decoration decoration-1">🎄</span>
                <span class="decoration decoration-2">⭐</span>
                <span class="decoration decoration-3">🎁</span>
                <span class="decoration decoration-4">❄️</span>
                
                <!-- Luces navideñas -->
                <div class="christmas-lights">
                    <div class="light"></div>
                    <div class="light"></div>
                    <div class="light"></div>
                    <div class="light"></div>
                    <div class="light"></div>
                </div>

                <h3 class="fw-bold mb-3 christmas-title">
                    <i class="fas fa-gift"></i> 🎅 Acceso Rápido Navideño 🎄
                </h3>
                <div class="card-sub christmas-subtitle">
                    ✨ Accede fácilmente a tu proceso de negocio con el menú de acceso rápido. 
                    ¡Que tengas una feliz temporada de ventas! 🎊
                </div>
                
                <div class="row">
                    <div class="col-sm-6 col-md-4">
                        <a href="venta_rapida_v2.php" style="text-decoration: none;">
                            <button class="btn btn-christmas-red btn-lg w-100 christmas-btn">
                                <div class="icon-big text-center icon-christmas">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h6>🎄 Venta Rápida</h6>
                            </button>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="venta_reserva_corte.php" style="text-decoration: none;">
                            <button class="btn btn-christmas-green btn-lg w-100 christmas-btn">
                                <div class="icon-big text-center icon-christmas">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <h6>🎁 Venta Por Reserva</h6>
                            </button>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="venta_corte_material.php" style="text-decoration: none;">
                            <button class="btn btn-christmas-gold btn-lg w-100 christmas-btn">
                                <div class="icon-big text-center icon-christmas">
                                    <i class="fas fa-luggage-cart"></i>
                                </div>
                                <h6>⭐ Atender Reserva</h6>
                            </button>
                        </a>
                    </div>
                    
                </div>
                
                <br>
                
                <?php if ($rol === '1') { ?>
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <a href="cajaChica.php" style="text-decoration: none;">
                                <button class="btn btn-christmas-red btn-lg w-100 christmas-btn">
                                    <div class="icon-big text-center icon-christmas">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <h6>🎅 Caja Chica</h6>
                                </button>
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="manejoCaja.php" style="text-decoration: none;">
                                <button class="btn btn-christmas-green btn-lg w-100 christmas-btn">
                                    <div class="icon-big text-center icon-christmas">
                                        <i class="fas fa-toolbox"></i>
                                    </div>
                                    <h6>❄️ Manejo de Caja</h6>
                                </button>
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="pagoCredito.php" style="text-decoration: none;">
                                <button class="btn btn-christmas-gold btn-lg w-100 christmas-btn">
                                    <div class="icon-big text-center icon-christmas">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <h6>🔔 Pagos al Crédito</h6>
                                </button>
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="generador_etiquetas.php" style="text-decoration: none;">
                            <button class="btn btn-christmas-blue btn-lg w-100 christmas-btn">
                                <div class="icon-big text-center icon-christmas">
                                    <i class="fas fa-tags"></i>
                                </div>
                                <h6>🏷️ Etiquetas de Precios</h6>
                            </button>
                        </a>
                    </div>
                <?php } ?>

            </div>
        </div>

        <hr style="border-color: rgba(255, 255, 255, 0.3); margin: 40px 0;">

    </div>
</div>

<!-- Script para generar copos de nieve -->
<script>
    function createSnowflakes() {
        const snowflakeCount = 50;
        const container = document.body;

        for (let i = 0; i < snowflakeCount; i++) {
            const snowflake = document.createElement('div');
            snowflake.classList.add('snowflake');
            snowflake.innerHTML = '❄';
            snowflake.style.left = Math.random() * 100 + '%';
            snowflake.style.animationDuration = (Math.random() * 3 + 5) + 's';
            snowflake.style.animationDelay = Math.random() * 5 + 's';
            snowflake.style.fontSize = (Math.random() * 0.8 + 0.5) + 'em';
            container.appendChild(snowflake);
        }
    }

    // Crear copos de nieve cuando cargue la página
    window.addEventListener('load', createSnowflakes);
</script>

<?php include("pie.php") ?>