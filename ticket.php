<?php
require("logica/clssConsultas.php");
require('fpdf/fpdf.php');

// ruta_a_tu_php.php
if (isset($_GET['id'])) {
    $idVenta = $_GET['id'];
    fnGenerarTicket($idVenta);    
}
?>
