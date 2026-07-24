<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");
include("../../includes/conexion.php");

$codVuelo = (int) ($_GET['codVuelo'] ?? 0);
$idUsuario = (int) $_SESSION['id'];

$vuelo = null;

if ($codVuelo > 0) {

    $sql = "SELECT * FROM vuelos WHERE codVuelo = $codVuelo";
    $resultado = mysqli_query($link, $sql);

    if ($resultado) {
        $vuelo = mysqli_fetch_assoc($resultado);
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

                            Vuelo no encontrado

                        </h2>

                        <p>

                            El vuelo que buscás no existe o ya no está disponible.

                        </p>

                        <a href="../vuelos/listar.php" class="btn btn-secondary">

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

$codAerolinea = (int) $vuelo['codAerolinea'];

$sqlProm = "SELECT * FROM promociones WHERE codAerolinea = $codAerolinea AND estadoPromocion = 'APROBADA'";

$resultado_prom = mysqli_query($link, $sqlProm);

$descuentoMaximo = 0;
$hoy = date("Y-m-d");

if ($resultado_prom) {
    while ($promocion = mysqli_fetch_assoc($resultado_prom)) {
        if (
            $promocion['descuentoPromocion'] > $descuentoMaximo
            &&
            $promocion['fechaLimitePromocion'] >= $hoy
        ) {
            $descuentoMaximo = $promocion['descuentoPromocion'];
        }
    }
}

$precio = $vuelo['precioVuelo'];
$precioFinal = $precio - ($precio * $descuentoMaximo / 100);
$asientosDisponibles = (int) $vuelo['asientosDisponibles'];

$origenOut = htmlspecialchars($vuelo['origenVuelo'], ENT_QUOTES, 'UTF-8');
$destinoOut = htmlspecialchars($vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8');
$fechaVueloOut = htmlspecialchars($vuelo['fechaVuelo'], ENT_QUOTES, 'UTF-8');
$horaSalidaOut = htmlspecialchars($vuelo['horaSalida'], ENT_QUOTES, 'UTF-8');

$error = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : null;

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2 id="tituloReservar">Reservar Vuelo</h2>

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
                        Precio: $<?= number_format($precio, 0, ',', '.') ?>
                    </div>
                    <div class="mb-3">
                        Descuento (se aplica el mayor disponible): <?= number_format($descuentoMaximo, 1, ',', '.') ?>%
                    </div>
                    <div class="mb-3">
                        Precio final por asiento: $<?= number_format($precioFinal, 0, ',', '.') ?>
                    </div>
                    <div class="mb-3">
                        Asientos disponibles: <?= $asientosDisponibles ?>
                    </div>

                    <?php if ($asientosDisponibles <= 0) { ?>

                        <div class="alert alert-warning" role="alert">
                            No quedan asientos disponibles para este vuelo.
                        </div>

                        <a href="../vuelos/listar.php" class="btn btn-secondary">

                            <span aria-hidden="true">←</span> Volver

                        </a>

                    <?php } else { ?>

                        <form action="guardar.php" method="post" aria-labelledby="tituloReservar">

                            <input type="hidden" name="codVuelo" value="<?= $codVuelo ?>">

                            <div class="mb-3">

                                <label for="cantAsientos" class="form-label">Cantidad de asientos</label>

                                <input
                                    type="number"
                                    id="cantAsientos"
                                    name="cantAsientos"
                                    class="form-control w-50"
                                    min="1"
                                    max="<?= $asientosDisponibles ?>"
                                    required
                                    aria-required="true"
                                    aria-describedby="cantAsientosAyuda">

                                <small id="cantAsientosAyuda" class="form-text text-muted">Podés reservar entre 1 y <?= $asientosDisponibles ?> asientos.</small>

                            </div>

                            <div class="d-flex gap-2">

                                <button class="btn btn-primary" type="submit">

                                    Reservar

                                </button>

                                <a href="../vuelos/listar.php" class="btn btn-outline-secondary">

                                    Volver

                                </a>

                            </div>

                        </form>

                    <?php } ?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>