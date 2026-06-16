<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id = $_GET['id'];

/*
    Verificar vuelos asociados
*/

$sqlVuelos = "

SELECT *

FROM vuelos

WHERE codAerolinea = $id

";

$resultadoVuelos = mysqli_query(
    $link,
    $sqlVuelos
);

if(mysqli_num_rows($resultadoVuelos) > 0)
{
    die("No se puede eliminar la aerolínea porque posee vuelos asociados.");
}

/*
    Verificar CEOs asociados
*/

$sqlCEO = "

SELECT *

FROM usuarios

WHERE codAerolinea = $id

AND tipoUsuario = 'CEO'

";

$resultadoCEO = mysqli_query(
    $link,
    $sqlCEO
);

if(mysqli_num_rows($resultadoCEO) > 0)
{
    die("No se puede eliminar la aerolínea porque tiene CEOs asignados.");
}

/*
    Eliminar aerolínea
*/

$sqlEliminar = "

DELETE FROM aerolineas

WHERE codAerolinea = $id

";

mysqli_query(
    $link,
    $sqlEliminar
);

header(
    "Location: listar.php"
);

exit();

?>