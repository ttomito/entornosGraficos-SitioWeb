<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

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
    <div class="row g-3 mb-4">

        <div class="col-md-3">

            <input
                type="text"
                class="form-control"
                placeholder="Origen">

        </div>

        <div class="col-md-3">

            <input
                type="text"
                class="form-control"
                placeholder="Destino">

        </div>

        <div class="col-md-3">

            <input
                type="date"
                class="form-control">

        </div>

        <div class="col-md-3">

            <button
                class="btn btn-primary w-100">

                Buscar vuelo

            </button>

        </div>

    </div>

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
                    <?php if ($fila['asientosDisponibles'] !=0){?>
                    
                        <?php $url = '../reservas/reservar.php?codVuelo=' . $fila['codVuelo']; ?>

                        <tr style="cursor:pointer;" onclick="window.location.href='<?= $url ?>'">
                            <td>

                                <img src="<?= $fila['imagenVuelo'] ?>" alt="" style="width:12vw; height:7vw;border-radius: 7px">
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

                                <button></button>

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