<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");
include("../../includes/conexion.php");

$codReserva = (int) ($_GET['codReserva'] ?? 0);
$idUsuario = (int) $_SESSION['id'];

$reserva = null;

if ($codReserva > 0) {

    $sql = "SELECT * FROM reservas WHERE codReserva = $codReserva AND codUsuario = $idUsuario";
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

                        <span aria-hidden="true"></span>Volver

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

    $sqlVuelo = "

    SELECT *

    FROM vuelos

    WHERE codVuelo = $codVuelo

    ";

    $resultadoVuelo = mysqli_query($link, $sqlVuelo);

    if ($resultadoVuelo) {
        $vuelo = mysqli_fetch_assoc($resultadoVuelo);
    }
}

$origenOut = $vuelo ? htmlspecialchars($vuelo['origenVuelo'], ENT_QUOTES, 'UTF-8') : 'No disponible';
$destinoOut = $vuelo ? htmlspecialchars($vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') : 'No disponible';
$fechaVueloOut = $vuelo ? htmlspecialchars($vuelo['fechaVuelo'], ENT_QUOTES, 'UTF-8') : '';
$horaSalidaOut = $vuelo ? htmlspecialchars($vuelo['horaSalida'], ENT_QUOTES, 'UTF-8') : '';

$estadoOut = htmlspecialchars($reserva['estadoReserva'], ENT_QUOTES, 'UTF-8');
$codReservaInt = $codReserva;

$error = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : null;

?>


<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card card-custom">

<div class="card-body p-5">

<h2>Datos reserva</h2>

<?php if ($error !== null) { ?>

<div class="alert alert-danger" role="alert">

    <?= $error ?>

</div>

<?php } ?>



<div class="mb-3">
    Fecha de vuelo:
    <?php if ($fechaVueloOut !== '') { ?>
        <time datetime="<?= $fechaVueloOut ?>"><?= $fechaVueloOut ?></time>
    <?php } else { ?>
        No disponible
    <?php } ?>
</div>
<div class="mb-3">
    Horario del vuelo:
    <?php if ($horaSalidaOut !== '') { ?>
        <time datetime="<?= $horaSalidaOut ?>"><?= $horaSalidaOut ?></time>
    <?php } else { ?>
        No disponible
    <?php } ?>
</div>
<div class="mb-3">
    Origen: <?= $origenOut ?>
</div>
<div class="mb-3">
    Destino: <?= $destinoOut ?>
</div>
<div class="mb-3">
    Precio: <?= $vuelo ? '$' . number_format($vuelo['precioVuelo'], 0, ',', '.') : 'No disponible' ?>
</div>
<div class="mb-3">
    Asientos disponibles: <?= $vuelo ? (int) $vuelo['asientosDisponibles'] : 'No disponible' ?>
</div>
<div class="mb-3">
    Asientos reservados: <?= (int) $reserva['cantAsientos'] ?>
</div>
<div class="mb-3">
    Estado: <?= $estadoOut ?>
</div>
<div>
<?php if ($reserva['estadoReserva']=='PENDIENTE'){ ?>
<a href="pagar.php?codReserva=<?= $codReservaInt ?>" class="btn btn-primary">

Pagar reserva

</a>

<a href="modificar.php?codReserva=<?= $codReservaInt ?>" class="btn btn-secondary">

Modificar reserva

</a>

<a href="cancelarReserva.php?codReserva=<?= $codReservaInt ?>" class="btn btn-danger" id="btnCancelarReserva">

Cancelar Reserva

</a>

<?php } elseif ($reserva['estadoReserva']=='CANCELADA') { ?>

<a href="listar.php" class="btn btn-danger">

<span aria-hidden="true"></span>Volver atrás

</a>

<?php } elseif ($reserva['estadoReserva']=='CONFIRMADA') { ?>

<a href="listar.php" class="btn btn-danger">

<span aria-hidden="true"></span>Volver atrás

</a>

<?php } ?>
</div>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    (function () {
        var btnCancelar = document.getElementById('btnCancelarReserva');

        if (!btnCancelar) {
            return;
        }

        btnCancelar.addEventListener('click', function (evento) {

            // Si por algún motivo SweetAlert no cargó (CDN caído, bloqueado, etc.),
            // usamos el confirm() nativo como respaldo en vez de dejar el botón roto.
            if (typeof Swal === 'undefined') {
                return confirm('¿Está seguro que desea cancelar la reserva?');
            }

            evento.preventDefault();

            var destino = btnCancelar.getAttribute('href');

            Swal.fire({
                icon: 'warning',
                title: '¿Cancelar reserva?',
                text: 'Esta acción no se puede deshacer.',
                showCancelButton: true,
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No',
                confirmButtonColor: '#dc3545'
            }).then(function (resultado) {
                if (resultado.isConfirmed) {
                    window.location.href = destino;
                }
            });
        });
    })();
</script>

<?php
include("../../includes/footer.php");
?>