<?php

session_start();

include("../includes/conexion.php");


$email = $_POST['email'];

$clave = $_POST['clave'];

$vSql = "

SELECT *

FROM usuarios

WHERE emailUsuario='$email'

AND claveUsuario='$clave'

";

$vResultado = mysqli_query(
    $link,
    $vSql
);

if(mysqli_num_rows($vResultado) == 0)
{
    header("Location: login.php?error=1");
    exit();
}

$usuario = mysqli_fetch_assoc($vResultado);

if($usuario['estadoCuenta'] != 'ACTIVA')
{
    header("Location: login.php?pendiente=1");
    exit();
}

$_SESSION['id'] = $usuario['codUsuario'];

$_SESSION['nombre'] = $usuario['nombreUsuario'];

$_SESSION['tipo'] = $usuario['tipoUsuario'];

if($usuario['tipoUsuario'] == 'ADMIN')
{
    header("Location: ../admin/dashboard.php");
}
elseif($usuario['tipoUsuario'] == 'CEO')
{
    header("Location: ../ceo/dashboard.php");
}
else
{
    header("Location: ../cliente/dashboard.php");
}

exit();

