<?php

require_once("../../includes/verificarSession.php");
require_once("../../includes/conexion.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$codReserva = (int) ($_POST['codReserva'] ?? 0);
$idUsuario  = (int) $_SESSION['id'];

function redirigirCon($tipo, $mensaje, $codReserva)
{
    $_SESSION['flash'] = [
        'tipo'    => $tipo,
        'mensaje' => $mensaje,
    ];

    $destino = $codReserva > 0
        ? 'verReserva.php?codReserva=' . $codReserva
        : 'listar.php';

    header('Location: ' . $destino);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirigirCon('error', 'La cancelación debe realizarse desde el botón de la reserva.', $codReserva);
}

$tokenRecibido = $_POST['csrf_token'] ?? '';

$tokenValido = !empty($_SESSION['csrf_token'])
    && is_string($tokenRecibido)
    && hash_equals($_SESSION['csrf_token'], $tokenRecibido);

if (!$tokenValido) {
    redirigirCon('error', 'La sesión expiró o el formulario no es válido. Volvé a intentarlo.', $codReserva);
}

if ($codReserva <= 0) {
    redirigirCon('error', 'La reserva indicada no es válida.', 0);
}

try {

    mysqli_begin_transaction($link);

    // FOR UPDATE bloquea la fila hasta el commit
    $sql = "SELECT codVuelo, cantAsientos
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
        redirigirCon('error', 'No encontramos esa reserva.', 0);
    }

    $codVuelo     = (int) $reserva['codVuelo'];
    $cantAsientos = (int) $reserva['cantAsientos'];

    $sqlUpdate = "UPDATE reservas
                  SET estadoReserva = 'CANCELADA'
                  WHERE codReserva = ?
                    AND codUsuario = ?
                    AND estadoReserva = 'PENDIENTE'";

    $stmtUpdate = mysqli_prepare($link, $sqlUpdate);
    mysqli_stmt_bind_param($stmtUpdate, "ii", $codReserva, $idUsuario);
    mysqli_stmt_execute($stmtUpdate);

    $filasAfectadas = mysqli_stmt_affected_rows($stmtUpdate);

    mysqli_stmt_close($stmtUpdate);

    if ($filasAfectadas !== 1) {
        mysqli_rollback($link);
        redirigirCon('error', 'Esta reserva ya no se puede cancelar.', $codReserva);
    }

    if ($codVuelo > 0 && $cantAsientos > 0) {

        $sqlAsientos = "UPDATE vuelos SET asientosDisponibles = asientosDisponibles + ? WHERE codVuelo = ?";

        $stmtAsientos = mysqli_prepare($link, $sqlAsientos);
        mysqli_stmt_bind_param($stmtAsientos, "ii", $cantAsientos, $codVuelo);
        mysqli_stmt_execute($stmtAsientos);
        mysqli_stmt_close($stmtAsientos);
    }

    mysqli_commit($link);

    redirigirCon('success', 'Tu reserva fue cancelada correctamente.', $codReserva);
} catch (mysqli_sql_exception $e) {

    mysqli_rollback($link);

    error_log('Error al cancelar reserva ' . $codReserva . ': ' . $e->getMessage());

    redirigirCon('error', 'Ocurrió un error al cancelar la reserva. Intentá nuevamente.', $codReserva);
}
    