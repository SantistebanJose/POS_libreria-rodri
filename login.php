<?php
session_start(); // Iniciar la sesión, necesario para usar $_SESSION

// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION['id'])) {
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>
    <style>
        body {
            background-color: #E9EEF1;
        }
      
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 500px; /* Aumenta el tamaño máximo del card */
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            background-color: #1a2035;
            color: #fff;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            padding: 20px;
            text-align: center;
        }
        .btn-custom {
            background-color: #1a2035;
            color: #fff;
        }
        .btn-custom:hover {
            background-color: #1a2035;
            color: #ffff;
        }
        
    </style>
</head>
<body>

<div class="container login-container">
    <div class="card login-card">
        <div class="login-header">
            <h3>Logo</h3>
        </div>
        <div id="form-login" class="card-body">
         
                <div class="mb-4">
                    <label for="text" class="form-label fs-4">Usuario: </label>
                    <input type="text" min="0" class="form-control fs-5" id="user" placeholder="Pachito">
                    <div id="errorUserLog" style="color: red; font-size: 1rem;"></div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label fs-4">Contraseña: </label>
                    <input type="password" class="form-control fs-5" id="password" placeholder="*****">
                    <div id="errorPassLog" style="color: red; font-size: 1rem;"></div>
                </div>
                <div class="d-grid">
                    <button onclick="iniciarSesion()" class="btn btn-custom btn-block fs-5">Entrar</button>
                </div>
           
            <div class="text-center mt-3 ">
                <a href="#" class="fs-5">¿No recuerdas tu contraseña?</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/scriptlogin.js"></script>

</html>