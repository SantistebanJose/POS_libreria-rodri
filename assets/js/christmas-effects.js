/* ============================================
   JAVASCRIPT EFECTOS NAVIDEÑOS
   ============================================ */

// Crear nieve cayendo
function createSnowflakes() {
    const snowflakesContainer = document.createElement('div');
    snowflakesContainer.className = 'snowflakes';
    document.body.appendChild(snowflakesContainer);
    
    const snowflakeChars = ['❄', '❅', '❆', '✻', '✼', '❉'];
    const numberOfFlakes = 50;
    
    for (let i = 0; i < numberOfFlakes; i++) {
        const snowflake = document.createElement('div');
        snowflake.className = 'snowflake';
        snowflake.textContent = snowflakeChars[Math.floor(Math.random() * snowflakeChars.length)];
        
        // Posición y velocidad aleatoria
        snowflake.style.left = Math.random() * 100 + '%';
        snowflake.style.animationDuration = (Math.random() * 3 + 5) + 's';
        snowflake.style.animationDelay = Math.random() * 5 + 's';
        snowflake.style.fontSize = (Math.random() * 0.5 + 0.5) + 'em';
        snowflake.style.opacity = Math.random() * 0.6 + 0.4;
        
        snowflakesContainer.appendChild(snowflake);
    }
}
// Crear nieve cayendo dentro del sidebar
function createSidebarSnow() {
    const sidebar = document.querySelector('.sidebar-wrapper');
    if (!sidebar) return;
    
    const snowContainer = document.createElement('div');
    snowContainer.style.position = 'absolute';
    snowContainer.style.top = '0';
    snowContainer.style.left = '0';
    snowContainer.style.width = '100%';
    snowContainer.style.height = '100%';
    snowContainer.style.pointerEvents = 'none';
    snowContainer.style.overflow = 'hidden';
    snowContainer.style.zIndex = '2';
    
    sidebar.style.position = 'relative';
    sidebar.insertBefore(snowContainer, sidebar.firstChild);
    
    setInterval(() => {
        const snowflake = document.createElement('div');
        snowflake.innerHTML = '❄';
        snowflake.style.position = 'absolute';
        snowflake.style.color = 'rgba(255, 255, 255, 0.8)';
        snowflake.style.fontSize = (Math.random() * 10 + 10) + 'px';
        snowflake.style.left = Math.random() * 100 + '%';
        snowflake.style.top = '-20px';
        snowflake.style.animation = `sidebar-snow ${Math.random() * 3 + 4}s linear`;
        snowflake.style.textShadow = '0 0 5px rgba(255, 255, 255, 0.8)';
        
        snowContainer.appendChild(snowflake);
        
        setTimeout(() => snowflake.remove(), 7000);
    }, 300);
}

// Agregar guirnalda animada en la parte superior
function addChristmasGarland() {
    const sidebarLogo = document.querySelector('.sidebar-logo');
    if (!sidebarLogo) return;
    
    const garland = document.createElement('div');
    garland.style.position = 'absolute';
    garland.style.top = '0';
    garland.style.left = '0';
    garland.style.width = '100%';
    garland.style.padding = '5px';
    garland.style.textAlign = 'center';
    garland.style.fontSize = '1.2em';
    garland.style.zIndex = '100';
    garland.style.background = 'rgba(0, 0, 0, 0.2)';
    garland.innerHTML = '🎅 🎄 ⛄ 🎁 🔔 ⭐ 🎅';
    
    // Animar los iconos
    let position = 0;
    setInterval(() => {
        position = (position + 1) % 7;
        const icons = ['🎅', '🎄', '⛄', '🎁', '🔔', '⭐', '🎊'];
        garland.innerHTML = icons.slice(position).concat(icons.slice(0, position)).join(' ');
    }, 500);
    
    sidebarLogo.appendChild(garland);
}

// Agregar muérdago en items aleatorios
function addMistletoe() {
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach((item, index) => {
        if (Math.random() > 0.6) {
            const mistletoe = document.createElement('span');
            mistletoe.innerHTML = '🌿';
            mistletoe.style.position = 'absolute';
            mistletoe.style.right = '15px';
            mistletoe.style.top = '50%';
            mistletoe.style.transform = 'translateY(-50%)';
            mistletoe.style.fontSize = '1.2em';
            mistletoe.style.opacity = '0.5';
            mistletoe.style.animation = 'sway 3s ease-in-out infinite';
            mistletoe.style.animationDelay = (index * 0.2) + 's';
            
            item.style.position = 'relative';
            item.appendChild(mistletoe);
        }
    });
}


// Efecto de luces intermitentes en el borde del sidebar
function addBlinkingLights() {
    const sidebar = document.querySelector('.sidebar');
    if (!sidebar) return;
    
    const lightsTop = document.createElement('div');
    lightsTop.style.position = 'absolute';
    lightsTop.style.top = '0';
    lightsTop.style.left = '0';
    lightsTop.style.width = '100%';
    lightsTop.style.height = '2px';
    lightsTop.style.zIndex = '1000';
    lightsTop.style.display = 'flex';
    lightsTop.style.justifyContent = 'space-around';
    
    const colors = ['#ff0000', '#00ff00', '#ffff00', '#0000ff', '#ff00ff'];
    
    for (let i = 0; i < 20; i++) {
        const light = document.createElement('div');
        light.style.width = '4px';
        light.style.height = '4px';
        light.style.borderRadius = '50%';
        light.style.backgroundColor = colors[i % colors.length];
        light.style.boxShadow = `0 0 10px ${colors[i % colors.length]}`;
        light.style.animation = `blink ${Math.random() * 1 + 0.5}s ease-in-out infinite`;
        light.style.animationDelay = (i * 0.1) + 's';
        lightsTop.appendChild(light);
    }
    
    sidebar.appendChild(lightsTop);
}

// Crear luces navideñas en el sidebar
function createChristmasLights() {
    const sidebar = document.querySelector('.sidebar-logo');
    if (sidebar) {
        const lightsContainer = document.createElement('div');
        lightsContainer.className = 'christmas-lights';
        
        for (let i = 0; i < 15; i++) {
            const light = document.createElement('div');
            light.className = 'christmas-light';
            lightsContainer.appendChild(light);
        }
        
        sidebar.insertBefore(lightsContainer, sidebar.firstChild);
    }
}

// Crear mensaje navideño
function createChristmasMessage() {
    const message = document.createElement('div');
    message.className = 'christmas-message';
    message.innerHTML = '🎄 ¡Feliz Navidad! 🎅';
    document.body.appendChild(message);
    
    // Ocultar después de 10 segundos
    setTimeout(() => {
        message.style.transition = 'opacity 1s ease';
        message.style.opacity = '0';
        setTimeout(() => message.remove(), 1000);
    }, 10000);
}

// Crear estrellas parpadeantes
function createStars() {
    const numberOfStars = 30;
    
    for (let i = 0; i < numberOfStars; i++) {
        const star = document.createElement('div');
        star.className = 'star';
        star.style.left = Math.random() * 100 + '%';
        star.style.top = Math.random() * 100 + '%';
        star.style.animationDelay = Math.random() * 3 + 's';
        star.style.animationDuration = (Math.random() * 2 + 2) + 's';
        document.body.appendChild(star);
    }
}

// Agregar iconos navideños a los títulos del menú
function decorateMenuItems() {
    const menuTexts = {
        'Adiministrador': '🎅',
        'Negocio': '🎁',
        'Facturador SUNAT': '⭐',
        'Datos': '🎄',
        'Crédito': '🔔',
        'Reserva': '❄️',
        'Reserva WEB': '☃️',
        'Venta': '🎉',
        'Pago': '🌟'
    };
    
    document.querySelectorAll('.nav-item > a > p').forEach(p => {
        const text = p.textContent.trim();
        if (menuTexts[text]) {
            p.innerHTML = `${menuTexts[text]} ${text}`;
        }
    });
}

// Efecto de confeti al hacer hover en accesos rápidos
function addConfettiEffect() {
    document.querySelectorAll('.quick-actions-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            createConfetti(this);
        });
    });
}

function createConfetti(element) {
    const confettiColors = ['#ff0000', '#00ff00', '#ffd700', '#0000ff', '#ff00ff'];
    const numberOfConfetti = 10;
    
    for (let i = 0; i < numberOfConfetti; i++) {
        const confetti = document.createElement('div');
        confetti.style.position = 'absolute';
        confetti.style.width = '5px';
        confetti.style.height = '5px';
        confetti.style.backgroundColor = confettiColors[Math.floor(Math.random() * confettiColors.length)];
        confetti.style.borderRadius = '50%';
        confetti.style.left = '50%';
        confetti.style.top = '50%';
        confetti.style.pointerEvents = 'none';
        confetti.style.zIndex = '10000';
        confetti.style.animation = 'confetti 1s ease-out forwards';
        
        element.style.position = 'relative';
        element.appendChild(confetti);
        
        setTimeout(() => confetti.remove(), 1000);
    }
}

// Agregar campanas que suenan (visual)
function addJingleBells() {
    const bell = document.createElement('div');
    bell.innerHTML = '🔔';
    bell.style.position = 'fixed';
    bell.style.top = '10px';
    bell.style.left = '50%';
    bell.style.transform = 'translateX(-50%)';
    bell.style.fontSize = '2em';
    bell.style.cursor = 'pointer';
    bell.style.zIndex = '10000';
    bell.style.transition = 'all 0.3s ease';
    
    bell.addEventListener('click', function() {
        this.style.animation = 'gift-shake 0.5s ease-in-out';
        createSnowBurst();
        setTimeout(() => {
            this.style.animation = '';
        }, 500);
    });
    
    document.body.appendChild(bell);
}

// Explosión de nieve al hacer clic en la campana
function createSnowBurst() {
    const burst = document.createElement('div');
    burst.style.position = 'fixed';
    burst.style.top = '50%';
    burst.style.left = '50%';
    burst.style.transform = 'translate(-50%, -50%)';
    burst.style.fontSize = '3em';
    burst.style.pointerEvents = 'none';
    burst.style.zIndex = '10001';
    burst.innerHTML = '❄️ ❄️ ❄️ ❄️ ❄️';
    burst.style.animation = 'confetti 2s ease-out forwards';
    
    document.body.appendChild(burst);
    setTimeout(() => burst.remove(), 2000);
}

// Contador regresivo para Navidad
function addChristmasCountdown() {
    const christmas = new Date(new Date().getFullYear(), 11, 25);
    const now = new Date();
    const diff = christmas - now;
    
    if (diff > 0) {
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        
        const countdown = document.createElement('div');
        countdown.style.position = 'fixed';
        countdown.style.bottom = '20px';
        countdown.style.right = '20px';
        countdown.style.background = 'linear-gradient(135deg, #e74c3c 0%, #c0392b 100%)';
        countdown.style.color = 'white';
        countdown.style.padding = '15px 20px';
        countdown.style.borderRadius = '15px';
        countdown.style.fontWeight = 'bold';
        countdown.style.fontSize = '14px';
        countdown.style.zIndex = '1000';
        countdown.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.3)';
        countdown.innerHTML = `🎄 Faltan ${days} días para Navidad 🎅`;
        
        document.body.appendChild(countdown);
    } else {
        const countdown = document.createElement('div');
        countdown.style.position = 'fixed';
        countdown.style.bottom = '20px';
        countdown.style.right = '20px';
        countdown.style.background = 'linear-gradient(135deg, #27ae60 0%, #229954 100%)';
        countdown.style.color = 'white';
        countdown.style.padding = '15px 20px';
        countdown.style.borderRadius = '15px';
        countdown.style.fontWeight = 'bold';
        countdown.style.fontSize = '14px';
        countdown.style.zIndex = '1000';
        countdown.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.3)';
        countdown.innerHTML = '🎄 ¡Feliz Navidad! 🎅';
        
        document.body.appendChild(countdown);
    }
}
// Agregar regalos flotantes
function addFloatingGifts() {
    const sidebar = document.querySelector('.sidebar-wrapper');
    if (!sidebar) return;
    
    const gifts = ['🎁', '🎀', '🎊', '🎉'];
    
    setInterval(() => {
        const gift = document.createElement('div');
        gift.innerHTML = gifts[Math.floor(Math.random() * gifts.length)];
        gift.style.position = 'absolute';
        gift.style.left = Math.random() * 80 + 10 + '%';
        gift.style.bottom = '-30px';
        gift.style.fontSize = '1.5em';
        gift.style.pointerEvents = 'none';
        gift.style.zIndex = '1';
        gift.style.animation = 'float-up 4s ease-out forwards';
        gift.style.opacity = '0.6';
        
        sidebar.appendChild(gift);
        
        setTimeout(() => gift.remove(), 4000);
    }, 3000);
}

// Animación de float-up para regalos
const floatUpStyle = document.createElement('style');
floatUpStyle.innerHTML = `
    @keyframes float-up {
        0% {
            bottom: -30px;
            opacity: 0.6;
            transform: rotate(0deg);
        }
        100% {
            bottom: 100%;
            opacity: 0;
            transform: rotate(360deg);
        }
    }
`;
document.head.appendChild(floatUpStyle);

// Efecto de brillo en el logo al pasar el mouse
function addLogoHoverEffect() {
    const logo = document.querySelector('.logo');
    if (!logo) return;
    
    logo.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.1)';
        this.style.transition = 'all 0.3s ease';
        
        // Crear explosión de estrellas
        for (let i = 0; i < 5; i++) {
            const star = document.createElement('span');
            star.innerHTML = '⭐';
            star.style.position = 'absolute';
            star.style.left = '50%';
            star.style.top = '50%';
            star.style.fontSize = '1em';
            star.style.pointerEvents = 'none';
            
            const angle = (Math.PI * 2 * i) / 5;
            const distance = 50;
            
            setTimeout(() => {
                star.style.transition = 'all 1s ease-out';
                star.style.transform = `translate(${Math.cos(angle) * distance}px, ${Math.sin(angle) * distance}px)`;
                star.style.opacity = '0';
            }, 10);
            
            this.appendChild(star);
            setTimeout(() => star.remove(), 1000);
        }
    });
    
    logo.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
}

// Contador de clics navideños en el árbol
function addChristmasTreeCounter() {
    const tree = document.querySelector('.sidebar-wrapper::after');
    let clicks = 0;
    
    const sidebar = document.querySelector('.sidebar-wrapper');
    if (!sidebar) return;
    
    sidebar.addEventListener('click', function(e) {
        // Verificar si el clic fue cerca del área del árbol (parte inferior)
        const rect = sidebar.getBoundingClientRect();
        const clickY = e.clientY - rect.top;
        
        if (clickY > rect.height - 100) {
            clicks++;
            
            // Crear efecto de regalo cayendo
            const gift = document.createElement('div');
            gift.innerHTML = '🎁';
            gift.style.position = 'absolute';
            gift.style.left = e.clientX - rect.left + 'px';
            gift.style.top = e.clientY - rect.top + 'px';
            gift.style.fontSize = '2em';
            gift.style.pointerEvents = 'none';
            gift.style.zIndex = '1000';
            gift.style.animation = 'bounce 0.5s ease-out';
            
            sidebar.appendChild(gift);
            setTimeout(() => gift.remove(), 500);
            
            // Efecto especial cada 5 clics
            if (clicks % 5 === 0) {
                createSidebarConfetti();
            }
        }
    });
}

// Crear confeti en el sidebar
function createSidebarConfetti() {
    const sidebar = document.querySelector('.sidebar-wrapper');
    if (!sidebar) return;
    
    for (let i = 0; i < 30; i++) {
        const confetti = document.createElement('div');
        const colors = ['#ff0000', '#00ff00', '#ffff00', '#0000ff', '#ff00ff'];
        confetti.style.position = 'absolute';
        confetti.style.left = '50%';
        confetti.style.top = '50%';
        confetti.style.width = '8px';
        confetti.style.height = '8px';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.borderRadius = '50%';
        confetti.style.pointerEvents = 'none';
        confetti.style.zIndex = '1001';
        
        const angle = (Math.PI * 2 * i) / 30;
        const distance = Math.random() * 100 + 50;
        
        setTimeout(() => {
            confetti.style.transition = 'all 1s ease-out';
            confetti.style.transform = `translate(${Math.cos(angle) * distance}px, ${Math.sin(angle) * distance}px) rotate(${Math.random() * 720}deg)`;
            confetti.style.opacity = '0';
        }, 10);
        
        sidebar.appendChild(confetti);
        setTimeout(() => confetti.remove(), 1000);
    }
}

// Mensaje navideño al pasar por cada sección
function addHoverMessages() {
    const messages = {
        'Administrador': '🎅 ¡Gestiona con alegría navideña!',
        'Negocio': '🎁 ¡Ventas prósperas y felices!',
        'Facturador SUNAT': '⭐ ¡Declara con espíritu festivo!',
        'Datos': '🎄 ¡Analiza tus datos navideños!',
        'Crédito': '🔔 ¡Créditos felices!',
        'Reserva': '❄️ ¡Reserva tu Navidad perfecta!',
        'Reserva WEB': '☃️ ¡Conexión navideña online!',
        'Venta': '🎉 ¡Vende con alegría festiva!',
        'Pago': '🌟 ¡Pagos brillantes!'
    };
    
    document.querySelectorAll('.nav-item > a').forEach(link => {
        const text = link.querySelector('p')?.textContent.trim();
        
        if (text && messages[text]) {
            link.addEventListener('mouseenter', function() {
                const tooltip = document.createElement('div');
                tooltip.className = 'christmas-tooltip';
                tooltip.innerHTML = messages[text];
                tooltip.style.position = 'fixed';
                tooltip.style.left = '260px';
                tooltip.style.background = 'linear-gradient(135deg, #c0392b 0%, #27ae60 100%)';
                tooltip.style.color = 'white';
                tooltip.style.padding = '10px 15px';
                tooltip.style.borderRadius = '10px';
                tooltip.style.fontSize = '14px';
                tooltip.style.fontWeight = 'bold';
                tooltip.style.zIndex = '10000';
                tooltip.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.3)';
                tooltip.style.animation = 'slideIn 0.3s ease-out';
                tooltip.style.whiteSpace = 'nowrap';
                
                const rect = this.getBoundingClientRect();
                tooltip.style.top = rect.top + (rect.height / 2) - 20 + 'px';
                
                document.body.appendChild(tooltip);
                
                this.tooltipElement = tooltip;
            });
            
            link.addEventListener('mouseleave', function() {
                if (this.tooltipElement) {
                    this.tooltipElement.style.animation = 'slideOut 0.3s ease-out';
                    setTimeout(() => {
                        if (this.tooltipElement) {
                            this.tooltipElement.remove();
                        }
                    }, 300);
                }
            });
        }
    });
}

// Agregar animaciones de tooltip
const tooltipStyle = document.createElement('style');
tooltipStyle.innerHTML = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(-20px);
        }
    }
    
    @keyframes bounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.5); }
    }
`;
document.head.appendChild(tooltipStyle);

// Inicializar todos los efectos del sidebar
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        console.log('🎄 Iniciando efectos navideños del sidebar...');
        
        createSidebarSnow();
        addChristmasGarland();
        addMistletoe();
        addBlinkingLights();
        addFloatingGifts();
        addLogoHoverEffect();
        addChristmasTreeCounter();
        addHoverMessages();
        
        console.log('✨ ¡Sidebar navideño listo!');
    }, 800);
});

// Inicializar todos los efectos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un momento para asegurar que todo esté cargado
    setTimeout(() => {
        createSnowflakes();
        createChristmasLights();
        createChristmasMessage();
        createStars();
        decorateMenuItems();
        addConfettiEffect();
        addJingleBells();
        addChristmasCountdown();
        
        console.log('🎄 ¡Efectos navideños activados! 🎅');
    }, 500);
});

// Efecto adicional: cambiar el favicon por uno navideño
function changeToChristmasFavicon() {
    const link = document.querySelector("link[rel*='icon']") || document.createElement('link');
    link.type = 'image/x-icon';
    link.rel = 'shortcut icon';
    // Mantener el favicon actual pero se podría cambiar
    document.getElementsByTagName('head')[0].appendChild(link);
}