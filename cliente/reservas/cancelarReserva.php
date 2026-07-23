<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$codReserva = (int) ($_GET['codReserva'] ?? 0);
$idUsuario = (int) $_SESSION['id'];

$destino = 'listar.php';
$tipoAlerta = 'error';
$mensaje = '';

if ($codReserva <= 0) {

    $mensaje = 'La reserva indicada no es válida.';

} else {

    $sql = "SELECT * FROM reservas WHERE codReserva = $codReserva AND codUsuario = $idUsuario";

    $resultado = mysqli_query($link, $sql);

    if (!$resultado || mysqli_num_rows($resultado) == 0) {

        $mensaje = 'No encontramos esa reserva.';

    } else {

        $reserva = mysqli_fetch_assoc($resultado);

        if ($reserva['estadoReserva'] !== 'PENDIENTE') {

            $mensaje = 'Esta reserva ya no se puede cancelar (su estado actual es "' . $reserva['estadoReserva'] . '").';

        } else {

            $codVuelo = (int) $reserva['codVuelo'];

            $sqlVuelo = "

            SELECT *

            FROM vuelos

            WHERE codVuelo = $codVuelo

            ";

            $resultadoVuelo = mysqli_query($link, $sqlVuelo);
            $vuelo = $resultadoVuelo ? mysqli_fetch_assoc($resultadoVuelo) : null;

            if (!$vuelo) {

                $mensaje = 'No pudimos verificar el vuelo asociado a esta reserva.';

            } else {

                $fechaVuelo = strtotime($vuelo['fechaVuelo']);
                $diferenciaHoras = ($fechaVuelo - time()) / 3600;

                if ($diferenciaHoras < 72) {

                    $mensaje = 'No podés cancelar una reserva con menos de 72 horas de anticipación al vuelo.';

                } else {

                    $cantAsientos = (int) $reserva['cantAsientos'];

                    $okReserva = mysqli_query($link, "

                        UPDATE reservas

                        SET estadoReserva = 'CANCELADA'

                        WHERE codReserva = $codReserva

                    ");

                    $okVuelo = mysqli_query($link, "

                        UPDATE vuelos

                        SET asientosDisponibles = asientosDisponibles + $cantAsientos

                        WHERE codVuelo = $codVuelo

                    ");

                    if ($okReserva && $okVuelo) {
                        $tipoAlerta = 'success';
                        $mensaje = 'Tu reserva fue cancelada correctamente.';
                    } else {
                        $mensaje = 'Ocurrió un error al cancelar la reserva. Intentá nuevamente.';
                    }
                }
            }
        }
    }
}

$tituloAlerta = $tipoAlerta === 'success' ? 'Listo' : 'No se pudo cancelar';

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