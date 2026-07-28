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

include("../../includes/header.php");

if ($errorBD) {
?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-custom">
                    <div class="card-body p-5 text-center" role="alert">
                        <h2 class="text-danger">Error al consultar la reserva</h2>
                        <p>Hubo un problema técnico. Intentá de nuevo en unos minutos.</p>
                        <a href="listar.php" class="btn btn-secondary">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
    include("../../includes/footer.php");
    exit();
}


if (!$reserva) {
?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-custom">
                    <div class="card-body p-5 text-center" role="alert">
                        <h2 class="text-danger">Reserva no encontrada</h2>
                        <p>La reserva que buscás no existe o no te pertenece.</p>
                        <a href="listar.php" class="btn btn-secondary">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
    include("../../includes/footer.php");
    exit();
}

$hayVuelo = !is_null($reserva['origenVuelo']);

$origenOut = $hayVuelo ? htmlspecialchars($reserva['origenVuelo'], ENT_QUOTES, 'UTF-8') : 'No disponible';

$destinoOut = $hayVuelo ? htmlspecialchars($reserva['destinoVuelo'], ENT_QUOTES, 'UTF-8') : 'No disponible';

$tsSalida = false;

if ($hayVuelo && !empty($reserva['fechaVuelo'])) {

    $fechaBase = substr((string) $reserva['fechaVuelo'], 0, 10);
    $horaBase = !empty($reserva['horaSalida']) ? (string) $reserva['horaSalida'] : '00:00:00';
    $tsSalida = strtotime($fechaBase . ' ' . $horaBase);
}

$yaSalio = ($tsSalida !== false && $tsSalida < time());

$fechaVueloOut = $tsSalida ? date('d/m/Y', $tsSalida) : '';
$fechaVueloISO = $tsSalida ? date('Y-m-d', $tsSalida) : '';

$hayHora       = $hayVuelo && !empty($reserva['horaSalida']);
$horaSalidaOut = ($tsSalida && $hayHora) ? date('H:i', $tsSalida) : '';

$estado = strtoupper(trim((string) $reserva['estadoReserva']));

$badgesEstado = [
    'CONFIRMADA' => ['clase' => 'bg-success',           'texto' => 'Confirmada'],
    'PENDIENTE'  => ['clase' => 'bg-warning text-dark', 'texto' => 'Pendiente'],
    'CANCELADA'  => ['clase' => 'bg-danger',            'texto' => 'Cancelada'],
];

$badge = isset($badgesEstado[$estado])
    ? $badgesEstado[$estado]
    : ['clase' => 'bg-secondary', 'texto' => ucfirst(strtolower($estado))];

// Pagar y modificar si el vuelo todavía no despegó
$puedeOperar  = ($estado === 'PENDIENTE' && !$yaSalio);
$puedeCancelar = ($estado === 'PENDIENTE');

$tokenCsrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2>Datos de la reserva</h2>

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

                        <dt class="col-sm-5">Fecha de vuelo</dt>
                        <dd class="col-sm-7">
                            <?php if ($fechaVueloOut !== '') { ?>
                                <time datetime="<?= $fechaVueloISO ?>"><?= $fechaVueloOut ?></time>
                            <?php } else { ?>
                                No disponible
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

                        <dt class="col-sm-5">Origen</dt>
                        <dd class="col-sm-7"><?= $origenOut ?></dd>

                        <dt class="col-sm-5">Destino</dt>
                        <dd class="col-sm-7"><?= $destinoOut ?></dd>

                        <dt class="col-sm-5">Asientos reservados</dt>
                        <dd class="col-sm-7"><?= (int) $reserva['cantAsientos'] ?></dd>

                        <dt class="col-sm-5">Total de la reserva</dt>
                        <dd class="col-sm-7">
                            $<?= number_format((float) $reserva['precioFinal'], 0, ',', '.') ?>
                        </dd>

                        <dt class="col-sm-5">Estado</dt>
                        <dd class="col-sm-7">
                            <span class="badge <?= $badge['clase'] ?>">
                                <?= htmlspecialchars($badge['texto'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </dd>

                    </dl>

                    <?php if ($yaSalio && $estado === 'PENDIENTE') { ?>
                        <div class="alert alert-secondary" role="status">
                            Este vuelo ya salió.
                        </div>
                    <?php } ?>

                    <div class="d-flex flex-wrap gap-2">

                        <?php if ($puedeOperar) { ?>

                            <a
                                href="pagar.php?codReserva=<?= $codReserva ?>"
                                class="btn btn-primary">
                                Pagar reserva
                            </a>

                            <a
                                href="modificar.php?codReserva=<?= $codReserva ?>"
                                class="btn btn-secondary">
                                Modificar reserva
                            </a>

                        <?php } ?>

                        <?php if ($puedeCancelar) { ?>

                            <form
                                method="POST"
                                action="cancelarReserva.php"
                                id="formCancelarReserva"
                                class="d-inline">

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= $tokenCsrf ?>">

                                <input
                                    type="hidden"
                                    name="codReserva"
                                    value="<?= $codReserva ?>">

                                <button type="submit" class="btn btn-danger">
                                    Cancelar reserva
                                </button>

                            </form>

                        <?php } ?>

                        <a href="listar.php" class="btn btn-outline-secondary">
                            Volver
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script
    src="https://cdn.jsdelivr.net/npm/sweetalert2@11"
    crossorigin="anonymous"></script>

<script>
    (function() {

        var flash = document.getElementById('flashReserva');

        if (!flash || typeof Swal === 'undefined') {
            return;
        }

        var tipo = flash.getAttribute('data-tipo') === 'success' ? 'success' : 'error';
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
    (function() {

        var formCancelar = document.getElementById('formCancelarReserva');

        if (!formCancelar) {
            return;
        }

        formCancelar.addEventListener('submit', function(evento) {

            if (typeof Swal === 'undefined') {

                if (!confirm('¿Está seguro que desea cancelar la reserva?')) {
                    evento.preventDefault();
                }

                return;
            }

            evento.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: '¿Cancelar reserva?',
                text: 'Esta acción no se puede deshacer.',
                showCancelButton: true,
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No',
                confirmButtonColor: '#dc3545'
            }).then(function(resultado) {

                if (resultado.isConfirmed) {
                    formCancelar.submit();
                }
            });
        });

    })();
</script>

<?php include("../../includes/footer.php"); ?>