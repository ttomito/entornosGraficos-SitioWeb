<?php

include("../includes/conexion.php");
$codSobre = $_POST['codSobre'];
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$mision = $_POST['mision'];
$vision = $_POST['vision'];

$sql = "

UPDATE sobre_nosotros

SET
titulo='$titulo',
descripcion='$descripcion',
mision='$mision',
vision='$vision'

WHERE codSobre=$codSobre

";

mysqli_query(
$link,
$sql
);

header(
"Location:pagina.php"
);

exit();