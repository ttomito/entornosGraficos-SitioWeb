<?php

$host = "localhost";
$usuario = "root";
$password = "";
$baseDatos = "sistema_vuelos";

$link = mysqli_connect(
    $host,
    $usuario,
    $password,
    $baseDatos
);


if(!$link)
{
    die("Error de conexión: " . mysqli_connect_error());
}
?>
