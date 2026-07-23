<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");
include("../../includes/conexion.php");

$codReserva = (int) ($_GET['codReserva'] ?? 0);
$idUsuario = (int) $_SESSION['id'];

$reserva = null;

if ($codReserva > 0) {

    $sql = "

    SELECT *

    FROM reservas

    WHERE codReserva = $codReserva

    AND codUsuario = $idUsuario

    ";

    $resultado = mysqli_query($link, $sql);

    if ($resultado) {
        $reserva = mysqli_fetch_assoc($resultado);
    }
}

if (!$reserva) {
?>

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card card-custom">

                    <div class="card-body p-5 text-center" role="alert">

                        <h2 class="text-danger">

                            Reserva no encontrada

                        </h2>

                        <p>

                            La reserva que buscás no existe o no te pertenece.

                        </p>

                        <a href="listar.php" class="btn btn-secondary">

                            <span aria-hidden="true">←</span> Volver

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

<?php

    include("../../includes/footer.php");
    exit();
}

if ($reserva['estadoReserva'] !== 'PENDIENTE') {
?>

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card card-custom">

                    <div class="card-body p-5 text-center" role="alert">

                        <h2 class="text-danger">

                            No se puede modificar esta reserva

                        </h2>

                        <p>

                            Su estado actual es "<?= htmlspecialchars($reserva['estadoReserva'], ENT_QUOTES, 'UTF-8') ?>".

                        </p>

                        <a href="verReserva.php?codReserva=<?= $codReserva ?>" class="btn btn-secondary">

                            <span aria-hidden="true">←</span> Volver a la reserva

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

<?php

    include("../../includes/footer.php");
    exit();
}

$codVuelo = (int) $reserva['codVuelo'];

$vuelo = null;

if ($codVuelo > 0) {

    $sqlvuelos = "

    SELECT *

    FROM vuelos

    WHERE codVuelo = $codVuelo

    ";

    $resultadoVuelo = mysqli_query($link, $sqlvuelos);

    if ($resultadoVuelo) {
        $vuelo = mysqli_fetch_assoc($resultadoVuelo);
    }
}

if (!$vuelo) {
?>

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card card-custom">

                    <div class="card-body p-5 text-center" role="alert">

                        <h2 class="text-danger">

                            No pudimos cargar el vuelo

                        </h2>

                        <p>

                            No encontramos el vuelo asociado a esta reserva.

                        </p>

                        <a href="verReserva.php?codReserva=<?= $codReserva ?>" class="btn btn-secondary">

                            <span aria-hidden="true">←</span> Volver a la reserva

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

<?php

    include("../../includes/footer.php");
    exit();
}

$cantAsientosActual = (int) $reserva['cantAsientos'];

$descuento = 0;
if ($cantAsientosActual > 0 && $vuelo['precioVuelo'] > 0) {
    $descuento = (($vuelo['precioVuelo'] - ($reserva['precioFinal'] / $cantAsientosActual)) / $vuelo['precioVuelo']) * 100;
}

$precioFinal = $vuelo['precioVuelo'] - ($vuelo['precioVuelo'] * $descuento / 100);

$origenOut = htmlspecialchars($vuelo['origenVuelo'], ENT_QUOTES, 'UTF-8');
$destinoOut = htmlspecialchars($vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8');
$fechaVueloOut = htmlspecialchars($vuelo['fechaVuelo'], ENT_QUOTES, 'UTF-8');
$horaSalidaOut = htmlspecialchars($vuelo['horaSalida'], ENT_QUOTES, 'UTF-8');

$asientosMax = (int) $vuelo['asientosDisponibles'] + $cantAsientosActual;

$error = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : null;

?>


<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2 id="tituloModificar">Modificar reserva</h2>

                    <?php if ($error !== null) { ?>

                        <div class="alert alert-danger" role="alert">

                            <?= $error ?>

                        </div>

                    <?php } ?>



                    <div class="mb-3">
                        Fecha de vuelo: <time datetime="<?= $fechaVueloOut ?>"><?= $fechaVueloOut ?></time>
                    </div>
                    <div class="mb-3">
                        Horario del vuelo: <time datetime="<?= $horaSalidaOut ?>"><?= $horaSalidaOut ?></time>
                    </div>
                    <div class="mb-3">
                        Origen: <?= $origenOut ?>
                    </div>
                    <div class="mb-3">
                        Destino: <?= $destinoOut ?>
                    </div>
                    <div class="mb-3">
                        Precio asientos: $<?= number_format($vuelo['precioVuelo'], 0, ',', '.') ?>
                    </div>

                    <div class="mb-3">
                        Descuento: <?= number_format($descuento, 1, ',', '.') ?>%
                    </div>
                    <div class="mb-3">
                        Precio final: $<?= number_format($precioFinal, 0, ',', '.') ?>
                    </div>
                    <div class="mb-3">
                        Asientos disponibles: <?= (int) $vuelo['asientosDisponibles'] ?>
                    </div>

                    <form action="guardarModificaciones.php" method="post" aria-labelledby="tituloModificar" novalidate>

                        <div class="mb-3">

                            <input type="hidden" name="codReserva" value="<?= $codReserva ?>">

                            <label for="cantAsientos" class="form-label">Asientos reservados</label>

                            <input
                                type="number"
                                id="cantAsientos"
                                name="cantAsientos"
                                class="form-control w-50"
                                min="1"
                                max="<?= $asientosMax ?>"
                                value="<?= $cantAsientosActual ?>"
                                required
                                aria-required="true"
                                aria-describedby="cantAsientosAyuda">

                            <small id="cantAsientosAyuda" class="form-text text-muted">Podés reservar entre 1 y <?= $asientosMax ?> asientos.</small>

                        </div>

                        <div class="d-flex gap-2">

                            <button class="btn btn-primary" type="submit">

                                Guardar cambios

                            </button>

                            <a href="verReserva.php?codReserva=<?= $codReserva ?>" class="btn btn-outline-secondary">

                                Cancelar

                            </a>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>