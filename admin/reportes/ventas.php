<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$sql = " SELECT r.codReserva, u.nombreUsuario, a.nombreAerolinea, v.origenVuelo, v.destinoVuelo, r.fechaReserva, r.precioFinal
FROM reservas r
INNER JOIN usuarios u
ON r.codUsuario = u.codUsuario
INNER JOIN vuelos v
ON r.codVuelo = v.codVuelo
INNER JOIN aerolineas a
ON v.codAerolinea = a.codAerolinea
WHERE r.estadoReserva = 'CONFIRMADA'
ORDER BY r.fechaReserva DESC ";

$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($link));
}

$sqlTotal = " SELECT SUM(precioFinal) total FROM reservas WHERE estadoReserva = 'CONFIRMADA'";
$totalVentas = mysqli_fetch_assoc(mysqli_query($link, $sqlTotal));

?>


<div class="container mt-5">

    <h2>Reporte de Ventas</h2>

    <div class="card card-custom mt-4">

        <div class="card-body">

            <div class="alert alert-info">

                <strong>Total vendido:</strong>
                $<?= $totalVentas['total'] ?? 0 ?>

            </div>

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Aerolínea</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha</th>
                        <th>Importe</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>

                        <tr>

                            <td><?= $fila['codReserva'] ?></td>
                            <td><?= $fila['nombreUsuario'] ?></td>
                            <td><?= $fila['nombreAerolinea'] ?></td>
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