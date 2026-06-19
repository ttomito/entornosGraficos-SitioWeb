<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$idUsuario= $_SESSION ['id'];
$codVuelo= $_POST['codVuelo'];
$fecha= date("Y-m-d");
$precio= $_POST['precio'];

$sql = "

INSERT INTO reservas
(
    codUsuario,
    codVuelo,
    fechaReserva,
    estadoReserva,
    precioFinal
)
VALUES
(
    $idUsuario,
    $codVuelo,
    '$fecha',
    'PENDIENTE',    
    $precio
)

";
mysqli_query(
    $link,
    $sql
);

header(
    "Location: listar.php"
);

exit();