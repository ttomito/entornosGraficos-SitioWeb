<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$id = $_GET['id'];

$idCEO = $_SESSION['id'];

$sql = "

SELECT p.*

FROM promociones p

INNER JOIN usuarios u
ON p.codAerolinea = u.codAerolinea

WHERE p.codPromocion = $id

AND u.codUsuario = $idCEO

";

$resultado = mysqli_query(
    $link,
    $sql
);

$promocion = mysqli_fetch_assoc(
    $resultado
);

if(!$promocion)
{
    die("Acceso denegado");
}

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2>

                        Editar Promoción

                    </h2>

                    <form
                    action="actualizar.php"
                    method="post">

                        <input
                        type="hidden"
                        name="id"
                        value="<?= $promocion['codPromocion'] ?>">

                        <div class="mb-3">

                            <label>

                                Descripción

                            </label>

                            <textarea
                            name="descripcion"
                            class="form-control"
                            required><?= $promocion['descripcionPromocion'] ?></textarea>

                        </div>

                        <div class="mb-3">

                            <label>

                                Descuento %

                            </label>

                            <input
                            type="number"
                            min="1"
                            max="100"
                            name="descuento"
                            class="form-control"
                            value="<?= $promocion['descuentoPromocion'] ?>"
                            required>

                        </div>

                        <div class="mb-3">
                            <label>

                            Fecha limite

                            </label>

                            <input
                            type="date"
                            name="fechaLimite"
                            class="form-control"
                            required>
                        </div>

                        <button
                        class="btn btn-primary">

                            Actualizar

                        </button>

                        <a
                        href="listar.php"
                        class="btn btn-secondary">

                            Cancelar

                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>