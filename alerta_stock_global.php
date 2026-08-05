<?php
/**
 * alerta_stock_global.php
 * ─────────────────────────────────────────────────────────────
 * Incluir este archivo dentro de cabecera.php (una sola línea):
 *
 *   <?php include("alerta_stock_global.php"); ?>
 *
 * Aparecerá automáticamente en TODAS las páginas del sistema
 * cuando haya productos bajo stock mínimo.
 * ─────────────────────────────────────────────────────────────
 */

/* ── 1. Obtener productos críticos ─────────────────────────── */
$alertaStock = listarArticulosSinStockMin();               // tu función existente
$alertaCriticos = array_filter($alertaStock, function($a) {
    return (int)$a['stock'] < (int)$a['flag_stock_minimo'];
});
$alertaCount = count($alertaCriticos);

if ($alertaCount === 0) return; // sin críticos → no renderizar nada
?>

<!-- ═══════════════════════════════════════════════════════════
     ALERTA GLOBAL DE STOCK BAJO  –  alerta_stock_global.php
     ═══════════════════════════════════════════════════════════ -->
<style>
/* Estilos solo para la alerta global — prefijo .gs- para no colisionar */
.gs-bar {
    position: sticky;
    top: 0;
    z-index: 1050;
    background: #fef2f2;
    border-bottom: 1px solid #fca5a5;
    font-family: sans-serif;
    font-size: 13px;
    padding: 0 1.25rem;
}
.gs-inner {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 44px;
    max-width: 1200px;
    margin: 0 auto;
    flex-wrap: wrap;
}
.gs-icon {
    width: 28px;
    height: 28px;
    background: #fca5a5;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.gs-icon svg { width: 14px; height: 14px; stroke: #7f1d1d; fill: none; stroke-width: 2; stroke-linecap: round; }
.gs-msg {
    font-weight: 500;
    color: #7f1d1d;
    white-space: nowrap;
}
.gs-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    flex: 1;
}
.gs-pill {
    font-size: 11px;
    padding: 2px 9px;
    border-radius: 20px;
    background: #fca5a5;
    color: #7f1d1d;
    font-weight: 500;
    white-space: nowrap;
}
.gs-pill span { opacity: .75; font-weight: 400; }
.gs-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-left: auto;
    flex-shrink: 0;
}
.gs-link {
    font-size: 12px;
    font-weight: 500;
    color: #991b1b;
    text-decoration: none;
    padding: 4px 12px;
    border-radius: 8px;
    border: 1px solid #fca5a5;
    background: transparent;
    white-space: nowrap;
    transition: background .15s;
}
.gs-link:hover { background: #fca5a5; color: #7f1d1d; }
.gs-close {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    border: none;
    background: transparent;
    color: #b91c1c;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    line-height: 1;
    transition: background .15s;
}
.gs-close:hover { background: #fca5a5; }

/* Notificación flotante (campana) — aparece cuando el banner está oculto */
.gs-fab {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 1049;
    background: #dc2626;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 52px;
    height: 52px;
    cursor: pointer;
    display: none;              /* oculto por defecto */
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 16px rgba(185,28,28,.35);
    transition: transform .15s;
}
.gs-fab:hover { transform: scale(1.08); }
.gs-fab svg { width: 22px; height: 22px; stroke: #fff; fill: none; stroke-width: 2; stroke-linecap: round; }
.gs-fab-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #fff;
    color: #dc2626;
    font-size: 10px;
    font-weight: 700;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: sans-serif;
}
@media print { .gs-bar, .gs-fab { display: none !important; } }
</style>

<!-- Barra sticky superior -->
<div class="gs-bar" id="gs-bar">
    <div class="gs-inner">

        <!-- Ícono de alerta -->
        <div class="gs-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>

        <!-- Mensaje principal -->
        <span class="gs-msg">
            <?php echo $alertaCount; ?> producto<?php echo $alertaCount !== 1 ? 's' : ''; ?>
            bajo stock mínimo
        </span>

        <!-- Pills con nombres (máximo 5 visibles, el resto colapsado) -->
        <div class="gs-pills">
            <?php
            $i = 0;
            foreach ($alertaCriticos as $a):
                $deficit = (float)$a['stock_min'] - (float)$a['stock'];
                if ($i >= 5) break;
            ?>
            <span class="gs-pill">
                <?php echo htmlspecialchars($a['articulo']); ?>
                <span>(+<?php echo $deficit; ?>)</span>
            </span>
            <?php $i++; endforeach; ?>

            <?php if ($alertaCount > 5): ?>
            <span class="gs-pill">+<?php echo ($alertaCount - 5); ?> más</span>
            <?php endif; ?>
        </div>

        <!-- Acciones -->
        <div class="gs-actions">
            <a class="gs-link" href="reposicion_stock.php">Ver reporte</a>
            <button class="gs-close" onclick="gsCerrar()" aria-label="Cerrar alerta">×</button>
        </div>

    </div>
</div>

<!-- Botón flotante (campana) que aparece al cerrar el banner -->
<button class="gs-fab" id="gs-fab" onclick="gsReabrir()"
        aria-label="<?php echo $alertaCount; ?> productos bajo stock mínimo">
    <svg viewBox="0 0 24 24">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
    </svg>
    <span class="gs-fab-badge"><?php echo min($alertaCount, 99); ?></span>
</button>

<script>
(function () {
    var KEY = 'gsBarHidden_<?php echo date("Ymd"); ?>';  // se resetea cada día

    var bar = document.getElementById('gs-bar');
    var fab = document.getElementById('gs-fab');

    /* Si el usuario ya cerró hoy, mostrar solo la campana */
    if (sessionStorage.getItem(KEY)) {
        bar.style.display = 'none';
        fab.style.display = 'flex';
    }

    window.gsCerrar = function () {
        bar.style.display = 'none';
        fab.style.display = 'flex';
        sessionStorage.setItem(KEY, '1');
    };

    window.gsReabrir = function () {
        bar.style.display = '';
        fab.style.display = 'none';
        sessionStorage.removeItem(KEY);
    };
})();
</script>