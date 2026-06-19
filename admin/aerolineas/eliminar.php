<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    header("Location: listar.php");
    exit();
}

//validamos que el id sea entero positivo
$id = $_GET['id'];
$activo = $_GET['activo'];

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}
if($activo != 0 && $activo != 1){
    header("Location: listar.php");
    exit();
}

$modifActivo = 0;

if($activo == 0){
   $modifActivo = 1;
}

/*Verificar vuelos asociados*/

$sqlVuelos = "SELECT * FROM vuelos WHERE codAerolinea = $id";

$resultadoVuelos = mysqli_query($link, $sqlVuelos);


if(mysqli_num_rows($resultadoVuelos) > 0)
{
    while($vuelo = mysqli_fetch_assoc($resultadoVuelos))
    {
        $codVuelo = $vuelo['codVuelo'];

        $sqlDesactivarReservas = "UPDATE reservas SET activo = $modifActivo WHERE codVuelo = $codVuelo";

        mysqli_query($link, $sqlDesactivarReservas);
    }

    $sqlDesactivarVuelos = "UPDATE vuelos SET activo = $modifActivo WHERE codAerolinea = $id";

    mysqli_query($link, $sqlDesactivarVuelos);

    if($modifActivo == 1){
        header("Location: listar.php?alerta=vuelos_activados");
    exit();
    } else {
        header("Location: listar.php?alerta=vuelos_desactivados");
        exit();
    }

    
}

$sqlActualizarAerolinea = "UPDATE aerolineas SET activo = $modifActivo WHERE codAerolinea = $id";

mysqli_query($link, $sqlActualizarAerolinea);

if($modifActivo == 1){
    header("Location: listar.php?alerta=activada");
    exit();
} else {
    header("Location: listar.php?alerta=eliminada");
    exit();
}



?>