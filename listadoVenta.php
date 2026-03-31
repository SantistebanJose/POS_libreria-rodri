<?php
include("cabecera.php");
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}
?>
<div class="container">
    <div class="page-inner">
        <div class="card text-start">
            <div class="card-body">
                <h4 class="card-title"><i class="fas fa-align-left"></i> Listado de Ventas</h4>
                <div class="card-sub">
                    Selecciona de acuerdo a las ventas que necesites :)
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="ventaDiaria" data-bs-toggle="pill" href="#pills-ventaDiaria" role="tab" aria-controls="pills-ventaDiaria" aria-selected="true"><i class="fas fa-clock"></i> Ventas del Día</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ventaSemanal" data-bs-toggle="pill" href="#pills-ventaSemanal" role="tab" aria-controls="pills-ventaSemanal" aria-selected="false"><i class="fas fa-calendar-alt"></i> Ventas de la Semana</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-contact-tab-nobd" data-bs-toggle="pill" href="#pills-contact-nobd" role="tab" aria-controls="pills-contact-nobd" aria-selected="false"><i class="fas fa-chart-bar"></i> Todas las Ventas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ventaRango" data-bs-toggle="pill" href="#pills-ventaRango" role="tab" aria-controls="pills-ventaRango" aria-selected="false"><i class="fas fa-calendar-week"></i> Ventas por Rango</a>
                        </li>
                    </ul>

                    <div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">

                        <!-- ═══════════════════════════════════════════
                             PESTAÑA 1: VENTAS DEL DÍA
                             ═══════════════════════════════════════════ -->
                        <div class="tab-pane fade show active" id="pills-ventaDiaria" role="tabpanel" aria-labelledby="ventaDiaria">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <strong>Total del Día:</strong> S/ <span id="totalDiario">0.00</span>
                                    </div>

                                    <?php
                                    $totalDiario    = 0;
                                    $ventasPorHora  = [];
                                    $estadosPagoDia = [];
                                    $filasDiarias   = [];

                                    foreach (fnListForVentasDiarias() as $datos) {
                                        $datos['accion_ajax'] = 'DETALLEVENTA_VENTA_ID';
                                        $totalDiario += floatval($datos["monto_venta_final"]);

                                        $hora = substr($datos["hora"], 0, 2) . ":00";
                                        $ventasPorHora[$hora] = ($ventasPorHora[$hora] ?? 0) + floatval($datos["monto_venta_final"]);

                                        $estado = $datos["estado_pago"] ?? "SIN ESTADO";
                                        $estadosPagoDia[$estado] = ($estadosPagoDia[$estado] ?? 0) + 1;

                                        $filasDiarias[] = $datos;
                                    }
                                    ksort($ventasPorHora);
                                    ?>

                                    <div class="row mb-4">
                                        <div class="col-md-7 mb-3">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="card-title text-muted mb-3"><i class="fas fa-clock"></i> Ventas por Hora</h6>
                                                    <div style="position:relative;height:200px;">
                                                        <canvas id="chartHoras"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 mb-3">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="card-title text-muted mb-3"><i class="fas fa-credit-card"></i> Estado de Pago</h6>
                                                    <div style="position:relative;height:200px;">
                                                        <canvas id="chartEstadoDia"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="TablaVentaDiaria" class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th><th>N° Ticket</th><th>Cliente</th><th>DÍA</th>
                                                    <th>FECHA</th><th>HORA</th><th>TOTAL(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th><th>pérdida</th><th>ESTADO</th><th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($filasDiarias as $datos):
                                                    $datosJSON = json_encode($datos); ?>
                                                    <tr>
                                                        <td><?php echo $datos["venta_id"] ?></td>
                                                        <td><?php echo $datos["codigo_tiket"] ?></td>
                                                        <td><?php echo $datos["cliente"] ?></td>
                                                        <td><?php echo $datos["dia_nombre"] ?></td>
                                                        <td><?php echo $datos["fecha"] ?></td>
                                                        <td><?php echo $datos["hora"] ?></td>
                                                        <td><?php echo "S/ " . $datos["total"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_final"] ?></td>
                                                        <td><?php echo "S/ " . $datos["perdida_utilidad"] ?></td>
                                                        <td><?php echo $datos["estado_pago"] ?></td>
                                                        <td>
                                                            <div class="mt-2 text-center d-flex justify-content-center">
                                                                <a onclick='abrirModalDetalle(<?php echo $datosJSON ?>)' class="btn btn-success btn-sm btn-round" role="button">DETALLE</a>
                                                                <a href="javascript:void(0);" onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>)' class="btn btn-secondary btn-round btn-sm mx-1" role="button">PDF</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ═══════════════════════════════════════════
                             PESTAÑA 2: VENTAS DE LA SEMANA
                             ═══════════════════════════════════════════ -->
                        <div class="tab-pane fade" id="pills-ventaSemanal" role="tabpanel" aria-labelledby="pills-profile-tab-nobd">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="alert alert-success">
                                        <strong>Total de la Semana:</strong> S/ <span id="totalSemanal">0.00</span>
                                    </div>

                                    <?php
                                    $totalSemanal   = 0;
                                    $ventasPorDia   = [];
                                    $filasSemanales = [];
                                    $diasOrden      = ['Lunes'=>1,'Martes'=>2,'Miércoles'=>3,'Jueves'=>4,'Viernes'=>5,'Sábado'=>6,'Domingo'=>7];

                                    foreach (fnListForVentasSemanales() as $datos) {
                                        $datos['accion_ajax'] = 'DETALLEVENTA_VENTA_ID';
                                        $totalSemanal += floatval($datos["monto_venta_final"]);

                                        $dia = $datos["dia_nombre"];
                                        if (!isset($ventasPorDia[$dia])) {
                                            $ventasPorDia[$dia] = ['total'=>0,'perdida'=>0,'orden'=>$diasOrden[$dia] ?? 8];
                                        }
                                        $ventasPorDia[$dia]['total']   += floatval($datos["monto_venta_final"]);
                                        $ventasPorDia[$dia]['perdida'] += abs(floatval($datos["perdida_utilidad"]));

                                        $filasSemanales[] = $datos;
                                    }
                                    uasort($ventasPorDia, fn($a,$b) => $a['orden'] <=> $b['orden']);
                                    ?>

                                    <div class="row mb-4">
                                        <div class="col-md-7 mb-3">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="card-title text-muted mb-3"><i class="fas fa-chart-line"></i> Tendencia Semanal</h6>
                                                    <div style="position:relative;height:200px;">
                                                        <canvas id="chartSemanaLinea"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 mb-3">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="card-title text-muted mb-3"><i class="fas fa-balance-scale"></i> Ingresos vs Pérdidas</h6>
                                                    <div style="position:relative;height:200px;">
                                                        <canvas id="chartSemanaBarra"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="TablaVentaSemanal" class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th><th>N° Ticket</th><th>Cliente</th><th>DÍA</th>
                                                    <th>FECHA</th><th>HORA</th><th>TOTAL(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th><th>pérdida</th><th>ESTADO</th><th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($filasSemanales as $datos):
                                                    $datosJSON = json_encode($datos); ?>
                                                    <tr>
                                                        <td><?php echo $datos["venta_id"] ?></td>
                                                        <td><?php echo $datos["codigo_tiket"] ?></td>
                                                        <td><?php echo $datos["cliente"] ?></td>
                                                        <td><?php echo $datos["dia_nombre"] ?></td>
                                                        <td><?php echo $datos["fecha"] ?></td>
                                                        <td><?php echo $datos["hora"] ?></td>
                                                        <td><?php echo "S/ " . $datos["total"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_final"] ?></td>
                                                        <td><?php echo "S/ " . $datos["perdida_utilidad"] ?></td>
                                                        <td><?php echo $datos["estado_pago"] ?></td>
                                                        <td>
                                                            <div class="mt-2 text-center d-flex justify-content-center">
                                                                <a onclick='abrirModalDetalle(<?php echo $datosJSON ?>)' class="btn btn-success btn-sm btn-round" role="button">DETALLE</a>
                                                                <a href="javascript:void(0);" onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>)' class="btn btn-secondary btn-round btn-sm mx-1" role="button">PDF</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ═══════════════════════════════════════════
                             PESTAÑA 3: TODAS LAS VENTAS
                             ═══════════════════════════════════════════ -->
                        <div class="tab-pane fade" id="pills-contact-nobd" role="tabpanel" aria-labelledby="pills-contact-tab-nobd">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="alert alert-primary">
                                        <strong>Total General:</strong> S/ <span id="totalGeneral">0.00</span>
                                    </div>

                                    <?php
                                    $totalGeneral    = 0;
                                    $ventasPorMes    = [];
                                    $estadosPagoGen  = [];
                                    $topClientes     = [];
                                    $filasGenerales  = [];

                                    foreach (fnListForVentasTodasLasVentas() as $datos) {
                                        $datos['accion_ajax'] = 'DETALLEVENTA_VENTA_ID';
                                        $totalGeneral += floatval($datos["monto_venta_final"]);

                                        $mes = substr($datos["fecha"], 0, 7);
                                        $ventasPorMes[$mes] = ($ventasPorMes[$mes] ?? 0) + floatval($datos["monto_venta_final"]);

                                        $estado = $datos["estado_pago"] ?? "SIN ESTADO";
                                        $estadosPagoGen[$estado] = ($estadosPagoGen[$estado] ?? 0) + 1;

                                        $cliente = $datos["cliente"] ?? "Sin nombre";
                                        $topClientes[$cliente] = ($topClientes[$cliente] ?? 0) + floatval($datos["monto_venta_final"]);

                                        $filasGenerales[] = $datos;
                                    }
                                    ksort($ventasPorMes);
                                    arsort($topClientes);
                                    $topClientes5 = array_slice($topClientes, 0, 5, true);
                                    ?>

                                    <div class="row mb-4">
                                        <div class="col-md-12 mb-3">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="card-title text-muted mb-3"><i class="fas fa-chart-area"></i> Evolución Mensual de Ventas</h6>
                                                    <div style="position:relative;height:200px;">
                                                        <canvas id="chartMensual"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 mb-3">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="card-title text-muted mb-3"><i class="fas fa-circle"></i> Tipos de Pago</h6>
                                                    <div style="position:relative;height:200px;">
                                                        <canvas id="chartEstadoGen"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7 mb-3">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="card-title text-muted mb-3"><i class="fas fa-trophy"></i> Top 5 Clientes</h6>
                                                    <div style="position:relative;height:200px;">
                                                        <canvas id="chartTopClientes"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="TablaVentaGeneral" class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th><th>N° Ticket</th><th>Cliente</th><th>DÍA</th>
                                                    <th>FECHA</th><th>HORA</th><th>TOTAL(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th><th>pérdida</th><th>ESTADO</th><th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($filasGenerales as $datos):
                                                    $datosJSON = json_encode($datos); ?>
                                                    <tr>
                                                        <td><?php echo $datos["venta_id"] ?></td>
                                                        <td><?php echo $datos["codigo_tiket"] ?></td>
                                                        <td><?php echo $datos["cliente"] ?></td>
                                                        <td><?php echo $datos["dia_nombre"] ?></td>
                                                        <td><?php echo $datos["fecha"] ?></td>
                                                        <td><?php echo $datos["hora"] ?></td>
                                                        <td><?php echo "S/ " . $datos["total"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_final"] ?></td>
                                                        <td><?php echo "S/ " . $datos["perdida_utilidad"] ?></td>
                                                        <td><?php echo $datos["estado_pago"] ?></td>
                                                        <td>
                                                            <div class="mt-2 text-center d-flex justify-content-center">
                                                                <a onclick='abrirModalDetalle(<?php echo $datosJSON ?>)' class="btn btn-success btn-sm btn-round" role="button">DETALLE</a>
                                                                <a href="javascript:void(0);" onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>)' class="btn btn-secondary btn-round btn-sm mx-1" role="button">PDF</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ═══════════════════════════════════════════
                             PESTAÑA 4: VENTAS POR RANGO
                             ═══════════════════════════════════════════ -->
                        <div class="tab-pane fade" id="pills-ventaRango" role="tabpanel" aria-labelledby="ventaRango">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="fechaInicio">Fecha Inicio:</label>
                                            <input type="date" id="fechaInicio" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="fechaFin">Fecha Fin:</label>
                                            <input type="date" id="fechaFin" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label>&nbsp;</label>
                                            <button onclick="filtrarPorRango()" class="btn btn-primary form-control">
                                                <i class="fas fa-search"></i> Buscar
                                            </button>
                                        </div>
                                    </div>

                                    <div id="grafico-rango-container" style="display:none;" class="mb-4">
                                        <div class="card shadow-sm">
                                            <div class="card-body">
                                                <h6 class="card-title text-muted mb-3"><i class="fas fa-chart-line"></i> Evolución en el Rango Seleccionado</h6>
                                                <div style="position:relative;height:200px;">
                                                    <canvas id="chartRango"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-warning" id="alertRango" style="display:none;">
                                        <strong>Total del Rango:</strong> S/ <span id="totalRango">0.00</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="TablaVentaRango" class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th><th>N° Ticket</th><th>Cliente</th><th>DÍA</th>
                                                    <th>FECHA</th><th>HORA</th><th>TOTAL(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th><th>pérdida</th><th>ESTADO</th><th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- fin tab-content -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle Venta -->
<style>
    .modal-dialog-custom { max-width: 900px; margin: 0 auto; }
    @media (max-width: 768px) { .modal-dialog-custom { max-width: 80%; } }
    @media (max-width: 576px) { .modal-dialog-custom { width: 100%; margin: 0 10px; max-width: 100%; } }
    .modal-content { padding: 15px; }
    .dataTable { overflow-x: auto; }
</style>

<div class="modal fade" id="modalDetalleVenta" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h4 class="card-title text-center" style="font-size:28px;">Venta de S/ <strong id="idMontoVenta"></strong></h4>
                <hr>
                <p class="card-text text-center" id="idUtilidad"></p>
                <div class="card-sub text-center">Aquí podrás revisar los datos de la venta.</div>
                <div class="row justify-content-center align-items-center sm-2">
                    <div class="col-sm-6">
                        <div class="card text-start">
                            <div class="card-body" style="color:indigo">
                                <h4 class="card-title" style="color:indigo"><i class="fas fa-user"></i> Cliente</h4>
                                <p class="card-text" id="nombreCliente"></p>
                                <hr>
                                <div><strong>N° DOCUMENTO:</strong> <span id="docCliente"></span></div>
                                <div><strong>Número de Celular:</strong> <span id="numCelCliente"></span></div>
                                <div><strong>Correo:</strong> <span id="emailCliente"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card text-start">
                            <div class="card-body">
                                <h4 class="card-text" style="color:green"><i class="fas fa-credit-card"></i> Monto Final: S/ <strong id="idMontoFinalVenta"></strong></h4>
                                <p>La venta real fue de <strong id="idTotalOriginal"></strong></p>
                                <div><strong>Atendido Por:</strong> <span id="idUsuario"></span></div>
                                <div><strong>Fecha:</strong> <span id="idFechaVenta"></span></div>
                                <div><strong>Hora:</strong> <span id="idHoraVenta"></span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">Detalle de Venta</div>
                        <div class="card-sub">Revisa el detalle de la venta :)</div>
                        <div class="table-responsive">
                            <table id="tablaDetalle" class="table table-head-bg-secondary mt-4">
                                <thead>
                                    <tr>
                                        <th scope="col">descripción</th>
                                        <th scope="col">corte</th>
                                        <th scope="col">Cant</th>
                                        <th scope="col">P.Uni</th>
                                        <th scope="col">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
// ─── Funciones originales ─────────────────────────────────────
function fn_abrir_pdf(id_venta) {
    window.open("ticket.php?id=" + parseInt(id_venta), "_blank");
}

function abrirModalDetalle(datosJsonVenta) {
    $('#modalDetalleVenta').modal('show');
    document.getElementById("nombreCliente").innerText = datosJsonVenta.cliente;
    document.getElementById("docCliente").innerText = datosJsonVenta.numero_doc_cliente;
    document.getElementById("numCelCliente").innerText = datosJsonVenta.telefonomovil_cliente;
    document.getElementById("emailCliente").innerText = datosJsonVenta.email_cliente;
    document.getElementById("idMontoVenta").innerText = datosJsonVenta.monto_venta_final;
    document.getElementById("idMontoFinalVenta").innerText = datosJsonVenta.monto_venta_final;
    document.getElementById("idTotalOriginal").innerText = "S/ " + datosJsonVenta.total;
    document.getElementById("idFechaVenta").innerText = datosJsonVenta.fecha;
    document.getElementById("idHoraVenta").innerText = datosJsonVenta.hora;
    document.getElementById("idUsuario").innerText = datosJsonVenta.usuario;

    if (datosJsonVenta.perdida_utilidad < 0) {
        document.getElementById("idUtilidad").innerHTML = "<span style='color:red'> En esta venta, PERDISTE un margen de utilidad de <strong>S/" + (parseFloat(datosJsonVenta.perdida_utilidad) * -1.00).toFixed(2) + ".</strong> </span>";
    } else {
        if (datosJsonVenta.estado_pago === "CREDITO" && datosJsonVenta.estado_final === "VENTA REALIZADA AL CREDITO - AUN DEBE") {
            document.getElementById("idUtilidad").innerHTML = "<b>" + datosJsonVenta.estado_final + "</b><br> <span style='color:green'>En esta venta fue realizada a CRÉDITO. Tiene abonado S/ " + datosJsonVenta.acumulado_deuda + " </span> <span style='color:orange'><br><strong>Para más información, Revisar en la seccion de Crédito [Historial Clientes] </span>";
        } else if (datosJsonVenta.estado_pago === "CREDITO" && datosJsonVenta.estado_final === "PAGADO - CREDTIO") {
            document.getElementById("idUtilidad").innerHTML = "<b>" + datosJsonVenta.estado_final + "</b><br><span style='color:orange'> En esta venta fue realizada a CRÉDITO. <strong style='color:green'>Pagó su DEUDA</span>";
        } else {
            document.getElementById("idUtilidad").innerHTML = "<span style='color:green'> <b> En esta venta, no hiciste rebajas :)</b> </span>";
        }
    }

    $.ajax({
        url: 'logica/clssConsultas.php',
        type: 'POST',
        data: { accion: datosJsonVenta.accion_ajax, venta_id: datosJsonVenta.venta_id },
        dataType: 'json',
        success: function(datosArticulo) {
            var tabla = document.getElementById("tablaDetalle").getElementsByTagName("tbody")[0];
            tabla.innerHTML = '';
            for (let i = 0; i < datosArticulo.length; i++) {
                let articulo = datosArticulo[i];
                let nuevaFila = tabla.insertRow();
                let totalCorte = (articulo["minutos"] === null && articulo["costo_por_minuto"] === null) ? '-' :
                    (articulo["minutos"] && articulo["costo_por_minuto"]) ?
                    (articulo["costo_por_minuto"] * articulo["minutos"]) : articulo["sub_total"] || '-';
                let totalCorteRedondeado = (totalCorte !== '-') ? "S/ " + totalCorte : totalCorte;
                nuevaFila.insertCell(0).innerHTML = articulo["descripcion"];
                nuevaFila.insertCell(1).textContent = totalCorteRedondeado;
                nuevaFila.insertCell(2).textContent = articulo["cantidad"] || '-';
                nuevaFila.insertCell(3).textContent = articulo["precio_unitario_articulo"] != null ? "S/ " + articulo["precio_unitario_articulo"] : '-';
                nuevaFila.insertCell(4).textContent = "S/ " + articulo["sub_total"] || '-';
            }
        },
        error: function(xhr, status, error) { console.error("Error al obtener los detalles de la venta:", error); }
    });
}

// ─── Función filtrarPorRango con gráfico integrado ────────────
let chartRangoInstance = null;

function filtrarPorRango() {
    var fechaInicio = document.getElementById('fechaInicio').value;
    var fechaFin    = document.getElementById('fechaFin').value;
    if (!fechaInicio || !fechaFin) { alert('Por favor selecciona ambas fechas'); return; }
    if (fechaInicio > fechaFin)    { alert('La fecha de inicio no puede ser mayor que la fecha fin'); return; }

    $.ajax({
        url: 'logica/clssConsultas.php',
        type: 'POST',
        data: { accion: 'VENTAS_POR_RANGO', fecha_inicio: fechaInicio, fecha_fin: fechaFin },
        dataType: 'json',
        success: function(datos) {
            var ventasPorFecha = {};
            var total = 0;
            datos.forEach(function(dato) {
                ventasPorFecha[dato.fecha] = (ventasPorFecha[dato.fecha] || 0) + parseFloat(dato.monto_venta_final);
                total += parseFloat(dato.monto_venta_final);
            });
            var fechasLabels = Object.keys(ventasPorFecha).sort();
            var fechasValues = fechasLabels.map(function(f) { return ventasPorFecha[f]; });

            document.getElementById('grafico-rango-container').style.display = 'block';
            if (chartRangoInstance) { chartRangoInstance.destroy(); }
            chartRangoInstance = new Chart(document.getElementById('chartRango'), {
                type: 'line',
                data: {
                    labels: fechasLabels,
                    datasets: [{
                        label: 'Ventas S/',  /* ✅ CORREGIDO */
                        data: fechasValues,
                        borderColor: '#ff9f1c',
                        backgroundColor: 'rgba(255,159,28,0.12)',
                        borderWidth: 2.5,
                        pointRadius: 5,
                        pointBackgroundColor: '#ff9f1c',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { ticks: { autoSkip: true, maxRotation: 45 } },
                        y: { beginAtZero: true, ticks: { callback: function(v) { return 'S/ ' + v.toFixed(0); } } }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: function(ctx) { return ' S/ ' + ctx.parsed.y.toFixed(2); } } }
                    }
                }
            });

            if ($.fn.DataTable.isDataTable('#TablaVentaRango')) { $('#TablaVentaRango').DataTable().destroy(); }
            $('#TablaVentaRango tbody').empty();
            var tbody = '';
            datos.forEach(function(dato) {
                dato.accion_ajax = 'DETALLEVENTA_VENTA_ID';
                var datosJSON = JSON.stringify(dato);
                tbody += '<tr>' +
                    '<td>' + dato.venta_id + '</td><td>' + dato.codigo_tiket + '</td><td>' + dato.cliente + '</td>' +
                    '<td>' + dato.dia_nombre + '</td><td>' + dato.fecha + '</td><td>' + dato.hora + '</td>' +
                    '<td>S/ ' + dato.total + '</td><td>S/ ' + dato.monto_venta_final + '</td>' +
                    '<td>S/ ' + dato.perdida_utilidad + '</td><td>' + dato.estado_pago + '</td>' +
                    '<td><div class="mt-2 text-center d-flex justify-content-center">' +
                        '<a onclick=\'abrirModalDetalle(' + datosJSON + ')\' class="btn btn-success btn-sm btn-round" role="button">DETALLE</a>' +
                        '<a href="javascript:void(0);" onclick="fn_abrir_pdf(' + dato.venta_id + ')" class="btn btn-secondary btn-round btn-sm mx-1" role="button">PDF</a>' +
                    '</div></td></tr>';
            });
            $('#TablaVentaRango tbody').html(tbody);
            $('#TablaVentaRango').DataTable({
                "order": [[0, 'desc']],
                language: {
                    "sLengthMenu":"Mostrar _MENU_ registros","sZeroRecords":"No se encontraron resultados",
                    "sEmptyTable":"Ningún dato disponible","sInfo":"Mostrando _START_ al _END_ de _TOTAL_ registros",
                    "sInfoEmpty":"0 registros","sSearch":"Buscar:","sLoadingRecords":"Cargando...",
                    "oPaginate":{"sFirst":"Primero","sPrevious":"Anterior","sNext":"Siguiente","sLast":"Último"}
                }
            });
            document.getElementById('totalRango').innerText = total.toFixed(2);
            document.getElementById('alertRango').style.display = 'block';
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            alert('Error al cargar las ventas. Revisa la consola para más detalles.');
        }
    });
}

// ─── Document Ready ───────────────────────────────────────────
$(document).ready(function() {
    document.getElementById('totalDiario').innerText   = '<?php echo number_format($totalDiario, 2); ?>';
    document.getElementById('totalSemanal').innerText  = '<?php echo number_format($totalSemanal, 2); ?>';
    document.getElementById('totalGeneral').innerText  = '<?php echo number_format($totalGeneral, 2); ?>';
    fnDataTables();
    initGraficos();
});

// ─── DataTables ───────────────────────────────────────────────
function fnDataTables() {
    $(".dataTable").DataTable({
        "order": [[0, 'desc']],
        language: {
            "sLengthMenu":"Mostrar _MENU_ registros","sZeroRecords":"No se encontraron resultados",
            "sEmptyTable":"Ningún dato disponible en esta tabla",
            "sInfo":"Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":"Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":"(filtrado de un total de _MAX_ registros)",
            "sSearch":"Buscar:","sLoadingRecords":"Cargando...",
            "oPaginate":{"sFirst":"Primero","sPrevious":"Anterior","sNext":"Siguiente","sLast":"Último"},
            "oAria":{"sSortAscending":": Activar para ordenar ascendente","sSortDescending":": Activar para ordenar descendente"}
        }
    });
}

// ─── Inicializar todos los gráficos ──────────────────────────
function initGraficos() {
    var COLORS = {
        primary: '#4361ee', success: '#2ec4b6', warning: '#ff9f1c',
        danger:  '#e63946', purple:  '#7b2d8b', gray: '#adb5bd'
    };

    function generarColores(n) {
        var pool = [COLORS.primary, COLORS.success, COLORS.warning, COLORS.danger, COLORS.purple, COLORS.gray];
        return Array.from({length: n}, function(_, i) { return pool[i % pool.length]; });
    }

    var baseOpts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    };

    // Datos desde PHP
    var horasLabels    = <?php echo json_encode(array_keys($ventasPorHora)); ?>;
    var horasValues    = <?php echo json_encode(array_values($ventasPorHora)); ?>;
    var estadoDiaL     = <?php echo json_encode(array_keys($estadosPagoDia)); ?>;
    var estadoDiaV     = <?php echo json_encode(array_values($estadosPagoDia)); ?>;
    var semanaLabels   = <?php echo json_encode(array_keys($ventasPorDia)); ?>;
    var semanaTotal    = <?php echo json_encode(array_column(array_values($ventasPorDia), 'total')); ?>;
    var semanaPerdida  = <?php echo json_encode(array_column(array_values($ventasPorDia), 'perdida')); ?>;
    var mesLabels      = <?php echo json_encode(array_keys($ventasPorMes)); ?>;
    var mesValues      = <?php echo json_encode(array_values($ventasPorMes)); ?>;
    var estadoGenL     = <?php echo json_encode(array_keys($estadosPagoGen)); ?>;
    var estadoGenV     = <?php echo json_encode(array_values($estadosPagoGen)); ?>;
    var topClientesL   = <?php echo json_encode(array_keys($topClientes5)); ?>;
    var topClientesV   = <?php echo json_encode(array_values($topClientes5)); ?>;

    // 1. Barras por hora ✅ CORREGIDO: label agregado
    new Chart(document.getElementById('chartHoras'), {
        type: 'bar',
        data: { labels: horasLabels, datasets: [{
            label: 'Ventas por Hora (S/)',
            data: horasValues,
            backgroundColor: 'rgba(67,97,238,0.15)',
            borderColor: COLORS.primary,
            borderWidth: 2,
            borderRadius: 6
        }]},
        options: Object.assign({}, baseOpts, {
            scales: { y: { beginAtZero: true, ticks: { callback: function(v){ return 'S/ '+v.toFixed(0); } } } },
            plugins: { legend:{display:false}, tooltip:{ callbacks:{ label: function(c){ return ' S/ '+c.parsed.y.toFixed(2); } } } }
        })
    });

    // 2. Dona estado pago día (ya tenía labels correctos)
    new Chart(document.getElementById('chartEstadoDia'), {
        type: 'doughnut',
        data: { labels: estadoDiaL, datasets: [{
            label: 'Estado de Pago',
            data: estadoDiaV,
            backgroundColor: generarColores(estadoDiaL.length),
            borderWidth: 2,
            borderColor: '#fff'
        }]},
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:true, position:'bottom', labels:{font:{size:11}} } } }
    });

    // 3. Línea tendencia semanal ✅ CORREGIDO: label agregado
    new Chart(document.getElementById('chartSemanaLinea'), {
        type: 'line',
        data: { labels: semanaLabels, datasets: [{
            label: 'Tendencia Semanal (S/)',
            data: semanaTotal,
            borderColor: COLORS.success,
            backgroundColor: 'rgba(46,196,182,0.12)',
            borderWidth: 2.5,
            pointRadius: 5,
            pointBackgroundColor: COLORS.success,
            fill: true,
            tension: 0.4
        }]},
        options: Object.assign({}, baseOpts, {
            scales: { y: { beginAtZero: true, ticks: { callback: function(v){ return 'S/ '+v.toFixed(0); } } } },
            plugins: { legend:{display:false}, tooltip:{ callbacks:{ label: function(c){ return ' S/ '+c.parsed.y.toFixed(2); } } } }
        })
    });

    // 4. Barras ingresos vs pérdidas (ya tenían labels correctos)
    new Chart(document.getElementById('chartSemanaBarra'), {
        type: 'bar',
        data: { labels: semanaLabels, datasets: [
            { label:'Ingresos', data: semanaTotal,   backgroundColor:'rgba(46,196,182,0.15)', borderColor:COLORS.success, borderWidth:2, borderRadius:5 },
            { label:'Pérdidas', data: semanaPerdida, backgroundColor:'rgba(230,57,70,0.15)',  borderColor:COLORS.danger,  borderWidth:2, borderRadius:5 }
        ]},
        options: Object.assign({}, baseOpts, {
            scales: { y: { beginAtZero: true, ticks: { callback: function(v){ return 'S/ '+v.toFixed(0); } } } },
            plugins: { legend:{ display:true, position:'bottom', labels:{font:{size:11}} }, tooltip:{ callbacks:{ label: function(c){ return ' S/ '+c.parsed.y.toFixed(2); } } } }
        })
    });

    // 5. Área evolución mensual ✅ CORREGIDO: label agregado
    new Chart(document.getElementById('chartMensual'), {
        type: 'line',
        data: { labels: mesLabels, datasets: [{
            label: 'Evolución Mensual (S/)',
            data: mesValues,
            borderColor: COLORS.primary,
            backgroundColor: 'rgba(67,97,238,0.10)',
            borderWidth: 2.5,
            pointRadius: 4,
            pointBackgroundColor: COLORS.primary,
            fill: true,
            tension: 0.3
        }]},
        options: Object.assign({}, baseOpts, {
            scales: {
                x: { ticks: { autoSkip: false, maxRotation: 45 } },
                y: { beginAtZero: true, ticks: { callback: function(v){ return 'S/ '+v.toFixed(0); } } }
            },
            plugins: { legend:{display:false}, tooltip:{ callbacks:{ label: function(c){ return ' S/ '+c.parsed.y.toFixed(2); } } } }
        })
    });

    // 6. Dona tipos de pago general (ya tenía labels correctos)
    new Chart(document.getElementById('chartEstadoGen'), {
        type: 'doughnut',
        data: { labels: estadoGenL, datasets: [{
            label: 'Tipos de Pago',
            data: estadoGenV,
            backgroundColor: generarColores(estadoGenL.length),
            borderWidth: 2,
            borderColor: '#fff'
        }]},
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:true, position:'bottom', labels:{font:{size:11}} } } }
    });

    // 7. Barras horizontales top 5 clientes (ya tenía label implícito por dataset único)
    new Chart(document.getElementById('chartTopClientes'), {
        type: 'bar',
        data: { labels: topClientesL, datasets: [{
            label: 'Top 5 Clientes (S/)',
            data: topClientesV,
            backgroundColor: ['rgba(255,159,28,0.2)','rgba(67,97,238,0.2)','rgba(46,196,182,0.2)','rgba(230,57,70,0.2)','rgba(123,45,139,0.2)'],
            borderColor:     [COLORS.warning, COLORS.primary, COLORS.success, COLORS.danger, COLORS.purple],
            borderWidth: 2,
            borderRadius: 6
        }]},
        options: Object.assign({}, baseOpts, {
            indexAxis: 'y',
            scales: { x: { ticks: { callback: function(v){ return 'S/ '+v.toFixed(0); } } } },
            plugins: { legend:{display:false}, tooltip:{ callbacks:{ label: function(c){ return ' S/ '+c.parsed.x.toFixed(2); } } } }
        })
    });
}
</script>

<?php include("pie.php"); ?>