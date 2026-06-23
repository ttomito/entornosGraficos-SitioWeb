<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id = $_GET['id'];

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}

// obtener la imagen antes de eliminar el registro
$sqlSelect = "SELECT imagen FROM novedades WHERE codNovedad = $id";
$resultadoSelect = mysqli_query($link, $sqlSelect);

if (!$resultadoSelect) {
    header("Location: listar.php?alerta=error_servidor");
    die("Error en la consulta: " . mysqli_error($link));
    exit();
}

$novedad = mysqli_fetch_assoc($resultadoSelect);

$sql = "DELETE FROM novedades WHERE codNovedad = $id";
$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    
    header("Location: listar.php?alerta=error_servidor");
    die("Error en la consulta: " . mysqli_error($link));

    } else {

    if (!empty($novedad['imagen'])) {
        $carpeta = "../../uploads/novedades/";
        $rutaImagen = $carpeta . $novedad['imagen'];
        if (file_exists($rutaImagen)) {
            unlink($rutaImagen);
        }
    }

    header("Location: listar.php?alerta=eliminada");
    exit();
}

?>