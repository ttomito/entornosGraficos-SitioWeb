<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$asientos = (int) ($_POST['cantAsientos'] ?? 0);
$codReserva = (int) ($_POST['codReserva'] ?? 0);
$idUsuario = (int) $_SESSION['id'];

$destino = 'listar.php';
$tipoAlerta = 'error';
$mensaje = '';

if ($codReserva <= 0) {

    $mensaje = 'La reserva indicada no es válida.';

} elseif ($asientos < 1) {

    $destino = "modificar.php?codReserva=$codReserva";
    $mensaje = 'La cantidad de asientos debe ser al menos 1.';

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

        $mensaje = 'Esta reserva ya no se puede modificar (su estado actual es "' . $reserva['estadoReserva'] . '").';

    } else {

        $codVuelo = (int) $reserva['codVuelo'];
        $cantAsientos = (int) $reserva['cantAsientos'];

        $sqlVueloSelect = "

        SELECT *

        FROM vuelos

        WHERE codVuelo = $codVuelo

        ";

        $resultadoVuelo = mysqli_query($link, $sqlVueloSelect);
        $vuelo = ($resultadoVuelo) ? mysqli_fetch_assoc($resultadoVuelo) : null;

        if (!$vuelo) {

            $mensaje = 'No encontramos el vuelo asociado a esta reserva.';

        } else {

            $nuevosDisponibles =
                $vuelo['asientosDisponibles']
                + $cantAsientos
                - $asientos;

            if ($nuevosDisponibles < 0) {

                $destino = "modificar.php?codReserva=$codReserva";
                $mensaje = 'No hay suficientes asientos disponibles.';

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

                $precioFinal = ($vuelo['precioVuelo'] - ($vuelo['precioVuelo'] * $descuentoMaximo / 100)) * $asientos;

                $sqlVueloUpdate = "

                UPDATE vuelos

                SET asientosDisponibles = asientosDisponibles + $cantAsientos - $asientos

                WHERE codVuelo = $codVuelo

                ";

                $okVuelo = mysqli_query($link, $sqlVueloUpdate);

                $sqlact = "

                UPDATE reservas

                SET cantAsientos = $asientos,
                precioFinal = $precioFinal

                WHERE codReserva = $codReserva

                ";

                $okReserva = mysqli_query($link, $sqlact);

                if ($okVuelo && $okReserva) {
                    $tipoAlerta = 'success';
                    $mensaje = 'Tu reserva fue modificada correctamente.';
                } else {
                    $destino = "modificar.php?codReserva=$codReserva";
                    $mensaje = 'Ocurrió un error al guardar los cambios. Intentá nuevamente.';
                }
            }
        }
    }
}

$tituloAlerta = $tipoAlerta === 'success' ? 'Listo' : 'No se pudo guardar';

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