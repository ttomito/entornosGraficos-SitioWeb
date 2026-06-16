<?php

session_start();

include("../../includes/conexion.php");

$idCEO = $_SESSION['id'];

$idVuelo = $_POST['id'];

$sqlValidacion = "

SELECT
v.*

FROM vuelos v

INNER JOIN usuarios u
ON v.codAerolinea = u.codAerolinea

WHERE v.codVuelo = $idVuelo

AND u.codUsuario = $idCEO

";

$validacion = mysqli_query(
    $link,
    $sqlValidacion
);

if(mysqli_num_rows($validacion) == 0)
{
    die("Acceso denegado");
}

$sql = "

UPDATE vuelos

SET
origenVuelo = '{$_POST['origen']}',
destinoVuelo = '{$_POST['destino']}',
fechaVuelo = '{$_POST['fecha']}',
horaSalida = '{$_POST['hora']}',
precioVuelo = '{$_POST['precio']}',
asientosDisponibles = '{$_POST['asientos']}'

WHERE codVuelo = $idVuelo

";

mysqli_query(
    $link,
    $sql
);

header("Location: listar.php");

exit();

?>