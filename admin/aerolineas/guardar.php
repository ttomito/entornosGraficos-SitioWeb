<?php

include("../../includes/verificarSesion.php");
include("../../includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listar.php");
    exit();
}

$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$pais = isset($_POST['pais']) ? trim($_POST['pais']) : '';


if ($nombre === '') {
    header("Location: crear.php?alerta=campos_vacios");
    exit();
}

if (mb_strlen($nombre) < 2) {
    header("Location: crear.php?alerta=nombre_corto");
    exit();
}

if (mb_strlen($nombre) > 100) {
    header("Location: crear.php?alerta=nombre_largo");
    exit();
}


if (mb_strlen($descripcion) > 500) {
    header("Location: crear.php?alerta=descripcion_larga");
    exit();
}

/*
| País: opcional, hasta 100 caracteres, solo letras/espacios/guiones/apóstrofes
*/

if ($pais !== '') {

    if (mb_strlen($pais) > 100) {
        header("Location: crear.php?alerta=pais_largo");
        exit();
    }

    if (!preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]+$/u", $pais)) {
        header("Location: crear.php?alerta=pais_invalido");
        exit();
    }
}

$sql = "INSERT INTO aerolineas (nombreAerolinea, descripcionAerolinea, codPais) VALUES (?, ?, ?)";

$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    error_log("Error al preparar la consulta: " . mysqli_error($link));
    header("Location: crear.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmt, "sss", $nombre, $descripcion, $pais);

$resultado = mysqli_stmt_execute($stmt);

if ($resultado) {
    mysqli_stmt_close($stmt);
    header("Location: listar.php?alerta=creada");
    exit();
} else {
    error_log("Error al insertar aerolínea: " . mysqli_error($link));
    mysqli_stmt_close($stmt);
    header("Location: crear.php?alerta=error_servidor");
    exit();
}