<?php

session_start();

include("../includes/conexion.php");

$email = trim($_POST['email'] ?? '');
$clave = $_POST['clave'] ?? '';

$emailValido = (mb_strlen($email) <= 100) && filter_var($email, FILTER_VALIDATE_EMAIL);

$claveValida = $clave !== '';

if (!$emailValido || !$claveValida) {
    header("Location: login.php?error=1");
    exit();
}


$emailEsc = mysqli_real_escape_string($link, $email);

$vSql = "SELECT * FROM usuarios WHERE emailUsuario='$emailEsc'";

$vResultado = mysqli_query($link, $vSql);

if (mysqli_num_rows($vResultado) == 0) {
    header("Location: login.php?error=1");
    exit();
}

$usuario = mysqli_fetch_assoc($vResultado);

if (!password_verify($clave, $usuario['claveUsuario'])) {
    header("Location: login.php?error=1");
    exit();
}

if (
    $usuario['tipoUsuario'] == 'CEO'
    &&
    $usuario['aprobadoAdmin'] == 'NO'
) {
    header("Location: login.php?esperando=1");
    exit();
}

if (
    $usuario['tipoUsuario'] == 'CLIENTE'
    &&
    $usuario['estadoCuenta'] != 'ACTIVA'
) {
    header("Location: login.php?pendiente=1");
    exit();
}

$_SESSION['id'] = $usuario['codUsuario'];

$_SESSION['nombre'] =
    $usuario['nombreUsuario'] . " " . $usuario['apellidoUsuario'];

$_SESSION['tipo'] = $usuario['tipoUsuario'];

if ($usuario['tipoUsuario'] == 'ADMIN') {
    header("Location: ../admin/dashboard.php");
} elseif ($usuario['tipoUsuario'] == 'CEO') {
    header("Location: ../ceo/dashboard.php");
} else {
    header("Location: ../cliente/dashboard.php");
}

exit();