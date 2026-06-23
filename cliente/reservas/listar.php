<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

$idCliente = $_SESSION['id'];

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
                        <th>Acción</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                <?php

                $sqlVuelo = "

                SELECT *

                FROM vuelos

                WHERE codVuelo = {$fila['codVuelo']}

                ";

                $resultadoVuelo = mysqli_query(
                    $link,
                    $sqlVuelo
                );

                $vuelo = mysqli_fetch_assoc(
                    $resultadoVuelo
                );

                ?>

                <tr>

                    <td>

                        <img
                        src="<?= $vuelo['imagenVuelo'] ?>"
                        style="
                        width:12vw;
                        height:7vw;
                        border-radius:7px;
                        object-fit:cover;
                        ">

                    </td>

                    <td>

                        <?= $fila['cantAsientos'] ?>

                    </td>

                    <td>

                        <?= $vuelo['origenVuelo'] ?>

                    </td>

                    <td>

                        <?= $vuelo['destinoVuelo'] ?>

                    </td>

                    <td>

                        <?= $vuelo['fechaVuelo'] ?>

                    </td>

                    <td>

                        <?= $fila['fechaReserva'] ?>

                    </td>

                    <td>

                        $<?= number_format(
                            $fila['precioFinal'],
                            0,
                            ',',
                            '.'
                        ) ?>

                    </td>

                    <td>

                        <?php

                        if(
                            $fila['estadoReserva']
                            ==
                            'CONFIRMADA'
                        )
                        {
                            echo
                            '<span class="badge bg-success">Confirmada</span>';
                        }
                        elseif(
                            $fila['estadoReserva']
                            ==
                            'PENDIENTE'
                        )
                        {
                            echo
                            '<span class="badge bg-warning text-dark">Pendiente</span>';
                        }
                        else
                        {
                            echo
                            '<span class="badge bg-danger">Cancelada</span>';
                        }

                        ?>

                    </td>

                    <td>

                        <a
                        href="verReserva.php?codReserva=<?= $fila['codReserva'] ?>"
                        class="btn btn-primary btn-sm">

                            Seguir solicitud

                        </a>

                    </td>

                </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include("../../includes/footer.php"); ?>