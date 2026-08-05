<?php
/**
 * DASHBOARD DE RENTABILIDAD E INVENTARIO
 * 
 * Tablas usadas:
 *   - articulo         : id, nombre, stock, precio_venta
 *   - compra           : id, fecha_compra, total, js_detalle_compra (JSON)
 *   - detalle_venta    : id, articulo_id, cantidad, precio_unitario, sub_total, costoxminuto, minutos
 *
 * Si algún nombre de tabla difiere, cámbialo en la sección "CONFIGURACIÓN DE TABLAS".
 */

include("cabecera.php");   // sesión, conexión $conectar

// ══════════════════════════════════════════════════════
//  CONFIGURACIÓN DE TABLAS  ← cambia aquí si es necesario
// ══════════════════════════════════════════════════════
define('TBL_ARTICULO',      'articulo');
define('TBL_COMPRA',        'compra');
define('TBL_DETALLE_VENTA', 'detalle_venta');

// ══════════════════════════════════════════════════════
//  HELPERS
// ══════════════════════════════════════════════════════

/**
 * Devuelve el último costo unitario de compra para cada articulo_id.
 * Recorre todas las compras ordenadas por fecha DESC y extrae
 * el campo precio_unitario_ del JSON.
 *
 * Retorna: [ articulo_id => ultimo_costo_unitario, ... ]
 */
function fnUltimoCostoUnitarioPorArticulo(): array {
    global $conectar;

    $sql = "SELECT js_detalle_compra, fecha_compra
            FROM " . TBL_COMPRA . "
            WHERE js_detalle_compra IS NOT NULL
            ORDER BY fecha_compra ASC, id ASC";   // ASC para que el último quede como final

    $stmt = $conectar->query($sql);
    $costos = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $items = json_decode($row['js_detalle_compra'], true);
        if (!is_array($items)) continue;

        foreach ($items as $item) {
            $artId = intval($item['articulo_id'] ?? 0);
            $pu    = floatval($item['precio_unitario_'] ?? 0);
            if ($artId > 0 && $pu > 0) {
                $costos[$artId] = $pu;   // sobreescribe → queda el último
            }
        }
    }

    return $costos;
}

/**
 * Todos los artículos con stock y precio_venta.
 */
function fnArticulos(): array {
    global $conectar;
    $stmt = $conectar->query("SELECT id, nombre, stock, precio_venta FROM " . TBL_ARTICULO);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Total de ingresos por ventas de artículos (excluye servicios: costoxminuto != null).
 */
function fnTotalIngresos(): float {
    global $conectar;
    $stmt = $conectar->query(
        "SELECT COALESCE(SUM(sub_total), 0) AS total
         FROM " . TBL_DETALLE_VENTA . "
         WHERE articulo_id IS NOT NULL"
    );
    return floatval($stmt->fetchColumn());
}

/**
 * Total de ingresos por servicios (corte / impresión 3D).
 */
function fnTotalIngresosServicios(): float {
    global $conectar;
    $stmt = $conectar->query(
        "SELECT COALESCE(SUM(costoxminuto * minutos), 0) AS total
         FROM " . TBL_DETALLE_VENTA . "
         WHERE articulo_id IS NULL AND costoxminuto IS NOT NULL AND minutos IS NOT NULL"
    );
    return floatval($stmt->fetchColumn());
}

/**
 * Total histórico invertido en compras.
 */
function fnTotalInvertidoCompras(): float {
    global $conectar;
    $stmt = $conectar->query("SELECT COALESCE(SUM(total), 0) FROM " . TBL_COMPRA);
    return floatval($stmt->fetchColumn());
}

/**
 * Cantidad total de compras registradas.
 */
function fnNumCompras(): int {
    global $conectar;
    $stmt = $conectar->query("SELECT COUNT(*) FROM " . TBL_COMPRA);
    return intval($stmt->fetchColumn());
}

/**
 * Top 10 artículos por valor de inventario (stock × costo unitario).
 */
function fnTopInventario(array $costos, array $articulos): array {
    $lista = [];
    foreach ($articulos as $a) {
        $id    = intval($a['id']);
        $stock = floatval($a['stock']);
        $costo = $costos[$id] ?? 0;
        $valor = $stock * $costo;
        if ($valor > 0) {
            $lista[] = [
                'nombre'      => $a['nombre'],
                'stock'       => $stock,
                'costo_u'     => $costo,
                'precio_v'    => floatval($a['precio_venta']),
                'valor_inv'   => $valor,
                'valor_venta' => $stock * floatval($a['precio_venta']),
            ];
        }
    }
    usort($lista, fn($a, $b) => $b['valor_inv'] <=> $a['valor_inv']);
    return array_slice($lista, 0, 15);
}

// ══════════════════════════════════════════════════════
//  CÁLCULOS PRINCIPALES
// ══════════════════════════════════════════════════════
$costos    = fnUltimoCostoUnitarioPorArticulo();
$articulos = fnArticulos();

// 1. Valor del inventario actual (stock × último costo)
$valorInventarioCosto  = 0.0;
$valorInventarioVenta  = 0.0;
$articulosSinCosto     = 0;

foreach ($articulos as $a) {
    $id    = intval($a['id']);
    $stock = floatval($a['stock']);
    $costo = $costos[$id] ?? 0;
    $pv    = floatval($a['precio_venta']);

    if ($costo > 0) {
        $valorInventarioCosto += $stock * $costo;
    } else {
        $articulosSinCosto++;
    }
    $valorInventarioVenta += $stock * $pv;
}

// 2. Ingresos
$totalIngresosArticulos  = fnTotalIngresos();
$totalIngresosServicios  = fnTotalIngresosServicios();
$totalIngresosGeneral    = $totalIngresosArticulos + $totalIngresosServicios;

// 3. Compras
$totalInvertidoHistorico = fnTotalInvertidoCompras();
$numCompras              = fnNumCompras();

// 4. Utilidad estimada
//    Usamos: Ingresos totales - Total invertido en compras (histórico)
//    (No es COGS exacto, pero es la mejor aproximación con JSON)
$utilidadEstimada        = $totalIngresosGeneral - $totalInvertidoHistorico;
$margenEstimado          = $totalIngresosGeneral > 0
                            ? ($utilidadEstimada / $totalIngresosGeneral) * 100
                            : 0;

// 5. Potencial de venta del inventario
$potencialVenta          = $valorInventarioVenta;
$gananciaPotencial       = $valorInventarioVenta - $valorInventarioCosto;

// 6. Top artículos
$topInventario           = fnTopInventario($costos, $articulos);

// 7. Evolución mensual de compras (últimos 12 meses)
function fnEvolucionMensualCompras(): array {
    global $conectar;
    // PostgreSQL: TO_CHAR para formatear fecha como 'YYYY-MM'
    $sql = "SELECT 
                TO_CHAR(fecha_compra::date, 'YYYY-MM') AS mes,
                SUM(total) AS total_mes,
                COUNT(*) AS num_compras
            FROM " . TBL_COMPRA . "
            WHERE fecha_compra::date >= (NOW() - INTERVAL '12 months')::date
            GROUP BY mes
            ORDER BY mes ASC";
    try {
        $stmt = $conectar->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Si falla retorna array vacío para no romper el gráfico
        return [];
    }
}
$evolucionCompras = fnEvolucionMensualCompras();

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard · Rentabilidad e Inventario</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
/* ── TOKENS ── */
:root {
  --bg:        #0f1117;
  --surface:   #1a1d27;
  --surface2:  #22263a;
  --border:    #2e3250;
  --accent:    #6c63ff;
  --accent2:   #00e5a0;
  --accent3:   #ff6b6b;
  --accent4:   #ffc93c;
  --text:      #e8eaf6;
  --muted:     #8892b0;
  --radius:    14px;
  --shadow:    0 4px 24px rgba(0,0,0,.45);
}

/* ── RESET ── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{
  background:var(--bg);
  color:var(--text);
  font-family:'Segoe UI',system-ui,sans-serif;
  font-size:15px;
  line-height:1.6;
  min-height:100vh;
}

/* ── LAYOUT ── */
.dash-wrap{max-width:1400px;margin:0 auto;padding:32px 20px 60px}

.dash-header{
  display:flex;align-items:center;gap:16px;
  margin-bottom:36px;
  padding-bottom:20px;
  border-bottom:1px solid var(--border);
}
.dash-header .icon{
  width:52px;height:52px;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  border-radius:12px;
  display:grid;place-items:center;
  font-size:24px;flex-shrink:0;
}
.dash-header h1{font-size:1.8rem;font-weight:700;letter-spacing:-.5px}
.dash-header p{color:var(--muted);font-size:.9rem;margin-top:2px}

/* ── KPI GRID ── */
.kpi-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(240px,1fr));
  gap:18px;
  margin-bottom:28px;
}
.kpi{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:22px 24px;
  position:relative;
  overflow:hidden;
  transition:transform .2s,box-shadow .2s;
}
.kpi:hover{transform:translateY(-3px);box-shadow:var(--shadow)}
.kpi::before{
  content:'';
  position:absolute;top:0;left:0;right:0;height:3px;
  background:var(--kpi-color,var(--accent));
}
.kpi-label{
  font-size:.78rem;font-weight:600;letter-spacing:.08em;
  text-transform:uppercase;color:var(--muted);margin-bottom:8px;
}
.kpi-value{
  font-size:2rem;font-weight:800;letter-spacing:-1px;
  color:var(--kpi-color,var(--text));
  line-height:1;
}
.kpi-sub{font-size:.8rem;color:var(--muted);margin-top:6px}
.kpi-icon{
  position:absolute;right:20px;top:50%;transform:translateY(-50%);
  font-size:2.2rem;opacity:.12;
}

/* ── SECCIÓN ── */
.section-title{
  font-size:1rem;font-weight:700;letter-spacing:.04em;
  text-transform:uppercase;color:var(--muted);
  margin:32px 0 14px;
  display:flex;align-items:center;gap:10px;
}
.section-title::after{
  content:'';flex:1;height:1px;background:var(--border);
}

/* ── DOS COLUMNAS ── */
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:20px}
@media(max-width:900px){.two-col{grid-template-columns:1fr}}

/* ── CARD ── */
.card{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:22px;
  box-shadow:var(--shadow);
}
.card h3{font-size:.95rem;font-weight:700;margin-bottom:16px;color:var(--text)}

/* ── TABLA TOP ── */
.top-table{width:100%;border-collapse:collapse;font-size:.82rem}
.top-table th{
  padding:8px 10px;text-align:left;
  color:var(--muted);font-weight:600;
  border-bottom:1px solid var(--border);
  white-space:nowrap;
}
.top-table td{
  padding:9px 10px;
  border-bottom:1px solid rgba(255,255,255,.04);
}
.top-table tr:last-child td{border-bottom:none}
.top-table tr:hover td{background:var(--surface2)}
.badge-gain{
  display:inline-block;
  padding:2px 8px;border-radius:20px;font-size:.75rem;font-weight:700;
  background:rgba(0,229,160,.12);color:var(--accent2);
}
.badge-loss{
  display:inline-block;
  padding:2px 8px;border-radius:20px;font-size:.75rem;font-weight:700;
  background:rgba(255,107,107,.12);color:var(--accent3);
}

/* ── BARRA PROGRESO ── */
.progress-wrap{margin-bottom:14px}
.progress-label{
  display:flex;justify-content:space-between;
  font-size:.8rem;margin-bottom:5px;
}
.progress-bar{
  height:8px;border-radius:99px;
  background:var(--surface2);overflow:hidden;
}
.progress-fill{
  height:100%;border-radius:99px;
  background:linear-gradient(90deg,var(--accent),var(--accent2));
  transition:width 1s ease;
}

/* ── ALERTA ── */
.alert-warn{
  background:rgba(255,201,60,.08);
  border:1px solid rgba(255,201,60,.3);
  border-radius:10px;padding:12px 16px;
  font-size:.85rem;color:var(--accent4);
  margin-bottom:20px;
}

/* ── UTILIDAD COLOR ── */
.positive{color:var(--accent2)}
.negative{color:var(--accent3)}

canvas{max-height:280px}
</style>
</head>
<body>
<div class="dash-wrap">

  <!-- HEADER -->
  <div class="dash-header">
    <div class="icon">📊</div>
    <div>
      <h1>Rentabilidad e Inventario</h1>
      <p>Actualizado al <?php echo date('d/m/Y H:i'); ?> &nbsp;·&nbsp; <?php echo count($articulos); ?> artículos en sistema</p>
    </div>
  </div>

  <?php if ($articulosSinCosto > 0): ?>
  <div class="alert-warn">
    ⚠️ <strong><?php echo $articulosSinCosto; ?> artículo(s)</strong> no tienen historial de compra registrado,
    por lo que no se incluyen en el valor de inventario a costo. El dato puede estar subestimado.
  </div>
  <?php endif; ?>

  <!-- KPIs FILA 1: INVENTARIO -->
  <div class="section-title">📦 Inventario Actual</div>
  <div class="kpi-grid">

    <div class="kpi" style="--kpi-color:#6c63ff">
      <div class="kpi-label">Valor a Costo</div>
      <div class="kpi-value">S/ <?php echo number_format($valorInventarioCosto, 2); ?></div>
      <div class="kpi-sub">Lo que tienes invertido en stock hoy</div>
      <div class="kpi-icon">📦</div>
    </div>

    <div class="kpi" style="--kpi-color:#00e5a0">
      <div class="kpi-label">Valor a Precio de Venta</div>
      <div class="kpi-value">S/ <?php echo number_format($valorInventarioVenta, 2); ?></div>
      <div class="kpi-sub">Si vendieras todo el stock hoy</div>
      <div class="kpi-icon">🏷️</div>
    </div>

    <div class="kpi" style="--kpi-color:#ffc93c">
      <div class="kpi-label">Ganancia Potencial del Stock</div>
      <div class="kpi-value <?php echo $gananciaPotencial >= 0 ? 'positive' : 'negative'; ?>">
        S/ <?php echo number_format($gananciaPotencial, 2); ?>
      </div>
      <div class="kpi-sub">Venta potencial − Costo del inventario</div>
      <div class="kpi-icon">💰</div>
    </div>

    <div class="kpi" style="--kpi-color:#8892b0">
      <div class="kpi-label">Total Invertido (Histórico)</div>
      <div class="kpi-value">S/ <?php echo number_format($totalInvertidoHistorico, 2); ?></div>
      <div class="kpi-sub"><?php echo $numCompras; ?> compras registradas</div>
      <div class="kpi-icon">🛒</div>
    </div>

  </div>

  <!-- KPIs FILA 2: VENTAS -->
  <div class="section-title">💵 Ventas e Ingresos</div>
  <div class="kpi-grid">

    <div class="kpi" style="--kpi-color:#00e5a0">
      <div class="kpi-label">Ingresos por Artículos</div>
      <div class="kpi-value">S/ <?php echo number_format($totalIngresosArticulos, 2); ?></div>
      <div class="kpi-sub">Total cobrado por productos físicos</div>
      <div class="kpi-icon">📋</div>
    </div>

    <div class="kpi" style="--kpi-color:#a78bfa">
      <div class="kpi-label">Ingresos por Servicios</div>
      <div class="kpi-value">S/ <?php echo number_format($totalIngresosServicios, 2); ?></div>
      <div class="kpi-sub">Cortes, impresión 3D, etc.</div>
      <div class="kpi-icon">✂️</div>
    </div>

    <div class="kpi" style="--kpi-color:#6c63ff">
      <div class="kpi-label">Ingresos Totales</div>
      <div class="kpi-value">S/ <?php echo number_format($totalIngresosGeneral, 2); ?></div>
      <div class="kpi-sub">Artículos + Servicios</div>
      <div class="kpi-icon">💵</div>
    </div>

    <div class="kpi" style="--kpi-color:<?php echo $utilidadEstimada >= 0 ? '#00e5a0' : '#ff6b6b'; ?>">
      <div class="kpi-label">Utilidad Estimada</div>
      <div class="kpi-value <?php echo $utilidadEstimada >= 0 ? 'positive' : 'negative'; ?>">
        S/ <?php echo number_format($utilidadEstimada, 2); ?>
      </div>
      <div class="kpi-sub">
        Margen: <strong><?php echo number_format($margenEstimado, 1); ?>%</strong>
        &nbsp;·&nbsp; Ingresos − Total comprado
      </div>
      <div class="kpi-icon"><?php echo $utilidadEstimada >= 0 ? '📈' : '📉'; ?></div>
    </div>

  </div>

  <!-- GRÁFICOS -->
  <div class="section-title">📈 Análisis Visual</div>
  <div class="two-col">

    <!-- Composición de ingresos -->
    <div class="card">
      <h3>Composición de Ingresos</h3>
      <canvas id="chartIngresos"></canvas>
    </div>

    <!-- Evolución de compras -->
    <div class="card">
      <h3>Inversión en Compras (últimos 12 meses)</h3>
      <canvas id="chartCompras"></canvas>
    </div>

  </div>

  <div class="two-col" style="margin-top:20px">

    <!-- Inventario vs Ventas -->
    <div class="card">
      <h3>Resumen Financiero</h3>
      <?php
        $items = [
          ['Valor inventario (costo)',   $valorInventarioCosto,      $totalInvertidoHistorico, '#6c63ff'],
          ['Valor inventario (venta)',   $valorInventarioVenta,      $valorInventarioVenta,    '#00e5a0'],
          ['Ingresos artículos',         $totalIngresosArticulos,    $totalIngresosGeneral,    '#a78bfa'],
          ['Ingresos servicios',         $totalIngresosServicios,    $totalIngresosGeneral,    '#ffc93c'],
          ['Total invertido en compras', $totalInvertidoHistorico,   $totalInvertidoHistorico, '#ff6b6b'],
        ];
        $max = max(array_column($items, 1)) ?: 1;
        foreach ($items as [$label, $val, $base, $color]):
          $pct = $base > 0 ? min(100, ($val / $base) * 100) : 0;
      ?>
      <div class="progress-wrap">
        <div class="progress-label">
          <span><?php echo $label ?></span>
          <strong>S/ <?php echo number_format($val, 2) ?></strong>
        </div>
        <div class="progress-bar">
          <div class="progress-fill" style="width:<?php echo $pct ?>%;background:<?php echo $color ?>"></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Ratio costo/venta inventario -->
    <div class="card">
      <h3>Ratio Costo vs Precio de Venta (Inventario)</h3>
      <canvas id="chartRatio"></canvas>
    </div>

  </div>

  <!-- TOP ARTÍCULOS POR VALOR DE INVENTARIO -->
  <div class="section-title">🏆 Top Artículos por Valor de Inventario</div>
  <div class="card">
    <div style="overflow-x:auto">
    <table class="top-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Artículo</th>
          <th>Stock</th>
          <th>Costo Unit.</th>
          <th>P. Venta</th>
          <th>Valor Inventario (costo)</th>
          <th>Valor Inventario (venta)</th>
          <th>Ganancia Potencial</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($topInventario as $i => $a):
          $ganancia = $a['valor_venta'] - $a['valor_inv'];
          $margen   = $a['valor_venta'] > 0 ? ($ganancia / $a['valor_venta']) * 100 : 0;
        ?>
        <tr>
          <td style="color:var(--muted)"><?php echo $i + 1 ?></td>
          <td><?php echo htmlspecialchars($a['nombre']) ?></td>
          <td><?php echo number_format($a['stock'], 2) ?></td>
          <td>S/ <?php echo number_format($a['costo_u'], 4) ?></td>
          <td>S/ <?php echo number_format($a['precio_v'], 2) ?></td>
          <td><strong>S/ <?php echo number_format($a['valor_inv'], 2) ?></strong></td>
          <td>S/ <?php echo number_format($a['valor_venta'], 2) ?></td>
          <td>
            <span class="<?php echo $ganancia >= 0 ? 'badge-gain' : 'badge-loss' ?>">
              S/ <?php echo number_format($ganancia, 2) ?>
              (<?php echo number_format($margen, 1) ?>%)
            </span>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </div>

</div><!-- /dash-wrap -->

<!-- ══ CHARTS ══ -->
<script>
Chart.defaults.color        = '#8892b0';
Chart.defaults.borderColor  = '#2e3250';

/* 1. Donut: composición de ingresos */
new Chart(document.getElementById('chartIngresos'), {
  type: 'doughnut',
  data: {
    labels: ['Artículos', 'Servicios'],
    datasets: [{
      data: [
        <?php echo round($totalIngresosArticulos, 2) ?>,
        <?php echo round($totalIngresosServicios, 2) ?>
      ],
      backgroundColor: ['#6c63ff','#00e5a0'],
      borderWidth: 0,
      hoverOffset: 8
    }]
  },
  options: {
    cutout: '65%',
    plugins: {
      legend: { position: 'bottom' },
      tooltip: {
        callbacks: {
          label: ctx => ' S/ ' + ctx.parsed.toLocaleString('es-PE', {minimumFractionDigits:2})
        }
      }
    }
  }
});

/* 2. Bar: evolución de compras */
const evMeses  = <?php echo json_encode(array_column($evolucionCompras, 'mes')); ?>;
const evTotals = <?php echo json_encode(array_map(fn($r)=>floatval($r['total_mes']), $evolucionCompras)); ?>;

new Chart(document.getElementById('chartCompras'), {
  type: 'bar',
  data: {
    labels: evMeses,
    datasets: [{
      label: 'Invertido (S/)',
      data: evTotals,
      backgroundColor: 'rgba(108,99,255,.7)',
      borderRadius: 6,
      borderSkipped: false
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: {
      y: {
        ticks: {
          callback: v => 'S/ ' + v.toLocaleString('es-PE')
        }
      }
    }
  }
});

/* 3. Donut: ratio costo vs venta inventario */
new Chart(document.getElementById('chartRatio'), {
  type: 'doughnut',
  data: {
    labels: ['Costo del stock', 'Margen potencial'],
    datasets: [{
      data: [
        <?php echo round($valorInventarioCosto, 2) ?>,
        <?php echo round(max(0, $gananciaPotencial), 2) ?>
      ],
      backgroundColor: ['#ff6b6b','#00e5a0'],
      borderWidth: 0,
      hoverOffset: 8
    }]
  },
  options: {
    cutout: '65%',
    plugins: {
      legend: { position: 'bottom' },
      tooltip: {
        callbacks: {
          label: ctx => ' S/ ' + ctx.parsed.toLocaleString('es-PE', {minimumFractionDigits:2})
        }
      }
    }
  }
});

/* Animación de barras de progreso al cargar */
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.progress-fill').forEach(el => {
    const w = el.style.width;
    el.style.width = '0';
    setTimeout(() => el.style.width = w, 100);
  });
});
</script>
</body>
</html>
<?php include("pie.php"); ?>