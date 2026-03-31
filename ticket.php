<?php
require("logica/clssConsultas.php");
require('fpdf/fpdf.php');

if (isset($_GET['id'])) {
    $idVenta  = $_GET['id'];
    $formato  = isset($_GET['formato']) ? strtoupper($_GET['formato']) : 'TICKET';
    fnGenerarTicket($idVenta, $formato);    
}
?>
