<?php

$host = "localhost";
$usuario = "root";
$password = "HolaMundo!123";
$baseDatos = "sistema_vuelos";

$link = mysqli_connect(
    $host,
    $usuario,
    $password,
    $baseDatos
);

var_dump($link);

if(!$link)
{
    die("Error de conexión: " . mysqli_connect_error());
}
echo "conexion cargada";
?>
