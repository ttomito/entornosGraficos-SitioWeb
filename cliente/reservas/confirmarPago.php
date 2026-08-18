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
    redirigirCon('error', 'La confirmación debe realizarse desde el formulario de pago.', $codReserva);
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
    $sql = "SELECT codVuelo
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

    $codVuelo = (int) $reserva['codVuelo'];

    if ($codVuelo > 0) {

        $sqlVuelo = "SELECT fechaVuelo, horaSalida
                     FROM vuelos
                     WHERE codVuelo = ?";

        $stmtVuelo = mysqli_prepare($link, $sqlVuelo);
        mysqli_stmt_bind_param($stmtVuelo, "i", $codVuelo);
        mysqli_stmt_execute($stmtVuelo);

        $resVuelo = mysqli_stmt_get_result($stmtVuelo);
        $vuelo    = mysqli_fetch_assoc($resVuelo);

        mysqli_stmt_close($stmtVuelo);

        if ($vuelo && !empty($vuelo['fechaVuelo'])) {

            $fechaBase = substr((string) $vuelo['fechaVuelo'], 0, 10);
            $horaBase = !empty($vuelo['horaSalida']) ? (string) $vuelo['horaSalida'] : '00:00:00';
            $tsSalida = strtotime($fechaBase . ' ' . $horaBase);

            if ($tsSalida !== false && $tsSalida < time()) {
                mysqli_rollback($link);
                redirigirCon('error', 'El vuelo ya salió, la reserva no se puede pagar.', $codReserva);
            }
        }
    }

    $sqlUpdate = "UPDATE reservas
                  SET estadoReserva = 'CONFIRMADA'
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
        redirigirCon('error', 'Esta reserva ya no se puede pagar.', $codReserva);
    }

    mysqli_commit($link);

    redirigirCon('success', 'Tu pago fue confirmado correctamente.', $codReserva);
} catch (mysqli_sql_exception $e) {

    mysqli_rollback($link);

    error_log('Error al confirmar pago de reserva ' . $codReserva . ': ' . $e->getMessage());

    redirigirCon('error', 'Ocurrió un error al confirmar el pago. Intentá nuevamente.', $codReserva);
}
