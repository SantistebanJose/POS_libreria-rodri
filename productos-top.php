<?php
include("cabecera.php");
include("logica/bd.php");
include("logica/clssVenta.php");
 
// ─── Top 10 por CANTIDAD vendida ──────────────────────────────────────────────
function obtenerTop10Productos($fecha_ini = null, $fecha_fin = null, $categoria = null) {
    global $conectar;
 
    $where = "WHERE r.deleted_at IS NULL AND r.articulo_id IS NOT NULL";
    $params = [];
 
    if ($fecha_ini && $fecha_fin) {
        $where .= " AND v.fecha BETWEEN :fecha_ini AND :fecha_fin";
        $params[':fecha_ini'] = $fecha_ini;
        $params[':fecha_fin'] = $fecha_fin;
    }
    if ($categoria) {
        $where .= " AND h.categoria = :categoria";
        $params[':categoria'] = $categoria;
    }
 
    $sql = "
        SELECT
            h.descripcion                                   AS producto,
            h.categoria,
            SUM(h.cantidad)                                 AS total_vendido,
            SUM(r.sub_total)                                AS total_ingresos,
            ROUND(AVG(r.precio_unitario_articulo)::numeric, 2) AS precio_promedio
        FROM rel_venta_articulo r
        JOIN hecho_articulo_movimiento h ON h.rel_venta_articulo_id = r.id
        JOIN venta v ON v.id = r.venta_id
        {$where}
        GROUP BY h.descripcion, h.categoria
        ORDER BY total_vendido DESC
        LIMIT 10
    ";
 
    try {
        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
 
// ─── Top 10 por INGRESOS ──────────────────────────────────────────────────────
function obtenerTop10PorIngresos($fecha_ini = null, $fecha_fin = null, $categoria = null) {
    global $conectar;
 
    $where = "WHERE r.deleted_at IS NULL AND r.articulo_id IS NOT NULL";
    $params = [];
 
    if ($fecha_ini && $fecha_fin) {
        $where .= " AND v.fecha BETWEEN :fecha_ini AND :fecha_fin";
        $params[':fecha_ini'] = $fecha_ini;
        $params[':fecha_fin'] = $fecha_fin;
    }
    if ($categoria) {
        $where .= " AND h.categoria = :categoria";
        $params[':categoria'] = $categoria;
    }
 
    $sql = "
        SELECT
            h.descripcion                                   AS producto,
            h.categoria,
            SUM(h.cantidad)                                 AS total_vendido,
            SUM(r.sub_total)                                AS total_ingresos,
            ROUND(AVG(r.precio_unitario_articulo)::numeric, 2) AS precio_promedio
        FROM rel_venta_articulo r
        JOIN hecho_articulo_movimiento h ON h.rel_venta_articulo_id = r.id
        JOIN venta v ON v.id = r.venta_id
        {$where}
        GROUP BY h.descripcion, h.categoria
        ORDER BY total_ingresos DESC
        LIMIT 10
    ";
 
    try {
        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
 
// ─── TODOS los productos ──────────────────────────────────────────────────────
function obtenerTodosProductos($fecha_ini = null, $fecha_fin = null, $categoria = null, $orden = 'ingresos') {
    global $conectar;
 
    $where = "WHERE r.deleted_at IS NULL AND r.articulo_id IS NOT NULL";
    $params = [];
 
    if ($fecha_ini && $fecha_fin) {
        $where .= " AND v.fecha BETWEEN :fecha_ini AND :fecha_fin";
        $params[':fecha_ini'] = $fecha_ini;
        $params[':fecha_fin'] = $fecha_fin;
    }
    if ($categoria) {
        $where .= " AND h.categoria = :categoria";
        $params[':categoria'] = $categoria;
    }
 
    $order_sql = match($orden) {
        'cantidad'  => 'total_vendido DESC',
        'precio'    => 'precio_promedio DESC',
        'producto'  => 'h.descripcion ASC',
        default     => 'total_ingresos DESC',
    };
 
    $sql = "
        SELECT
            h.descripcion                                      AS producto,
            h.categoria,
            SUM(h.cantidad)                                    AS total_vendido,
            SUM(r.sub_total)                                   AS total_ingresos,
            ROUND(AVG(r.precio_unitario_articulo)::numeric, 2) AS precio_promedio,
            COUNT(DISTINCT r.venta_id)                         AS num_ventas
        FROM rel_venta_articulo r
        JOIN hecho_articulo_movimiento h ON h.rel_venta_articulo_id = r.id
        JOIN venta v ON v.id = r.venta_id
        {$where}
        GROUP BY h.descripcion, h.categoria
        ORDER BY {$order_sql}
    ";
 
    try {
        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
 
// ─── Métricas generales ───────────────────────────────────────────────────────
function obtenerMetricas($fecha_ini = null, $fecha_fin = null) {
    global $conectar;
 
    $where = "WHERE r.deleted_at IS NULL AND r.articulo_id IS NOT NULL";
    $params = [];
 
    if ($fecha_ini && $fecha_fin) {
        $where .= " AND v.fecha BETWEEN :fecha_ini AND :fecha_fin";
        $params[':fecha_ini'] = $fecha_ini;
        $params[':fecha_fin'] = $fecha_fin;
    }
 
    $sql = "
        SELECT
            COUNT(DISTINCT r.venta_id)    AS total_ventas,
            SUM(r.cantidad)               AS total_unidades,
            SUM(r.sub_total)              AS total_ingresos,
            COUNT(DISTINCT r.articulo_id) AS productos_distintos
        FROM rel_venta_articulo r
        JOIN venta v ON v.id = r.venta_id
        {$where}
    ";
 
    try {
        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['total_ventas'=>0,'total_unidades'=>0,'total_ingresos'=>0,'productos_distintos'=>0];
    }
}
 
// ─── Categorías únicas ────────────────────────────────────────────────────────
function obtenerCategorias() {
    global $conectar;
    try {
        $stmt = $conectar->query("
            SELECT DISTINCT categoria
            FROM hecho_articulo_movimiento
            WHERE categoria IS NOT NULL
            ORDER BY categoria
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        return [];
    }
}
 
// ─── Leer filtros ─────────────────────────────────────────────────────────────
$fecha_ini = (isset($_GET['fecha_ini']) && $_GET['fecha_ini'] !== '') ? $_GET['fecha_ini'] : null;
$fecha_fin = (isset($_GET['fecha_fin']) && $_GET['fecha_fin'] !== '') ? $_GET['fecha_fin'] : null;
$categoria = (isset($_GET['categoria']) && $_GET['categoria'] !== '') ? $_GET['categoria'] : null;
 
$orden_todos = (isset($_GET['orden_todos']) && $_GET['orden_todos'] !== '') ? $_GET['orden_todos'] : 'ingresos';
 
// ─── Obtener datos ────────────────────────────────────────────────────────────
$top10          = obtenerTop10Productos($fecha_ini, $fecha_fin, $categoria);
$top10_ingresos = obtenerTop10PorIngresos($fecha_ini, $fecha_fin, $categoria);
$todos          = obtenerTodosProductos($fecha_ini, $fecha_fin, $categoria, $orden_todos);
$metricas       = obtenerMetricas($fecha_ini, $fecha_fin);
$categorias     = obtenerCategorias();
 
// ─── Preparar datos Chart.js — reporte 1 (cantidad) ──────────────────────────
$labels         = array_column($top10, 'producto');
$cantidades     = array_column($top10, 'total_vendido');
$ingresos       = array_column($top10, 'total_ingresos');
$labels_cortos  = array_map(fn($l) => mb_strlen($l) > 20 ? mb_substr($l,0,20).'...' : $l, $labels);
 
// ─── Preparar datos Chart.js — reporte 2 (ingresos) ──────────────────────────
$labels_ing        = array_column($top10_ingresos, 'producto');
$cantidades_ing    = array_column($top10_ingresos, 'total_vendido');
$ingresos_ing      = array_column($top10_ingresos, 'total_ingresos');
$labels_cortos_ing = array_map(fn($l) => mb_strlen($l) > 20 ? mb_substr($l,0,20).'...' : $l, $labels_ing);
?>
 
<style>
/* ══════════════════════════════════════════════════════════
   TOP 10 PRODUCTOS — estilos integrados al tema LB Rodri
══════════════════════════════════════════════════════════ */
.rpt-header {
    background: linear-gradient(135deg, #0d2a5e 0%, #2251a3 60%, #6861ce 100%);
    border-radius: 16px;
    padding: 2rem 2.5rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.rpt-header::before {
    content:''; position:absolute; top:-50px; right:-50px;
    width:200px; height:200px; border-radius:50%;
    background:rgba(254,249,124,.07);
}
.rpt-header::after {
    content:''; position:absolute; bottom:-70px; left:28%;
    width:280px; height:280px; border-radius:50%;
    background:rgba(255,255,255,.04);
}
.rpt-header h3 {
    color:#fff; font-size:1.7rem; font-weight:800;
    margin:0 0 .3rem; position:relative;
}
.rpt-header h3 span { color:#fef97c; }
.rpt-header p {
    color:rgba(255,255,255,.72); margin:0;
    font-size:.9rem; position:relative;
}
 
/* ── Header reporte 2 (verde) ── */
.rpt-header-ing {
    background: linear-gradient(135deg, #0a4a2a 0%, #17a060 60%, #00b5ad 100%);
    border-radius: 16px;
    padding: 2rem 2.5rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.rpt-header-ing::before {
    content:''; position:absolute; top:-50px; right:-50px;
    width:200px; height:200px; border-radius:50%;
    background:rgba(254,249,124,.07);
}
.rpt-header-ing::after {
    content:''; position:absolute; bottom:-70px; left:28%;
    width:280px; height:280px; border-radius:50%;
    background:rgba(255,255,255,.04);
}
.rpt-header-ing h3 {
    color:#fff; font-size:1.7rem; font-weight:800;
    margin:0 0 .3rem; position:relative;
}
.rpt-header-ing h3 span { color:#fef97c; }
.rpt-header-ing p {
    color:rgba(255,255,255,.72); margin:0;
    font-size:.9rem; position:relative;
}
 
/* ── Divisor ── */
.seccion-divider {
    border: none;
    border-top: 2px dashed #dee2e6;
    margin: 2.5rem 0;
}
 
/* ── Tarjetas métricas ── */
.met-card {
    border-radius:14px; padding:1.3rem 1.4rem;
    display:flex; align-items:center; gap:1rem;
    border:none; box-shadow:0 3px 16px rgba(0,0,0,.07);
    transition:transform .2s, box-shadow .2s;
    background:#fff;
}
.met-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.12); }
.met-ico {
    width:50px; height:50px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    font-size:1.4rem; flex-shrink:0;
}
.met-val { font-size:1.55rem; font-weight:800; line-height:1; margin-bottom:.15rem; }
.met-lbl { font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; opacity:.6; font-weight:700; }
.mc1 .met-ico { background:rgba(34,81,163,.12);  color:#2251a3; }
.mc1 .met-val { color:#2251a3; }
.mc2 .met-ico { background:rgba(23,160,96,.12);   color:#17a060; }
.mc2 .met-val { color:#17a060; }
.mc3 .met-ico { background:rgba(104,97,206,.12);  color:#6861ce; }
.mc3 .met-val { color:#6861ce; }
.mc4 .met-ico { background:rgba(255,159,67,.12);  color:#ff9f43; }
.mc4 .met-val { color:#ff9f43; }
 
/* ── Filtros ── */
.filtros-box {
    background:#fff; border-radius:14px;
    padding:1.2rem 1.5rem; margin-bottom:1.5rem;
    box-shadow:0 2px 10px rgba(0,0,0,.06);
}
.filtros-box label {
    font-size:.75rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.04em;
    color:#666; margin-bottom:.3rem; display:block;
}
.filtros-box .form-control,
.filtros-box .form-select {
    border-radius:8px; border:1.5px solid #e0e0e0; font-size:.88rem;
}
.filtros-box .form-control:focus,
.filtros-box .form-select:focus {
    border-color:#2251a3;
    box-shadow:0 0 0 .2rem rgba(34,81,163,.18);
}
.btn-filtrar {
    background:linear-gradient(135deg,#2251a3,#0d2a5e);
    color:#fff; border:none; border-radius:8px;
    padding:.5rem 1.3rem; font-weight:700; font-size:.88rem;
    transition:opacity .2s;
}
.btn-filtrar:hover { opacity:.85; color:#fff; }
.btn-limpiar {
    background:#f2f2f2; color:#666; border:none;
    border-radius:8px; padding:.5rem 1rem; font-weight:600;
}
.btn-limpiar:hover { background:#e0e0e0; }
 
/* ── Paneles ── */
.panel-card {
    background:#fff; border-radius:14px;
    padding:1.4rem 1.5rem;
    box-shadow:0 2px 12px rgba(0,0,0,.07);
    height:100%;
}
.panel-title {
    font-size:.92rem; font-weight:800; color:#0d2a5e;
    margin-bottom:1rem; display:flex; align-items:center; gap:.5rem;
}
.panel-title i { color:#2251a3; }
.panel-title.ing i { color:#17a060; }
 
/* ── Tabla ranking ── */
.rank-table { width:100%; border-collapse:separate; border-spacing:0 5px; }
.rank-table thead th {
    font-size:.7rem; text-transform:uppercase;
    letter-spacing:.07em; color:#aaa; font-weight:700;
    padding:.4rem 1rem; border:none; background:transparent;
}
.rank-table tbody tr {
    background:#fff; border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,.05);
    transition:transform .15s, box-shadow .15s;
}
.rank-table tbody tr:hover {
    transform:translateX(4px);
    box-shadow:0 4px 18px rgba(34,81,163,.14);
}
.rank-table tbody tr.ing-row:hover {
    box-shadow:0 4px 18px rgba(23,160,96,.18);
}
.rank-table tbody td {
    padding:.85rem 1rem; border:none; vertical-align:middle;
}
.rank-table tbody td:first-child { border-radius:10px 0 0 10px; }
.rank-table tbody td:last-child  { border-radius:0 10px 10px 0; }
 
/* Medallas */
.rank-badge {
    width:34px; height:34px; border-radius:50%;
    display:inline-flex; align-items:center; justify-content:center;
    font-weight:800; font-size:.88rem; flex-shrink:0;
}
.rb-1 { background:linear-gradient(135deg,#FFD700,#FFA500); color:#fff; box-shadow:0 3px 10px rgba(255,165,0,.45); }
.rb-2 { background:linear-gradient(135deg,#C0C0C0,#9E9E9E); color:#fff; box-shadow:0 3px 10px rgba(158,158,158,.4); }
.rb-3 { background:linear-gradient(135deg,#CD7F32,#8B4513); color:#fff; box-shadow:0 3px 10px rgba(139,69,19,.4); }
.rb-n { background:#f2f2f2; color:#999; }
 
.prod-name { font-weight:700; font-size:.9rem; color:#0d2a5e; margin-bottom:.12rem; }
.prod-cat  { font-size:.73rem; color:#bbb; font-weight:500; }
 
/* Barras de progreso */
.prog-wrap     { background:#eef2ff; border-radius:99px; height:7px; overflow:hidden; margin-top:5px; max-width:170px; }
.prog-fill     { height:100%; border-radius:99px; background:linear-gradient(90deg,#2251a3,#6861ce); transition:width .9s cubic-bezier(.4,0,.2,1); }
.prog-wrap-ing { background:#e6f9f2; border-radius:99px; height:7px; overflow:hidden; margin-top:5px; max-width:170px; }
.prog-fill-ing { height:100%; border-radius:99px; background:linear-gradient(90deg,#17a060,#00b5ad); transition:width .9s cubic-bezier(.4,0,.2,1); }
 
/* ── Header reporte 3 (naranja/dorado) ── */
.rpt-header-todos {
    background: linear-gradient(135deg, #7d3800 0%, #e67e22 60%, #ff9f43 100%);
    border-radius: 16px;
    padding: 2rem 2.5rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.rpt-header-todos::before {
    content:''; position:absolute; top:-50px; right:-50px;
    width:200px; height:200px; border-radius:50%;
    background:rgba(254,249,124,.07);
}
.rpt-header-todos::after {
    content:''; position:absolute; bottom:-70px; left:28%;
    width:280px; height:280px; border-radius:50%;
    background:rgba(255,255,255,.04);
}
.rpt-header-todos h3 {
    color:#fff; font-size:1.7rem; font-weight:800;
    margin:0 0 .3rem; position:relative;
}
.rpt-header-todos h3 span { color:#fef97c; }
.rpt-header-todos p {
    color:rgba(255,255,255,.8); margin:0;
    font-size:.9rem; position:relative;
}
 
/* ── Tabla completa de todos los productos ── */
.todos-table { width:100%; border-collapse:separate; border-spacing:0 4px; }
.todos-table thead th {
    font-size:.7rem; text-transform:uppercase;
    letter-spacing:.07em; color:#aaa; font-weight:700;
    padding:.5rem .9rem; border:none; background:transparent;
    cursor:pointer; user-select:none;
    white-space:nowrap;
}
.todos-table thead th:hover { color:#e67e22; }
.todos-table thead th.sort-active { color:#e67e22; }
.todos-table tbody tr {
    background:#fff; border-radius:8px;
    box-shadow:0 1px 5px rgba(0,0,0,.04);
    transition:transform .12s, box-shadow .12s;
}
.todos-table tbody tr:hover {
    transform:translateX(3px);
    box-shadow:0 3px 14px rgba(230,126,34,.15);
}
.todos-table tbody td {
    padding:.72rem .9rem; border:none; vertical-align:middle;
    font-size:.875rem;
}
.todos-table tbody td:first-child { border-radius:8px 0 0 8px; }
.todos-table tbody td:last-child  { border-radius:0 8px 8px 0; }
 
/* Búsqueda en tabla */
.search-todos {
    border-radius:8px; border:1.5px solid #e0e0e0;
    font-size:.88rem; padding:.45rem .9rem;
    transition:border-color .2s;
}
.search-todos:focus {
    border-color:#e67e22;
    box-shadow:0 0 0 .18rem rgba(230,126,34,.2);
    outline:none;
}
 
/* Paginación */
.pag-wrap { display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
.pag-btn {
    width:32px; height:32px; border-radius:7px; border:1.5px solid #e0e0e0;
    background:#fff; font-size:.8rem; font-weight:700; color:#666;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:all .15s;
}
.pag-btn:hover { border-color:#e67e22; color:#e67e22; }
.pag-btn.active { background:#e67e22; border-color:#e67e22; color:#fff; }
.pag-btn:disabled { opacity:.4; cursor:not-allowed; }
.pag-info { font-size:.78rem; color:#aaa; margin-left:.3rem; }
 
/* Orden select */
.orden-select {
    border-radius:8px; border:1.5px solid #e0e0e0;
    font-size:.82rem; padding:.3rem .7rem;
}
.orden-select:focus { border-color:#e67e22; outline:none; box-shadow:0 0 0 .18rem rgba(230,126,34,.2); }
 
/* Pill categoría */
.cat-pill {
    display:inline-block; font-size:.68rem; font-weight:700;
    padding:.15rem .55rem; border-radius:99px;
    background:#fff3e0; color:#e67e22;
    white-space:nowrap;
}
 
/* Barra inline en tabla */
.mini-bar-wrap { background:#f5f5f5; border-radius:99px; height:5px; width:80px; overflow:hidden; display:inline-block; vertical-align:middle; margin-left:6px; }
.mini-bar-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,#e67e22,#ff9f43); }
 
/* Resumen total */
.resumen-box {
    background: linear-gradient(135deg,#fff8f0,#fff);
    border:1.5px solid #ffe0b2; border-radius:12px;
    padding:.9rem 1.2rem; margin-bottom:1rem;
    display:flex; flex-wrap:wrap; gap:1.5rem; align-items:center;
}
.resumen-item { font-size:.82rem; color:#666; }
.resumen-item strong { font-size:1.05rem; color:#e67e22; font-weight:800; display:block; line-height:1.1; }
 
 
.empty-box { text-align:center; padding:4rem 2rem; color:#ccc; }
.empty-box i { font-size:3rem; display:block; margin-bottom:1rem; }
 
/* ── Print ── */
@media print {
    .sidebar, .main-header, .filtros-box, .btn-export-wrap { display:none !important; }
    .rpt-header, .rpt-header-ing { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
}
@media (max-width:768px) {
    .rpt-header, .rpt-header-ing { padding:1.4rem 1.2rem; }
    .rpt-header h3, .rpt-header-ing h3 { font-size:1.3rem; }
}
</style>
 
<div class="container-fluid">
<div class="page-inner">
 
    <!-- ═══════════════════════════════════════════════════════════════════════
         REPORTE 1 — TOP 10 POR CANTIDAD VENDIDA
    ════════════════════════════════════════════════════════════════════════════ -->
    <div class="rpt-header mb-3">
        <h3><i class="fas fa-trophy me-2"></i> Top 10 <span>Productos</span> Más Vendidos <small style="font-size:.9rem;opacity:.8;">(por Cantidad)</small></h3>
        <p>
            <?php if ($fecha_ini && $fecha_fin): ?>
                Periodo: <?php echo date('d/m/Y', strtotime($fecha_ini)); ?> — <?php echo date('d/m/Y', strtotime($fecha_fin)); ?>
            <?php else: ?>
                Todos los periodos registrados
            <?php endif; ?>
            <?php if ($categoria): ?> &nbsp;·&nbsp; Categoría: <strong style="color:#fef97c;"><?php echo htmlspecialchars($categoria); ?></strong><?php endif; ?>
        </p>
    </div>
 
    <!-- MÉTRICAS (compartidas) -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="met-card mc1">
                <div class="met-ico"><i class="fas fa-shopping-cart"></i></div>
                <div>
                    <div class="met-val"><?php echo number_format($metricas['total_ventas'] ?? 0); ?></div>
                    <div class="met-lbl">Ventas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="met-card mc2">
                <div class="met-ico"><i class="fas fa-boxes"></i></div>
                <div>
                    <div class="met-val"><?php echo number_format($metricas['total_unidades'] ?? 0); ?></div>
                    <div class="met-lbl">Unidades</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="met-card mc3">
                <div class="met-ico"><i class="fas fa-coins"></i></div>
                <div>
                    <div class="met-val">S/ <?php echo number_format($metricas['total_ingresos'] ?? 0, 2); ?></div>
                    <div class="met-lbl">Ingresos</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="met-card mc4">
                <div class="met-ico"><i class="fas fa-tags"></i></div>
                <div>
                    <div class="met-val"><?php echo number_format($metricas['productos_distintos'] ?? 0); ?></div>
                    <div class="met-lbl">Productos</div>
                </div>
            </div>
        </div>
    </div>
 
    <!-- FILTROS (aplica a ambos reportes) -->
    <div class="filtros-box">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-md-3">
                <label>Desde</label>
                <input type="date" name="fecha_ini" class="form-control"
                    value="<?php echo htmlspecialchars($fecha_ini ?? ''); ?>">
            </div>
            <div class="col-12 col-md-3">
                <label>Hasta</label>
                <input type="date" name="fecha_fin" class="form-control"
                    value="<?php echo htmlspecialchars($fecha_fin ?? ''); ?>">
            </div>
            <div class="col-12 col-md-3">
                <label>Categoría</label>
                <select name="categoria" class="form-select">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>"
                            <?php echo ($categoria === $cat) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-filtrar flex-grow-1">
                    <i class="fas fa-search me-1"></i> Filtrar
                </button>
                <a href="productos-top.php" class="btn btn-limpiar px-3" title="Limpiar filtros">
                    <i class="fas fa-broom"></i>
                </a>
            </div>
        </form>
    </div>
 
    <!-- TABLA + GRAFICOS REPORTE 1 -->
    <?php if (empty($top10)): ?>
        <div class="empty-box">
            <i class="fas fa-chart-bar"></i>
            <h5>Sin datos para mostrar</h5>
            <p>Ajusta los filtros o verifica que haya ventas registradas.</p>
        </div>
    <?php else:
        $max_cant = max(array_column($top10, 'total_vendido')) ?: 1;
    ?>
    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="panel-card">
                <div class="panel-title"><i class="fas fa-medal"></i> Ranking por Cantidad Vendida</div>
                <div class="table-responsive">
                    <table class="rank-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th class="text-center">Uds.</th>
                                <th class="text-end">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($top10 as $i => $row):
                            $pos   = $i + 1;
                            $pct   = round(($row['total_vendido'] / $max_cant) * 100);
                            $badge = match($pos) { 1=>'rb-1', 2=>'rb-2', 3=>'rb-3', default=>'rb-n' };
                            $emoji = match($pos) { 1=>'&#x1F947;', 2=>'&#x1F948;', 3=>'&#x1F949;', default=>$pos };
                        ?>
                            <tr>
                                <td><span class="rank-badge <?php echo $badge; ?>"><?php echo $emoji; ?></span></td>
                                <td>
                                    <div class="prod-name"><?php echo htmlspecialchars($row['producto']); ?></div>
                                    <div class="prod-cat"><?php echo htmlspecialchars($row['categoria'] ?? '—'); ?></div>
                                    <div class="prog-wrap"><div class="prog-fill" style="width:<?php echo $pct; ?>%;"></div></div>
                                </td>
                                <td class="text-center">
                                    <span style="font-weight:800;color:#2251a3;font-size:1.05rem;"><?php echo number_format($row['total_vendido']); ?></span>
                                </td>
                                <td class="text-end">
                                    <span style="font-weight:700;color:#17a060;">S/ <?php echo number_format($row['total_ingresos'], 2); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5 d-flex flex-column gap-3">
            <div class="panel-card">
                <div class="panel-title"><i class="fas fa-chart-bar"></i> Unidades Vendidas</div>
                <canvas id="chartUnidades" height="220"></canvas>
            </div>
            <div class="panel-card">
                <div class="panel-title"><i class="fas fa-chart-pie"></i> Distribución de Ingresos</div>
                <canvas id="chartIngresos" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="btn-export-wrap text-end mt-3">
        <button class="btn btn-outline-secondary btn-round btn-sm" onclick="exportarCSV()">
            <i class="fas fa-file-csv me-1"></i> Exportar CSV (Cantidad)
        </button>
        <button class="btn btn-outline-secondary btn-round btn-sm ms-2" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Imprimir
        </button>
    </div>
    <?php endif; ?>
 
 
    <!-- ═══════════════════════════════════════════════════════════════════════
         DIVISOR
    ════════════════════════════════════════════════════════════════════════════ -->
    <hr class="seccion-divider">
 
 
    <!-- ═══════════════════════════════════════════════════════════════════════
         REPORTE 2 — TOP 10 POR MAYOR INGRESO
    ════════════════════════════════════════════════════════════════════════════ -->
    <div class="rpt-header-ing mb-3">
        <h3><i class="fas fa-coins me-2"></i> Top 10 <span>Productos</span> con Mayor Ingreso <small style="font-size:.9rem;opacity:.8;">(por Ingresos)</small></h3>
        <p>
            <?php if ($fecha_ini && $fecha_fin): ?>
                Periodo: <?php echo date('d/m/Y', strtotime($fecha_ini)); ?> — <?php echo date('d/m/Y', strtotime($fecha_fin)); ?>
            <?php else: ?>
                Todos los periodos registrados
            <?php endif; ?>
            <?php if ($categoria): ?> &nbsp;·&nbsp; Categoría: <strong style="color:#fef97c;"><?php echo htmlspecialchars($categoria); ?></strong><?php endif; ?>
        </p>
    </div>
 
    <!-- TABLA + GRAFICOS REPORTE 2 -->
    <?php if (empty($top10_ingresos)): ?>
        <div class="empty-box">
            <i class="fas fa-chart-bar"></i>
            <h5>Sin datos para mostrar</h5>
            <p>Ajusta los filtros o verifica que haya ventas registradas.</p>
        </div>
    <?php else:
        $max_ing = max(array_column($top10_ingresos, 'total_ingresos')) ?: 1;
    ?>
    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="panel-card">
                <div class="panel-title ing"><i class="fas fa-medal"></i> Ranking por Mayor Ingreso</div>
                <div class="table-responsive">
                    <table class="rank-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th class="text-end">Ingresos</th>
                                <th class="text-center">Uds.</th>
                                <th class="text-end">Precio Prom.</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($top10_ingresos as $i => $row):
                            $pos   = $i + 1;
                            $pct   = round(($row['total_ingresos'] / $max_ing) * 100);
                            $badge = match($pos) { 1=>'rb-1', 2=>'rb-2', 3=>'rb-3', default=>'rb-n' };
                            $emoji = match($pos) { 1=>'&#x1F947;', 2=>'&#x1F948;', 3=>'&#x1F949;', default=>$pos };
                        ?>
                            <tr class="ing-row">
                                <td><span class="rank-badge <?php echo $badge; ?>"><?php echo $emoji; ?></span></td>
                                <td>
                                    <div class="prod-name"><?php echo htmlspecialchars($row['producto']); ?></div>
                                    <div class="prod-cat"><?php echo htmlspecialchars($row['categoria'] ?? '—'); ?></div>
                                    <div class="prog-wrap-ing"><div class="prog-fill-ing" style="width:<?php echo $pct; ?>%;"></div></div>
                                </td>
                                <td class="text-end">
                                    <span style="font-weight:800;color:#17a060;font-size:1.05rem;">S/ <?php echo number_format($row['total_ingresos'], 2); ?></span>
                                </td>
                                <td class="text-center">
                                    <span style="font-weight:700;color:#2251a3;"><?php echo number_format($row['total_vendido']); ?></span>
                                </td>
                                <td class="text-end">
                                    <span style="font-weight:600;color:#6861ce;">S/ <?php echo number_format($row['precio_promedio'], 2); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5 d-flex flex-column gap-3">
            <div class="panel-card">
                <div class="panel-title ing"><i class="fas fa-chart-bar"></i> Ingresos por Producto</div>
                <canvas id="chartIngresosBar" height="220"></canvas>
            </div>
            <div class="panel-card">
                <div class="panel-title ing"><i class="fas fa-chart-pie"></i> Distribución de Unidades</div>
                <canvas id="chartUnidadesDona" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="btn-export-wrap text-end mt-3 mb-4">
        <button class="btn btn-outline-secondary btn-round btn-sm" onclick="exportarCSVIngresos()">
            <i class="fas fa-file-csv me-1"></i> Exportar CSV (Ingresos)
        </button>
        <button class="btn btn-outline-secondary btn-round btn-sm ms-2" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Imprimir
        </button>
    </div>
    <?php endif; ?>
 
 
 
    <!-- ═══════════════════════════════════════════════════════════════════════
         DIVISOR 2
    ════════════════════════════════════════════════════════════════════════════ -->
    <hr class="seccion-divider">
 
    <!-- ═══════════════════════════════════════════════════════════════════════
         REPORTE 3 — TODOS LOS PRODUCTOS
    ════════════════════════════════════════════════════════════════════════════ -->
    <div class="rpt-header-todos mb-3">
        <h3><i class="fas fa-list me-2"></i> <span>Todos</span> los Productos</h3>
        <p>
            Listado completo con cantidades vendidas e ingresos generados.
            <?php if ($fecha_ini && $fecha_fin): ?>
                · Periodo: <?php echo date('d/m/Y', strtotime($fecha_ini)); ?> — <?php echo date('d/m/Y', strtotime($fecha_fin)); ?>
            <?php endif; ?>
            <?php if ($categoria): ?> · Categoría: <strong style="color:#fef97c;"><?php echo htmlspecialchars($categoria); ?></strong><?php endif; ?>
        </p>
    </div>
 
    <?php if (empty($todos)): ?>
        <div class="empty-box">
            <i class="fas fa-box-open"></i>
            <h5>Sin productos para mostrar</h5>
            <p>Ajusta los filtros o verifica que haya ventas registradas.</p>
        </div>
    <?php else:
        $total_todos_uds = array_sum(array_column($todos, 'total_vendido'));
        $total_todos_ing = array_sum(array_column($todos, 'total_ingresos'));
        $max_ing_todos   = max(array_column($todos, 'total_ingresos')) ?: 1;
    ?>
 
    <div class="panel-card mb-3">
 
        <!-- Resumen global -->
        <div class="resumen-box">
            <div class="resumen-item">
                <strong><?php echo count($todos); ?></strong>
                Productos distintos
            </div>
            <div class="resumen-item">
                <strong><?php echo number_format($total_todos_uds); ?></strong>
                Unidades totales
            </div>
            <div class="resumen-item">
                <strong>S/ <?php echo number_format($total_todos_ing, 2); ?></strong>
                Ingresos totales
            </div>
        </div>
 
        <!-- Controles -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <input type="text" id="buscarProducto" class="search-todos flex-grow-1"
                placeholder="&#xf002; Buscar producto..." style="min-width:180px;">
 
            <form method="GET" class="d-flex align-items-center gap-1 mb-0">
                <?php if ($fecha_ini): ?><input type="hidden" name="fecha_ini" value="<?php echo htmlspecialchars($fecha_ini); ?>"><?php endif; ?>
                <?php if ($fecha_fin): ?><input type="hidden" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>"><?php endif; ?>
                <?php if ($categoria): ?><input type="hidden" name="categoria" value="<?php echo htmlspecialchars($categoria); ?>"><?php endif; ?>
                <label style="font-size:.75rem;font-weight:700;color:#aaa;text-transform:uppercase;white-space:nowrap;">Ordenar:</label>
                <select name="orden_todos" class="orden-select" onchange="this.form.submit()">
                    <option value="ingresos"  <?php echo $orden_todos==='ingresos' ?'selected':''; ?>>Mayor ingreso</option>
                    <option value="cantidad"  <?php echo $orden_todos==='cantidad' ?'selected':''; ?>>Mayor cantidad</option>
                    <option value="precio"    <?php echo $orden_todos==='precio'   ?'selected':''; ?>>Mayor precio prom.</option>
                    <option value="producto"  <?php echo $orden_todos==='producto' ?'selected':''; ?>>Nombre A-Z</option>
                </select>
            </form>
 
            <button class="btn btn-outline-secondary btn-round btn-sm" onclick="exportarCSVTodos()">
                <i class="fas fa-file-csv me-1"></i> CSV
            </button>
        </div>
 
        <!-- Tabla -->
        <div class="table-responsive">
            <table class="todos-table" id="tablaTodos">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th class="text-end">Ingresos</th>
                        <th class="text-center">Unidades</th>
                        <th class="text-end">Precio Prom.</th>
                        <th class="text-center">Ventas</th>
                        <th style="width:100px;">% Ingreso</th>
                    </tr>
                </thead>
                <tbody id="tbodyTodos">
                <?php foreach ($todos as $i => $row):
                    $pct_ing = $total_todos_ing > 0 ? round(($row['total_ingresos'] / $total_todos_ing) * 100, 2) : 0;
                    $bar_w   = round(($row['total_ingresos'] / $max_ing_todos) * 100);
                ?>
                    <tr data-nombre="<?php echo strtolower(htmlspecialchars($row['producto'])); ?>"
                        data-cat="<?php echo strtolower(htmlspecialchars($row['categoria'] ?? '')); ?>">
                        <td style="color:#ccc;font-weight:700;font-size:.8rem;"><?php echo $i+1; ?></td>
                        <td>
                            <span class="prod-name"><?php echo htmlspecialchars($row['producto']); ?></span>
                        </td>
                        <td>
                            <span class="cat-pill"><?php echo htmlspecialchars($row['categoria'] ?? '—'); ?></span>
                        </td>
                        <td class="text-end">
                            <span style="font-weight:800;color:#e67e22;">
                                S/ <?php echo number_format($row['total_ingresos'], 2); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span style="font-weight:700;color:#2251a3;">
                                <?php echo number_format($row['total_vendido']); ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <span style="font-weight:600;color:#6861ce;">
                                S/ <?php echo number_format($row['precio_promedio'], 2); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span style="font-weight:600;color:#666;">
                                <?php echo number_format($row['num_ventas']); ?>
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:5px;">
                                <div class="mini-bar-wrap">
                                    <div class="mini-bar-fill" style="width:<?php echo $bar_w; ?>%;"></div>
                                </div>
                                <span style="font-size:.72rem;color:#aaa;white-space:nowrap;"><?php echo $pct_ing; ?>%</span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
 
        <!-- Paginación -->
        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
            <div class="pag-wrap" id="paginacion"></div>
            <span class="pag-info" id="pagInfo"></span>
        </div>
    </div>
 
    <?php endif; ?>
 
</div><!-- .page-inner -->
</div><!-- .container-fluid -->
 
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
 
<?php if (!empty($top10)): ?>
<script>
// ── Datos reporte 1 (cantidad) ────────────────────────────────────────────────
const labelsCortos    = <?php echo json_encode(array_values($labels_cortos)); ?>;
const labelsCompletos = <?php echo json_encode(array_values($labels)); ?>;
const dataUnidades    = <?php echo json_encode(array_map('intval',   array_values($cantidades))); ?>;
const dataIngresos    = <?php echo json_encode(array_map('floatval', array_values($ingresos))); ?>;
 
// ── Datos reporte 2 (ingresos) ────────────────────────────────────────────────
const labelsCortosIng    = <?php echo json_encode(array_values($labels_cortos_ing)); ?>;
const labelsCompletosIng = <?php echo json_encode(array_values($labels_ing)); ?>;
const dataUnidadesIng    = <?php echo json_encode(array_map('intval',   array_values($cantidades_ing))); ?>;
const dataIngresosIng    = <?php echo json_encode(array_map('floatval', array_values($ingresos_ing))); ?>;
 
const paleta = [
    '#2251a3','#6861ce','#17a060','#ff9f43','#ee4d4d',
    '#00b5ad','#0d2a5e','#a29bfe','#fd79a8','#fdcb6e'
];
const paletaVerde = [
    '#17a060','#00b5ad','#2251a3','#6861ce','#ff9f43',
    '#ee4d4d','#0d2a5e','#a29bfe','#fd79a8','#fdcb6e'
];
 
// ── Reporte 1: barras unidades ────────────────────────────────────────────────
new Chart(document.getElementById('chartUnidades'), {
    type: 'bar',
    data: {
        labels: labelsCortos,
        datasets: [{ label:'Unidades', data:dataUnidades, backgroundColor:paleta, borderRadius:6, borderSkipped:false }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display:false },
            tooltip: { callbacks: { title:(i)=>labelsCompletos[i[0].dataIndex], label:(i)=>` ${i.raw.toLocaleString()} unidades` } }
        },
        scales: {
            x: { ticks:{font:{size:10},maxRotation:38}, grid:{display:false} },
            y: { ticks:{font:{size:10}}, grid:{color:'rgba(0,0,0,0.05)'} }
        }
    }
});
 
// ── Reporte 1: dona ingresos ──────────────────────────────────────────────────
new Chart(document.getElementById('chartIngresos'), {
    type: 'doughnut',
    data: {
        labels: labelsCortos,
        datasets: [{ data:dataIngresos, backgroundColor:paleta, borderWidth:2, borderColor:'#fff', hoverOffset:8 }]
    },
    options: {
        responsive: true, cutout:'58%',
        plugins: {
            legend: { position:'right', labels:{ font:{size:10}, boxWidth:12, padding:8 } },
            tooltip: { callbacks: { title:(i)=>labelsCompletos[i[0].dataIndex], label:(i)=>` S/ ${i.raw.toFixed(2)}` } }
        }
    }
});
 
// ── Reporte 2: barras ingresos ────────────────────────────────────────────────
new Chart(document.getElementById('chartIngresosBar'), {
    type: 'bar',
    data: {
        labels: labelsCortosIng,
        datasets: [{ label:'Ingresos', data:dataIngresosIng, backgroundColor:paletaVerde, borderRadius:6, borderSkipped:false }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display:false },
            tooltip: { callbacks: { title:(i)=>labelsCompletosIng[i[0].dataIndex], label:(i)=>` S/ ${i.raw.toLocaleString('es-PE',{minimumFractionDigits:2})}` } }
        },
        scales: {
            x: { ticks:{font:{size:10},maxRotation:38}, grid:{display:false} },
            y: { ticks:{ font:{size:10}, callback:(v)=>'S/ '+v.toLocaleString() }, grid:{color:'rgba(0,0,0,0.05)'} }
        }
    }
});
 
// ── Reporte 2: dona unidades ──────────────────────────────────────────────────
new Chart(document.getElementById('chartUnidadesDona'), {
    type: 'doughnut',
    data: {
        labels: labelsCortosIng,
        datasets: [{ data:dataUnidadesIng, backgroundColor:paletaVerde, borderWidth:2, borderColor:'#fff', hoverOffset:8 }]
    },
    options: {
        responsive: true, cutout:'58%',
        plugins: {
            legend: { position:'right', labels:{ font:{size:10}, boxWidth:12, padding:8 } },
            tooltip: { callbacks: { title:(i)=>labelsCompletosIng[i[0].dataIndex], label:(i)=>` ${i.raw.toLocaleString()} unidades` } }
        }
    }
});
 
// ── Exportar CSV reporte 1 ────────────────────────────────────────────────────
function exportarCSV() {
    const filas = <?php echo json_encode(array_map(fn($r,$i) => [
        'pos'       => $i+1,
        'producto'  => $r['producto'],
        'categoria' => $r['categoria'] ?? '',
        'unidades'  => $r['total_vendido'],
        'ingresos'  => number_format($r['total_ingresos'],2,'.',''),
    ], $top10, array_keys($top10))); ?>;
    let csv = "Posicion,Producto,Categoria,Unidades,Ingresos (S/)\n";
    filas.forEach(r => { csv += `${r.pos},"${r.producto}","${r.categoria}",${r.unidades},${r.ingresos}\n`; });
    const blob = new Blob(["\uFEFF"+csv], {type:'text/csv;charset=utf-8;'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'top10_cantidad_<?php echo date("Ymd"); ?>.csv';
    a.click();
}
 
// ── Exportar CSV reporte 2 ────────────────────────────────────────────────────
function exportarCSVIngresos() {
    const filas = <?php echo json_encode(array_map(fn($r,$i) => [
        'pos'             => $i+1,
        'producto'        => $r['producto'],
        'categoria'       => $r['categoria'] ?? '',
        'ingresos'        => number_format($r['total_ingresos'],2,'.',''),
        'unidades'        => $r['total_vendido'],
        'precio_promedio' => number_format($r['precio_promedio'],2,'.',''),
    ], $top10_ingresos, array_keys($top10_ingresos))); ?>;
    let csv = "Posicion,Producto,Categoria,Ingresos (S/),Unidades,Precio Promedio (S/)\n";
    filas.forEach(r => { csv += `${r.pos},"${r.producto}","${r.categoria}",${r.ingresos},${r.unidades},${r.precio_promedio}\n`; });
    const blob = new Blob(["\uFEFF"+csv], {type:'text/csv;charset=utf-8;'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'top10_ingresos_<?php echo date("Ymd"); ?>.csv';
    a.click();
}// ── Reporte 3: búsqueda + paginación ─────────────────────────────────────────
(function() {
    const POR_PAG = 25;
    let pagActual = 1;
    let filasFiltradas = [];
 
    const tbody      = document.getElementById('tbodyTodos');
    const buscar     = document.getElementById('buscarProducto');
    const paginacion = document.getElementById('paginacion');
    const pagInfo    = document.getElementById('pagInfo');
 
    if (!tbody) return;
 
    const todasFilas = Array.from(tbody.querySelectorAll('tr'));
 
    function filtrar() {
        const q = buscar.value.toLowerCase().trim();
        filasFiltradas = todasFilas.filter(tr =>
            tr.dataset.nombre.includes(q) || tr.dataset.cat.includes(q)
        );
        pagActual = 1;
        renderizar();
    }
 
    function renderizar() {
        const total   = filasFiltradas.length;
        const totalPag = Math.max(1, Math.ceil(total / POR_PAG));
        pagActual = Math.min(pagActual, totalPag);
 
        const desde = (pagActual - 1) * POR_PAG;
        const hasta = Math.min(desde + POR_PAG, total);
 
        todasFilas.forEach(tr => tr.style.display = 'none');
        filasFiltradas.slice(desde, hasta).forEach(tr => tr.style.display = '');
 
        // Info
        pagInfo.textContent = `Mostrando ${desde+1}–${hasta} de ${total} productos`;
 
        // Botones paginación
        paginacion.innerHTML = '';
        const addBtn = (label, page, disabled = false, active = false) => {
            const b = document.createElement('button');
            b.className = 'pag-btn' + (active ? ' active' : '');
            b.innerHTML = label;
            b.disabled  = disabled;
            b.onclick   = () => { pagActual = page; renderizar(); };
            paginacion.appendChild(b);
        };
 
        addBtn('&laquo;', 1,             pagActual === 1);
        addBtn('&lsaquo;', pagActual - 1, pagActual === 1);
 
        // Páginas visibles: max 5
        let start = Math.max(1, pagActual - 2);
        let end   = Math.min(totalPag, start + 4);
        start     = Math.max(1, end - 4);
        for (let p = start; p <= end; p++) {
            addBtn(p, p, false, p === pagActual);
        }
 
        addBtn('&rsaquo;', pagActual + 1, pagActual === totalPag);
        addBtn('&raquo;', totalPag,       pagActual === totalPag);
    }
 
    buscar.addEventListener('input', filtrar);
    filtrar(); // inicializar
})();
 
// ── Exportar CSV reporte 3 ────────────────────────────────────────────────────
function exportarCSVTodos() {
    const filas = <?php echo json_encode(array_map(fn($r, $i) => [
        'pos'             => $i + 1,
        'producto'        => $r['producto'],
        'categoria'       => $r['categoria'] ?? '',
        'ingresos'        => number_format($r['total_ingresos'], 2, '.', ''),
        'unidades'        => $r['total_vendido'],
        'precio_promedio' => number_format($r['precio_promedio'], 2, '.', ''),
        'num_ventas'      => $r['num_ventas'],
    ], $todos, array_keys($todos))); ?>;
 
    let csv = "N°,Producto,Categoría,Ingresos (S/),Unidades,Precio Promedio (S/),Num. Ventas\n";
    filas.forEach(r => {
        csv += `${r.pos},"${r.producto}","${r.categoria}",${r.ingresos},${r.unidades},${r.precio_promedio},${r.num_ventas}\n`;
    });
 
    const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'todos_productos_<?php echo date("Ymd"); ?>.csv';
    a.click();
}
</script>
<?php endif; ?>

<?php include("pie.php"); ?>