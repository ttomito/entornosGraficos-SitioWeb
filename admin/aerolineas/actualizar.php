<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

//solo aceptamos post
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listar.php");
    exit();
}

$id = $_POST['id'];
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$pais = $_POST['pais'];

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}
if (empty($nombre) || empty($pais)) {
    header("Location: editar.php?alerta=campos_vacios");
    exit();
}

$sql = "UPDATE aerolineas SET nombreAerolinea = '$nombre', descripcionAerolinea = '$descripcion', codPais = '$pais' WHERE codAerolinea = $id";

$resultado = mysqli_query($link, $sql);
if (!$resultado) {
    error_log("Error al actualizar aerolínea: " . mysqli_stmt_error($stmt));
    header("Location: editar.php?alerta=error_servidor");
    exit();
}

header("Location: listar.php?alerta=actualizada");

exit();

?>