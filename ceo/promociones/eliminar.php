<?php

include("../../includes/verificarSessionCEO.php");
include("../../includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listar.php");
    exit();
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$idCEO = (int) ($_SESSION['id'] ?? 0);

if ($id <= 0 || $idCEO <= 0) {
    header("Location: listar.php");
    exit();
}

// verificamos que la promoción exista y pertenezca a la aerolínea de este CEO
$sqlPropietario = "SELECT p.codPromocion
FROM promociones p
INNER JOIN usuarios u
ON p.codAerolinea = u.codAerolinea
WHERE p.codPromocion = ?
AND u.codUsuario = ?";

$stmtPropietario = mysqli_prepare($link, $sqlPropietario);

if (!$stmtPropietario) {
    error_log("Error al preparar la verificación: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmtPropietario, "ii", $id, $idCEO);
mysqli_stmt_execute($stmtPropietario);
mysqli_stmt_store_result($stmtPropietario);

if (mysqli_stmt_num_rows($stmtPropietario) === 0) {
    mysqli_stmt_close($stmtPropietario);
    header("Location: listar.php?alerta=no_encontrada");
    exit();
}

mysqli_stmt_close($stmtPropietario);

mysqli_begin_transaction($link);
$exito = true;

$sqlBorrarDestinos = "DELETE FROM promociones_destinos WHERE codPromocion = ?";
$stmtBorrarDestinos = mysqli_prepare($link, $sqlBorrarDestinos);

if (!$stmtBorrarDestinos) {
    $exito = false;
} else {
    mysqli_stmt_bind_param($stmtBorrarDestinos, "i", $id);
    $exito = mysqli_stmt_execute($stmtBorrarDestinos);
    mysqli_stmt_close($stmtBorrarDestinos);
}

if ($exito) {
    $sqlBorrar = "DELETE FROM promociones WHERE codPromocion = ?";
    $stmtBorrar = mysqli_prepare($link, $sqlBorrar);

    if (!$stmtBorrar) {
        $exito = false;
    } else {
        mysqli_stmt_bind_param($stmtBorrar, "i", $id);
        $exito = mysqli_stmt_execute($stmtBorrar);
        mysqli_stmt_close($stmtBorrar);
    }
}

if (!$exito) {
    mysqli_rollback($link);
    error_log("Error al eliminar promoción (id=$id): " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_commit($link);

header("Location: listar.php?alerta=eliminado");
exit();
