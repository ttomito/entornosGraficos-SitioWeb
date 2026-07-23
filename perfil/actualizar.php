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
$claveConfirmacion = $_POST['clave_confirmacion'];

if ($nombre === '' || mb_strlen($nombre) > 60) {
    header("Location: index.php?alerta=datos_invalidos");
    exit();
}
 
if ($apellido === '' || mb_strlen($apellido) > 60) {
    header("Location: index.php?alerta=datos_invalidos");
    exit();
}
 
if (!preg_match('/^\d{7,8}$/', $dni)) {
    header("Location: index.php?alerta=dni_invalido");
    exit();
}
 
if (!preg_match('/^[\d\s\-\+\(\)]{6,20}$/', $telefono)) {
    header("Location: index.php?alerta=telefono_invalido");
    exit();
}
 
if ($clave !== '' && strlen($clave) < 8) {
    header("Location: index.php?alerta=clave_corta");
    exit();
}

if ($clave !== '' && $clave !== $claveConfirmacion) {
    header("Location: index.php?alerta=clave_no_coincide");
    exit();
}

if ($clave !== '' && !preg_match('/^[A-Za-z\d!@#$%^&*()_+\-=\[\]{};:\'",.<>\/?`~\\\\]{8,}$/', $clave)) {
    header("Location: index.php?alerta=clave_invalida");
    exit();
}

$sqlVerificar = "SELECT * FROM usuarios WHERE dniUsuario = '$dni'
AND codUsuario <> $id";

$resultadoVerificar = mysqli_query($link,$sqlVerificar);

if(mysqli_num_rows($resultadoVerificar)>0)
{
    header("Location: index.php?alerta=dni");
    exit();
}


if(!empty($clave))
{

    $sql = "UPDATE usuarios SET
    nombreUsuario='$nombre',
    apellidoUsuario='$apellido',
    dniUsuario='$dni',
    telefonoUsuario='$telefono',
    claveUsuario='$clave'
    WHERE codUsuario=$id";

}
else
{

    $sql = "UPDATE usuarios SET
    nombreUsuario='$nombre',
    apellidoUsuario='$apellido',
    dniUsuario='$dni',
    telefonoUsuario='$telefono'
    WHERE codUsuario=$id";

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