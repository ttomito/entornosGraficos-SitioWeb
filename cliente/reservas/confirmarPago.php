<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$codReserva =
(int)$_POST['codReserva'];

$idUsuario =
$_SESSION['id'];

$sql = "

UPDATE reservas

SET estadoReserva='CONFIRMADA'

WHERE codReserva=$codReserva

AND codUsuario=$idUsuario

";

mysqli_query(
$link,
$sql
);

header(
"Location:listar.php"
);

exit();

?>