<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$codReserva =
(int)$_GET['codReserva'];

$idUsuario =
$_SESSION['id'];

$sql = "

SELECT *

FROM reservas

WHERE codReserva = $codReserva

AND codUsuario = $idUsuario

";

$resultado =
mysqli_query(
$link,
$sql
);

if(
mysqli_num_rows($resultado)==0
)
{
header("Location:listar.php");
exit();
}

$reserva =
mysqli_fetch_assoc(
$resultado
);

$sqlVuelo = "

SELECT *

FROM vuelos

WHERE codVuelo =
{$reserva['codVuelo']}

";

$resultadoVuelo =
mysqli_query(
$link,
$sqlVuelo
);

$vuelo =
mysqli_fetch_assoc(
$resultadoVuelo
);

$fechaVuelo =
strtotime(
$vuelo['fechaVuelo']
);

$diferenciaHoras =
(
$fechaVuelo
-
time()
)
/
3600;

if(
$diferenciaHoras < 72
)
{
header(
"Location:listar.php?error=No puede cancelar una reserva con menos de 72 horas"
);
exit();
}

mysqli_query(
$link,
"

UPDATE reservas

SET estadoReserva='CANCELADA'

WHERE codReserva=$codReserva

"
);

mysqli_query(
$link,
"

UPDATE vuelos

SET asientosDisponibles =
asientosDisponibles +
{$reserva['cantAsientos']}

WHERE codVuelo =
{$reserva['codVuelo']}

"
);

header(
"Location:listar.php"
);

exit();

?>