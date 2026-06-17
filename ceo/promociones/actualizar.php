<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id = $_POST['id'];

$descripcion = $_POST['descripcion'];

$descuento = $_POST['descuento'];

if($descuento < 1 || $descuento > 100)
{
    die("Descuento inválido");
}

$sql = "

UPDATE promociones

SET
descripcionPromocion = '$descripcion',
descuentoPromocion = $descuento,
estadoPromocion = 'PENDIENTE'

WHERE codPromocion = $id

";

mysqli_query(
    $link,
    $sql
);

header(
    "Location: listar.php"
);

exit();