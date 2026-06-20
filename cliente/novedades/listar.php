<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

$sql = "

SELECT *

FROM novedades

ORDER BY fechaPublicacion DESC

";

$resultado = mysqli_query(
    $link,
    $sql
);
?>

<div class="container mt-4">


    <div class="d-flex justify-content-between mb-4">

        <h2>

            Novedades

        </h2>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <table class="table table-hover">

                <thead>

                    <tr>
                        <th>Titulo</th>
                        <th>Fecha de publicacion</th>
                        <th>Fecha de expiracion</th>

                    </tr>

                </thead>

                <tbody>
                <?php $hoy = new DateTime() ?>
                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>
                <?php
                $fechaPublicacion = new DateTime($fila['fechaPublicacion']);
                $fechaExpiracion = new DateTime($fila['fechaExpiracion']);
                if ($fechaExpiracion>$hoy && $hoy>$fechaPublicacion) {?>
                    <?php $url = '../novedades/verNovedad.php?codNovedad=' . $fila['codNovedad'];  ?>
                        <tr style="cursor:pointer;" onclick="window.location.href='<?= $url ?>'">
                            <td>

                                <?= $fila['tituloNovedad'] ?>

                            </td>                         

                            <td>

                                <?= $fila['fechaPublicacion'] ?>

                            </td>


                            <td>

                                <?= $fila['fechaExpiracion'] ?>

                            </td>




                        </tr>
                    <?php } ?>
                    <?php } ?>
                </tbody>

            </table>

        </div>

    </div>

</div>
<?php include("../../includes/footer.php"); ?>