<?php

include("../../includes/verificarSessionAdmin.php");
include("../../includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listar.php");
    exit();
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0) {
    header("Location: listar.php");
    exit();
}

$sqlActual = "SELECT activo FROM aerolineas WHERE codAerolinea = ?";
$stmtActual = mysqli_prepare($link, $sqlActual);

if (!$stmtActual) {
    error_log("Error al preparar consulta de estado: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmtActual, "i", $id);
mysqli_stmt_execute($stmtActual);
$resultadoActual = mysqli_stmt_get_result($stmtActual);
$aerolinea = $resultadoActual ? mysqli_fetch_assoc($resultadoActual) : null;
mysqli_stmt_close($stmtActual);

if (!$aerolinea) {
    header("Location: listar.php?alerta=no_encontrada");
    exit();
}

$modifActivo = ((int)$aerolinea['activo'] === 1) ? 0 : 1;

mysqli_begin_transaction($link);
$exito = true;
$tieneVuelos = false;

$sqlVuelos = "SELECT codVuelo FROM vuelos WHERE codAerolinea = ?";
$stmtVuelos = mysqli_prepare($link, $sqlVuelos);

if (!$stmtVuelos) {
    $exito = false;
} else {
    mysqli_stmt_bind_param($stmtVuelos, "i", $id);
    if (mysqli_stmt_execute($stmtVuelos)) {
        $resultadoVuelos = mysqli_stmt_get_result($stmtVuelos);
        $tieneVuelos = $resultadoVuelos && mysqli_num_rows($resultadoVuelos) > 0;
    } else {
        $exito = false;
    }
    mysqli_stmt_close($stmtVuelos);
}

if ($exito && $tieneVuelos) {

    $sqlReservas = "
        UPDATE reservas r
        INNER JOIN vuelos v
        ON v.codVuelo = r.codVuelo
        SET r.activo = ?
        WHERE v.codAerolinea = ?
    ";
    $stmtReservas = mysqli_prepare($link, $sqlReservas);

    if (!$stmtReservas) {
        $exito = false;
    } else {
        mysqli_stmt_bind_param($stmtReservas, "ii", $modifActivo, $id);
        $exito = mysqli_stmt_execute($stmtReservas);
        mysqli_stmt_close($stmtReservas);
    }

    if ($exito) {
        $sqlActualizarVuelos = "UPDATE vuelos SET activo = ? WHERE codAerolinea = ?";
        $stmtActualizarVuelos = mysqli_prepare($link, $sqlActualizarVuelos);

        if (!$stmtActualizarVuelos) {
            $exito = false;
        } else {
            mysqli_stmt_bind_param($stmtActualizarVuelos, "ii", $modifActivo, $id);
            $exito = mysqli_stmt_execute($stmtActualizarVuelos);
            mysqli_stmt_close($stmtActualizarVuelos);
        }
    }
}

if ($exito) {
    $sqlActualizarAerolinea = "UPDATE aerolineas SET activo = ? WHERE codAerolinea = ?";
    $stmtAerolinea = mysqli_prepare($link, $sqlActualizarAerolinea);

    if (!$stmtAerolinea) {
        $exito = false;
    } else {
        mysqli_stmt_bind_param($stmtAerolinea, "ii", $modifActivo, $id);
        $exito = mysqli_stmt_execute($stmtAerolinea);
        mysqli_stmt_close($stmtAerolinea);
    }
}

if (!$exito) {
    mysqli_rollback($link);
    error_log("Error al cambiar estado de aerolínea (id=$id): " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_commit($link);

if ($modifActivo === 1) {
    header("Location: listar.php?alerta=" . ($tieneVuelos ? 'vuelos_activados' : 'activada'));
} else {
    header("Location: listar.php?alerta=" . ($tieneVuelos ? 'vuelos_desactivados' : 'eliminada'));
}

exit();