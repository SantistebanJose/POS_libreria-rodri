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

.status-step.active .status-label {
    color: #1572e8;
}

.status-step.completed .status-label {
    color: #28a745;
}

.status-step.cancelled .status-label {
    color: #dc3545;
}

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

<div class="modal fade" id="modalDetalleReservaWeb" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card border-primary">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="card-body">
                        <div class="card-body text-center">
                            <h4><i class="fas fa-cloud"></i> Detalle de Reserva Web #<span id="modalReservaId"></span></h4>
                            <div class="card-sub text-center">
                                Gestiona el estado de la reserva <strong>WEB.</strong>
                            </div>
                        </div>

                        <!-- Timeline de Estados -->
                        <div class="status-timeline" id="statusTimeline">
                            <div class="status-step" data-status="pendiente" onclick="cambiarEstado('pendiente')">
                                <div class="status-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="status-label">Pendiente</div>
                            </div>
                            <div class="status-step" data-status="confirmado" onclick="cambiarEstado('confirmado')">
                                <div class="status-icon">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <div class="status-label">Confirmado</div>
                            </div>
                            <div class="status-step" data-status="preparando" onclick="cambiarEstado('preparando')">
                                <div class="status-icon">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <div class="status-label">Preparando</div>
                            </div>
                            <div class="status-step" data-status="listo" onclick="cambiarEstado('listo')">
                                <div class="status-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="status-label">Listo</div>
                            </div>
                            <div class="status-step" data-status="entregado" onclick="cambiarEstado('entregado')">
                                <div class="status-icon">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <div class="status-label">Entregado</div>
                            </div>
                        </div>

                        <div class="text-center mb-3">
                            <button class="btn btn-danger btn-sm" onclick="cambiarEstado('cancelado')">
                                <i class="fas fa-times-circle"></i> Cancelar Reserva
                            </button>
                        </div>

                        <hr>

                        <!-- Información del Cliente -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Cliente:</strong> <span id="modalNombreCliente"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Documento:</strong> <span id="modalDocumento"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Teléfono:</strong> <span id="modalTelefono"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Total:</strong> S/ <span id="modalTotal"></span>
                            </div>
                        </div>

                        <hr>

                        <!-- Detalle de Artículos -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Artículos de la Reserva</h5>
                            <button class="btn btn-success btn-sm" onclick="agregarArticulo()">
                                <i class="fas fa-plus"></i> Agregar Artículo
                            </button>
                        </div>
                        
                        <div id="detalleReservaList" class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Artículo ID</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="detalleArticulosBody">
                                </tbody>
                                <tfoot>
                                    <tr class="table-secondary">
                                        <td colspan="4" class="text-end"><strong>TOTAL:</strong></td>
                                        <td colspan="2"><strong>S/ <span id="totalReserva">0.00</span></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div id="notasContainer" class="mt-3">
                            <strong>Notas:</strong>
                            <textarea id="modalNotas" class="form-control" rows="3" placeholder="Agregar notas sobre la reserva..."></textarea>
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
                            case "pendiente":
                                $color = "warning";
                                break;
                            case "confirmado":
                                $color = "info";
                                break;
                            case "preparando":
                                $color = "primary";
                                break;
                            case "listo":
                                $color = "success";
                                break;
                            case "entregado":
                                $color = "success";
                                break;
                            case "cancelado":
                                $color = "danger";
                                break;
                        }
                        
                        $iniciales = strtoupper(substr($datosReserva["nombres_cliente"], 0, 1));
                ?>
                    <div class="d-flex cursor-pointer reserva-item" style="cursor: pointer;" 
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
            let estadoActual = null;
            let detallesReserva = [];

            const estadosPasos = {
                'pendiente': 1,
                'confirmado': 2,
                'preparando': 3,
                'listo': 4,
                'entregado': 5,
                'cancelado': 0
            };

            function fn_abrir_modal_detalle(jsdatos) {
                $('#modalDetalleReservaWeb').modal('show');
                
                console.log('Datos de reserva:', jsdatos);
                
                reservaActualId = jsdatos.id;
                estadoActual = jsdatos.estado.toLowerCase();
                
                document.getElementById('modalReservaId').textContent = jsdatos.id;
                document.getElementById('modalNombreCliente').textContent = 
                    jsdatos.nombres_cliente + ' ' + jsdatos.apelldios_cliente;
                document.getElementById('modalDocumento').textContent = jsdatos.numero_documento;
                document.getElementById('modalTelefono').textContent = jsdatos.telefonomovil_cliente;
                document.getElementById('modalTotal').textContent = parseFloat(jsdatos.total).toFixed(2);
                
                actualizarTimeline(estadoActual);
                
                // Cargar notas
                document.getElementById('modalNotas').value = jsdatos.notas || '';
                
                // Procesar detalles
                if (typeof jsdatos.json_detalles === 'string') {
                    try {
                        detallesReserva = JSON.parse(jsdatos.json_detalles);
                    } catch (e) {
                        console.error('Error al parsear json_detalles:', e);
                        detallesReserva = [];
                    }
                } else {
                    detallesReserva = jsdatos.json_detalles || [];
                }
                
                renderizarTablaArticulos();
            }

            function renderizarTablaArticulos() {
                const tbody = document.getElementById('detalleArticulosBody');
                tbody.innerHTML = '';
                let total = 0;
                
                if (Array.isArray(detallesReserva) && detallesReserva.length > 0) {
                    detallesReserva.forEach(function(detalle, index) {
                        total += parseFloat(detalle.subtotal || 0);
                        
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${detalle.articulo_id || 'N/A'}</td>
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
                document.getElementById('modalTotal').textContent = total.toFixed(2);
            }

            function editarCantidad(index, detalleId) {
                const detalle = detallesReserva[index];
                
                Swal.fire({
                    title: 'Editar Cantidad',
                    input: 'number',
                    inputValue: detalle.cantidad,
                    inputAttributes: {
                        min: 1,
                        step: 1
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        const nuevaCantidad = parseInt(result.value);
                        const nuevoSubtotal = nuevaCantidad * parseFloat(detalle.precio_unitario);
                        
                        actualizarDetalleReserva(detalleId, {
                            cantidad: nuevaCantidad,
                            subtotal: nuevoSubtotal.toFixed(2)
                        }, index);
                    }
                });
            }

            function editarPrecio(index, detalleId) {
                const detalle = detallesReserva[index];
                
                Swal.fire({
                    title: 'Editar Precio Unitario',
                    input: 'number',
                    inputValue: detalle.precio_unitario,
                    inputAttributes: {
                        min: 0,
                        step: 0.01
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        const nuevoPrecio = parseFloat(result.value);
                        const nuevoSubtotal = detalle.cantidad * nuevoPrecio;
                        
                        actualizarDetalleReserva(detalleId, {
                            precio_unitario: nuevoPrecio.toFixed(2),
                            subtotal: nuevoSubtotal.toFixed(2)
                        }, index);
                    }
                });
            }

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
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Actualizando...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        if (response.success) {
                            // Actualizar local
                            Object.assign(detallesReserva[index], cambios);
                            renderizarTablaArticulos();
                            
                            Swal.fire({
                                icon: 'success',
                                title: '¡Actualizado!',
                                timer: 1000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo actualizar', 'error');
                    }
                });
            }

            function eliminarArticulo(detalleId, index) {
                Swal.fire({
                    title: '¿Eliminar artículo?',
                    text: "Esta acción no se puede deshacer",
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
                            data: {
                                accion: 'ELIMINARDETALLE',
                                detalle_id: detalleId,
                                reserva_id: reservaActualId
                            },
                            success: function(response) {
                                if (response.success) {
                                    detallesReserva.splice(index, 1);
                                    renderizarTablaArticulos();
                                    
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Eliminado',
                                        timer: 1000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            }
                        });
                    }
                });
            }

            function agregarArticulo() {
                Swal.fire({
                    title: 'Agregar Artículo',
                    html: `
                        <input id="articuloId" class="swal2-input" placeholder="ID Artículo" type="number">
                        <input id="cantidad" class="swal2-input" placeholder="Cantidad" type="number" value="1">
                        <input id="precioUnitario" class="swal2-input" placeholder="Precio Unitario" type="number" step="0.01">
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Agregar',
                    cancelButtonText: 'Cancelar',
                    preConfirm: () => {
                        return {
                            articulo_id: document.getElementById('articuloId').value,
                            cantidad: document.getElementById('cantidad').value,
                            precio_unitario: document.getElementById('precioUnitario').value
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const datos = result.value;
                        const subtotal = parseFloat(datos.cantidad) * parseFloat(datos.precio_unitario);
                        
                        $.ajax({
                            url: 'logica/clssReserva.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                accion: 'AGREGARDETALLE',
                                reserva_id: reservaActualId,
                                articulo_id: datos.articulo_id,
                                cantidad: datos.cantidad,
                                precio_unitario: datos.precio_unitario,
                                subtotal: subtotal.toFixed(2)
                            },
                            success: function(response) {
                                if (response.success) {
                                    detallesReserva.push({
                                        id: response.detalle_id,
                                        articulo_id: datos.articulo_id,
                                        cantidad: datos.cantidad,
                                        precio_unitario: datos.precio_unitario,
                                        subtotal: subtotal.toFixed(2)
                                    });
                                    renderizarTablaArticulos();
                                    Swal.fire('¡Agregado!', '', 'success');
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            }
                        });
                    }
                });
            }

            function actualizarNotas() {
                const notas = document.getElementById('modalNotas').value;
                
                $.ajax({
                    url: 'logica/clssReserva.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        accion: 'ACTUALIZARNOTAS',
                        reserva_id: reservaActualId,
                        notas: notas
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Notas guardadas',
                                timer: 1000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }
                });
            }

            function actualizarTimeline(estado) {
                const pasoActual = estadosPasos[estado] || 1;
                
                document.querySelectorAll('.status-step').forEach(step => {
                    step.classList.remove('active', 'completed', 'cancelled');
                });
                
                if (estado === 'cancelado') {
                    document.querySelectorAll('.status-step').forEach(step => {
                        step.classList.add('cancelled');
                    });
                } else {
                    document.querySelectorAll('.status-step').forEach(step => {
                        const stepStatus = step.getAttribute('data-status');
                        const stepPaso = estadosPasos[stepStatus];
                        
                        if (stepPaso < pasoActual) {
                            step.classList.add('completed');
                        } else if (stepPaso === pasoActual) {
                            step.classList.add('active');
                        }
                    });
                }
            }

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
                                accion: 'ACTUALIZARESTADO',
                                reserva_id: reservaActualId,
                                nuevo_estado: nuevoEstado
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Actualizando...',
                                    text: 'Por favor espera',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Éxito!',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    
                                    estadoActual = nuevoEstado;
                                    actualizarTimeline(nuevoEstado);
                                    actualizarBadgeEnLista(reservaActualId, nuevoEstado);
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error AJAX:', error);
                                Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
                            }
                        });
                    }
                });
            }

            function actualizarBadgeEnLista(reservaId, nuevoEstado) {
                const colores = {
                    'pendiente': 'warning',
                    'confirmado': 'info',
                    'preparando': 'primary',
                    'listo': 'success',
                    'entregado': 'success',
                    'cancelado': 'danger'
                };
                
                const reservaItem = document.querySelector(`.reserva-item[data-reserva-id="${reservaId}"]`);
                if (reservaItem) {
                    const badge = reservaItem.querySelector('.badge');
                    if (badge) {
                        badge.className = `badge bg-${colores[nuevoEstado]} ms-2`;
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