<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$codReserva = (int) ($_POST['codReserva'] ?? 0);
$idUsuario = (int) $_SESSION['id'];

$destino = 'listar.php';
$tipoAlerta = 'error';
$mensaje = '';

if ($codReserva <= 0) {

    $mensaje = 'La reserva indicada no es válida.';

} else {

    $sql = "

    SELECT *

    FROM reservas

    WHERE codReserva = $codReserva

    AND codUsuario = $idUsuario

    ";

    $resultado = mysqli_query($link, $sql);
    $reserva = ($resultado) ? mysqli_fetch_assoc($resultado) : null;

    if (!$reserva) {

        $mensaje = 'No encontramos esa reserva.';

    } elseif ($reserva['estadoReserva'] !== 'PENDIENTE') {

        $mensaje = 'Esta reserva ya no se puede pagar (su estado actual es "' . $reserva['estadoReserva'] . '").';

    } else {

        $sqlUpdate = "

        UPDATE reservas

        SET estadoReserva = 'CONFIRMADA'

        WHERE codReserva = $codReserva

        AND codUsuario = $idUsuario

        ";

        $ok = mysqli_query($link, $sqlUpdate);

        if ($ok && mysqli_affected_rows($link) > 0) {
            $tipoAlerta = 'success';
            $mensaje = 'Tu pago fue confirmado correctamente.';
        } else {
            $mensaje = 'Ocurrió un error al confirmar el pago. Intentá nuevamente.';
        }
    }
}

$tituloAlerta = $tipoAlerta === 'success' ? 'Listo' : 'No se pudo confirmar el pago';

include("../../includes/header.php");

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card card-custom">

                <div class="card-body p-5 text-center">

                    <div
                        class="alert <?= $tipoAlerta === 'success' ? 'alert-success' : 'alert-danger' ?>"
                        role="alert">

                        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>

                    </div>

                    <a href="<?= htmlspecialchars($destino, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary" id="btnContinuar">

                        Continuar

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: <?= json_encode($tipoAlerta) ?>,
            title: <?= json_encode($tituloAlerta, JSON_UNESCAPED_UNICODE) ?>,
            text: <?= json_encode($mensaje, JSON_UNESCAPED_UNICODE) ?>,
            confirmButtonText: 'Aceptar'
        }).then(function () {
            window.location.href = <?= json_encode($destino) ?>;
        });
    }
</script>

<?php include("../../includes/footer.php"); ?>