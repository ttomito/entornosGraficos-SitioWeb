<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$idCEO = $_SESSION['id'];

$sqlCEO = "

SELECT codAerolinea

FROM usuarios

WHERE codUsuario = $idCEO

";

$resultadoCEO = mysqli_query(
    $link,
    $sqlCEO
);

$ceo = mysqli_fetch_assoc(
    $resultadoCEO
);

$codAerolinea = $ceo['codAerolinea'];

if(!$codAerolinea)
{
?>

<div class="container mt-4">

    <div class="alert alert-warning">

        <h4>

            Aerolínea no asignada

        </h4>

        <p>

            Un administrador todavía no le asignó una aerolínea.

            No puede gestionar vuelos hasta que eso ocurra.

        </p>

    </div>

</div>

<?php

include("../../includes/footer.php");

exit();

}

$sql = "

SELECT *

FROM vuelos

WHERE codAerolinea = $codAerolinea

ORDER BY fechaVuelo

";

$resultado = mysqli_query(
    $link,
    $sql
);

?>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>

            Gestión de Vuelos

        </h2>

        <a
        href="crear.php"
        class="btn btn-success">

            Nuevo Vuelo

        </a>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Precio</th>
                        <th>Asientos</th>
                        <th>Acciones</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>

                        <td>

                            <?= $fila['codVuelo'] ?>

                        </td>

                        <td>

                            <?= $fila['origenVuelo'] ?>

                        </td>

                        <td>

                            <?= $fila['destinoVuelo'] ?>

                        </td>

                        <td>

                            <?= $fila['fechaVuelo'] ?>

                        </td>

                        <td>

                            <?= $fila['horaSalida'] ?>

                        </td>

                        <td>

                            $<?= $fila['precioVuelo'] ?>

                        </td>

                        <td>

                            <?= $fila['asientosDisponibles'] ?>

                        </td>

                        <td>

                            <a
                            href="editar.php?id=<?= $fila['codVuelo'] ?>"
                            class="btn btn-warning btn-sm">

                                Editar

                            </a>

                            <a
                            href="eliminar.php?id=<?= $fila['codVuelo'] ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('¿Desea eliminar este vuelo?');">

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