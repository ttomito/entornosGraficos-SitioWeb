<?php

session_start();

include("../../includes/conexion.php");

$idCEO = $_SESSION['id'];
$idVuelo = $_GET['id'];
$activo = $_GET['activo'];

if($idCEO <= 0){
    header("Location: listar.php");
    die("Acceso denegado");
    exit();
}

$nuevoActivo = 0;
if($activo == 1){
    $nuevoActivo = 0;
} else {
    $nuevoActivo = 1;
}

$sqlValidacion = "SELECT v.*
FROM vuelos v
INNER JOIN usuarios u
ON v.codAerolinea = u.codAerolinea
WHERE v.codVuelo = $idVuelo
AND u.codUsuario = $idCEO";

$validacion = mysqli_query($link, $sqlValidacion);

if (mysqli_num_rows($validacion) == 0) {
    die("Acceso denegado");
}

$sqlVuelos = "UPDATE vuelos SET activo = $nuevoActivo WHERE codVuelo = $idVuelo";
$resultadoVuelos = mysqli_query($link, $sqlVuelos);

if(!$resultadoVuelos){

    header("Location: listar.php?alerta=error_servidor");
    die("Error en la consulta: " . mysqli_error($link));

} else {

    $sqlReservas = "UPDATE reservas SET activo = $nuevoActivo WHERE codVuelo = $idVuelo";
    $resultadoReservas = mysqli_query($link, $sqlReservas);

    if(!$resultadoReservas){
        header("Location: listar.php?alerta=error_servidor");
        die("Error en la consulta: " . mysqli_error($link));
    } else {
        header("Location: listar.php?alerta=eliminado");
        exit();
    }
    
}

exit();
