<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id = $_POST['id'];

if($id <= 0){
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

$sql = "UPDATE promociones
SET
descripcionPromocion = '$descripcion',
descuentoPromocion = $descuento,
estadoPromocion = 'PENDIENTE',
fechaLimitePromocion = '$fechaLimite'
WHERE codPromocion = $id";

$resultado = mysqli_query($link, $sql);

if(!$resultado){
    header("Location: listar.php?alerta=error_servidor");
    exit();
} else {
    header("Location: listar.php?alerta=modificada");
    exit();
}
