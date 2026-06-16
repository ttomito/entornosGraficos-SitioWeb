<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$id = $_GET['id'];

$sql = "
SELECT *
FROM aerolineas
WHERE codAerolinea = $id
";

$resultado = mysqli_query(
    $link,
    $sql
);

$aerolinea = mysqli_fetch_assoc(
    $resultado
);

?>

<div class="container mt-4">

    <div class="card card-custom">

        <div class="card-body">

            <h2 class="mb-4">

                Editar Aerolínea

            </h2>

            <form
            action="actualizar.php"
            method="post">

                <input
                type="hidden"
                name="id"
                value="<?= $aerolinea['codAerolinea'] ?>">

                <div class="mb-3">

                    <label>

                        Nombre

                    </label>

                    <input
                    type="text"
                    name="nombre"
                    class="form-control"
                    value="<?= $aerolinea['nombreAerolinea'] ?>"
                    required>

                </div>

                <div class="mb-3">

                    <label>

                        Descripción

                    </label>

                    <textarea
                    name="descripcion"
                    class="form-control"><?= $aerolinea['descripcionAerolinea'] ?></textarea>

                </div>

                <div class="mb-3">

                    <label>

                        País

                    </label>

                    <input
                    type="text"
                    name="pais"
                    class="form-control"
                    value="<?= $aerolinea['codPais'] ?>">

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

<?php
include("../../includes/footer.php");
?>