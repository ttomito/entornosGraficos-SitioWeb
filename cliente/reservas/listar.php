<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

$idCliente= $_SESSION['id'];

$sql = "

SELECT *

FROM reservas

WHERE codUsuario = $idCliente

ORDER BY codReserva DESC

";

$resultado = mysqli_query(
    $link,
    $sql
);


?>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>

            Historial de reservas

        </h2>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <table class="table table-hover">

                <thead>

                    <tr>
                        <th>Imagen</th>
                        <th>Asientos</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha vuelo</th>
                        <th>Fecha reserva</th>
                        <th>Precio Final</th>
                        <th>Estado</th>
                    </tr>

                </thead>

                <tbody>
                    <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>
                    <?php
                        $sql = "

                        SELECT *

                        FROM vuelos

                        WHERE codVuelo = {$fila['codVuelo']}

                        ";

                        $resultado_2 = mysqli_query(
                            $link,
                            $sql
                        );
                        $fila_2 = mysqli_fetch_assoc($resultado_2);
                    ?>
                    <?php $url = '../reservas/verReserva.php?codReserva=' . $fila['codReserva']; ?>

                        <tr style="cursor:pointer;" onclick="window.location.href='<?= $url ?>'">

                            <td>

                                <img
                                    src="<?= $fila_2['imagenVuelo'] ?>"
                                    style="width:12vw; height:7vw; border-radius:7px"
                                >

                            </td>

                            <td>

                                <?= $fila['cantAsientos'] ?>

                            </td>

                            <td>

                                <?= $fila_2['origenVuelo'] ?>

                            </td>

                            <td>

                                <?= $fila_2['destinoVuelo'] ?>
                                
                            </td>

                            <td>

                                <?= $fila_2['fechaVuelo'] ?>
                                
                            </td>

                            <td>

                                <?= $fila['fechaReserva'] ?>
                                
                            </td>

                            <td>
                                <?= $fila['precioFinal'] ?>
                            </td>

                            <td>

                                <?= $fila['estadoReserva'] ?>
                                
                            </td>



                        </tr>
                        
                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
<?php include("../../includes/footer.php"); ?>