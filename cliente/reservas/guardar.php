<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$idUsuario = (int) $_SESSION['id'];
$codVuelo = (int) ($_POST['codVuelo'] ?? 0);
$cantAsientos = (int) ($_POST['cantAsientos'] ?? 0);
$fecha = date("Y-m-d");

$destino = '../vuelos/listar.php';
$tipoAlerta = 'error';
$mensaje = '';

if ($codVuelo <= 0) {

    $mensaje = 'El vuelo indicado no es válido.';

} elseif ($cantAsientos <= 0) {

    $destino = "reservar.php?codVuelo=$codVuelo";
    $mensaje = 'La cantidad de asientos debe ser mayor a cero.';

} else {

    $sqlVuelo = "SELECT * FROM vuelos WHERE codVuelo = $codVuelo";
    $resultadoVuelo = mysqli_query($link, $sqlVuelo);
    $vuelo = $resultadoVuelo ? mysqli_fetch_assoc($resultadoVuelo) : null;

    if (!$vuelo) {

        $mensaje = 'No encontramos ese vuelo.';

    } else {


        $codAerolinea = (int) $vuelo['codAerolinea'];

        $sqlProm = "

        SELECT *

        FROM promociones

        WHERE codAerolinea = $codAerolinea

        AND estadoPromocion = 'APROBADA'

        ";

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

        $precioPorAsiento = $vuelo['precioVuelo'] - ($vuelo['precioVuelo'] * $descuentoMaximo / 100);
        $precioFinal = $precioPorAsiento * $cantAsientos;

        $sql = "

        SELECT *

        FROM reservas

        WHERE codUsuario = $idUsuario

        AND codVuelo = $codVuelo

        AND estadoReserva != 'CANCELADA'

        ";

        $resultado = mysqli_query($link, $sql);

        if ($resultado && mysqli_num_rows($resultado) > 0) {

            $destino = "reservar.php?codVuelo=$codVuelo";
            $mensaje = 'Ya tenés una reserva activa para este vuelo.';

        } else {

            mysqli_begin_transaction($link);

            $sqlUpdateAsientos = "

            UPDATE vuelos

            SET asientosDisponibles = asientosDisponibles - $cantAsientos

            WHERE codVuelo = $codVuelo

            AND asientosDisponibles >= $cantAsientos

            ";

            $okAsientos = mysqli_query($link, $sqlUpdateAsientos);

            if (!$okAsientos || mysqli_affected_rows($link) === 0) {

                mysqli_rollback($link);
                $destino = "reservar.php?codVuelo=$codVuelo";
                $mensaje = 'No hay suficientes asientos disponibles.';

            } else {

                $sqlInsert = "

                INSERT INTO reservas (codUsuario, codVuelo, fechaReserva, estadoReserva, precioFinal, cantAsientos)

                VALUES ($idUsuario, $codVuelo, '$fecha', 'PENDIENTE', $precioFinal, $cantAsientos)

                ";

                $okInsert = mysqli_query($link, $sqlInsert);

                if (!$okInsert) {

                    mysqli_rollback($link);
                    $destino = "reservar.php?codVuelo=$codVuelo";
                    $mensaje = 'Ocurrió un error al crear la reserva. Intentá nuevamente.';

                } else {

                    mysqli_commit($link);
                    $tipoAlerta = 'success';
                    $mensaje = 'Tu reserva fue creada correctamente. Quedó pendiente de pago.';
                }
            }
        }
    }
}

$tituloAlerta = $tipoAlerta === 'success' ? 'Listo' : 'No se pudo reservar';

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