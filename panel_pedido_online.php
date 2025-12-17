<?php
/**
 * Panel de Pedidos Online - Para el sistema POS
 * Muestra las notificaciones de pedidos de la tienda online
 */

require_once 'cabecera.php';
require_once 'includes/db.php';

$db = new DB();

// Marcar notificación como leída si se recibe el parámetro
if (isset($_GET['marcar_leido']) && is_numeric($_GET['marcar_leido'])) {
    $stmt = $db->pdo->prepare("UPDATE notificaciones_pos SET leido = 1, fecha_lectura = NOW() WHERE id = ?");
    $stmt->execute([$_GET['marcar_leido']]);
}

// Obtener pedidos pendientes
$stmt = $db->pdo->prepare("
    SELECT 
        n.id as notif_id,
        n.venta_id,
        n.mensaje,
        n.datos_json,
        n.leido,
        n.fecha_creacion,
        v.estado_venta,
        v.notas_pedido,
        v.monto_total,
        p.nombre_completo,
        p.telefono,
        p.email
    FROM notificaciones_pos n
    INNER JOIN venta v ON n.venta_id = v.id
    INNER JOIN persona p ON n.id_persona = p.id
    WHERE n.tipo = 'nuevo_pedido_online'
    ORDER BY n.fecha_creacion DESC
    LIMIT 50
");
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar pedidos no leídos
$stmt_count = $db->pdo->query("SELECT COUNT(*) FROM notificaciones_pos WHERE leido = 0 AND tipo = 'nuevo_pedido_online'");
$pedidos_nuevos = $stmt_count->fetchColumn();
?>

<style>
.pedido-card {
    transition: all 0.3s ease;
    border-left: 4px solid #0066ff;
}
.pedido-card.no-leido {
    background: #e3f2fd;
    border-left-color: #ff6b6b;
}
.pedido-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.badge-nuevo {
    background: #ff6b6b;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}
.whatsapp-btn {
    background: #25D366;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.whatsapp-btn:hover {
    background: #128C7E;
    color: white;
}
</style>

<div class="container">
    <div class="page-inner">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h3><i class="fas fa-shopping-bag"></i> Pedidos Online</h3>
                    <p class="text-muted mb-0">Gestiona los pedidos recibidos desde la tienda web</p>
                </div>
                <?php if ($pedidos_nuevos > 0): ?>
                <span class="badge-nuevo">
                    <i class="fas fa-bell"></i> <?= $pedidos_nuevos ?> nuevo(s)
                </span>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <?php if (empty($pedidos)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox" style="font-size: 4rem; color: #ddd;"></i>
                        <h4 class="mt-3">No hay pedidos online</h4>
                        <p class="text-muted">Los pedidos de la tienda web aparecerán aquí</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($pedidos as $pedido): 
                            $datos = json_decode($pedido['datos_json'], true);
                            $no_leido = $pedido['leido'] == 0;
                        ?>
                        <div class="col-md-6 mb-3">
                            <div class="card pedido-card <?= $no_leido ? 'no-leido' : '' ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-user"></i> <?= htmlspecialchars($pedido['nombre_completo']) ?>
                                            <?php if ($no_leido): ?>
                                            <span class="badge bg-danger ms-2">Nuevo</span>
                                            <?php endif; ?>
                                        </h5>
                                        <span class="badge bg-<?= $pedido['estado_venta'] == 'Pendiente' ? 'warning' : 'success' ?>">
                                            <?= $pedido['estado_venta'] ?>
                                        </span>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="far fa-clock"></i> 
                                            <?= date('d/m/Y H:i', strtotime($pedido['fecha_creacion'])) ?>
                                        </small>
                                    </div>

                                    <div class="mb-2">
                                        <strong>Pedido #<?= $pedido['venta_id'] ?></strong>
                                    </div>

                                    <div class="mb-2">
                                        <i class="fas fa-phone"></i> 
                                        <a href="tel:<?= htmlspecialchars($pedido['telefono']) ?>">
                                            <?= htmlspecialchars($pedido['telefono']) ?>
                                        </a>
                                    </div>

                                    <?php if (!empty($pedido['email'])): ?>
                                    <div class="mb-2">
                                        <i class="fas fa-envelope"></i> 
                                        <?= htmlspecialchars($pedido['email']) ?>
                                    </div>
                                    <?php endif; ?>

                                    <div class="mb-2">
                                        <i class="fas fa-box"></i> 
                                        <?= $datos['items'] ?? 0 ?> producto(s)
                                    </div>

                                    <div class="mb-3">
                                        <strong style="font-size: 1.3rem; color: #0066ff;">
                                            S/. <?= number_format($pedido['monto_total'], 2) ?>
                                        </strong>
                                    </div>

                                    <?php if (!empty($pedido['notas_pedido'])): ?>
                                    <div class="alert alert-info mb-3">
                                        <strong>Notas:</strong> <?= htmlspecialchars($pedido['notas_pedido']) ?>
                                    </div>
                                    <?php endif; ?>

                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="venta_corte_material.php?venta_id=<?= $pedido['venta_id'] ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Ver Detalle
                                        </a>

                                        <a href="https://wa.me/51<?= preg_replace('/[^0-9]/', '', $pedido['telefono']) ?>?text=Hola%20<?= urlencode($pedido['nombre_completo']) ?>,%20tu%20pedido%20%23<?= $pedido['venta_id'] ?>%20está%20en%20proceso" 
                                           target="_blank"
                                           class="whatsapp-btn btn-sm">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </a>

                                        <?php if ($no_leido): ?>
                                        <a href="?marcar_leido=<?= $pedido['notif_id'] ?>" 
                                           class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i> Marcar leído
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh cada 30 segundos para mostrar nuevos pedidos
setInterval(function() {
    // Solo recargar si hay pedidos nuevos
    fetch('api_check_pedidos.php')
        .then(r => r.json())
        .then(data => {
            if (data.nuevos > 0 && !document.querySelector('.badge-nuevo')) {
                location.reload();
            }
        });
}, 30000);
</script>

<?php require_once 'pie.php'; ?>