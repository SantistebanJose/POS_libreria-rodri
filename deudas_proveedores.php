<?php
include("cabecera.php");
?>

<style>
    /* ── Tarjetas de estadísticas ── */
    .stat-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 16px 20px;
        transition: box-shadow .2s;
    }
    .stat-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08); }
    .stat-card .lbl {
        font-size: 11px;
        color: #8a9ab0;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 6px;
        font-weight: 600;
    }
    .stat-card .val { font-size: 26px; font-weight: 700; margin: 0; }
    .val-rojo    { color: #c0392b; }
    .val-naranja { color: #c87f0a; }
    .val-azul    { color: #1a5276; }
    .val-verde   { color: #1a7a3f; }

    /* ── Tabs de filtro ── */
    .deuda-tabs { display: flex; gap: 6px; flex-wrap: wrap; }
    .deuda-tab {
        padding: 6px 18px;
        border-radius: 20px;
        border: 1px solid #dde3ec;
        background: #fff;
        font-size: 13px;
        cursor: pointer;
        color: #556;
        font-weight: 500;
        transition: all .15s;
    }
    .deuda-tab.activo, .deuda-tab:hover {
        background: #6861ce;
        color: #fff;
        border-color: #6861ce;
    }

    /* ── Tarjetas de deuda ── */
    .deuda-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        border-left: 5px solid #ccc;
        padding: 16px 20px;
        margin-bottom: 12px;
        transition: box-shadow .2s;
    }
    .deuda-card:hover { box-shadow: 0 3px 14px rgba(0,0,0,.07); }
    .deuda-card.vencida { border-left-color: #c0392b; }
    .deuda-card.proxima { border-left-color: #c87f0a; }
    .deuda-card.ok      { border-left-color: #1a7a3f; }
    .deuda-card.pagada  { border-left-color: #aab; opacity: .65; }

    .deuda-proveedor { font-size: 15px; font-weight: 700; color: #1a2a3a; }
    .deuda-monto     { font-size: 22px; font-weight: 700; white-space: nowrap; }
    .deuda-monto.vencida { color: #c0392b; }
    .deuda-monto.proxima { color: #c87f0a; }
    .deuda-monto.ok      { color: #1a5276; }
    .deuda-monto.pagada  { color: #99a; }

    .deuda-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .deuda-badge.vencida { background: #fde8e8; color: #c0392b; }
    .deuda-badge.proxima { background: #fef3cd; color: #856404; }
    .deuda-badge.ok      { background: #d4f0e0; color: #1a7a3f; }
    .deuda-badge.pagada  { background: #eef1f5; color: #778; }

    .deuda-fecha  { font-size: 12px; color: #8a9ab0; }
    .deuda-desc   { font-size: 13px; color: #667; margin-top: 6px; line-height: 1.5; }
    .deuda-cuotas { font-size: 13px; color: #556; margin-top: 3px; }
    .deuda-cuotas strong { color: #334; }
    .deuda-actions {
        display: flex; gap: 8px; flex-wrap: wrap;
        margin-top: 12px; padding-top: 10px;
        border-top: 1px solid #f0f2f5;
    }

    .sin-deudas   { text-align: center; padding: 60px 20px; color: #aab; font-size: 15px; }
    .cargando-msg { text-align: center; padding: 50px; color: #8a9ab0; }
</style>

<div class="container">
    <div class="page-inner">
        <div class="card text-start">
            <div class="card-body">

                <!-- Cabecera -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-credit-card"></i> Deudas a Proveedores
                    </h4>
                    <button class="btn btn-success rounded-5" onclick="abrirModalNuevaDeuda()">
                        <i class="fas fa-plus-circle"></i> Nueva Deuda
                    </button>
                </div>
                <hr>

                <!-- Estadísticas -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-auto flex-md-fill">
                        <div class="stat-card">
                            <div class="lbl">Total Adeudado</div>
                            <p class="val val-rojo" id="st-total">—</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-auto flex-md-fill">
                        <div class="stat-card">
                            <div class="lbl">Vencidas</div>
                            <p class="val val-naranja" id="st-vencidas">—</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-auto flex-md-fill">
                        <div class="stat-card">
                            <div class="lbl">Próx. 7 Días</div>
                            <p class="val val-azul" id="st-proximas">—</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-auto flex-md-fill">
                        <div class="stat-card">
                            <div class="lbl">Pendientes</div>
                            <p class="val val-azul" id="st-pendientes">—</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-auto flex-md-fill">
                        <div class="stat-card">
                            <div class="lbl">Pagadas</div>
                            <p class="val val-verde" id="st-pagadas">—</p>
                        </div>
                    </div>
                </div>

                <!-- Filtros + buscador -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div class="deuda-tabs">
                        <button class="deuda-tab activo" data-filtro="todas"     onclick="cambiarFiltro(this)">Todas</button>
                        <button class="deuda-tab"        data-filtro="pendiente" onclick="cambiarFiltro(this)">Pendientes</button>
                        <button class="deuda-tab"        data-filtro="vencida"   onclick="cambiarFiltro(this)">Vencidas</button>
                        <button class="deuda-tab"        data-filtro="pagada"    onclick="cambiarFiltro(this)">Pagadas</button>
                    </div>
                    <input type="text" id="buscadorDeuda" class="form-control"
                           style="max-width:220px; border-radius:25px; border:2px solid #6861ce;"
                           placeholder="Buscar proveedor..." oninput="buscarLocal()">
                </div>

                <!-- Lista de deudas -->
                <div id="listaDeudas">
                    <div class="cargando-msg">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Cargando deudas...</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     MODAL NUEVA / EDITAR DEUDA
════════════════════════════════════════════ -->
<div class="modal fade" id="modalDeuda" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalDeudaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalDeudaLabel">
                    <i class="fas fa-plus-circle"></i> Nueva Deuda a Proveedor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="f-id">
                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label"><strong>Proveedor</strong> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="f-proveedor"
                               placeholder="Ej: Tailoy, Carvimsa, Norma...">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label"><strong>Monto (S/)</strong> <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="f-monto"
                               placeholder="0.00" step="0.01" min="0.01">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Fecha límite de pago</strong> <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="f-fecha">
                    </div>

                    <div class="col-12">
                        <label class="form-label"><strong>Descripción</strong> <span class="text-muted">(opcional)</span></label>
                        <textarea class="form-control" id="f-desc" rows="2"
                                  placeholder="Ej: Mercadería escolar al crédito, útiles..."></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label"><strong>Cuotas / Forma de pago</strong> <span class="text-muted">(opcional)</span></label>
                        <input type="text" class="form-control" id="f-cuotas"
                               placeholder="Ej: 3 cuotas de S/2,600 — pago único...">
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-round" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success btn-round" id="btnGuardarDeuda" onclick="guardarDeuda()">
                    <i class="fas fa-save"></i> Guardar Deuda
                </button>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/scriptNotify.js"></script>

<script>
    // ── URL del backend (archivo separado, sin HTML) ──
    const API = 'logica/clssDeudaProveedor.php';

    let todasDeudas  = [];
    let filtroActual = 'todas';

    // ══════════════════════════════════════════════════
    $(document).ready(function () {
        cargarEstadisticas();
        cargarDeudas();
    });

    // ══════════════════════════════════════════════════
    // ESTADÍSTICAS
    // ══════════════════════════════════════════════════
    function cargarEstadisticas() {
        $.ajax({
            method:   'GET',
            url:      API,
            dataType: 'json',
            data:     { accion: 'ESTADISTICAS' },
            success: function (r) {
                if (!r.ok) return;
                var d = r.data;
                $('#st-total').text('S/ ' + parseFloat(d.total_adeudado).toLocaleString('es-PE', { minimumFractionDigits: 2 }));
                $('#st-vencidas').text(d.total_vencidas);
                $('#st-proximas').text(d.proximas_7dias);
                $('#st-pendientes').text(d.total_pendientes);
                $('#st-pagadas').text(d.total_pagadas);
            },
            error: function (xhr) { console.error('Stats error:', xhr.responseText); }
        });
    }

    // ══════════════════════════════════════════════════
    // LISTAR DEUDAS
    // ══════════════════════════════════════════════════
    function cargarDeudas() {
        $('#listaDeudas').html('<div class="cargando-msg"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Cargando deudas...</p></div>');
        $.ajax({
            method:   'GET',
            url:      API,
            dataType: 'json',
            data:     { accion: 'LISTAR', filtro: filtroActual },
            success: function (r) {
                if (!r.ok) {
                    $('#listaDeudas').html('<div class="sin-deudas"><i class="fas fa-exclamation-circle fa-2x mb-2"></i><p>Error: ' + r.msg + '</p></div>');
                    return;
                }
                todasDeudas = r.data;
                renderLista(todasDeudas);
            },
            error: function (xhr) {
                console.error('LISTAR error:', xhr.responseText);
                $('#listaDeudas').html('<div class="sin-deudas"><i class="fas fa-wifi fa-2x mb-2"></i><p>Error de conexión. Revisa la consola.</p></div>');
            }
        });
    }

    // ══════════════════════════════════════════════════
    // RENDERIZAR
    // ══════════════════════════════════════════════════
    function renderLista(deudas) {
        if (!deudas.length) {
            $('#listaDeudas').html('<div class="sin-deudas"><i class="fas fa-check-circle fa-2x mb-2"></i><p>No hay deudas en esta categoría.</p></div>');
            return;
        }

        var html = '';
        $.each(deudas, function (i, d) {
            var sem   = d.semaforo;
            var fecha = new Date(d.fecha_limite + 'T00:00:00').toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
            var dias  = parseInt(d.dias_restantes);
            var label = d.estado === 'pagada'  ? 'Pagada'
                      : dias < 0               ? 'Vencida hace ' + Math.abs(dias) + ' día' + (Math.abs(dias) === 1 ? '' : 's')
                      : dias === 0             ? 'Vence hoy'
                      :                          'Vence en ' + dias + ' día' + (dias === 1 ? '' : 's');

            var btnEstado = d.estado !== 'pagada'
                ? '<button class="btn btn-success btn-round btn-sm" onclick="cambiarEstado(' + d.id + ',\'pagada\')"><i class="fas fa-check"></i> Marcar pagada</button>'
                : '<button class="btn btn-warning btn-round btn-sm"  onclick="cambiarEstado(' + d.id + ',\'pendiente\')"><i class="fas fa-undo"></i> Reabrir</button>';

            html += '<div class="deuda-card ' + sem + '" id="card-' + d.id + '">';
            html += '  <div class="d-flex justify-content-between align-items-start gap-2">';
            html += '    <div>';
            html += '      <div class="deuda-proveedor">' + escHtml(d.proveedor) + '</div>';
            html += '      <div class="d-flex align-items-center gap-2 mt-1">';
            html += '        <span class="deuda-badge ' + sem + '">' + label + '</span>';
            html += '        <span class="deuda-fecha"><i class="fas fa-calendar-alt"></i> Fecha límite: ' + fecha + '</span>';
            html += '      </div>';
            html += '    </div>';
            html += '    <div class="deuda-monto ' + sem + '">S/ ' + parseFloat(d.monto).toLocaleString('es-PE', { minimumFractionDigits: 2 }) + '</div>';
            html += '  </div>';
            if (d.descripcion) html += '  <div class="deuda-desc"><i class="fas fa-info-circle text-muted"></i> ' + escHtml(d.descripcion) + '</div>';
            if (d.cuotas)      html += '  <div class="deuda-cuotas"><strong><i class="fas fa-layer-group"></i> Cuotas:</strong> ' + escHtml(d.cuotas) + '</div>';
            html += '  <div class="deuda-actions">';
            html += '    ' + btnEstado;
            html += '    <button class="btn btn-warning btn-round btn-sm" onclick="abrirModalEditarDeuda(' + d.id + ')"><i class="fa fa-edit"></i> Editar</button>';
            html += '    <button class="btn btn-danger  btn-round btn-sm" onclick="fnEliminarDeuda(' + d.id + ',\'' + escHtml(d.proveedor) + '\')"><i class="fas fa-times-circle"></i> Eliminar</button>';
            html += '  </div>';
            html += '</div>';
        });

        $('#listaDeudas').html(html);
    }

    // ══════════════════════════════════════════════════
    // FILTROS + BÚSQUEDA LOCAL
    // ══════════════════════════════════════════════════
    function cambiarFiltro(btn) {
        $('.deuda-tab').removeClass('activo');
        $(btn).addClass('activo');
        filtroActual = $(btn).data('filtro');
        $('#buscadorDeuda').val('');
        cargarDeudas();
    }

    function buscarLocal() {
        var q = $('#buscadorDeuda').val().toLowerCase().trim();
        renderLista(q ? todasDeudas.filter(function (d) {
            return d.proveedor.toLowerCase().includes(q) || (d.descripcion || '').toLowerCase().includes(q);
        }) : todasDeudas);
    }

    // ══════════════════════════════════════════════════
    // MODAL ABRIR
    // ══════════════════════════════════════════════════
    function abrirModalNuevaDeuda() {
        $('#modalDeudaLabel').html('<i class="fas fa-plus-circle"></i> Nueva Deuda a Proveedor');
        $('#f-id').val('');
        $('#f-proveedor').val('');
        $('#f-monto').val('');
        $('#f-fecha').val('');
        $('#f-desc').val('');
        $('#f-cuotas').val('');
        new bootstrap.Modal(document.getElementById('modalDeuda')).show();
    }

    function abrirModalEditarDeuda(id) {
        var d = todasDeudas.find(function (x) { return x.id == id; });
        if (!d) return;
        $('#modalDeudaLabel').html('<i class="fas fa-edit"></i> Editar Deuda — ' + escHtml(d.proveedor));
        $('#f-id').val(d.id);
        $('#f-proveedor').val(d.proveedor);
        $('#f-monto').val(d.monto);
        $('#f-fecha').val(d.fecha_limite);
        $('#f-desc').val(d.descripcion || '');
        $('#f-cuotas').val(d.cuotas || '');
        new bootstrap.Modal(document.getElementById('modalDeuda')).show();
    }

    // ══════════════════════════════════════════════════
    // GUARDAR
    // ══════════════════════════════════════════════════
    function guardarDeuda() {
        var id        = $('#f-id').val();
        var proveedor = $('#f-proveedor').val().trim();
        var monto     = $('#f-monto').val();
        var fecha     = $('#f-fecha').val();

        if (!proveedor) { Swal.fire('Ups!', 'Debes ingresar el nombre del proveedor.', 'error'); return; }
        if (!monto || parseFloat(monto) <= 0) { Swal.fire('Ups!', 'El monto debe ser mayor a cero.', 'error'); return; }
        if (!fecha) { Swal.fire('Ups!', 'Debes seleccionar la fecha límite de pago.', 'error'); return; }

        $('#btnGuardarDeuda').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        $.ajax({
            method:   'POST',
            url:      API,
            dataType: 'json',
            data: {
                accion:       'GUARDAR',
                id:           id ? parseInt(id) : 0,
                proveedor:    proveedor,
                monto:        parseFloat(monto),
                fecha_limite: fecha,
                descripcion:  $('#f-desc').val().trim(),
                cuotas:       $('#f-cuotas').val().trim()
            },
            success: function (r) {
                if (r.ok) {
                    bootstrap.Modal.getInstance(document.getElementById('modalDeuda')).hide();
                    Swal.fire({ title: id ? '¡Actualizado!' : '¡Registrado!', text: r.msg, icon: 'success', timer: 1500, showConfirmButton: false });
                    cargarEstadisticas();
                    cargarDeudas();
                } else {
                    Swal.fire('Error', r.msg, 'error');
                }
                $('#btnGuardarDeuda').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Deuda');
            },
            error: function (xhr) {
                console.error('GUARDAR error:', xhr.responseText);
                Swal.fire('Error', 'Hubo un problema con la solicitud.', 'error');
                $('#btnGuardarDeuda').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Deuda');
            }
        });
    }

    // ══════════════════════════════════════════════════
    // CAMBIAR ESTADO
    // ══════════════════════════════════════════════════
    function cambiarEstado(id, estado) {
        $.ajax({
            method:   'POST',
            url:      API,
            dataType: 'json',
            data:     { accion: 'CAMBIAR_ESTADO', id: id, estado: estado },
            success: function (r) {
                if (r.ok) {
                    Swal.fire({ icon: 'success', title: r.msg, timer: 1200, showConfirmButton: false });
                    cargarEstadisticas();
                    cargarDeudas();
                } else {
                    Swal.fire('Error', r.msg, 'error');
                }
            },
            error: function (xhr) { console.error('CAMBIAR_ESTADO error:', xhr.responseText); }
        });
    }

    // ══════════════════════════════════════════════════
    // ELIMINAR
    // ══════════════════════════════════════════════════
    function fnEliminarDeuda(id, nombre) {
        Swal.fire({
            title: '¿Eliminar deuda?',
            html:  'Se eliminará la deuda con <strong>' + nombre + '</strong>.<br>Esta acción no se puede deshacer.',
            icon:  'warning',
            showCancelButton:   true,
            confirmButtonColor: '#d33',
            cancelButtonColor:  '#3085d6',
            confirmButtonText:  'Sí, eliminar',
            cancelButtonText:   'Cancelar'
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    method:   'POST',
                    url:      API,
                    dataType: 'json',
                    data:     { accion: 'ELIMINAR', id: id },
                    success: function (r) {
                        if (r.ok) {
                            Swal.fire({ icon: 'success', title: r.msg, timer: 1200, showConfirmButton: false });
                            cargarEstadisticas();
                            cargarDeudas();
                        } else {
                            Swal.fire('Error', r.msg, 'error');
                        }
                    },
                    error: function (xhr) { console.error('ELIMINAR error:', xhr.responseText); }
                });
            }
        });
    }

    // ══════════════════════════════════════════════════
    // HELPER
    // ══════════════════════════════════════════════════
    function escHtml(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(String(str || '')));
        return d.innerHTML;
    }
</script>

<?php
include("pie.php");
?>