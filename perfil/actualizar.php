<?php

include("../includes/verificarSession.php");
include("../includes/conexion.php");

$id = $_SESSION['id'];

if($id <= 0)
{
    header("Location: index.php");
    exit();
}

$nombre    = $_POST['nombre'];
$apellido  = $_POST['apellido'];
$dni       = $_POST['dni'];
$telefono  = $_POST['telefono'];
$clave     = $_POST['clave'];

/*
Verificamos que el DNI no pertenezca a otro usuario
*/

$sqlVerificar = "

SELECT *

FROM usuarios

WHERE dniUsuario = '$dni'

AND codUsuario <> $id

";

$resultadoVerificar = mysqli_query($link,$sqlVerificar);

if(mysqli_num_rows($resultadoVerificar)>0)
{
    header("Location: index.php?alerta=dni");
    exit();
}

/*
Actualización
*/

if(!empty($clave))
{

    $sql = "

    UPDATE usuarios

    SET

    nombreUsuario='$nombre',
    apellidoUsuario='$apellido',
    dniUsuario='$dni',
    telefonoUsuario='$telefono',
    claveUsuario='$clave'

    WHERE codUsuario=$id

    ";

}
else
{

    $sql = "

    UPDATE usuarios

    SET

    nombreUsuario='$nombre',
    apellidoUsuario='$apellido',
    dniUsuario='$dni',
    telefonoUsuario='$telefono'

    WHERE codUsuario=$id

    ";

}

$resultado = mysqli_query($link,$sql);

if(!$resultado)
{
    header("Location: index.php?alerta=error_servidor");
    exit();
}

$_SESSION['nombre']=$nombre." ".$apellido;
header("Location: index.php?alerta=actualizado");
exit();

?>