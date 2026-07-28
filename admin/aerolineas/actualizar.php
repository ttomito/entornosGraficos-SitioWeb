<?php

include("../../includes/verificarSessionAdmin.php");
include("../../includes/conexion.php");

// Solo aceptamos POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listar.php");
    exit();
}

$id          = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nombre      = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$pais        = strtoupper(trim($_POST['pais'] ?? ''));

if ($id === false || $id === null || $id <= 0) {
    header("Location: listar.php");
    exit();
}

if ($nombre === '' || $descripcion === '' || $pais === '') {
    header("Location: editar.php?id=$id&alerta=campos_vacios");
    exit();
}

if (mb_strlen($nombre) < 2) {
    header("Location: editar.php?id=$id&alerta=nombre_corto");
    exit();
}

if (mb_strlen($nombre) > 100) {
    header("Location: editar.php?id=$id&alerta=nombre_largo");
    exit();
}

if (mb_strlen($descripcion) > 500) {
    header("Location: editar.php?id=$id&alerta=descripcion_larga");
    exit();
}

if (!preg_match('/^[A-Z]{2}$/', $pais)) {
    header("Location: editar.php?id=$id&alerta=pais_invalido");
    exit();
}

$sql = "UPDATE aerolineas
        SET nombreAerolinea = ?, descripcionAerolinea = ?, codPais = ?
        WHERE codAerolinea = ?";

$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    error_log("Error al preparar la consulta: " . mysqli_error($link));
    header("Location: editar.php?id=$id&alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmt, "sssi", $nombre, $descripcion, $pais, $id);

$resultado = mysqli_stmt_execute($stmt);

if (!$resultado) {
    error_log("Error al actualizar aerolínea: " . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    header("Location: editar.php?id=$id&alerta=error_servidor");
    exit();
}

mysqli_stmt_close($stmt);

header("Location: listar.php?alerta=actualizada");
exit();