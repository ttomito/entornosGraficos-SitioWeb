<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id = $_POST['id'];

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}

$texto = $_POST['texto'];
$publicacion = $_POST['publicacion'];
$expiracion = $_POST['expiracion'];


if (empty($texto) || empty($publicacion) || empty($expiracion)) {
    header("Location: editar.php?alerta=campos_vacios");
    exit();
}

$sql = "UPDATE novedades SET textoNovedad = '$texto', fechaPublicacion = '$publicacion', fechaExpiracion = '$expiracion'
        WHERE codNovedad = $id";

$resultado = mysqli_query($link,$sql);


if (!$resultado) {
    header("Location: listar.php?alerta=error_servidor");
    die("Error en la consulta: " . mysqli_error($link));
    exit();
} else {
    header("Location: listar.php?alerta=actualizada");
    exit();
}




?>