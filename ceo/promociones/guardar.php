<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$idCEO = $_SESSION['id'];

if($idCEO <= 0){
    die("Acceso denegado");
}

$descripcion = $_POST['descripcion'];
$descuento = $_POST['descuento'];
$fechaLimite = $_POST['fechaLimite'];

if(empty($descripcion) || empty($descuento) || empty($fechaLimite)){
    header("Location: listar.php?alerta=campos_vacios");
    die("Acceso denegado");
}

if ($descuento < 1 || $descuento > 100) {
    header("Location: listar.php?alerta=descuento_invalido");
    die("El descuento debe estar entre 1% y 100%");
}

if ($fechaLimite <= date('Y-m-d')) {
    header("Location: listar.php?alerta=fecha_invalida");
    die("La fecha límite debe ser posterior a hoy");
}

$sqlCEO = "SELECT codAerolinea FROM usuarios WHERE codUsuario = $idCEO";
$resultadoCEO = mysqli_query($link, $sqlCEO);

if (!$resultadoCEO) {
    die(mysqli_error($link));
}

$ceo = mysqli_fetch_assoc($resultadoCEO);
$codAerolinea = $ceo['codAerolinea'];

$sql = "INSERT INTO promociones (codAerolinea, descripcionPromocion, descuentoPromocion, estadoPromocion, fechaLimitePromocion)
VALUES ($codAerolinea, '$descripcion', $descuento, 'PENDIENTE', '$fechaLimite')";

$respuesta = mysqli_query($link, $sql);

if(!$respuesta){
    die("Error SQL: " . mysqli_error($link));
} else {
    header("Location: listar.php?alerta=creada");
    exit();
}

