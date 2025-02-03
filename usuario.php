<?php
include("cabecera.php");
?>

<div
    class="container">

    <div class="page-inner">
        <div class="card text-start">

            <div class="card-body">


                <h4 class="card-title">Usuarios</h4>
                <hr>
                <div
                    class="row justify-content-center align-items-center md-2">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="" class="form-label">Persona</label>

                            <input
                                type="text"
                                class="form-control"
                                name=""
                                id=""
                                aria-describedby="helpId"
                                placeholder="" />
                            <small id="helpId" class="form-text text-muted">Si no lo tienes registrado, Presioanr en el boton +</small>
                        </div>
                        <div
                            class="row justify-content-center align-items-center sm-2">
                            <div class="col-sm-4">
                                <div class="form-floating form-floating-custom mb-3">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="floatingInput"
                                        placeholder="name@example.com" />
                                    <label for="floatingInput">Nombre de Usuario</label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-floating form-floating-custom mb-3">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="floatingInput"
                                        placeholder="name@example.com" />
                                    <label for="floatingInput">Contraseña</label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">Rol</label>
                                    <select
                                        class="form-select form-select-sm"
                                        name=""
                                        id="">
                                        <option selected>Select one</option>
                                        <option value="">New Delhi</option>
                                        <option value="">Istanbul</option>
                                        <option value="">Jakarta</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="text-center">
                            <a
                                name=""
                                id=""
                                class="btn btn-primary"
                                href="#"
                                role="button">Agregar Usuario</a>
                        </div>





                    </div>

                    <div class="col-sm-6">
                        <div
                            class="table-responsive-sm">
                            <table
                                class="table table-light">
                                <thead>
                                    <tr>
                                        <th scope="col">Column 1</th>
                                        <th scope="col">Column 2</th>
                                        <th scope="col">Column 3</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="">
                                        <td scope="row">R1C1</td>
                                        <td>R1C2</td>
                                        <td>R1C3</td>
                                    </tr>
                                    <tr class="">
                                        <td scope="row">Item</td>
                                        <td>Item</td>
                                        <td>Item</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
include("pie.php");
?>