<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$idCEO = $_SESSION['id'];

$sql = "

SELECT

r.codReserva,
u.nombreUsuario,
v.origenVuelo,
v.destinoVuelo,
r.fechaReserva,
r.precioFinal

FROM reservas r

INNER JOIN usuarios u
ON r.codUsuario = u.codUsuario

INNER JOIN vuelos v
ON r.codVuelo = v.codVuelo

WHERE r.estadoReserva = 'CONFIRMADA'

AND v.codAerolinea =
(
    SELECT codAerolinea
    FROM usuarios
    WHERE codUsuario = $idCEO
)

ORDER BY r.fechaReserva DESC

";

$resultado = mysqli_query(
    $link,
    $sql
);

$sqlTotal = "

SELECT SUM(r.precioFinal) total

FROM reservas r

INNER JOIN vuelos v
ON r.codVuelo = v.codVuelo

WHERE r.estadoReserva = 'CONFIRMADA'

AND v.codAerolinea =
(
    SELECT codAerolinea
    FROM usuarios
    WHERE codUsuario = $idCEO
)

";

$total =
mysqli_fetch_assoc(
mysqli_query($link,$sqlTotal)
);

?>

<div class="container mt-5">

    <h2>

        Reporte de Ventas

    </h2>

    <div class="alert alert-success">

        Total vendido:

        <strong>

            $<?= $total['total'] ?? 0 ?>

        </strong>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha</th>
                        <th>Importe</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>

                        <td><?= $fila['codReserva'] ?></td>

                        <td><?= $fila['nombreUsuario'] ?></td>

                        <td><?= $fila['origenVuelo'] ?></td>

                        <td><?= $fila['destinoVuelo'] ?></td>

                        <td><?= $fila['fechaReserva'] ?></td>

                        <td>$<?= $fila['precioFinal'] ?></td>

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