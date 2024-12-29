<?php
include("cabecera.php");
include("logica/clssVenta.php");

if (isset($_GET['id'])) {
    
    $id = $_GET['id'];
}

?>

<div
    class="container">

    <div
        class="card"
        style="
            background-color:$ {
                1: orangered;
            }
            border-color:$ {
                2: darkblue;
            };
            
        ">

        <div class="card-body">
            <h4 class="card-title">Venta</h4>
            <div class="mb-3">
                <label for="" class="form-label">Movimiento</label>
                <select
                    class="form-select form-select-md"
                    name=""
                    id="">
                    <option selected>Seleccione</option>
                    <?php foreach(listarMovimientos2() as $movimiento): ?>
                        <option value="<?php echo htmlspecialchars($movimiento['id']); ?>">
                            <?php echo htmlspecialchars($movimiento['descripcion']); ?>
                        </option>
                    <?php endforeach ?>

                </select>
            </div>
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
            </div>
            <hr>

            <div class="container">
                <form>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label
                                for="inputName"
                                class="col-4 col-form-label">Minutos</label>
                            <div
                                class="row">
                                <div class="col"><a
                                        name=""
                                        id=""
                                        class="btn btn-primary"
                                        href="#"
                                        role="button">-</a>
                                </div>
                                <div class="col">10</div>
                                <div class="col"><a
                                        name=""
                                        id=""
                                        class="btn btn-primary"
                                        href="#"
                                        role="button">+</a>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <label
                                for="inputName"
                                class="col-4 col-form-label">Buscar Materiales</label>
                            <div
                                class="col-8">
                                <input
                                    type="Text"
                                    class="form-control"
                                    name="inputName"
                                    id="inputName"
                                    placeholder="Material - Madera Balsa Redonda" />
                            </div>
                            <br>
                        </div>

                        <div class="mb-3 row">
                            <div class="offset-sm-4 col-sm-8">
                                <button type="submit" class="btn btn-primary">
                                    Agregar MRD
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <hr>
            <hr>

            <div
                class="row ">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Detalle Corte</div>
                        </div>
                        <div class="card-body">
                            <div class="card-sub">
                                Aquí la venta por minutos en corte de MAQUINA
                            </div>
                            <table class="table mt-3">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">MINUTOS</th>
                                        <th scope="col">COSTO x MINUTO</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Mark</td>
                                        <td>Otto</td>

                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Jacob</td>
                                        <td>Thornton</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Detalle Materiales</div>
                        </div>
                        <div class="card-body">
                            <div class="card-sub">
                                Aquí la venta de los materiales
                            </div>
                            <table class="table mt-3">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">MATERIAL</th>
                                        <th scope="col">Precio Unitario</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Mark</td>
                                        <td>Otto</td>

                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Jacob</td>
                                        <td>Thornton</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-title" >Total <span>S/ xx.xx</span></div>


        </div>
    </div>

</div>


<?php
include("pie.php");
?>