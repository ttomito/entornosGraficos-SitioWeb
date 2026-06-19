<?php

include("../../includes/verificarSesion.php");

include("../../includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listar.php");
    exit();
}

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$pais = $_POST['pais'];

if (empty($nombre) || empty($pais) || empty($descripcion)) {
    header("Location: crear.php");
    exit();
}

$sql = "INSERT INTO aerolineas (nombreAerolinea, descripcionAerolinea, codPais) VALUES ('$nombre','$descripcion','$pais')";

$resultado = mysqli_query($link, $sql);

if ($resultado) {
    header("Location: listar.php");
    exit();
} else {
    error_log("Error al insertar aerolínea: " . mysqli_error($link));
    header("Location: crear.php");
}