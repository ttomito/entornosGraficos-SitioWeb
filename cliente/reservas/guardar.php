<?php

require_once("../../includes/verificarSession.php");
require_once("../../includes/conexion.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$idUsuario    = (int) $_SESSION['id'];
$codVuelo     = (int) ($_POST['codVuelo'] ?? 0);
$cantAsientos = (int) ($_POST['cantAsientos'] ?? 0);

function redirigirCon($tipo, $mensaje, $destino){
    $_SESSION['flash'] = [
        'tipo'    => $tipo,
        'mensaje' => $mensaje,
    ];

    header('Location: ' . $destino);
    exit();
}

$volverAReservar = $codVuelo > 0 ? 'reservar.php?codVuelo=' . $codVuelo : '../vuelos/listar.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirigirCon('error', 'La reserva debe realizarse desde el formulario.', $volverAReservar);
}

$tokenRecibido = $_POST['csrf_token'] ?? '';

$tokenValido = !empty($_SESSION['csrf_token'])
    && is_string($tokenRecibido)
    && hash_equals($_SESSION['csrf_token'], $tokenRecibido);

if (!$tokenValido) {
    redirigirCon('error', 'La sesión expiró o el formulario no es válido. Volvé a intentarlo.', $volverAReservar);
}

if ($codVuelo <= 0) {
    redirigirCon('error', 'El vuelo indicado no es válido.', '../vuelos/listar.php');
}

if ($cantAsientos <= 0) {
    redirigirCon('error', 'La cantidad de asientos debe ser mayor a cero.', $volverAReservar);
}

try {

    mysqli_begin_transaction($link);

    $sqlVuelo = "SELECT codAerolinea,
                        destinoVuelo,
                        precioVuelo,
                        fechaVuelo,
                        horaSalida
                 FROM vuelos
                 WHERE codVuelo = ?
                   AND activo = 1
                 FOR UPDATE";

    $stmtVuelo = mysqli_prepare($link, $sqlVuelo);
    mysqli_stmt_bind_param($stmtVuelo, "i", $codVuelo);
    mysqli_stmt_execute($stmtVuelo);

    $resVuelo = mysqli_stmt_get_result($stmtVuelo);
    $vuelo    = mysqli_fetch_assoc($resVuelo);

    mysqli_stmt_close($stmtVuelo);

    if (!$vuelo) {
        mysqli_rollback($link);
        redirigirCon('error', 'No encontramos ese vuelo.', '../vuelos/listar.php');
    }

    // No se puede reservar un vuelo que ya salió
    if (!empty($vuelo['fechaVuelo'])) {

        $fechaBase = substr((string) $vuelo['fechaVuelo'], 0, 10);

        $horaBase = !empty($vuelo['horaSalida'])
            ? (string) $vuelo['horaSalida']
            : '00:00:00';

        $tsSalida = strtotime($fechaBase . ' ' . $horaBase);

        if ($tsSalida !== false && $tsSalida < time()) {
            mysqli_rollback($link);
            redirigirCon('error', 'Este vuelo ya salió, no se puede reservar.', '../vuelos/listar.php');
        }
    }

    //Una promoción aplica a un vuelo solo si el destino de ese vuelo está cargado en promociones_destinos
    
    $codAerolinea = (int) $vuelo['codAerolinea'];
    $destinoVuelo = (string) $vuelo['destinoVuelo'];

    $sqlProm = "SELECT COALESCE(MAX(p.descuentoPromocion), 0) AS descuento
                FROM promociones p
                INNER JOIN promociones_destinos pd
                        ON pd.codPromocion = p.codPromocion
                WHERE p.codAerolinea = ?
                  AND p.estadoPromocion = 'APROBADA'
                  AND p.fechaLimitePromocion >= CURDATE()
                  AND LOWER(TRIM(pd.destinoVuelo)) = LOWER(TRIM(?))";

    $stmtProm = mysqli_prepare($link, $sqlProm);
    mysqli_stmt_bind_param($stmtProm, "is", $codAerolinea, $destinoVuelo);
    mysqli_stmt_execute($stmtProm);

    $resProm  = mysqli_stmt_get_result($stmtProm);
    $filaProm = mysqli_fetch_assoc($resProm);

    mysqli_stmt_close($stmtProm);

    $descuento = (float) ($filaProm['descuento'] ?? 0);

    if ($descuento < 0)   { $descuento = 0.0; }
    if ($descuento > 100) { $descuento = 100.0; }

    $precioPorAsiento = (float) $vuelo['precioVuelo'] * (1 - $descuento / 100);
    $precioFinal      = round($precioPorAsiento * $cantAsientos, 2);

    // ya tiene una reserva activa para este vuelo?
  
    $sqlExiste = "SELECT codReserva
                  FROM reservas
                  WHERE codUsuario = ?
                    AND codVuelo = ?
                    AND estadoReserva <> 'CANCELADA'
                  LIMIT 1
                  FOR UPDATE";

    $stmtExiste = mysqli_prepare($link, $sqlExiste);
    mysqli_stmt_bind_param($stmtExiste, "ii", $idUsuario, $codVuelo);
    mysqli_stmt_execute($stmtExiste);

    $resExiste = mysqli_stmt_get_result($stmtExiste);
    $yaExiste  = mysqli_fetch_assoc($resExiste);

    mysqli_stmt_close($stmtExiste);

    if ($yaExiste) {
        mysqli_rollback($link);
        redirigirCon('error', 'Ya tenés una reserva activa para este vuelo.', $volverAReservar);
    }

    // Descuento de asientos

    $sqlAsientos = "UPDATE vuelos
                    SET asientosDisponibles = asientosDisponibles - ?
                    WHERE codVuelo = ?
                      AND asientosDisponibles >= ?";

    $stmtAsientos = mysqli_prepare($link, $sqlAsientos);
    mysqli_stmt_bind_param($stmtAsientos, "iii", $cantAsientos, $codVuelo, $cantAsientos);
    mysqli_stmt_execute($stmtAsientos);

    $filasAsientos = mysqli_stmt_affected_rows($stmtAsientos);

    mysqli_stmt_close($stmtAsientos);

    if ($filasAsientos !== 1) {
        mysqli_rollback($link);
        redirigirCon('error', 'No hay suficientes asientos disponibles.', $volverAReservar);
    }

    // Alta de la reserva


    $fechaReserva = date("Y-m-d");

    $sqlInsert = "INSERT INTO reservas (codUsuario, codVuelo, fechaReserva, estadoReserva, precioFinal, cantAsientos)
    VALUES (?, ?, ?, 'PENDIENTE', ?, ?)";

    $stmtInsert = mysqli_prepare($link, $sqlInsert);
    mysqli_stmt_bind_param(
        $stmtInsert,
        "iisdi",
        $idUsuario,
        $codVuelo,
        $fechaReserva,
        $precioFinal,
        $cantAsientos
    );
    mysqli_stmt_execute($stmtInsert);

    $codReservaNueva = mysqli_insert_id($link);

    mysqli_stmt_close($stmtInsert);

    mysqli_commit($link);

    redirigirCon(
        'success',
        'Tu reserva fue creada correctamente. Quedó pendiente de pago.',
        'verReserva.php?codReserva=' . (int) $codReservaNueva
    );

} catch (mysqli_sql_exception $e) {

    mysqli_rollback($link);

    error_log('Error al crear reserva del vuelo ' . $codVuelo . ': ' . $e->getMessage());

    redirigirCon('error', 'Ocurrió un error al crear la reserva. Intentá nuevamente.', $volverAReservar);
}