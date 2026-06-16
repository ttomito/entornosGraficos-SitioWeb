<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id = $_POST['id'];

$nombre = $_POST['nombre'];

$descripcion = $_POST['descripcion'];

$pais = $_POST['pais'];

$sql = "

UPDATE aerolineas

SET

nombreAerolinea = '$nombre',

descripcionAerolinea = '$descripcion',

codPais = '$pais'

WHERE codAerolinea = $id

";

mysqli_query(
    $link,
    $sql
);

header(
    "Location: listar.php"
);

exit();

?>