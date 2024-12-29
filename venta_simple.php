<?php
    include("cabecera.php");
$id_movimiento = 5;
?>
<div
    class="container">
    <div class="page-inner">

        <div
            class="card"
            style="
            background-color:$ {
                1: orangered;
            }
            border-color:$ {
                2: darkblue;
            }
        ">
            <div class="card-body">
                <h4 class="card-title">Venta Simple</h4>
                <p class="card-text"><?php echo "VENTDA ID: ", $id_movimiento ?></p>
            </div>
        </div>
    </div>


</div>

<?php
include("pie.php");
?>