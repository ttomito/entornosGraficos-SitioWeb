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

mysqli_set_charset($link, "utf8mb4");


if(!$link)
{
    die("Error de conexión: " . mysqli_connect_error());
}
?>
