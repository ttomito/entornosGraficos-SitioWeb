<?php

include("../../includes/verificarSessionCeo.php");
include("../../includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listar.php");
    exit();
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

$idCEO = (int) ($_SESSION['id'] ?? 0);
$idVuelo = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($idCEO <= 0 || $idVuelo <= 0) {
    header("Location: listar.php");
    exit();
}

$sqlValidacion = "SELECT v.activo
FROM vuelos v
INNER JOIN usuarios u
ON v.codAerolinea = u.codAerolinea
WHERE v.codVuelo = ?
AND u.codUsuario = ?";

$stmtValidacion = mysqli_prepare($link, $sqlValidacion);

if (!$stmtValidacion) {
    error_log("Error al preparar la validación: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmtValidacion, "ii", $idVuelo, $idCEO);
mysqli_stmt_execute($stmtValidacion);
$resultadoValidacion = mysqli_stmt_get_result($stmtValidacion);
$vueloActual = $resultadoValidacion ? mysqli_fetch_assoc($resultadoValidacion) : null;
mysqli_stmt_close($stmtValidacion);

if (!$vueloActual) {
    header("Location: listar.php?alerta=acceso_denegado");
    exit();
}

$nuevoActivo = ((int)$vueloActual['activo'] === 1) ? 0 : 1;

mysqli_begin_transaction($link);
$exito = true;

$sqlVuelos = "UPDATE vuelos SET activo = ? WHERE codVuelo = ?";
$stmtVuelos = mysqli_prepare($link, $sqlVuelos);

if (!$stmtVuelos) {
    $exito = false;
} else {
    mysqli_stmt_bind_param($stmtVuelos, "ii", $nuevoActivo, $idVuelo);
    $exito = mysqli_stmt_execute($stmtVuelos);
    mysqli_stmt_close($stmtVuelos);
}

if ($exito) {
    $sqlReservas = "UPDATE reservas SET activo = ? WHERE codVuelo = ?";
    $stmtReservas = mysqli_prepare($link, $sqlReservas);

    if (!$stmtReservas) {
        $exito = false;
    } else {
        mysqli_stmt_bind_param($stmtReservas, "ii", $nuevoActivo, $idVuelo);
        $exito = mysqli_stmt_execute($stmtReservas);
        mysqli_stmt_close($stmtReservas);
    }
}

if (!$exito) {
    mysqli_rollback($link);
    error_log("Error al cambiar estado de vuelo (id=$idVuelo): " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_commit($link);

header("Location: listar.php?alerta=" . ($nuevoActivo === 1 ? 'activado' : 'eliminado'));
exit();
