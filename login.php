<?php
session_start();
if (isset($_SESSION['id'])) {
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/caracoles.png" type="image/x-icon" />
    <title>Librería Bazar Rodri — POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,700;0,900;1,700&family=Syne:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --blue:      #1a4fa0;
            --blue-deep: #0d2a5e;
            --yellow:    #f0c930;
            --cream:     #fdf9ef;
            --ink:       #0d1b3e;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: var(--blue-deep);
            font-family: 'Syne', sans-serif;
            overflow: hidden;
            position: relative;
        }

        /* ── animated background ── */
        .bg-shapes {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .bg-shapes span {
            position: absolute;
            border-radius: 50%;
            background: var(--yellow);
            opacity: .06;
            animation: float linear infinite;
        }

        .bg-shapes span:nth-child(1)  { width:320px; height:320px; top:-80px;  left:-60px;  animation-duration:22s; }
        .bg-shapes span:nth-child(2)  { width:180px; height:180px; bottom:10%; right:5%;    animation-duration:18s; animation-delay:-8s; opacity:.04; }
        .bg-shapes span:nth-child(3)  { width:90px;  height:90px;  top:40%;    left:10%;   animation-duration:14s; animation-delay:-4s; }
        .bg-shapes span:nth-child(4)  { width:50px;  height:50px;  bottom:15%; left:30%;   animation-duration:10s; animation-delay:-2s; opacity:.08; }

        @keyframes float {
            0%   { transform: translateY(0) scale(1); }
            50%  { transform: translateY(-30px) scale(1.05); }
            100% { transform: translateY(0) scale(1); }
        }

        /* ── card ── */
        .card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 460px;
            margin: 1rem;
            animation: appear .6s cubic-bezier(.22,1,.36,1) both;
        }

        @keyframes appear {
            from { opacity:0; transform: translateY(32px) scale(.97); }
            to   { opacity:1; transform: translateY(0) scale(1); }
        }

        /* ── brand block ── */
        .brand {
            background: var(--yellow);
            border-radius: 20px 20px 0 0;
            padding: 2.2rem 2.5rem 1.8rem;
            display: flex;
            align-items: center;
            gap: 1.2rem;
            position: relative;
            overflow: hidden;
        }

        .brand::after {
            content: '';
            position: absolute;
            right: -30px;
            top: -30px;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: rgba(13,26,62,.12);
        }

        .brand-icon {
            width: 62px;
            height: 62px;
            background: var(--blue-deep);
            border-radius: 16px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            box-shadow: 0 6px 20px rgba(13,26,62,.25);
        }

        .brand-icon i { font-size: 1.7rem; color: var(--yellow); }

        .brand-text h1 {
            font-family: 'Fraunces', serif;
            font-size: 1.45rem;
            font-weight: 900;
            color: var(--blue-deep);
            line-height: 1.15;
        }

        .brand-text p {
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(13,26,62,.55);
            margin-top: .25rem;
        }

        .since-badge {
            margin-left: auto;
            background: var(--blue-deep);
            color: var(--yellow);
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .06em;
            padding: .3rem .75rem;
            border-radius: 999px;
            white-space: nowrap;
            position: relative;
            z-index: 1;
        }

        /* ── form area ── */
        .form-area {
            background: var(--cream);
            padding: 2rem 2.5rem;
        }

        .section-title {
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--blue);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1.5px;
            background: var(--blue);
            opacity: .15;
            border-radius: 2px;
        }

        .field {
            margin-bottom: 1.1rem;
        }

        .field label {
            display: block;
            font-size: .75rem;
            font-weight: 600;
            color: #4a5680;
            margin-bottom: .4rem;
        }

        .field-input {
            position: relative;
        }

        .field-input i.ico {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: .85rem;
            color: #9aa3be;
            pointer-events: none;
            transition: color .2s;
        }

        .field-input input {
            width: 100%;
            padding: .78rem 1rem .78rem 2.5rem;
            background: #fff;
            border: 1.5px solid #dde3f0;
            border-radius: 10px;
            font-family: 'Syne', sans-serif;
            font-size: .93rem;
            color: var(--ink);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .field-input input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3.5px rgba(26,79,160,.1);
        }

        .field-input input::placeholder { color: #b8bfd4; }

        .toggle-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9aa3be;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            transition: color .2s, background .2s;
        }

        .toggle-btn:hover { color: var(--blue); background: rgba(26,79,160,.07); }

        .err { font-size: .75rem; color: #d94040; margin-top: .3rem; min-height: .9rem; }

        .btn-enter {
            width: 100%;
            margin-top: 1.6rem;
            padding: .9rem;
            background: var(--blue-deep);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Syne', sans-serif;
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .04em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .6rem;
            transition: background .2s, transform .12s, box-shadow .2s;
            box-shadow: 0 6px 22px rgba(13,26,62,.28);
            position: relative;
            overflow: hidden;
        }

        .btn-enter::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg, transparent 30%, rgba(240,201,48,.22) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform .45s;
        }

        .btn-enter:hover { background: var(--blue); }
        .btn-enter:hover::before { transform: translateX(100%); }
        .btn-enter:active { transform: scale(.98); box-shadow: none; }

        .btn-enter .arrow-wrap {
            width: 28px;
            height: 28px;
            background: var(--yellow);
            border-radius: 8px;
            display: grid;
            place-items: center;
        }

        .btn-enter .arrow-wrap i { color: var(--blue-deep); font-size: .85rem; }

        .hint {
            margin-top: 1.2rem;
            text-align: center;
            font-size: .78rem;
            color: #8890aa;
            line-height: 1.6;
        }

        .hint strong { color: #4a5680; }

        /* ── footer ── */
        .card-footer {
            background: var(--blue-deep);
            border-radius: 0 0 20px 20px;
            padding: .85rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-footer .pos-label {
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255,255,255,.35);
        }

        .card-footer .dev-credit {
            font-size: .68rem;
            color: rgba(255,255,255,.35);
        }

        .card-footer .dev-credit strong {
            color: var(--yellow);
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="bg-shapes">
    <span></span><span></span><span></span><span></span>
</div>

<div class="card">
    <!-- BRAND HEADER -->
    <div class="brand">
        <div class="brand-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="brand-text">
            <h1>Librería<br>Bazar Rodri</h1>
            <p>Punto de Venta</p>
        </div>
        <span class="since-badge">Desde 2023</span>
    </div>

    <!-- FORM -->
    <div class="form-area">
        <div class="section-title">Acceso al sistema</div>

        <div class="field">
            <label for="user">Usuario</label>
            <div class="field-input">
                <input type="text" id="user" placeholder="user0001" autocomplete="username">
                <i class="fas fa-user ico"></i>
            </div>
            <div id="errorUserLog" class="err"></div>
        </div>

        <div class="field">
            <label for="password">Contraseña</label>
            <div class="field-input">
                <input type="password" id="password" placeholder="••••••••" autocomplete="current-password">
                <i class="fas fa-lock ico"></i>
                <button type="button" id="togglePassword" class="toggle-btn" title="Ver contraseña">
                    <i class="far fa-eye"></i>
                </button>
            </div>
            <div id="errorPassLog" class="err"></div>
        </div>

        <button onclick="iniciarSesion()" class="btn-enter">
            Entrar al sistema
            <span class="arrow-wrap"><i class="fas fa-arrow-right"></i></span>
        </button>

        <p class="hint">
            <strong>¿Olvidaste tu contraseña?</strong><br>
            Contacta con la administración para recuperarla 🙂
        </p>
    </div>

    <!-- FOOTER -->
    <div class="card-footer">
        <span class="pos-label">Sistema POS</span>
        <span class="dev-credit">Desarrollado por <strong>Captain</strong></span>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/scriptlogin.js"></script>
<script>
    document.getElementById("togglePassword").addEventListener("click", function () {
        const field = document.getElementById("password");
        const isPass = field.type === "password";
        field.type = isPass ? "text" : "password";
        this.innerHTML = isPass
            ? '<i class="fas fa-eye-slash"></i>'
            : '<i class="far fa-eye"></i>';
    });
</script>
</body>
</html>