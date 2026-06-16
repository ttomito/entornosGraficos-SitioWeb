<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$texto = $_POST['texto'];
$publicacion = $_POST['publicacion'];
$expiracion = $_POST['expiracion'];

$sql = "

INSERT INTO novedades
(
    textoNovedad,
    fechaPublicacion,
    fechaExpiracion
)
VALUES
(
    '$texto',
    '$publicacion',
    '$expiracion'
)

";

mysqli_query($link,$sql);

header("Location: listar.php");
exit();

?>