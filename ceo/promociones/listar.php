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

$sql = "

SELECT *

FROM promociones

WHERE codAerolinea = $codAerolinea

ORDER BY codPromocion DESC

";

$resultado = mysqli_query(
    $link,
    $sql
);

?>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>

            Gestión de Promociones

        </h2>

        <a
        href="crear.php"
        class="btn btn-success">

            Nueva Promoción

        </a>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Descuento</th>
                        <th>Fecha limite</th>
                        <th>Estado</th>
                        <th>Acciones</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>

                        <td><?= $fila['codPromocion'] ?></td>

                        <td><?= $fila['descripcionPromocion'] ?></td>

                        <td><?= $fila['descuentoPromocion'] ?>%</td>

                        <td><?= $fila['fechaLimitePromocion'] ?></td>

                        <td><?= $fila['estadoPromocion'] ?></td>

                        <td>

                            <a
                            href="editar.php?id=<?= $fila['codPromocion'] ?>"
                            class="btn btn-warning btn-sm">

                                Editar

                            </a>

                            <a
                            href="eliminar.php?id=<?= $fila['codPromocion'] ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('¿Eliminar promoción?')">

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