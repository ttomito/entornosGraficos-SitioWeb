<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$idCEO = $_SESSION['id'];

if ($idCEO <= 0) {
    die("Acceso denegado");
}

$descripcion    = $_POST['descripcion'] ?? '';
$destinosCrudos = $_POST['destinos'] ?? [];
$descuento      = $_POST['descuento'] ?? '';
$fechaLimite    = $_POST['fechaLimite'] ?? '';

if (!is_array($destinosCrudos)) {
    $destinosCrudos = [];
}

// Limpiamos espacios y descartamos vacíos
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

// Quitamos duplicados sin importar mayúsculas/minúsculas
$destinosUnicos = [];
foreach ($destinos as $d) {
    $destinosUnicos[mb_strtolower($d)] = $d;
}
$destinos = array_values($destinosUnicos);

if (!is_numeric($descuento) || $descuento < 1 || $descuento > 100) {
    header("Location: crear.php?alerta=descuento_invalido");
    exit();
}

if (strtotime($fechaLimite) === false || $fechaLimite <= date('Y-m-d')) {
    header("Location: crear.php?alerta=fecha_invalida");
    exit();
}

$sqlCEO = "SELECT codAerolinea FROM usuarios WHERE codUsuario = $idCEO";
$resultadoCEO = mysqli_query($link, $sqlCEO);

if (!$resultadoCEO) {
    header("Location: crear.php?alerta=error_servidor");
    exit();
}

$ceo = mysqli_fetch_assoc($resultadoCEO);
$codAerolinea = $ceo['codAerolinea'];

mysqli_begin_transaction($link);

$sql = "INSERT INTO promociones (codAerolinea, descripcionPromocion, descuentoPromocion, estadoPromocion, fechaLimitePromocion)
VALUES ($codAerolinea, '$descripcion', $descuento, 'PENDIENTE', '$fechaLimite')";

$respuesta = mysqli_query($link, $sql);

if (!$respuesta) {
    mysqli_rollback($link);
    header("Location: crear.php?alerta=error_servidor");
    exit();
}

$codPromocion = mysqli_insert_id($link);

foreach ($destinos as $destino) {

    $destinoEsc = mysqli_real_escape_string($link, $destino);

    $sqlDestino = "INSERT INTO promocionesDestinos (codPromocion, destinoVuelo)
    VALUES ($codPromocion, '$destinoEsc')";

    $respuestaDestino = mysqli_query($link, $sqlDestino);

    if (!$respuestaDestino) {
        mysqli_rollback($link);
        header("Location: crear.php?alerta=error_servidor");
        exit();
    }
}

mysqli_commit($link);

header("Location: listar.php?alerta=creada");
exit();
