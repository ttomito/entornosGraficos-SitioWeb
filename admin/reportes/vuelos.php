<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$sql = "

SELECT
v.*,
a.nombreAerolinea

FROM vuelos v

INNER JOIN aerolineas a
ON v.codAerolinea = a.codAerolinea

ORDER BY fechaVuelo

";

$resultado = mysqli_query(
    $link,
    $sql
);

?>

<div class="container mt-5">

    <h2>

        Reporte de Vuelos

    </h2>

    <div class="card card-custom mt-4">

        <div class="card-body">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Aerolínea</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha</th>
                        <th>Precio</th>
                        <th>Asientos</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>

                        <td><?= $fila['codVuelo'] ?></td>

                        <td><?= $fila['nombreAerolinea'] ?></td>

                        <td><?= $fila['origenVuelo'] ?></td>

                        <td><?= $fila['destinoVuelo'] ?></td>

                        <td><?= $fila['fechaVuelo'] ?></td>

                        <td>$<?= $fila['precioVuelo'] ?></td>

                        <td><?= $fila['asientosDisponibles'] ?></td>

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