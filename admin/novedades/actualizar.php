<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id = $_POST['id'];

$texto = $_POST['texto'];

$publicacion = $_POST['publicacion'];

$expiracion = $_POST['expiracion'];

$sql = "

UPDATE novedades

SET
textoNovedad = '$texto',
fechaPublicacion = '$publicacion',
fechaExpiracion = '$expiracion'

WHERE codNovedad = $id

";

mysqli_query($link,$sql);

header("Location: listar.php");

exit();

?>