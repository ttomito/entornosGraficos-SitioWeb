<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$idUsuario= $_SESSION ['id'];
$codVuelo= $_POST['codVuelo'];
$fecha= date("Y-m-d");
$cantAsientos= $_POST['cantAsientos'];
$precio= $_POST['precio']*$cantAsientos;

$sqlVuelo = "

SELECT asientosDisponibles, precioVuelo

FROM vuelos

WHERE codVuelo = $codVuelo

";

$resultadoVuelo = mysqli_query(
    $link,
    $sqlVuelo
);

$vuelo = mysqli_fetch_assoc(
    $resultadoVuelo
);

$asientosDisponibles = $vuelo['asientosDisponibles'];

$precio= $vuelo['precioVuelo']*$cantAsientos;

if($cantAsientos > $asientosDisponibles)
{
    header(
        "Location: reservar.php?codVuelo=$codVuelo&error=No hay suficientes asientos disponibles"
    );
    exit();
}

if($cantAsientos <= 0)
{
    header(
        "Location: reservar.php?codVuelo=$codVuelo&error=La cantidad de asientos debe ser mayor a cero"
    );
    exit();
}
$sql = "

SELECT *

FROM reservas

WHERE codUsuario = $idUsuario

AND codVuelo = $codVuelo

AND estadoReserva != 'CANCELADA'

";

$resultado = mysqli_query(
    $link,
    $sql
);

if(mysqli_num_rows($resultado) > 0)
{
    header(
        "Location: reservar.php?codVuelo=$codVuelo&error=Ya tienes una reserva para este vuelo"
    );
    exit();
}
$sql = "

INSERT INTO reservas
(
    codUsuario,
    codVuelo,
    fechaReserva,
    estadoReserva,
    precioFinal,
    cantAsientos
)
VALUES
(
    $idUsuario,
    $codVuelo,
    '$fecha',
    'PENDIENTE',    
    $precio,
    $cantAsientos

)

";

mysqli_query(
    $link,
    $sql
);

$nuevosAsientos = $asientosDisponibles - $cantAsientos;

$sql = "

UPDATE vuelos

SET asientosDisponibles = $nuevosAsientos

WHERE codVuelo = $codVuelo

";

mysqli_query(
    $link,
    $sql
);

header(
    "Location: listar.php"
);

exit();