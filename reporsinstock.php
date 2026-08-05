<?php
include("cabecera.php");

$datos = listarArticulosSinStockMin();

// Nombres de meses en español, sin depender de locale del servidor
$meses = [
  1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
  5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
  9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
];
$fechaHoy = date('d') . ' de ' . $meses[(int)date('n')] . ' de ' . date('Y');
?>

<style>
  .reporte-wrap * { box-sizing: border-box; margin: 0; padding: 0; }
  .reporte-wrap { font-family: sans-serif; padding: 1.5rem; }
  .rr-header { padding-bottom: 1rem; border-bottom: 1px solid #e5e5e5; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 8px; }
  .rr-header h2 { font-size: 20px; font-weight: 500; color: #1a1a1a; }
  .rr-header p { font-size: 13px; color: #888; margin-top: 3px; }
  .rr-print-btn { font-size: 12px; padding: 5px 14px; border-radius: 8px; border: 1px solid #ddd; background: transparent; color: #666; cursor: pointer; }
  .rr-print-btn:hover { background: #f5f5f5; }
  .rr-metrics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 1.25rem; }
  .rr-metric { background: #f7f7f5; border-radius: 8px; padding: 0.85rem 1rem; }
  .rr-metric-label { font-size: 12px; color: #888; margin-bottom: 4px; }
  .rr-metric-value { font-size: 22px; font-weight: 500; color: #1a1a1a; }
  .rr-metric-value.danger { color: #a32d2d; }
  .rr-metric-value.ok { color: #3b6d11; }
  .rr-metric-sub { font-size: 11px; color: #aaa; margin-top: 2px; }
  .rr-filters { display: flex; gap: 8px; margin-bottom: 1rem; flex-wrap: wrap; align-items: center; }
  .rr-filter-btn { font-size: 12px; padding: 5px 12px; border-radius: 8px; border: 1px solid #ddd; background: transparent; color: #666; cursor: pointer; }
  .rr-filter-btn:hover { background: #f5f5f5; }
  .rr-filter-btn.active { background: #f0f0ee; color: #1a1a1a; border-color: #bbb; font-weight: 500; }
  .rr-search { font-size: 13px; padding: 5px 10px; border: 1px solid #ddd; border-radius: 8px; background: transparent; color: #1a1a1a; width: 180px; }
  .rr-search:focus { outline: none; border-color: #aaa; }
  .rr-table { width: 100%; border-collapse: collapse; font-size: 13px; }
  .rr-table thead th { font-size: 11px; font-weight: 500; color: #888; text-transform: uppercase; letter-spacing: 0.04em; padding: 0 10px 8px; border-bottom: 1px solid #e5e5e5; text-align: left; }
  .rr-table thead th:not(:first-child):not(:nth-child(2)) { text-align: right; }
  .rr-table tbody tr { border-bottom: 1px solid #f0f0f0; }
  .rr-table tbody tr:hover { background: #fafafa; }
  .rr-table tbody tr:last-child { border-bottom: none; }
  .rr-table td { padding: 9px 10px; color: #1a1a1a; vertical-align: middle; }
  .rr-table td:not(:first-child):not(:nth-child(2)) { text-align: right; }
  .rr-id { color: #aaa; font-size: 12px; }
  .rr-bar-wrap { display: flex; align-items: center; gap: 8px; justify-content: flex-end; }
  .rr-bar { height: 4px; width: 60px; background: #e5e5e5; border-radius: 2px; overflow: hidden; flex-shrink: 0; }
  .rr-fill { height: 100%; border-radius: 2px; }
  .rr-fill-danger { background: #e24b4a; }
  .rr-fill-ok { background: #639922; }
  .rr-deficit { font-weight: 500; color: #a32d2d; }
  .rr-badge { font-size: 11px; font-weight: 500; padding: 3px 10px; border-radius: 8px; white-space: nowrap; }
  .rr-badge-danger { background: #fcebeb; color: #a32d2d; }
  .rr-badge-ok { background: #eaf3de; color: #3b6d11; }
  .rr-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; margin-right: 6px; }
  .rr-dot-danger { background: #e24b4a; }
  .rr-dot-ok { background: #639922; }
  .rr-empty { text-align: center; padding: 2rem; color: #aaa; font-size: 13px; }
</style>

<div class="container">
  <div class="page-inner">
    <div class="card">
      <div class="card-body reporte-wrap">

        <div class="rr-header">
          <div>
            <h2>Reposición de stock</h2>
            <p>Artículos bajo stock mínimo &middot; <?php echo $fechaHoy; ?></p>
          </div>
          <button class="rr-print-btn" onclick="window.print()">Imprimir / PDF</button>
        </div>

        <?php
          $total     = count($datos);
          $compra    = 0;
          $ok        = 0;
          foreach ($datos as $a) {
            if ((float)$a['stock'] < (float)$a['stock_min']) $compra++;
            else $ok++;
          }
        ?>

        <div class="rr-metrics">
          <div class="rr-metric">
            <div class="rr-metric-label">Total artículos</div>
            <div class="rr-metric-value"><?php echo $total; ?></div>
            <div class="rr-metric-sub">en el reporte</div>
          </div>
          <div class="rr-metric">
            <div class="rr-metric-label">Reponer stock</div>
            <div class="rr-metric-value danger"><?php echo $compra; ?></div>
            <div class="rr-metric-sub">bajo stock mínimo</div>
          </div>
          <div class="rr-metric">
            <div class="rr-metric-label">OK</div>
            <div class="rr-metric-value ok"><?php echo $ok; ?></div>
            <div class="rr-metric-sub">stock suficiente</div>
          </div>
        </div>

        <div class="rr-filters">
          <button class="rr-filter-btn active" onclick="rrFiltro('all', this)">Todos</button>
          <button class="rr-filter-btn" onclick="rrFiltro('compra', this)">Reponer stock</button>
          <button class="rr-filter-btn" onclick="rrFiltro('ok', this)">OK</button>
          <input class="rr-search" type="text" placeholder="Buscar artículo..." oninput="rrBuscar(this.value)" />
        </div>

        <table class="rr-table" id="rr-tabla">
          <thead>
            <tr>
              <th>ID</th>
              <th>Artículo</th>
              <th>Stock mín.</th>
              <th>Stock actual</th>
              <th>Déficit</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($datos as $a):
              $stockActual = (float)$a['stock'];
              $stockMin    = (float)$a['stock_min'];
              $esCompra    = $stockActual < $stockMin;
              $estado      = $esCompra ? 'compra' : 'ok';
              $pct         = $stockMin > 0
                               ? min(100, round(($stockActual / $stockMin) * 100))
                               : 0;
              $deficit     = $stockMin - $stockActual;
            ?>
            <tr data-estado="<?php echo $estado; ?>" data-nombre="<?php echo strtolower(htmlspecialchars((string)($a['articulo'] ?? ''))); ?>">
              <td class="rr-id"><?php echo htmlspecialchars((string)($a['id'] ?? '')); ?></td>
              <td>
                <span class="rr-dot <?php echo $esCompra ? 'rr-dot-danger' : 'rr-dot-ok'; ?>"></span>
                <?php echo htmlspecialchars((string)($a['articulo'] ?? '')); ?>
              </td>
              <td><?php echo htmlspecialchars((string)($a['stock_min'] ?? '0')); ?></td>
              <td>
                <div class="rr-bar-wrap">
                  <span><?php echo htmlspecialchars((string)($a['stock'] ?? '0')); ?></span>
                  <div class="rr-bar">
                    <div class="rr-fill <?php echo $esCompra ? 'rr-fill-danger' : 'rr-fill-ok'; ?>" style="width:<?php echo $pct; ?>%"></div>
                  </div>
                </div>
              </td>
              <td><?php echo $esCompra ? '<span class="rr-deficit">+' . $deficit . '</span>' : '<span style="color:#aaa">—</span>'; ?></td>
              <td>
                <?php if ($esCompra): ?>
                  <span class="rr-badge rr-badge-danger">Reponer stock</span>
                <?php else: ?>
                  <span class="rr-badge rr-badge-ok">OK</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <p class="rr-empty" id="rr-empty" style="display:none">No se encontraron artículos.</p>

      </div>
    </div>
  </div>
</div>

<script>
var rrFiltroActivo = 'all';
var rrBusquedaActiva = '';

function rrAplicar() {
  var filas = document.querySelectorAll('#rr-tabla tbody tr');
  var visibles = 0;
  filas.forEach(function(fila) {
    var estado  = fila.getAttribute('data-estado');
    var nombre  = fila.getAttribute('data-nombre');
    var matchF  = rrFiltroActivo === 'all' || estado === rrFiltroActivo;
    var matchB  = rrBusquedaActiva === '' || nombre.indexOf(rrBusquedaActiva) !== -1;
    var mostrar = matchF && matchB;
    fila.style.display = mostrar ? '' : 'none';
    if (mostrar) visibles++;
  });
  document.getElementById('rr-empty').style.display = visibles === 0 ? 'block' : 'none';
}

function rrFiltro(f, btn) {
  rrFiltroActivo = f;
  document.querySelectorAll('.rr-filter-btn').forEach(function(b) { b.classList.remove('active'); });
  btn.classList.add('active');
  rrAplicar();
}

function rrBuscar(v) {
  rrBusquedaActiva = v.toLowerCase();
  rrAplicar();
}
</script>

<?php include("pie.php"); ?>