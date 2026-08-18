<?php

include("../../includes/verificarSessionAdmin.php");
include("../../includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listar.php");
    exit();
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}

// Obtener la imagen antes de eliminar el registro
$sqlSelect = "SELECT imagen FROM novedades WHERE codNovedad = ?";
$stmtSelect = mysqli_prepare($link, $sqlSelect);

if (!$stmtSelect) {
    error_log("Error al preparar la consulta: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmtSelect, "i", $id);
mysqli_stmt_execute($stmtSelect);
$resultadoSelect = mysqli_stmt_get_result($stmtSelect);
$novedad = $resultadoSelect ? mysqli_fetch_assoc($resultadoSelect) : null;
mysqli_stmt_close($stmtSelect);

if (!$novedad) {
    header("Location: listar.php?alerta=no_encontrada");
    exit();
}

$sql = "DELETE FROM novedades WHERE codNovedad = ?";
$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    error_log("Error al preparar la eliminación: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $id);
$resultado = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$resultado) {
    error_log("Error al eliminar novedad (id=$id): " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

if (!empty($novedad['imagen'])) {
    $carpeta = "../../uploads/novedades/";
    $rutaImagen = $carpeta . basename($novedad['imagen']);
    if (file_exists($rutaImagen)) {
        unlink($rutaImagen);
    }
}

header("Location: listar.php?alerta=eliminada");
exit();
