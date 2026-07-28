<?php

include("../../includes/verificarSessionCEO.php");
include("../../includes/conexion.php");

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

$id    = (int) ($_POST['id'] ?? 0);
$idCEO = (int) ($_SESSION['id'] ?? 0);

if ($id <= 0 || $idCEO <= 0) {
    header("Location: listar.php");
    exit();
}

// verificamos que la promoción exista y pertenezca a la aerolínea de este CEO
$sqlPropietario = "SELECT p.codPromocion FROM promociones p
INNER JOIN usuarios u ON p.codAerolinea = u.codAerolinea
WHERE p.codPromocion = ? AND u.codUsuario = ?";

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

$descripcion    = $_POST['descripcion'] ?? '';
$destinosCrudos = $_POST['destinos'] ?? [];
$descuento      = $_POST['descuento'] ?? '';
$fechaLimite    = $_POST['fechaLimite'] ?? '';

if (!is_array($destinosCrudos)) {
    $destinosCrudos = [];
}

$destinos = [];
foreach ($destinosCrudos as $d) {
    $d = trim((string) $d);
    if ($d !== '') {
        $destinos[] = $d;
    }
}

if (trim($descripcion) === '' || empty($destinos) || $descuento === '' || trim($fechaLimite) === '') {
    header("Location: editar.php?id=$id&alerta=campos_vacios");
    exit();
}

if (!preg_match('/^[\p{L}\p{N}\s.,;:¡!¿?%\-\(\)]{1,200}$/u', $descripcion)) {
    header("Location: editar.php?id=$id&alerta=descripcion_invalida");
    exit();
}

foreach ($destinos as $d) {
    if (!preg_match('/^[\p{L}\p{N}\s.,\-]{1,100}$/u', $d)) {
        header("Location: editar.php?id=$id&alerta=destino_invalido");
        exit();
    }
}

$destinosUnicos = [];
foreach ($destinos as $d) {
    $destinosUnicos[mb_strtolower($d)] = $d;
}
$destinos = array_values($destinosUnicos);

if (!is_numeric($descuento) || $descuento < 1 || $descuento > 100) {
    header("Location: editar.php?id=$id&alerta=descuento_invalido");
    exit();
}
$descuento = (int) $descuento;

if (strtotime($fechaLimite) === false || $fechaLimite <= date('Y-m-d')) {
    header("Location: editar.php?id=$id&alerta=fecha_invalida");
    exit();
}

mysqli_begin_transaction($link);
$exito = true;

$sql = "UPDATE promociones
SET
descripcionPromocion = ?,
descuentoPromocion = ?,
estadoPromocion = 'PENDIENTE',
fechaLimitePromocion = ?
WHERE codPromocion = ?";

$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    $exito = false;
} else {
    mysqli_stmt_bind_param($stmt, "sisi", $descripcion, $descuento, $fechaLimite, $id);
    $exito = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

if ($exito) {
    $sqlBorrarDestinos = "DELETE FROM promociones_destinos WHERE codPromocion = ?";
    $stmtBorrarDestinos = mysqli_prepare($link, $sqlBorrarDestinos);

    if (!$stmtBorrarDestinos) {
        $exito = false;
    } else {
        mysqli_stmt_bind_param($stmtBorrarDestinos, "i", $id);
        $exito = mysqli_stmt_execute($stmtBorrarDestinos);
        mysqli_stmt_close($stmtBorrarDestinos);
    }
}

if ($exito) {
    $sqlDestino = "INSERT INTO promociones_destinos (codPromocion, destinoVuelo) VALUES (?, ?)";
    $stmtDestino = mysqli_prepare($link, $sqlDestino);

    if (!$stmtDestino) {
        $exito = false;
    } else {
        foreach ($destinos as $destino) {
            mysqli_stmt_bind_param($stmtDestino, "is", $id, $destino);

            if (!mysqli_stmt_execute($stmtDestino)) {
                $exito = false;
                break;
            }
        }

        mysqli_stmt_close($stmtDestino);
    }
}

if (!$exito) {
    mysqli_rollback($link);
    error_log("Error al actualizar promoción (id=$id): " . mysqli_error($link));
    header("Location: editar.php?id=$id&alerta=error_servidor");
    exit();
}

mysqli_commit($link);

header("Location: listar.php?alerta=modificada");
exit();
