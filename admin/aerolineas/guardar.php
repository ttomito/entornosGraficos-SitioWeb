<?php

include("../../includes/verificarSesion.php");

include("../../includes/conexion.php");

$nombre = $_POST['nombre'];

$descripcion = $_POST['descripcion'];

$pais = $_POST['pais'];

$sql = "

INSERT INTO aerolineas
(
    nombreAerolinea,
    descripcionAerolinea,
    codPais
)
VALUES
(
    '$nombre',
    '$descripcion',
    '$pais'
)

";

$resultado = mysqli_query(
    $link,
    $sql
);

if($resultado)
{
    header("Location: listar.php");
    exit();
}
else
{
    echo mysqli_error($link);
}