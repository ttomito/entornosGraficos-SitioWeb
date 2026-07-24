<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

$idCliente = (int) $_SESSION['id'];

$sql = "SELECT * FROM reservas
WHERE codUsuario = $idCliente
ORDER BY codReserva DESC";

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

    <?php if (mysqli_num_rows($resultado) === 0) { ?>

        <div class="alert alert-info" role="status">
            Todavía no tenés reservas.
        </div>

    <?php } else { ?>

        <div class="card card-custom">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover">

                        <caption class="visually-hidden">Historial de reservas del usuario</caption>

                        <thead>

                            <tr>

                                <th scope="col">Imagen</th>
                                <th scope="col">Asientos</th>
                                <th scope="col">Origen</th>
                                <th scope="col">Destino</th>
                                <th scope="col">Fecha vuelo</th>
                                <th scope="col">Fecha reserva</th>
                                <th scope="col">Precio Final</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acción</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>

                                <?php

                                $codVueloInt = (int) $fila['codVuelo'];

                                $sqlVuelo = "

                SELECT *

                FROM vuelos

                WHERE codVuelo = $codVueloInt

                ";

                                $resultadoVuelo = mysqli_query(
                                    $link,
                                    $sqlVuelo
                                );

                                $vuelo = mysqli_fetch_assoc(
                                    $resultadoVuelo
                                );

                                // Por si el vuelo fue eliminado pero la reserva sigue existiendo
                                $origenOut = $vuelo ? htmlspecialchars($vuelo['origenVuelo'], ENT_QUOTES, 'UTF-8') : 'No disponible';
                                $destinoOut = $vuelo ? htmlspecialchars($vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') : 'No disponible';
                                $fechaVueloOut = $vuelo ? htmlspecialchars($vuelo['fechaVuelo'], ENT_QUOTES, 'UTF-8') : '';
                                $imagenOut = $vuelo ? htmlspecialchars($vuelo['imagenVuelo'], ENT_QUOTES, 'UTF-8') : '';

                                $fechaReservaOut = htmlspecialchars($fila['fechaReserva'], ENT_QUOTES, 'UTF-8');
                                $codReservaInt = (int) $fila['codReserva'];

                                ?>

                                <tr>

                                    <td>

                                        <?php if ($vuelo) { ?>
                                            <img
                                                src="../../uploads/vuelos/<?= $imagenOut ?>"
                                                alt="Vuelo de <?= $origenOut ?> a <?= $destinoOut ?>"
                                                style="
                            width:120px;
                            height:80px;
                            border-radius:7px;
                            object-fit:cover;
                            ">
                                        <?php } ?>

                                    </td>

                                    <td>

                                        <?= (int) $fila['cantAsientos'] ?>

                                    </td>

                                    <td>

                                        <?= $origenOut ?>

                                    </td>

                                    <td>

                                        <?= $destinoOut ?>

                                    </td>

                                    <td>

                                        <?php if ($fechaVueloOut !== '') { ?>
                                            <time datetime="<?= $fechaVueloOut ?>"><?= $fechaVueloOut ?></time>
                                        <?php } ?>

                                    </td>

                                    <td>

                                        <time datetime="<?= $fechaReservaOut ?>"><?= $fechaReservaOut ?></time>

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

                                        if (
                                            $fila['estadoReserva']
                                            ==
                                            'CONFIRMADA'
                                        ) {
                                            echo
                                            '<span class="badge bg-success">Confirmada</span>';
                                        } elseif (
                                            $fila['estadoReserva']
                                            ==
                                            'PENDIENTE'
                                        ) {
                                            echo
                                            '<span class="badge bg-warning text-dark">Pendiente</span>';
                                        } else {
                                            echo
                                            '<span class="badge bg-danger">Cancelada</span>';
                                        }

                                        ?>

                                    </td>

                                    <td>

                                        <a
                                            href="verReserva.php?codReserva=<?= $codReservaInt ?>"
                                            class="btn btn-primary btn-sm">

                                            Seguir solicitud
                                            <span class="visually-hidden"> del vuelo <?= $origenOut ?> a <?= $destinoOut ?>, reservado el <?= $fechaReservaOut ?></span>

                                        </a>

                                    </td>

                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    <?php } ?>

</div>

<?php include("../../includes/footer.php"); ?>