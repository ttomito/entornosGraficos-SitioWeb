<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id = $_GET['id'];

$sql = "

DELETE FROM aerolineas

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