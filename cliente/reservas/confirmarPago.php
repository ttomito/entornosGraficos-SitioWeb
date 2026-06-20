<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$codReserva = $_POST['codReserva'];

$sql = "

UPDATE reservas

SET estadoReserva = 'CONFIRMADA'

WHERE codReserva = $codReserva

";

mysqli_query(
    $link,
    $sql
);

header("Location: listar.php");
exit();

?>