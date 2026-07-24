<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listar.php");
    exit();
}

$idCEO = isset($_POST['idCEO']) ? (int)$_POST['idCEO'] : 0;
$codAerolineaRaw = isset($_POST['codAerolinea']) ? trim($_POST['codAerolinea']) : '';

if ($idCEO <= 0) {
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

if ($codAerolineaRaw === '' || !ctype_digit($codAerolineaRaw)) {
    header("Location: asignar.php?id=$idCEO&alerta=campos_vacios");
    exit();
}

$codAerolinea = (int)$codAerolineaRaw;

if ($codAerolinea === 0) {

    $sql = "UPDATE usuarios SET codAerolinea = NULL WHERE codUsuario = ?";
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $idCEO);
    }
} else {

    /*
    | Se verifica que la aerolínea elegida exista antes de asignarla
    */

    $sqlVerificar = "SELECT codAerolinea FROM aerolineas WHERE codAerolinea = ?";
    $stmtVerificar = mysqli_prepare($link, $sqlVerificar);

    if (!$stmtVerificar) {
        error_log("Error al preparar la verificación: " . mysqli_error($link));
        header("Location: listar.php?alerta=error_servidor");
        exit();
    }

    mysqli_stmt_bind_param($stmtVerificar, "i", $codAerolinea);
    mysqli_stmt_execute($stmtVerificar);
    mysqli_stmt_store_result($stmtVerificar);

    if (mysqli_stmt_num_rows($stmtVerificar) === 0) {
        mysqli_stmt_close($stmtVerificar);
        header("Location: asignar.php?id=$idCEO&alerta=aerolinea_invalida");
        exit();
    }

    mysqli_stmt_close($stmtVerificar);

    $sql = "UPDATE usuarios SET codAerolinea = ? WHERE codUsuario = ?";
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $codAerolinea, $idCEO);
    }
}

if (!$stmt) {
    error_log("Error al preparar la consulta: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

$resultado = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($resultado) {
    header("Location: listar.php?alerta=asignada");
    exit();
} else {
    error_log("Error al asignar aerolínea: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}
