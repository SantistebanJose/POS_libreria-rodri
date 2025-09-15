<?php
include("cabecera.php");
?>

<div class="container">
    <div class="page-inner">
        <div class="card text-start">
            <div class="card-body">
                <h4 class="card-title"> <i class="fab fa-staylinked"></i> Ventas Declaradas a SUNAT </h4>
                <div class="card-sub">
                    Marque <strong>en el boton verde</strong> a los comprobantes que desea <strong>declarar a SUNAT.</strong>
                </div>

                <div class="tablita-responsive">
                    <div class="table-responsive">
                        <table id="tabla_boletas" class="dataTable display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>SERIE</th>
                                    <th>CORRELATIVO</th>
                                    <th>Fecha Emision</th>
                                    <th>Total</th>
                                    <th>SUNAT</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach (listComprobantesDeclarados() as $datos) {
                                    // Se hace un escape de las comillas para pasar el JSON correctamente
                                    $datosJSON = json_encode($datos);
                                ?>
                                    <tr>
                                        <td><?php echo $datos["id"] ?></td>
                                        <td><?php echo $datos["serie"] ?></td>
                                        <td><?php echo $datos["correlativo_texto"] ?></td>
                                        <td><?php echo $datos["fecha_emision"] ?></td>
                                        <td><?php echo $datos["total"] ?></td>
                                        <td><?php echo $datos["mensaje_sunat"] ?></td>
                                        <td>
                                            <div class="mt-2 text-center d-flex justify-content-center">
                                                <a
                                                    href="javascript:void(0);"
                                                    onclick='fn_abrir_modal_xml(<?php echo $datosJSON; ?>)'
                                                    class="btn btn-success btn-round btn-sm mx-1"
                                                    role="button" aria-label="XML">
                                                    XML
                                                </a>
                                                <a
                                                    href="javascript:void(0);"
                                                    onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>)'
                                                    class="btn btn-secondary btn-round btn-sm mx-1"
                                                    role="button" aria-label="PDF">
                                                    PDF
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>

<!-- Modal for displaying XML -->
<div class="modal fade" id="modal_generico" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card text-start">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="card-body">
                        <h5 class="card-title text-center" id="modalClienteLabel"> <i class="fas fa-user"></i> <span id="idTitulo"></span> </h5>
                        <div class="card-sub text-center">
                            Aquí podrás revisar el XML enviado a <strong>SUNAT.</strong>
                        </div>
                        <div id="contenido_xml">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS and JS for DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $(".dataTable").DataTable({
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sPrevious": "Anterior",
                    "sNext": "Siguiente",
                    "sLast": "Último"
                }
            }
        });
    });

    
    function fn_abrir_modal_xml(xml) {
        document.getElementById("contenido_xml").innerText = ""; 
        document.getElementById("idTitulo").innerText = "XML";
        var xmlbase64 = xml["xmlbase64"]; 
        document.getElementById("contenido_xml").innerText = xmlbase64; 
        $('#modal_generico').modal('show');
    }

    
    function fn_abrir_pdf(id_venta) {
        window.open("ticket.php?id=" + parseInt(id_venta), "_blank");
    }
</script>

<?php
include("pie.php");
?>
