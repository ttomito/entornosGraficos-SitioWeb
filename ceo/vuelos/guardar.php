<?php

session_start();

include("../../includes/conexion.php");

$idCEO = $_SESSION['id'];

$sqlCEO = "

SELECT codAerolinea

FROM usuarios

WHERE codUsuario = $idCEO

";

$resultadoCEO = mysqli_query($link,$sqlCEO);

$ceo = mysqli_fetch_assoc($resultadoCEO);

$codAerolinea = $ceo['codAerolinea'];

$origen = $_POST['origen'];
$destino = $_POST['destino'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];
$precio = $_POST['precio'];
$asientos = $_POST['asientos'];
$imagen = $_POST['imagen'];

$sql = "

INSERT INTO vuelos
(
codAerolinea,
origenVuelo,
destinoVuelo,
fechaVuelo,
horaSalida,
precioVuelo,
asientosDisponibles,
imagenVuelo
)
VALUES
(
'$codAerolinea',
'$origen',
'$destino',
'$fecha',
'$hora',
'$precio',
'$asientos',
'$imagen'
)

";

mysqli_query($link,$sql);

header("Location: listar.php");

exit();

?>