<?php

include("../includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: editar.php");
    exit();
}

$codSobre = isset($_POST['codSobre']) ? (int)$_POST['codSobre'] : 0;
$titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$mision = isset($_POST['mision']) ? trim($_POST['mision']) : '';
$vision = isset($_POST['vision']) ? trim($_POST['vision']) : '';

if ($codSobre <= 0) {
    header("Location: editar.php?alerta=error_servidor");
    exit();
}

if (mb_strlen($titulo) < 3 || mb_strlen($titulo) > 150) {
    header("Location: editar.php?alerta=titulo_invalido");
    exit();
}

if (mb_strlen($descripcion) < 10 || mb_strlen($descripcion) > 1000) {
    header("Location: editar.php?alerta=descripcion_invalida");
    exit();
}

if (mb_strlen($mision) < 10 || mb_strlen($mision) > 1000) {
    header("Location: editar.php?alerta=mision_invalida");
    exit();
}

if (mb_strlen($vision) < 10 || mb_strlen($vision) > 1000) {
    header("Location: editar.php?alerta=vision_invalida");
    exit();
}

$sql = "

UPDATE sobre_nosotros

SET
titulo = ?,
descripcion = ?,
mision = ?,
vision = ?

WHERE codSobre = ?

";

$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    error_log("Error al preparar la actualización: " . mysqli_error($link));
    header("Location: editar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmt, "ssssi", $titulo, $descripcion, $mision, $vision, $codSobre);

$resultado = mysqli_stmt_execute($stmt);
$filasAfectadas = $resultado ? mysqli_stmt_affected_rows($stmt) : 0;
mysqli_stmt_close($stmt);

if (!$resultado) {
    error_log("Error al actualizar sobre_nosotros: " . mysqli_error($link));
    header("Location: editar.php?alerta=error_servidor");
    exit();
}

header("Location: pagina.php?alerta=actualizado");
exit();
