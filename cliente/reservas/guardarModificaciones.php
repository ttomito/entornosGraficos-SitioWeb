<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$asientos= $_POST['cantAsientos'];

$codReserva = $_POST['codReserva'];

$sql = "

SELECT *

FROM reservas

WHERE codReserva = $codReserva

";


$idUsuario =
$_SESSION['id'];

$sql = "

SELECT *

FROM reservas

WHERE codReserva = $codReserva

AND codUsuario = $idUsuario

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

$sql = "

SELECT *

FROM vuelos

WHERE codVuelo = $codVuelo

";

$resultadoVuelo = mysqli_query(
    $link,
    $sql
);

$vuelo = mysqli_fetch_assoc(
    $resultadoVuelo
);

$nuevosDisponibles =
    $vuelo['asientosDisponibles']
    + $cantAsientos
    - $asientos;

if($nuevosDisponibles < 0)
{
    header(
        "Location: modificar.php?codReserva=$codReserva&error=No hay suficientes asientos disponibles"
    );
    exit();
}
$codAerolinea=$vuelo['codAerolinea'];

$sqlProm = "

SELECT *

FROM promociones

WHERE codAerolinea = $codAerolinea

AND estadoPromocion = 'APROBADA'

";

$resultado_prom = mysqli_query(
    $link,
    $sqlProm);

$descuentoMaximo = 0;
$hoy = date("Y-m-d");
while($promocion=mysqli_fetch_assoc($resultado_prom))
    {
        if(
            $promocion['descuentoPromocion']>$descuentoMaximo 
            && 
            $promocion['fechaLimitePromocion']>=$hoy)
            {
                $descuentoMaximo = $promocion['descuentoPromocion'];
            }
    };
$precioFinal = ($vuelo['precioVuelo']-($vuelo['precioVuelo']*$descuentoMaximo/100)) * $asientos;


$sqlVuelo = "


UPDATE vuelos

SET asientosDisponibles = asientosDisponibles + $cantAsientos - $asientos

WHERE codVuelo = $codVuelo

";

mysqli_query(
    $link,
    $sqlVuelo
);
$sqlact="

UPDATE reservas

SET cantAsientos = $asientos,
precioFinal = $precioFinal

WHERE codReserva = $codReserva

";

mysqli_query(
    $link,
    $sqlact
);



header("Location: listar.php");
exit();

?>