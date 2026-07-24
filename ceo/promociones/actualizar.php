<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id    = (int) ($_POST['id'] ?? 0);
$idCEO = (int) ($_SESSION['id'] ?? 0);

if ($id <= 0 || $idCEO <= 0) {
    die("Acceso denegado");
}

$sqlPropietario = "SELECT p.codPromocion FROM promociones p
INNER JOIN usuarios u
ON p.codAerolinea = u.codAerolinea
WHERE p.codPromocion = $id
AND u.codUsuario = $idCEO";

$resultadoPropietario = mysqli_query($link, $sqlPropietario);

if (!$resultadoPropietario || mysqli_num_rows($resultadoPropietario) === 0) {
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
    header("Location: listar.php?alerta=campos_vacios");
    exit();
}

if (!preg_match('/^[\p{L}\p{N}\s.,;:¡!¿?%\-\(\)]{1,200}$/u', $descripcion)) {
    header("Location: listar.php?alerta=descripcion_invalida");
    exit();
}

foreach ($destinos as $d) {
    if (!preg_match('/^[\p{L}\p{N}\s.,\-]{1,100}$/u', $d)) {
        header("Location: listar.php?alerta=destino_invalido");
        exit();
    }
}

$destinosUnicos = [];
foreach ($destinos as $d) {
    $destinosUnicos[mb_strtolower($d)] = $d;
}
$destinos = array_values($destinosUnicos);

if (!is_numeric($descuento) || $descuento < 1 || $descuento > 100) {
    header("Location: listar.php?alerta=descuento_invalido");
    exit();
}

if (strtotime($fechaLimite) === false || $fechaLimite <= date('Y-m-d')) {
    header("Location: listar.php?alerta=fecha_invalida");
    exit();
}

$descripcionEsc = mysqli_real_escape_string($link, $descripcion);

mysqli_begin_transaction($link);

$sql = "UPDATE promociones
SET
descripcionPromocion = '$descripcionEsc',
descuentoPromocion = $descuento,
estadoPromocion = 'PENDIENTE',
fechaLimitePromocion = '$fechaLimite'
WHERE codPromocion = $id";

$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    mysqli_rollback($link);
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

$sqlBorrarDestinos = "DELETE FROM promocionesDestinos WHERE codPromocion = $id";
$resultadoBorrar = mysqli_query($link, $sqlBorrarDestinos);

if (!$resultadoBorrar) {
    mysqli_rollback($link);
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

foreach ($destinos as $destino) {

    $destinoEsc = mysqli_real_escape_string($link, $destino);

    $sqlDestino = "INSERT INTO promocionesDestinos (codPromocion, destinoVuelo)
    VALUES ($id, '$destinoEsc')";

    $respuestaDestino = mysqli_query($link, $sqlDestino);

    if (!$respuestaDestino) {
        mysqli_rollback($link);
        header("Location: listar.php?alerta=error_servidor");
        exit();
    }
}

mysqli_commit($link);

header("Location: listar.php?alerta=modificada");
exit();
