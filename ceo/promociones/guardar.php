<?php

include("../../includes/verificarSessionCEO.php");
include("../../includes/conexion.php");

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header("Location: crear.php?alerta=error_servidor");
    exit();
}

$idCEO = (int) ($_SESSION['id'] ?? 0);

if ($idCEO <= 0) {
    header("Location: crear.php");
    exit();
}

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
    header("Location: crear.php?alerta=campos_vacios");
    exit();
}

if (!preg_match('/^[\p{L}\p{N}\s.,;:¡!¿?%\-\(\)]{1,200}$/u', $descripcion)) {
    header("Location: crear.php?alerta=descripcion_invalida");
    exit();
}

foreach ($destinos as $d) {
    if (!preg_match('/^[\p{L}\p{N}\s.,\-]{1,100}$/u', $d)) {
        header("Location: crear.php?alerta=destino_invalido");
        exit();
    }
}

// sacamos los duplicados sin importar mayusculas/minusculas
$destinosUnicos = [];
foreach ($destinos as $d) {
    $destinosUnicos[mb_strtolower($d)] = $d;
}
$destinos = array_values($destinosUnicos);

if (!is_numeric($descuento) || $descuento < 1 || $descuento > 100) {
    header("Location: crear.php?alerta=descuento_invalido");
    exit();
}
$descuento = (int) $descuento;

if (strtotime($fechaLimite) === false || $fechaLimite <= date('Y-m-d')) {
    header("Location: crear.php?alerta=fecha_invalida");
    exit();
}

$sqlCEO = "SELECT codAerolinea FROM usuarios WHERE codUsuario = ?";
$stmtCEO = mysqli_prepare($link, $sqlCEO);

if (!$stmtCEO) {
    error_log("Error al preparar la consulta de CEO: " . mysqli_error($link));
    header("Location: crear.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmtCEO, "i", $idCEO);
mysqli_stmt_execute($stmtCEO);
$resultadoCEO = mysqli_stmt_get_result($stmtCEO);
$ceo = $resultadoCEO ? mysqli_fetch_assoc($resultadoCEO) : null;
mysqli_stmt_close($stmtCEO);

if (!$ceo || $ceo['codAerolinea'] === null) {
    header("Location: crear.php?alerta=sin_aerolinea");
    exit();
}

$codAerolinea = (int) $ceo['codAerolinea'];

mysqli_begin_transaction($link);
$exito = true;

$sql = "INSERT INTO promociones (codAerolinea, descripcionPromocion, descuentoPromocion, estadoPromocion, fechaLimitePromocion)
        VALUES (?, ?, ?, 'PENDIENTE', ?)";

$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    $exito = false;
} else {
    mysqli_stmt_bind_param($stmt, "isis", $codAerolinea, $descripcion, $descuento, $fechaLimite);
    $exito = mysqli_stmt_execute($stmt);
    $codPromocion = $exito ? mysqli_insert_id($link) : null;
    mysqli_stmt_close($stmt);
}

if ($exito) {
    $sqlDestino = "INSERT INTO promociones_destinos (codPromocion, destinoVuelo) VALUES (?, ?)";
    $stmtDestino = mysqli_prepare($link, $sqlDestino);

    if (!$stmtDestino) {
        $exito = false;
    } else {
        foreach ($destinos as $destino) {
            mysqli_stmt_bind_param($stmtDestino, "is", $codPromocion, $destino);

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
    error_log("Error al crear promoción: " . mysqli_error($link));
    header("Location: crear.php?alerta=error_servidor");
    exit();
}

mysqli_commit($link);

header("Location: listar.php?alerta=creada");
exit();
