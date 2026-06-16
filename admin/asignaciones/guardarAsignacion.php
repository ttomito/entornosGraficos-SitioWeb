<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$idCEO = $_POST['idCEO'];

$codAerolinea = $_POST['codAerolinea'];

if($codAerolinea == 0)
{
    $sql = "

    UPDATE usuarios

    SET codAerolinea = NULL

    WHERE codUsuario = $idCEO

    ";
}
else
{
    $sql = "

    UPDATE usuarios

    SET codAerolinea = $codAerolinea

    WHERE codUsuario = $idCEO

    ";
}

mysqli_query(
    $link,
    $sql
);

header(
    "Location: listar.php"
);

exit();

?>