<?php

session_start();

include("conexion.php");

if(!isset($_SESSION['id']))
{
    header("Location: ../auth/login.php");
    exit();
}

$id = $_SESSION['id'];

$sql = "
SELECT *
FROM usuarios
WHERE codUsuario = $id
";

$resultado = mysqli_query($link,$sql);

if(mysqli_num_rows($resultado) == 0)
{
    session_destroy();

    header("Location: ../auth/login.php");
    exit();
}