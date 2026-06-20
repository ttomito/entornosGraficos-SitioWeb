<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");



$codReserva = $_GET['codReserva'];

$sql = "

SELECT *

FROM reservas

WHERE codReserva = $codReserva

";

$resultado = mysqli_query(
    $link,
    $sql
);

$reserva = mysqli_fetch_assoc(
    $resultado
);

$codVuelo = $reserva['codVuelo'];
$cantAsientos = $reserva['cantAsientos'];

if($reserva['estadoReserva'] == 'CANCELADA')
{
    header("Location: listar.php");
    exit();
}

$sqlVuelo = "


UPDATE vuelos

SET asientosDisponibles = asientosDisponibles + $cantAsientos

WHERE codVuelo = $codVuelo

";

mysqli_query(
    $link,
    $sqlVuelo
);
$sqlact="

UPDATE reservas

SET estadoReserva = 'CANCELADA'

WHERE codReserva = $codReserva

";

mysqli_query(
    $link,
    $sqlact
);



header("Location: listar.php");
exit();

?>