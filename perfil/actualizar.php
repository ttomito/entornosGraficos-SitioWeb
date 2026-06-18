<?php

include("../includes/verificarSession.php");
include("../includes/conexion.php");

$id = $_SESSION['id'];

$nombre = $_POST['nombre'];

$telefono = $_POST['telefono'];

$clave = $_POST['clave'];

if($clave != "")
{
    $sql = "

    UPDATE usuarios

    SET
    nombreUsuario = '$nombre',
    telefonoUsuario = '$telefono',
    claveUsuario = '$clave'

    WHERE codUsuario = $id

    ";
}
else
{
    $sql = "

    UPDATE usuarios

    SET
    nombreUsuario = '$nombre',
    telefonoUsuario = '$telefono'

    WHERE codUsuario = $id

    ";
}

mysqli_query(
    $link,
    $sql
);

$_SESSION['nombre'] = $nombre;

header(
    "Location: index.php"
);

exit();

?>