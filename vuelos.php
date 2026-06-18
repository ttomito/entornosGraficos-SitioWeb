<?php

include("includes/header.php");
include("includes/conexion.php");



$sql = "

SELECT *

FROM vuelos

ORDER BY codVuelo DESC

";

$resultado = mysqli_query(
    $link,
    $sql
);
?>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>

            Vuelos disponibles

        </h2>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <table class="table table-hover">

                <thead>

                    <tr>
                        <th>imagen</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha</th>

                        <th>Precio</th>


                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>

                        <td>

                            <img src="<?= $fila['imagenVuelo'] ?>" alt="" style="width:12vw; height:7vw;object-fit: cover;border-radius: 7px; padding: 0">
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

                            $<?= $fila['precioVuelo'] ?>

                        </td>

                        <td>



                        </td>



                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
<?php include("includes/footer.php"); ?>