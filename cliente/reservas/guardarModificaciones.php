<?php

require_once("../../includes/verificarSession.php");
require_once("../../includes/conexion.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$codReserva = (int) ($_POST['codReserva'] ?? 0);
$asientos   = (int) ($_POST['cantAsientos'] ?? 0);
$idUsuario  = (int) $_SESSION['id'];

//Guarda el mensaje en la sesión

function redirigirCon($tipo, $mensaje, $destino)
{
    $_SESSION['flash'] = [
        'tipo'    => $tipo,
        'mensaje' => $mensaje,
    ];

    header('Location: ' . $destino);
    exit();
}

$volverAModificar = $codReserva > 0 ? 'modificar.php?codReserva=' . $codReserva : 'listar.php';

$volverAReserva = $codReserva > 0 ? 'verReserva.php?codReserva=' . $codReserva : 'listar.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirigirCon('error', 'Los cambios deben enviarse desde el formulario.', $volverAModificar);
}

$tokenRecibido = $_POST['csrf_token'] ?? '';

$tokenValido = !empty($_SESSION['csrf_token'])
    && is_string($tokenRecibido)
    && hash_equals($_SESSION['csrf_token'], $tokenRecibido);

if (!$tokenValido) {
    redirigirCon('error', 'La sesión expiró o el formulario no es válido. Volvé a intentarlo.', $volverAModificar);
}

if ($codReserva <= 0) {
    redirigirCon('error', 'La reserva indicada no es válida.', 'listar.php');
}

if ($asientos < 1) {
    redirigirCon('error', 'La cantidad de asientos debe ser al menos 1.', $volverAModificar);
}

try {

    mysqli_begin_transaction($link);

    //Reserva

    $sql = "SELECT codVuelo, cantAsientos, precioFinal, estadoReserva
            FROM reservas
            WHERE codReserva = ? AND codUsuario = ?
            FOR UPDATE";

    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $codReserva, $idUsuario);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $reserva   = mysqli_fetch_assoc($resultado);

    mysqli_stmt_close($stmt);

    if (!$reserva) {
        mysqli_rollback($link);
        redirigirCon('error', 'No encontramos esa reserva.', 'listar.php');
    }

    if ($reserva['estadoReserva'] !== 'PENDIENTE') {
        mysqli_rollback($link);
        redirigirCon('error', 'Esta reserva ya no se puede modificar.', $volverAReserva);
    }

    $codVuelo           = (int) $reserva['codVuelo'];
    $cantAsientosActual = (int) $reserva['cantAsientos'];
    $precioFinalActual  = (float) $reserva['precioFinal'];

    // Sin cambios
    if ($asientos === $cantAsientosActual) {
        mysqli_rollback($link);
        redirigirCon('error', 'No modificaste la cantidad de asientos.', $volverAModificar);
    }

    //Vuelo

    $sqlVuelo = "SELECT fechaVuelo, horaSalida
                 FROM vuelos
                 WHERE codVuelo = ?
                 FOR UPDATE";

    $stmtVuelo = mysqli_prepare($link, $sqlVuelo);
    mysqli_stmt_bind_param($stmtVuelo, "i", $codVuelo);
    mysqli_stmt_execute($stmtVuelo);

    $resVuelo = mysqli_stmt_get_result($stmtVuelo);
    $vuelo    = mysqli_fetch_assoc($resVuelo);

    mysqli_stmt_close($stmtVuelo);

    if (!$vuelo) {
        mysqli_rollback($link);
        redirigirCon('error', 'No encontramos el vuelo asociado a esta reserva.', $volverAReserva);
    }

    if (!empty($vuelo['fechaVuelo'])) {

        $fechaBase = substr((string) $vuelo['fechaVuelo'], 0, 10);

        $horaBase = !empty($vuelo['horaSalida'])
            ? (string) $vuelo['horaSalida']
            : '00:00:00';

        $tsSalida = strtotime($fechaBase . ' ' . $horaBase);

        if ($tsSalida !== false && $tsSalida < time()) {
            mysqli_rollback($link);
            redirigirCon('error', 'El vuelo ya salió, la reserva no se puede modificar.', $volverAReserva);
        }
    }

    $precioUnitario = $cantAsientosActual > 0 ? ($precioFinalActual / $cantAsientosActual) : 0.0;
    $nuevoPrecioFinal = round($precioUnitario * $asientos, 2);

    $delta = $asientos - $cantAsientosActual;

    $sqlAsientos = "UPDATE vuelos
                    SET asientosDisponibles = asientosDisponibles - ?
                    WHERE codVuelo = ?
                      AND asientosDisponibles >= ?";

    $stmtAsientos = mysqli_prepare($link, $sqlAsientos);
    mysqli_stmt_bind_param($stmtAsientos, "iii", $delta, $codVuelo, $delta);
    mysqli_stmt_execute($stmtAsientos);

    $filasAsientos = mysqli_stmt_affected_rows($stmtAsientos);

    mysqli_stmt_close($stmtAsientos);

    if ($filasAsientos !== 1) {
        mysqli_rollback($link);
        redirigirCon('error', 'No hay suficientes asientos disponibles.', $volverAModificar);
    }

    // Actualizamos la reserva


    $sqlUpdate = "UPDATE reservas
                  SET cantAsientos = ?,
                      precioFinal  = ?
                  WHERE codReserva = ?
                    AND codUsuario = ?
                    AND estadoReserva = 'PENDIENTE'";

    $stmtUpdate = mysqli_prepare($link, $sqlUpdate);
    mysqli_stmt_bind_param(
        $stmtUpdate,
        "idii",
        $asientos,
        $nuevoPrecioFinal,
        $codReserva,
        $idUsuario
    );
    mysqli_stmt_execute($stmtUpdate);

    $filasReserva = mysqli_stmt_affected_rows($stmtUpdate);

    mysqli_stmt_close($stmtUpdate);

    if ($filasReserva !== 1) {
        mysqli_rollback($link);
        redirigirCon('error', 'Esta reserva ya no se puede modificar.', $volverAReserva);
    }

    mysqli_commit($link);

    redirigirCon('success', 'Tu reserva fue modificada correctamente.', $volverAReserva);

} catch (mysqli_sql_exception $e) {

    mysqli_rollback($link);

    error_log('Error al modificar la reserva ' . $codReserva . ': ' . $e->getMessage());

    redirigirCon('error', 'Ocurrió un error al guardar los cambios. Intentá nuevamente.', $volverAModificar);
}