<?php
include("cabecera.php");
?>

<style>
.status-timeline {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 20px 0;
}

.status-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    flex: 1;
}

.status-step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 25px;
    left: 60%;
    width: 80%;
    height: 3px;
    background: #e0e0e0;
    z-index: 0;
}

.status-step.active:not(:last-child)::after {
    background: #1572e8;
}

.status-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    background: #e0e0e0;
    color: #666;
    z-index: 1;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.status-icon:hover {
    transform: scale(1.1);
}

.status-step.active .status-icon {
    background: #1572e8;
    color: white;
    box-shadow: 0 4px 12px rgba(21, 114, 232, 0.4);
}

.status-step.completed .status-icon {
    background: #28a745;
    color: white;
}

.status-step.cancelled .status-icon {
    background: #dc3545;
    color: white;
}

.status-label {
    margin-top: 10px;
    font-size: 12px;
    font-weight: 600;
    text-align: center;
    color: #666;
}

.status-step.active .status-label  { color: #1572e8; }
.status-step.completed .status-label { color: #28a745; }
.status-step.cancelled .status-label { color: #dc3545; }

.editable-cell {
    cursor: pointer;
    transition: background-color 0.2s;
}
.editable-cell:hover {
    background-color: #f0f8ff;
}
.btn-action {
    padding: 2px 8px;
    font-size: 12px;
}
</style>

<!-- ===== MODAL DETALLE RESERVA ===== -->
<div class="modal fade" id="modalDetalleReservaWeb" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card border-primary">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="card-body">
                        <div class="text-center mb-2">
                            <h4><i class="fas fa-cloud"></i> Detalle de Reserva Web #<span id="modalReservaId"></span></h4>
                            <div class="card-sub">Gestiona el estado de la reserva <strong>WEB.</strong></div>
                        </div>

                        <!-- Timeline -->
                        <div class="status-timeline" id="statusTimeline">
                            <div class="status-step" data-status="pendiente" onclick="cambiarEstado('pendiente')">
                                <div class="status-icon"><i class="fas fa-clock"></i></div>
                                <div class="status-label">Pendiente</div>
                            </div>
                            <div class="status-step" data-status="confirmado" onclick="cambiarEstado('confirmado')">
                                <div class="status-icon"><i class="fas fa-clipboard-check"></i></div>
                                <div class="status-label">Confirmado</div>
                            </div>
                            <div class="status-step" data-status="preparando" onclick="cambiarEstado('preparando')">
                                <div class="status-icon"><i class="fas fa-box-open"></i></div>
                                <div class="status-label">Preparando</div>
                            </div>
                            <div class="status-step" data-status="listo" onclick="cambiarEstado('listo')">
                                <div class="status-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="status-label">Listo</div>
                            </div>
                            <div class="status-step" data-status="entregado" onclick="cambiarEstado('entregado')">
                                <div class="status-icon"><i class="fas fa-handshake"></i></div>
                                <div class="status-label">Entregado</div>
                            </div>
                        </div>

                        <div class="text-center mb-3">
                            <button class="btn btn-danger btn-sm" onclick="cambiarEstado('cancelado')">
                                <i class="fas fa-times-circle"></i> Cancelar Reserva
                            </button>
                        </div>

                        <hr>

                        <!-- Info Cliente -->
                        <div class="row mb-3">
                            <div class="col-md-6"><strong>Cliente:</strong> <span id="modalNombreCliente"></span></div>
                            <div class="col-md-6"><strong>Documento:</strong> <span id="modalDocumento"></span></div>
                            <div class="col-md-6"><strong>Teléfono:</strong> <span id="modalTelefono"></span></div>
                            <div class="col-md-6"><strong>Total:</strong> S/ <span id="modalTotal"></span></div>
                        </div>

                        <hr>

                        <!-- Artículos -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Artículos de la Reserva</h5>
                            <button class="btn btn-success btn-sm" onclick="agregarArticulo()">
                                <i class="fas fa-plus"></i> Agregar Artículo
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Artículo</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="detalleArticulosBody"></tbody>
                                <tfoot>
                                    <tr class="table-secondary">
                                        <td colspan="4" class="text-end"><strong>TOTAL:</strong></td>
                                        <td colspan="2"><strong>S/ <span id="totalReserva">0.00</span></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Notas -->
                        <div class="mt-3">
                            <strong>Notas:</strong>
                            <textarea id="modalNotas" class="form-control mt-1" rows="3" placeholder="Agregar notas sobre la reserva..."></textarea>
                            <button class="btn btn-sm btn-primary mt-2" onclick="actualizarNotas()">
                                <i class="fas fa-save"></i> Guardar Notas
                            </button>
                        </div>

                        <hr>
                        <div class="text-center">
                            <button class="btn btn-secondary btn-round" data-bs-dismiss="modal">
                                <i class="far fa-times-circle"></i> Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL AGREGAR ARTÍCULO ===== -->
<div class="modal fade" id="modalAgregarArticulo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle text-success"></i> Agregar Artículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Buscador -->
                <div class="mb-3" style="position: relative;">
                    <label class="form-label fw-bold">Buscar artículo:</label>
                    <input type="text" id="buscarArticulo" class="form-control"
                           placeholder="Escribe el nombre..." autocomplete="off">
                    <div id="resultadosBusqueda" style="
                        display: none;
                        position: absolute;
                        z-index: 9999;
                        width: 100%;
                        max-height: 220px;
                        overflow-y: auto;
                        border: 1px solid #ddd;
                        border-radius: 0 0 6px 6px;
                        background: white;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    "></div>
                </div>

                <!-- Artículo seleccionado -->
                <div id="articuloSeleccionado" class="alert alert-success py-2" style="display:none;">
                    <i class="fas fa-check-circle"></i>
                    <strong id="nombreSeleccionado"></strong><br>
                    <small>Stock disponible: <span id="stockSeleccionado"></span> | Precio sugerido: S/ <span id="precioSeleccionado"></span></small>
                    <input type="hidden" id="articuloIdSeleccionado">
                </div>

                <!-- Cantidad -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Cantidad:</label>
                    <input type="number" id="cantidadAgregar" class="form-control" value="1" min="1">
                </div>

                <!-- Precio -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Precio Unitario (S/):</label>
                    <input type="number" id="precioAgregar" class="form-control" step="0.01" min="0" placeholder="0.00">
                </div>

                <!-- Error -->
                <div id="errorAgregar" class="alert alert-danger" style="display:none;"></div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="confirmarAgregarArticulo()">
                    <i class="fas fa-plus"></i> Agregar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===== LISTADO ===== -->
<div class="container">
    <div class="page-inner">
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <h4 class="card-title"><i class="fas fa-align-left"></i> Listado de Reservas Web</h4>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" onclick="location.reload()">
                            <i class="fas fa-sync"></i> Actualizar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body" id="listaReservas">
                <?php 
                $reservas = fnListadoDeReservasWeb();
                
                if (empty($reservas)) {
                    echo '<div class="alert alert-info text-center">No hay reservas disponibles</div>';
                } else {
                    foreach ($reservas as $datosReserva) {
                        $datosReservaJSON = htmlspecialchars(json_encode($datosReserva), ENT_QUOTES, 'UTF-8');
                        $color = "secondary";
                        $estadoTexto = ucfirst($datosReserva["estado"]);
                        switch(strtolower($datosReserva["estado"])) {
                            case "pendiente":  $color = "warning"; break;
                            case "confirmado": $color = "info";    break;
                            case "preparando": $color = "primary"; break;
                            case "listo":      $color = "success"; break;
                            case "entregado":  $color = "success"; break;
                            case "cancelado":  $color = "danger";  break;
                        }
                        $iniciales = strtoupper(substr($datosReserva["nombres_cliente"], 0, 1));
                ?>
                    <div class="d-flex reserva-item" style="cursor: pointer;"
                         data-reserva-id="<?php echo $datosReserva['id']; ?>"
                         onclick='fn_abrir_modal_detalle(<?php echo $datosReservaJSON ?>)'>
                        <div class="avatar avatar-online">
                            <span class="avatar-title rounded-circle border border-white bg-info">
                                <?php echo $iniciales ?>
                            </span>
                        </div>
                        <div class="flex-1 ms-3 pt-1">
                            <h6 class="text-uppercase fw-bold mb-1">
                                <?php echo htmlspecialchars($datosReserva["nombres_cliente"] . " " . $datosReserva["apelldios_cliente"]) ?>
                                <span class="badge bg-<?php echo $color ?> ms-2"><?php echo $estadoTexto ?></span>
                            </h6>
                            <small class="text-muted">
                                Doc: <?php echo htmlspecialchars($datosReserva["numero_documento"]) ?> |
                                Tel: <?php echo htmlspecialchars($datosReserva["telefonomovil_cliente"]) ?> |
                                Total: S/ <?php echo number_format($datosReserva["total"], 2) ?>
                            </small>
                        </div>
                        <div class="float-end pt-1">
                            <small class="text-muted">
                                <?php echo date("d/m/Y H:i", strtotime($datosReserva["fechareserva"])); ?>
                            </small>
                        </div>
                    </div>
                    <div class="separator-dashed"></div>
                <?php
                    }
                }
                ?>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let reservaActualId = null;
            let estadoActual    = null;
            let detallesReserva = [];
            let busquedaTimeout = null;

            const estadosPasos = {
                'pendiente': 1, 'confirmado': 2, 'preparando': 3,
                'listo': 4,     'entregado': 5,  'cancelado': 0
            };

            // ── Inicializar buscador de artículos ──────────────────────────
            document.addEventListener('DOMContentLoaded', function () {

                document.getElementById('buscarArticulo').addEventListener('keyup', function () {
                    clearTimeout(busquedaTimeout);
                    const termino   = this.value.trim();
                    const contenedor = document.getElementById('resultadosBusqueda');

                    if (termino.length < 2) {
                        contenedor.style.display = 'none';
                        return;
                    }

                    busquedaTimeout = setTimeout(() => {
                        $.ajax({
                            url: 'logica/clssReserva.php',
                            type: 'POST',
                            dataType: 'json',
                            data: { accion: 'BUSCAR_ARTICULO', termino: termino },
                            success: function (response) {
                                if (!response.success || response.data.length === 0) {
                                    contenedor.innerHTML = '<div style="padding:10px;color:#999;text-align:center">No se encontraron artículos</div>';
                                    contenedor.style.display = 'block';
                                    return;
                                }
                                contenedor.innerHTML = response.data.map(a => `
                                    <div onclick="seleccionarArticulo(${a.id}, '${a.nombre.replace(/'/g, "\\'")}', ${a.precio_venta}, ${a.stock})"
                                         style="padding:8px 12px;cursor:pointer;border-bottom:1px solid #eee;"
                                         onmouseover="this.style.background='#f0f8ff'"
                                         onmouseout="this.style.background='white'">
                                        <strong>${a.nombre}</strong>
                                        <span style="float:right;color:#1572e8">S/ ${parseFloat(a.precio_venta).toFixed(2)}</span>
                                        <br><small style="color:#999">Stock: ${a.stock}</small>
                                    </div>
                                `).join('');
                                contenedor.style.display = 'block';
                            }
                        });
                    }, 350);
                });

                // Cerrar resultados al hacer clic fuera
                document.addEventListener('click', function (e) {
                    if (!document.getElementById('buscarArticulo').contains(e.target)) {
                        document.getElementById('resultadosBusqueda').style.display = 'none';
                    }
                });
            });

            // ── Abrir modal detalle ────────────────────────────────────────
            function fn_abrir_modal_detalle(jsdatos) {
                $('#modalDetalleReservaWeb').modal('show');

                reservaActualId = jsdatos.id;
                estadoActual    = jsdatos.estado.toLowerCase();

                document.getElementById('modalReservaId').textContent     = jsdatos.id;
                document.getElementById('modalNombreCliente').textContent  = jsdatos.nombres_cliente + ' ' + jsdatos.apelldios_cliente;
                document.getElementById('modalDocumento').textContent      = jsdatos.numero_documento;
                document.getElementById('modalTelefono').textContent       = jsdatos.telefonomovil_cliente;
                document.getElementById('modalTotal').textContent          = parseFloat(jsdatos.total).toFixed(2);
                document.getElementById('modalNotas').value                = jsdatos.notas || '';

                actualizarTimeline(estadoActual);

                if (typeof jsdatos.json_detalles === 'string') {
                    try { detallesReserva = JSON.parse(jsdatos.json_detalles); }
                    catch (e) { detallesReserva = []; }
                } else {
                    detallesReserva = jsdatos.json_detalles || [];
                }

                renderizarTablaArticulos();
            }

            // ── Tabla artículos ───────────────────────────────────────────
            function renderizarTablaArticulos() {
                const tbody = document.getElementById('detalleArticulosBody');
                tbody.innerHTML = '';
                let total = 0;

                if (Array.isArray(detallesReserva) && detallesReserva.length > 0) {
                    detallesReserva.forEach(function (detalle, index) {
                        total += parseFloat(detalle.subtotal || 0);
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${detalle.nombre || 'ID: ' + detalle.articulo_id}</td>
                            <td class="editable-cell" onclick="editarCantidad(${index}, ${detalle.id})">${detalle.cantidad || 0}</td>
                            <td class="editable-cell" onclick="editarPrecio(${index}, ${detalle.id})">S/ ${parseFloat(detalle.precio_unitario || 0).toFixed(2)}</td>
                            <td>S/ ${parseFloat(detalle.subtotal || 0).toFixed(2)}</td>
                            <td>
                                <button class="btn btn-danger btn-action" onclick="eliminarArticulo(${detalle.id}, ${index})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay artículos</td></tr>';
                }

                document.getElementById('totalReserva').textContent = total.toFixed(2);
                document.getElementById('modalTotal').textContent   = total.toFixed(2);
            }

            // ── Editar cantidad ───────────────────────────────────────────
            function editarCantidad(index, detalleId) {
                const detalle = detallesReserva[index];
                Swal.fire({
                    title: 'Editar Cantidad',
                    input: 'number',
                    inputValue: detalle.cantidad,
                    inputAttributes: { min: 1, step: 1 },
                    showCancelButton: true,
                    confirmButtonText: 'Guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        const nuevaCantidad  = parseInt(result.value);
                        const nuevoSubtotal  = nuevaCantidad * parseFloat(detalle.precio_unitario);
                        actualizarDetalleReserva(detalleId, {
                            cantidad: nuevaCantidad,
                            subtotal: nuevoSubtotal.toFixed(2)
                        }, index);
                    }
                });
            }

            // ── Editar precio ─────────────────────────────────────────────
            function editarPrecio(index, detalleId) {
                const detalle = detallesReserva[index];
                Swal.fire({
                    title: 'Editar Precio Unitario',
                    input: 'number',
                    inputValue: detalle.precio_unitario,
                    inputAttributes: { min: 0, step: 0.01 },
                    showCancelButton: true,
                    confirmButtonText: 'Guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        const nuevoPrecio   = parseFloat(result.value);
                        const nuevoSubtotal = detalle.cantidad * nuevoPrecio;
                        actualizarDetalleReserva(detalleId, {
                            precio_unitario: nuevoPrecio.toFixed(2),
                            subtotal: nuevoSubtotal.toFixed(2)
                        }, index);
                    }
                });
            }

            // ── Actualizar detalle (AJAX) ─────────────────────────────────
            function actualizarDetalleReserva(detalleId, cambios, index) {
                $.ajax({
                    url: 'logica/clssReserva.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        accion: 'ACTUALIZARDETALLE',
                        detalle_id: detalleId,
                        reserva_id: reservaActualId,
                        cambios: JSON.stringify(cambios)
                    },
                    beforeSend: function () {
                        Swal.fire({ title: 'Actualizando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    },
                    success: function (response) {
                        if (response.success) {
                            Object.assign(detallesReserva[index], cambios);
                            renderizarTablaArticulos();
                            Swal.fire({ icon: 'success', title: '¡Actualizado!', timer: 1000, showConfirmButton: false });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'No se pudo actualizar', 'error');
                    }
                });
            }

            // ── Eliminar artículo ─────────────────────────────────────────
            function eliminarArticulo(detalleId, index) {
                Swal.fire({
                    title: '¿Eliminar artículo?',
                    text: 'Esta acción no se puede deshacer',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'logica/clssReserva.php',
                            type: 'POST',
                            dataType: 'json',
                            data: { accion: 'ELIMINARDETALLE', detalle_id: detalleId, reserva_id: reservaActualId },
                            success: function (response) {
                                if (response.success) {
                                    detallesReserva.splice(index, 1);
                                    renderizarTablaArticulos();
                                    Swal.fire({ icon: 'success', title: 'Eliminado', timer: 1000, showConfirmButton: false });
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            }
                        });
                    }
                });
            }

            // ── Abrir modal agregar artículo ──────────────────────────────
            function agregarArticulo() {
                document.getElementById('buscarArticulo').value            = '';
                document.getElementById('resultadosBusqueda').style.display = 'none';
                document.getElementById('articuloSeleccionado').style.display = 'none';
                document.getElementById('articuloIdSeleccionado').value    = '';
                document.getElementById('cantidadAgregar').value           = 1;
                document.getElementById('precioAgregar').value             = '';
                document.getElementById('errorAgregar').style.display      = 'none';

                $('#modalAgregarArticulo').modal('show');

                $('#modalAgregarArticulo').one('shown.bs.modal', function () {
                    document.getElementById('buscarArticulo').focus();
                });
            }

            // ── Seleccionar artículo de la lista ─────────────────────────
            function seleccionarArticulo(id, nombre, precio, stock) {
                document.getElementById('articuloIdSeleccionado').value        = id;
                document.getElementById('nombreSeleccionado').textContent      = nombre;
                document.getElementById('stockSeleccionado').textContent       = stock;
                document.getElementById('precioSeleccionado').textContent      = parseFloat(precio).toFixed(2);
                document.getElementById('precioAgregar').value                 = parseFloat(precio).toFixed(2);
                document.getElementById('cantidadAgregar').max                 = stock;
                document.getElementById('articuloSeleccionado').style.display  = 'block';
                document.getElementById('resultadosBusqueda').style.display    = 'none';
                document.getElementById('buscarArticulo').value                = nombre;
            }

            // ── Confirmar agregar artículo ────────────────────────────────
            function confirmarAgregarArticulo() {
                const articuloId = document.getElementById('articuloIdSeleccionado').value;
                const cantidad   = parseInt(document.getElementById('cantidadAgregar').value);
                const precio     = parseFloat(document.getElementById('precioAgregar').value);
                const errorDiv   = document.getElementById('errorAgregar');

                errorDiv.style.display = 'none';

                if (!articuloId) {
                    errorDiv.textContent = 'Debes seleccionar un artículo de la lista.';
                    errorDiv.style.display = 'block';
                    return;
                }
                if (!cantidad || cantidad < 1) {
                    errorDiv.textContent = 'La cantidad debe ser mayor a 0.';
                    errorDiv.style.display = 'block';
                    return;
                }
                if (!precio || precio <= 0) {
                    errorDiv.textContent = 'El precio debe ser mayor a 0.';
                    errorDiv.style.display = 'block';
                    return;
                }

                const nombre   = document.getElementById('nombreSeleccionado').textContent;
                const subtotal = cantidad * precio;

                $.ajax({
                    url: 'logica/clssReserva.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        accion:          'AGREGARDETALLE',
                        reserva_id:      reservaActualId,
                        articulo_id:     articuloId,
                        cantidad:        cantidad,
                        precio_unitario: precio,
                        subtotal:        subtotal.toFixed(2)
                    },
                    success: function (response) {
                        if (response.success) {
                            detallesReserva.push({
                                id:              response.detalle_id,
                                articulo_id:     articuloId,
                                nombre:          nombre,
                                cantidad:        cantidad,
                                precio_unitario: precio,
                                subtotal:        subtotal.toFixed(2)
                            });
                            renderizarTablaArticulos();
                            $('#modalAgregarArticulo').modal('hide');
                            Swal.fire({ icon: 'success', title: '¡Artículo agregado!', timer: 1200, showConfirmButton: false });
                        } else {
                            errorDiv.textContent   = response.message;
                            errorDiv.style.display = 'block';
                        }
                    },
                    error: function () {
                        errorDiv.textContent   = 'Error de conexión. Intenta nuevamente.';
                        errorDiv.style.display = 'block';
                    }
                });
            }

            // ── Actualizar notas ──────────────────────────────────────────
            function actualizarNotas() {
                $.ajax({
                    url: 'logica/clssReserva.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        accion:     'ACTUALIZARNOTAS',
                        reserva_id: reservaActualId,
                        notas:      document.getElementById('modalNotas').value
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Notas guardadas', timer: 1000, showConfirmButton: false });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }
                });
            }

            // ── Timeline ──────────────────────────────────────────────────
            function actualizarTimeline(estado) {
                const pasoActual = estadosPasos[estado] || 1;
                document.querySelectorAll('.status-step').forEach(step => {
                    step.classList.remove('active', 'completed', 'cancelled');
                });

                if (estado === 'cancelado') {
                    document.querySelectorAll('.status-step').forEach(step => step.classList.add('cancelled'));
                } else {
                    document.querySelectorAll('.status-step').forEach(step => {
                        const stepPaso = estadosPasos[step.getAttribute('data-status')];
                        if (stepPaso < pasoActual)       step.classList.add('completed');
                        else if (stepPaso === pasoActual) step.classList.add('active');
                    });
                }
            }

            // ── Cambiar estado ────────────────────────────────────────────
            function cambiarEstado(nuevoEstado) {
                if (!reservaActualId) {
                    Swal.fire('Error', 'No hay reserva seleccionada', 'error');
                    return;
                }
                Swal.fire({
                    title: '¿Cambiar estado?',
                    text: `¿Deseas cambiar el estado a "${nuevoEstado.toUpperCase()}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'logica/clssReserva.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                accion:       'ACTUALIZARESTADO',
                                reserva_id:   reservaActualId,
                                nuevo_estado: nuevoEstado
                            },
                            beforeSend: function () {
                                Swal.fire({ title: 'Actualizando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({ icon: 'success', title: '¡Éxito!', text: response.message, timer: 1500, showConfirmButton: false });
                                    estadoActual = nuevoEstado;
                                    actualizarTimeline(nuevoEstado);
                                    actualizarBadgeEnLista(reservaActualId, nuevoEstado);
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
                            }
                        });
                    }
                });
            }

            // ── Badge en lista ────────────────────────────────────────────
            function actualizarBadgeEnLista(reservaId, nuevoEstado) {
                const colores = {
                    'pendiente': 'warning', 'confirmado': 'info',
                    'preparando': 'primary', 'listo': 'success',
                    'entregado': 'success',  'cancelado': 'danger'
                };
                const reservaItem = document.querySelector(`.reserva-item[data-reserva-id="${reservaId}"]`);
                if (reservaItem) {
                    const badge = reservaItem.querySelector('.badge');
                    if (badge) {
                        badge.className   = `badge bg-${colores[nuevoEstado]} ms-2`;
                        badge.textContent = nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1);
                    }
                }
            }
        </script>
    </div>
</div>

<?php
include("pie.php");
?>