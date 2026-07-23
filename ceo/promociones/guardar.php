<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$idCEO = $_SESSION['id'];

if($idCEO <= 0){
    die("Acceso denegado");
}

$descripcion = $_POST['descripcion'] ?? '';
$descuento   = $_POST['descuento'] ?? '';
$fechaLimite = $_POST['fechaLimite'] ?? '';

if(trim($descripcion) === '' || $descuento === '' || trim($fechaLimite) === ''){
    header("Location: crear.php?alerta=campos_vacios");
    exit();
}

if (!preg_match('/^[\p{L}\p{N}\s.,;:¡!¿?%\-\(\)]{1,200}$/u', $descripcion)) {
    header("Location: crear.php?alerta=descripcion_invalida");
    exit();
}

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

$sql = "INSERT INTO promociones (codAerolinea, descripcionPromocion, descuentoPromocion, estadoPromocion, fechaLimitePromocion)
VALUES ($codAerolinea, '$descripcion', $descuento, 'PENDIENTE', '$fechaLimite')";

$respuesta = mysqli_query($link, $sql);

if(!$respuesta){
    header("Location: crear.php?alerta=error_servidor");
    exit();
}

header("Location: listar.php?alerta=creada");
exit();

?>