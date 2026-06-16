<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$sql = "
SELECT *
FROM aerolineas
ORDER BY codAerolinea
";

$resultado = mysqli_query(
    $link,
    $sql
);

?>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>

            Gestión de Aerolíneas

        </h2>

        <a
        href="crear.php"
        class="btn btn-success">

            Nueva Aerolínea

        </a>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>País</th>
                        <th>Acciones</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>

                        <td>

                            <?= $fila['codAerolinea'] ?>

                        </td>

                        <td>

                            <?= $fila['nombreAerolinea'] ?>

                        </td>

                        <td>

                            <?= $fila['descripcionAerolinea'] ?>

                        </td>

                        <td>

                            <?= $fila['codPais'] ?>

                        </td>

                        <td>

                            <a
                            href="editar.php?id=<?= $fila['codAerolinea'] ?>"
                            class="btn btn-warning btn-sm">

                                Editar

                            </a>

                            <a
                            href="eliminar.php?id=<?= $fila['codAerolinea'] ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('¿Desea eliminar la aerolínea <?= $fila['nombreAerolinea'] ?>?');">

                                Eliminar

                            </a>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>