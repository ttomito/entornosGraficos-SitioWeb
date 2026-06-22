<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id = $_GET['id'];

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}

$sql = "DELETE FROM novedades WHERE codNovedad = $id";
$resultado = mysqli_query($link,$sql);

if(!$resultado){
    header("Location: listar.php?alerta=error_servidor");
    die("Error en la consulta: " . mysqli_error($link));
    exit();
} else {
    header("Location: listar.php?alerta=eliminada");
    exit();
}



?>