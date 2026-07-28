<?php

require_once("../../includes/verificarSession.php");
require_once("../../includes/conexion.php");

$codReserva = (int) ($_GET['codReserva'] ?? 0);
$idUsuario  = (int) $_SESSION['id'];

$reserva = null;
$errorBD = false;

if ($codReserva > 0) {

    $sql = "SELECT r.*,
                   v.origenVuelo,
                   v.destinoVuelo,
                   v.fechaVuelo,
                   v.horaSalida
            FROM reservas r
            LEFT JOIN vuelos v ON v.codVuelo = r.codVuelo
            WHERE r.codReserva = ? AND r.codUsuario = ?";

    $stmtReserva = mysqli_prepare($link, $sql);

    if (!$stmtReserva) {

        $errorBD = true;
    } else {

        mysqli_stmt_bind_param($stmtReserva, "ii", $codReserva, $idUsuario);
        mysqli_stmt_execute($stmtReserva);

        $resultadoReserva = mysqli_stmt_get_result($stmtReserva);

        if (!$resultadoReserva) {
            $errorBD = true;
        } else {
            $reserva = mysqli_fetch_assoc($resultadoReserva);
        }

        mysqli_stmt_close($stmtReserva);
    }
}

$pantallaError = null;

$volverAReserva = 'verReserva.php?codReserva=' . $codReserva;

if ($errorBD) {

    $pantallaError = [
        'titulo' => 'Error al consultar la reserva',
        'texto'  => 'Hubo un problema técnico. Intentá de nuevo en unos minutos.',
        'href'   => 'listar.php',
        'link'   => 'Volver',
    ];
} elseif (!$reserva) {

    $pantallaError = [
        'titulo' => 'Reserva no encontrada',
        'texto'  => 'La reserva que buscás no existe o no te pertenece.',
        'href'   => 'listar.php',
        'link'   => 'Volver',
    ];
} elseif ($reserva['estadoReserva'] !== 'PENDIENTE') {

    $pantallaError = [
        'titulo' => 'No se puede pagar esta reserva',
        'texto'  => 'Su estado actual es "' . $reserva['estadoReserva'] . '".',
        'href'   => $volverAReserva,
        'link'   => 'Volver a la reserva',
    ];
} elseif (is_null($reserva['origenVuelo'])) {

    $pantallaError = [
        'titulo' => 'No pudimos cargar el vuelo',
        'texto'  => 'No encontramos el vuelo asociado a esta reserva.',
        'href'   => $volverAReserva,
        'link'   => 'Volver a la reserva',
    ];
}

$tsSalida = false;

if ($pantallaError === null && !empty($reserva['fechaVuelo'])) {

    $fechaBase = substr((string) $reserva['fechaVuelo'], 0, 10);

    $horaBase = !empty($reserva['horaSalida'])
        ? (string) $reserva['horaSalida']
        : '00:00:00';

    $tsSalida = strtotime($fechaBase . ' ' . $horaBase);

    if ($tsSalida !== false && $tsSalida < time()) {

        $pantallaError = [
            'titulo' => 'El vuelo ya salió',
            'texto'  => 'Esta reserva no se puede pagar porque el vuelo ya despegó.',
            'href'   => $volverAReserva,
            'link'   => 'Volver a la reserva',
        ];
    }
}

include("../../includes/header.php");

if ($pantallaError !== null) {
?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-custom">
                    <div class="card-body p-5 text-center" role="alert">

                        <h2 class="text-danger">
                            <?= htmlspecialchars($pantallaError['titulo'], ENT_QUOTES, 'UTF-8') ?>
                        </h2>

                        <p>
                            <?= htmlspecialchars($pantallaError['texto'], ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <a
                            href="<?= htmlspecialchars($pantallaError['href'], ENT_QUOTES, 'UTF-8') ?>"
                            class="btn btn-secondary">
                            <span aria-hidden="true">&larr;</span>
                            <?= htmlspecialchars($pantallaError['link'], ENT_QUOTES, 'UTF-8') ?>
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

$origenOut  = htmlspecialchars($reserva['origenVuelo'], ENT_QUOTES, 'UTF-8');
$destinoOut = htmlspecialchars($reserva['destinoVuelo'], ENT_QUOTES, 'UTF-8');

$fechaVueloOut = $tsSalida ? date('d/m/Y', $tsSalida) : 'No disponible';
$fechaVueloISO = $tsSalida ? date('Y-m-d', $tsSalida) : '';

$hayHora       = !empty($reserva['horaSalida']);
$horaSalidaOut = ($tsSalida && $hayHora) ? date('H:i', $tsSalida) : '';

$tokenCsrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2 id="tituloPagar">Pagar reserva</h2>

                    <dl class="row mb-4">

                        <dt class="col-sm-5">Origen</dt>
                        <dd class="col-sm-7"><?= $origenOut ?></dd>

                        <dt class="col-sm-5">Destino</dt>
                        <dd class="col-sm-7"><?= $destinoOut ?></dd>

                        <dt class="col-sm-5">Fecha del vuelo</dt>
                        <dd class="col-sm-7">
                            <?php if ($fechaVueloISO !== '') { ?>
                                <time datetime="<?= $fechaVueloISO ?>"><?= $fechaVueloOut ?></time>
                            <?php } else { ?>
                                <?= $fechaVueloOut ?>
                            <?php } ?>
                        </dd>

                        <dt class="col-sm-5">Horario del vuelo</dt>
                        <dd class="col-sm-7">
                            <?php if ($horaSalidaOut !== '') { ?>
                                <time datetime="<?= $horaSalidaOut ?>"><?= $horaSalidaOut ?> hs</time>
                            <?php } else { ?>
                                No disponible
                            <?php } ?>
                        </dd>

                        <dt class="col-sm-5">Cantidad de asientos</dt>
                        <dd class="col-sm-7"><?= (int) $reserva['cantAsientos'] ?></dd>

                        <dt class="col-sm-5">Precio total</dt>
                        <dd class="col-sm-7">
                            $<?= number_format((float) $reserva['precioFinal'], 0, ',', '.') ?>
                        </dd>

                    </dl>

                    <form
                        action="confirmarPago.php"
                        method="post"
                        aria-labelledby="tituloPagar">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= $tokenCsrf ?>">

                        <input
                            type="hidden"
                            name="codReserva"
                            value="<?= $codReserva ?>">

                        <div class="d-flex flex-wrap gap-2">

                            <button type="submit" class="btn btn-success">
                                Confirmar pago
                            </button>

                            <a
                                href="<?= htmlspecialchars($volverAReserva, ENT_QUOTES, 'UTF-8') ?>"
                                class="btn btn-outline-secondary">
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