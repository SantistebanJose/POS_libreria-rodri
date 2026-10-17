/* Efectos de temporada discretos. Todas las capas decorativas ignoran el mouse. */
(function () {
    'use strict';

    function iniciarEfectosNavidenos() {
        if (document.documentElement.dataset.navidadIniciada === 'true') return;
        document.documentElement.dataset.navidadIniciada = 'true';

        var esLogin = Boolean(document.querySelector('.btn-enter'));
        document.body.classList.add(esLogin ? 'modo-navidad-login' : 'modo-navidad-pos');
        if (esLogin) agregarSaludoLogin();

        var capa = document.createElement('div');
        capa.className = 'navidad-capa';
        capa.setAttribute('aria-hidden', 'true');

        var guirnalda = document.createElement('div');
        guirnalda.className = 'navidad-guirnalda';
        for (var luz = 0; luz < 18; luz++) {
            var bombilla = document.createElement('span');
            bombilla.className = 'navidad-bombilla';
            bombilla.style.setProperty('--demora-luz', (-Math.random() * 2.4) + 's');
            guirnalda.appendChild(bombilla);
        }
        capa.appendChild(guirnalda);

        var copos = ['❄', '❅', '❆'];
        var total = window.matchMedia('(max-width: 768px)').matches ? 32 : 58;
        for (var i = 0; i < total; i++) {
            var copo = document.createElement('span');
            copo.className = 'navidad-copo';
            copo.textContent = copos[Math.floor(Math.random() * copos.length)];
            copo.style.left = (Math.random() * 100) + '%';
            copo.style.setProperty('--duracion', (7 + Math.random() * 9) + 's');
            copo.style.setProperty('--demora', (-Math.random() * 18) + 's');
            copo.style.setProperty('--tamano', (16 + Math.random() * 16) + 'px');
            copo.style.setProperty('--opacidad', (.65 + Math.random() * .35).toFixed(2));
            copo.style.setProperty('--deriva', (Math.random() * 150 - 75) + 'px');
            capa.appendChild(copo);
        }

        var contador = document.createElement('aside');
        contador.className = 'navidad-contador';
        contador.setAttribute('aria-live', 'polite');
        contador.setAttribute('aria-label', 'Cuenta regresiva para Navidad');
        capa.appendChild(contador);
        actualizarContadorNavidad(contador);
        window.setInterval(function () { actualizarContadorNavidad(contador); }, 60 * 60 * 1000);

        document.body.appendChild(capa);
    }

    function agregarSaludoLogin() {
        var saludo = document.createElement('aside');
        saludo.className = 'navidad-saludo-login';
        saludo.innerHTML = '<span class="navidad-santa-avatar" aria-hidden="true">🎅</span>' +
            '<span class="navidad-santa-mensaje"><strong>¡Ho, ho, hola!</strong><small>Bienvenido a Librería Bazar Rodri</small></span>';
        document.body.appendChild(saludo);
    }

    function actualizarContadorNavidad(elemento) {
        var ahora = new Date();
        var navidad = new Date(ahora.getFullYear(), 11, 25);
        if (ahora > navidad && ahora.toDateString() !== navidad.toDateString()) {
            navidad = new Date(ahora.getFullYear() + 1, 11, 25);
        }
        var dias = Math.ceil((navidad.getTime() - ahora.getTime()) / 86400000);
        if (dias <= 0) {
            elemento.innerHTML = '<span class="navidad-contador-icono">🎄</span><span class="navidad-contador-texto"><strong>¡Feliz Navidad!</strong><small>Que tengas una linda celebración</small></span>';
            return;
        }
        elemento.innerHTML = '<span class="navidad-contador-icono">🎄</span><span class="navidad-contador-numero">' + dias + '</span><span class="navidad-contador-texto"><strong>días para Navidad</strong><small>25 de diciembre</small></span>';
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciarEfectosNavidenos, { once: true });
    } else {
        iniciarEfectosNavidenos();
    }
})();
