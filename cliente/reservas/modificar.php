<?php

require_once("../../includes/verificarSession.php");
require_once("../../includes/conexion.php");

$codReserva = (int) ($_GET['codReserva'] ?? 0);
$idUsuario  = (int) $_SESSION['id'];

$flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : null;
unset($_SESSION['flash']);

$reserva = null;
$errorBD = false;

if ($codReserva > 0) {

    $sql = "SELECT r.*,
                   v.origenVuelo,
                   v.destinoVuelo,
                   v.fechaVuelo,
                   v.horaSalida,
                   v.precioVuelo,
                   v.asientosDisponibles
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
        'titulo' => 'No se puede modificar esta reserva',
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
            'texto'  => 'Esta reserva no se puede modificar porque el vuelo ya despegó.',
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

$cantAsientosActual = (int) $reserva['cantAsientos'];

$precioUnitario = $cantAsientosActual > 0
    ? ((float) $reserva['precioFinal'] / $cantAsientosActual)
    : 0.0;

$precioVueloActual = (float) $reserva['precioVuelo'];

$descuento = 0.0;

if ($precioVueloActual > 0 && $precioUnitario < $precioVueloActual) {
    $descuento = (($precioVueloActual - $precioUnitario) / $precioVueloActual) * 100;
}

$origenOut  = htmlspecialchars($reserva['origenVuelo'], ENT_QUOTES, 'UTF-8');
$destinoOut = htmlspecialchars($reserva['destinoVuelo'], ENT_QUOTES, 'UTF-8');

$fechaVueloOut = $tsSalida ? date('d/m/Y', $tsSalida) : 'No disponible';
$fechaVueloISO = $tsSalida ? date('Y-m-d', $tsSalida) : '';

$hayHora       = !empty($reserva['horaSalida']);
$horaSalidaOut = ($tsSalida && $hayHora) ? date('H:i', $tsSalida) : '';

// Los asientos propios ya están descontados del vuelo, por eso se suman
$asientosMax = (int) $reserva['asientosDisponibles'] + $cantAsientosActual;

$tokenCsrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2 id="tituloModificar">Modificar reserva</h2>

                    <?php if ($flash !== null) { ?>
                        <div
                            id="flashReserva"
                            class="alert alert-<?= $flash['tipo'] === 'success' ? 'success' : 'danger' ?>"
                            data-tipo="<?= $flash['tipo'] === 'success' ? 'success' : 'error' ?>"
                            role="alert">
                            <?= htmlspecialchars($flash['mensaje'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php } ?>

                    <dl class="row mb-4">

                        <dt class="col-sm-6">Fecha de vuelo</dt>
                        <dd class="col-sm-6">
                            <?php if ($fechaVueloISO !== '') { ?>
                                <time datetime="<?= $fechaVueloISO ?>"><?= $fechaVueloOut ?></time>
                            <?php } else { ?>
                                <?= $fechaVueloOut ?>
                            <?php } ?>
                        </dd>

                        <dt class="col-sm-6">Horario del vuelo</dt>
                        <dd class="col-sm-6">
                            <?php if ($horaSalidaOut !== '') { ?>
                                <time datetime="<?= $horaSalidaOut ?>"><?= $horaSalidaOut ?> hs</time>
                            <?php } else { ?>
                                No disponible
                            <?php } ?>
                        </dd>

                        <dt class="col-sm-6">Origen</dt>
                        <dd class="col-sm-6"><?= $origenOut ?></dd>

                        <dt class="col-sm-6">Destino</dt>
                        <dd class="col-sm-6"><?= $destinoOut ?></dd>

                        <dt class="col-sm-6">Tarifa actual del vuelo</dt>
                        <dd class="col-sm-6">
                            $<?= number_format($precioVueloActual, 0, ',', '.') ?>
                        </dd>

                        <?php if ($descuento > 0) { ?>
                            <dt class="col-sm-6">Descuento aplicado</dt>
                            <dd class="col-sm-6"><?= number_format($descuento, 1, ',', '.') ?>%</dd>
                        <?php } ?>

                        <dt class="col-sm-6">Precio por asiento</dt>
                        <dd class="col-sm-6">
                            $<?= number_format($precioUnitario, 0, ',', '.') ?>
                        </dd>

                        <dt class="col-sm-6">Asientos disponibles</dt>
                        <dd class="col-sm-6"><?= (int) $reserva['asientosDisponibles'] ?></dd>

                        <dt class="col-sm-6">Total</dt>
                        <dd class="col-sm-6">
                            <strong id="totalReserva"
                                    data-unitario="<?= $precioUnitario ?>">
                                $<?= number_format((float) $reserva['precioFinal'], 0, ',', '.') ?>
                            </strong>
                        </dd>

                    </dl>

                    <p class="text-muted small">
                        El precio por asiento de tu reserva se mantiene aunque cambies
                        la cantidad. No se recalcula con la tarifa vigente.
                    </p>

                    <form
                        action="guardarModificaciones.php"
                        method="post"
                        aria-labelledby="tituloModificar">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= $tokenCsrf ?>">

                        <input
                            type="hidden"
                            name="codReserva"
                            value="<?= $codReserva ?>">

                        <div class="mb-3">

                            <label for="cantAsientos" class="form-label">
                                Asientos reservados
                            </label>

                            <input
                                type="number"
                                id="cantAsientos"
                                name="cantAsientos"
                                class="form-control w-50"
                                min="1"
                                max="<?= $asientosMax ?>"
                                step="1"
                                value="<?= $cantAsientosActual ?>"
                                required
                                aria-describedby="cantAsientosAyuda">

                            <small id="cantAsientosAyuda" class="form-text text-muted">
                                Podés reservar entre 1 y <?= $asientosMax ?> asientos.
                            </small>

                        </div>

                        <div class="d-flex flex-wrap gap-2">

                            <button class="btn btn-primary" type="submit">
                                Guardar cambios
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

<script
    src="https://cdn.jsdelivr.net/npm/sweetalert2@11"
    crossorigin="anonymous"></script>

<script>
    (function () {

        // Mensaje que pudo dejar guardarModificaciones.php
        var flash = document.getElementById('flashReserva');

        if (!flash || typeof Swal === 'undefined') {
            return;
        }

        var tipo    = flash.getAttribute('data-tipo') === 'success' ? 'success' : 'error';
        var mensaje = flash.textContent.trim();

        flash.style.display = 'none';

        Swal.fire({
            icon: tipo,
            title: tipo === 'success' ? 'Listo' : 'No se pudo completar',
            text: mensaje,
            confirmButtonText: 'Aceptar'
        });

    })();
</script>

<script>
    (function () {

        // Recalcula el total mientras el usuario cambia la cantidad

        var campo = document.getElementById('cantAsientos');
        var total = document.getElementById('totalReserva');

        if (!campo || !total) {
            return;
        }

        var unitario = parseFloat(total.getAttribute('data-unitario'));

        if (isNaN(unitario)) {
            return;
        }

        function actualizarTotal() {

            var cantidad = parseInt(campo.value, 10);

            if (isNaN(cantidad) || cantidad < 1) {
                total.textContent = '—';
                return;
            }

            var monto = Math.round(unitario * cantidad);

            total.textContent = '$' + monto.toLocaleString('es-AR');
        }

        campo.addEventListener('input', actualizarTotal);

    })();
</script>

<?php include("../../includes/footer.php"); ?>