<?php
include("cabecera.php");
?>

<div
    class="container">


    <div class="page-inner">
        <a
            name=""
            id=""
            class="btn btn-success btn-round"
            onclick='abriModalRegistroCompra()'
            role="button">Nueva Compra <i class="fas fa-plus"> </i></a>
        <br>
        <br>
        <div class="card text-start">

            <div class="card-body">
                <h4 class="card-title"><i class="fas fa-shopping-bag"></i> Registro de Compras</h4>
                <div class="card-sub">
                    Aquí podrás Registrar la Compras que realizas a tus proveedores. Una vez registrado, el <strong>Stock de tus productos</strong> tambien se actualiza.
                </div>
                <div class="row justify-content-center align-items-center sm-2">
                    <div class="col-sm-6">
                        <div class="card text-start">
                            <div class="card-body">
                                <h4 class="card-title"><i class="fas fa-shipping-fast"> </i> Datos de la Compra</h4>
                                <hr>
                                <div id="idUsuarioCompra" style="display: none;"> <?php echo $id_usuario_s ?></div>
                                <div><i class="far fa-user"> </i><strong> <?php echo $id_usuario_s . " - " . $nombre . ", " . $ape_usuario ?> </strong> </div>
                                <hr>
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Proveedor</strong></label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            class="form-control"
                                            name=""
                                            id=""
                                            aria-describedby="helpId"
                                            placeholder="Escribe al Proveedor" />
                                        <button class="btn btn-outline-secondary" type="button">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>

                                    <small id="helpId" class="form-text text-muted">
                                        <div class="card-sub">
                                            Si no encuentras a tu proveedor,<strong> Registra a tú Proveedor con el boton de Más <i class="fas fa-plus"></i> </strong>
                                        </div>
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Fecha de Compra</strong></label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        name=""
                                        id=""
                                        aria-describedby="helpId"
                                        placeholder="" />
                                </div>
                                <div
                                    class="row justify-content-center align-items-center">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label"><strong>N° de Comprobante</strong></label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name=""
                                                id=""
                                                aria-describedby="helpId"
                                                placeholder="" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="" class="form-label"><strong>Imagen de Comprobante</strong></label>
                                                <input
                                                    type="file"
                                                    class="form-control-file"
                                                    id="exampleFormControlFile1" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="form-label"><strong>Total de Compra</strong></label>
                                    <div class="input-group mb-3">

                                        <span class="input-group-text">S/</span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            aria-label="Amount (to the nearest dollar)" />
                                        <span class="input-group-text">.00</span>
                                    </div>
                                </div>



                            </div>
                        </div>

                    </div>
                    <div class="col-sm-6">
                        <div class="card text-start">
                            <div class="card-body">
                                <h4 class="card-title"><i class="fas fa-cart-plus"></i> Articulos</h4>
                                <hr>
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong> Articulo</strong></label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            class="form-control"
                                            name=""
                                            id="idBuscarArticulos"
                                            aria-describedby="helpId"
                                            placeholder="Escribe algo" />
                                        <button class="btn btn-outline-secondary" type="button" onclick='fnAbrirModalRegistroArticulos()'>
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>

                                    <small id="helpId" class="form-text text-muted">
                                        <div class="card-sub">
                                            Si no encuentras algun Articulo,<strong> Registra el Articulo con el boton de Más <i class="fas fa-plus"></i> </strong>
                                        </div>
                                    </small>
                                </div>
                                <hr>
                                <div id="idPanelProducto">
                                    <div class="mb-3">
                                        <label for="" class="form-label"><strong>Articulo</strong></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name=""
                                            id=""
                                            aria-describedby="helpId"
                                            placeholder="ARTICULO 1" />
                                    </div>
                                    <div class="row">
                                        <div class="card-body">
                                            <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="pills-home-tab-icon" data-bs-toggle="pill" href="#pills-home-icon" role="tab" aria-controls="pills-home-icon" aria-selected="true">
                                                        Cantidad Exacta
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="pills-profile-tab-icon" data-bs-toggle="pill" href="#pills-profile-icon" role="tab" aria-controls="pills-profile-icon" aria-selected="false">
                                                        Cajas
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="pills-contact-tab-icon" data-bs-toggle="pill" href="#pills-contact-icon" role="tab" aria-controls="pills-contact-icon" aria-selected="false">
                                                        Paquete Por Unidades de Medida
                                                    </a>
                                                </li>
                                            </ul>
                                            <hr>
                                            <div class="tab-content mt-2 mb-3" id="pills-with-icon-tabContent">
                                                <div class="tab-pane fade show active" id="pills-home-icon" role="tabpanel" aria-labelledby="pills-home-tab-icon">
                                                    <div
                                                        class="row justify-content-center align-items-center">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for="" class="form-label"><strong>Cantidad de Articulos</strong></label>
                                                                <div class="input-group mb-3">
                                                                    <span class="input-group-text">#</span>
                                                                    <input
                                                                        type="number"
                                                                        class="form-control"
                                                                        aria-label="Amount (to the nearest dollar)" />
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for="" class="form-label"><strong>Precio Unitario (S/)</strong></label>
                                                                <div class="input-group mb-3">
                                                                    <span class="input-group-text">S/</span>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        aria-label="Amount (to the nearest dollar)" />
                                                                    <span class="input-group-text">.00</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for="" class="form-label"><strong>Precio de Venta (S/)</strong></label>
                                                                <div class="input-group mb-3">
                                                                    <span class="input-group-text">S/</span>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        aria-label="Amount (to the nearest dollar)" />
                                                                    <span class="input-group-text">.00</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <a
                                                                name=""
                                                                id=""
                                                                class="btn btn-success btn-round"
                                                                onclick=''
                                                                role="button">Agregar <i class="fas fa-plus"> </i></a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="pills-profile-icon" role="tabpanel" aria-labelledby="pills-profile-tab-icon">
                                                    <div
                                                        class="row justify-content-center align-items-center">
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="" class="form-label"><strong>Cantidad de Cajas</strong></label>
                                                                <div class="input-group mb-3">
                                                                    <span class="input-group-text">#</span>
                                                                    <input
                                                                        type="number"
                                                                        class="form-control"
                                                                        aria-label="Amount (to the nearest dollar)" />
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="" class="form-label"><strong>Unidades Por Cajas</strong></label>
                                                                <div class="input-group mb-3">
                                                                    <span class="input-group-text">#</span>
                                                                    <input
                                                                        type="number"
                                                                        class="form-control"
                                                                        aria-label="Amount (to the nearest dollar)" />
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="" class="form-label"><strong>Total de Caja (S/)</strong></label>
                                                                <div class="input-group mb-3">
                                                                    <span class="input-group-text">S/</span>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        aria-label="Amount (to the nearest dollar)" />
                                                                    <span class="input-group-text">.00</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="" class="form-label"><strong>Precio Por Unidad (S/)</strong></label>
                                                                <div class="input-group mb-3">
                                                                    <span class="input-group-text">S/</span>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        aria-label="Amount (to the nearest dollar)" />
                                                                    <span class="input-group-text">.00</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="" class="form-label"><strong>Precio de VENTA Por Unidad (S/)</strong></label>
                                                                <div class="input-group mb-3">
                                                                    <span class="input-group-text">S/</span>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        aria-label="Amount (to the nearest dollar)" />
                                                                    <span class="input-group-text">.00</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4 text-center">
                                                            <a
                                                                name=""
                                                                id=""
                                                                class="btn btn-success btn-round"
                                                                onclick=''
                                                                role="button">Agregar <i class="fas fa-plus"> </i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="pills-contact-icon" role="tabpanel" aria-labelledby="pills-contact-tab-icon">
                                                    <div
                                                        class="row justify-content-center align-items-center">
                                                        <div class="col-sm-4">
                                                            <div class="input-group">
                                                                <select class="form-select form-select-md form-control" name="" id="">
                                                                    <option selected>Unidad de Medida</option>
                                                                    <option value="">Cien</option>
                                                                    <option value="">Mil</option>
                                                                    <option value="">Millar</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="" class="form-label"><strong>Cantidad de Paquetes</strong></label>
                                                                <div class="input-group mb-3">
                                                                    <span class="input-group-text">#</span>
                                                                    <input
                                                                        type="number"
                                                                        class="form-control"
                                                                        aria-label="Amount (to the nearest dollar)" />
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="" class="form-label"><strong>Precio de Paquete (S/)</strong></label>
                                                                <div class="input-group mb-3">
                                                                    <span class="input-group-text">S/</span>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        aria-label="Amount (to the nearest dollar)" />
                                                                    <span class="input-group-text">.00</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="" class="form-label"><strong>Precio de Por Unidad (S/)</strong></label>
                                                                <div class="input-group mb-3">
                                                                    <span class="input-group-text">S/</span>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        aria-label="Amount (to the nearest dollar)" />
                                                                    <span class="input-group-text">.00</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="" class="form-label"><strong>Precio de VENTA Por Unidad (S/)</strong></label>
                                                                <div class="input-group mb-3">
                                                                    <span class="input-group-text">S/</span>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        aria-label="Amount (to the nearest dollar)" />
                                                                    <span class="input-group-text">.00</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3">
                                                            <a
                                                                name=""
                                                                id=""
                                                                class="btn btn-success btn-round"
                                                                onclick=''
                                                                role="button">Agregar <i class="fas fa-plus"> </i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card text-start">

                    <div class="card-body">
                        <h4 class="card-title">Articulos de Compra</h4>
                        <hr>
                        <div
                            class="table-responsive-sm">
                            <table
                                class="table">
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
<style>
    /* Tamaño por defecto para pantallas grandes (computadoras) */
    .modal-dialog-custom {
        max-width: 80%;
        /* Este sería el tamaño 'normal' para computadoras */
        margin: 0 auto;
        /* Centra el modal */
    }

    /* Tamaño para pantallas medianas (tabletas) */
    @media (max-width: 768px) {
        .modal-dialog-custom {
            max-width: 80%;
            /* 80% del ancho de la pantalla en tabletas */
        }
    }

    /* Tamaño para pantallas pequeñas (teléfonos móviles) */
    @media (max-width: 576px) {
        .modal-dialog-custom {
            width: 100%;
            /* Asegura que el modal ocupe todo el ancho disponible en móviles */
            margin: 0 10px;
            /* Da un poco de espacio a los lados en móviles */
            max-width: 100%;
            /* No permite que el modal se haga más grande que el 100% */
        }
    }

    /* Asegura que el contenido del modal no se desborde */
    .modal-content {
        padding: 15px;
        /* Espaciado dentro del modal para que el contenido no esté pegado a los bordes */
    }

    .dataTable {
        overflow-x: auto;
        /* Para permitir desplazamiento horizontal si es necesario */
    }
</style>

<div
    class="modal fade"
    id="modalRegistroCompra"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document"> <!-- Usamos la clase personalizada aquí -->
        <div class="modal-content">


            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-shopping-bag"></i> Registro de Compras</h4>
                <hr>
                <div class="card-sub text-center">
                    Aquí podrás Registrar la Compras que realizas a tus proveedores. Una vez registrado, el <strong>Stock de tus productos</strong> tambien se actualiza.
                </div>
                <div class="row justify-content-center align-items-center sm-2">
                    <div class="col-sm-6">
                        <div class="card text-start">
                            <div class="card-body">
                                <h4 class="card-title">Compra</h4>
                                <div class="mb-3">
                                    <label for="" class="form-label">Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name=""
                                        id=""
                                        aria-describedby="helpId"
                                        placeholder="" />
                                    <small id="helpId" class="form-text text-muted">Help text</small>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="col-sm-6">
                        <div class="card text-start">
                            <div class="card-body">
                                <h4 class="card-title">Productos</h4>
                                <p class="card-text">Body</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<div
    class="modal fade"
    id="modalRegistroArticulos"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document"> <!-- Usamos la clase personalizada aquí -->
        <div class="modal-content">


            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-shopping-bag"></i> Registro de Articulos</h4>
                <hr>
                <div class="card-sub text-center">
                    Aquí podrás buscar los Articulos. Una vez registrado.
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    function abriModalRegistroCompra() {
        $('#modalRegistroCompra').modal('show');

        fnAbrirModalRegistroArticulos()
    }

    function fnAbrirModalRegistroArticulos() {
        $('#modalRegistroArticulos').modal('show');

    }
</script>

<?php
include("pie.php");
?>