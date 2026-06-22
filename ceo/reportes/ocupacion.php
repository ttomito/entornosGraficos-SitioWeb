<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$idCEO = $_SESSION['id'];

$sql = "SELECT
v.codVuelo,
v.origenVuelo,
v.destinoVuelo,
v.fechaVuelo,
v.asientosDisponibles,

COALESCE(SUM(r.cantAsientos),0) ocupados

FROM vuelos v
LEFT JOIN reservas r
ON v.codVuelo = r.codVuelo
AND r.estadoReserva = 'CONFIRMADA'
WHERE v.codAerolinea = (SELECT codAerolinea FROM usuarios WHERE codUsuario = $idCEO)
GROUP BY v.codVuelo ORDER BY v.fechaVuelo";

$resultado = mysqli_query($link, $sql);

?>

<div class="container mt-5">

    <h2>Ocupación de Vuelos</h2>

    <div class="card card-custom">

        <div class="card-body">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>Vuelo</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha</th>
                        <th>Reservas</th>
                        <th>Asientos Disponibles</th>

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

                            <?= $fila['ocupados'] ?>

                        </td>
                        <td>
                            <?= $fila['asientosDisponibles'] ?>
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