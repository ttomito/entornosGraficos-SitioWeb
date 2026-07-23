<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

include("../includes/conexion.php");


$nombre         = trim($_POST['nombre'] ?? '');
$apellido       = trim($_POST['apellido'] ?? '');
$dni            = trim($_POST['dni'] ?? '');
$email          = trim($_POST['email'] ?? '');
$telefono       = trim($_POST['telefono'] ?? '');
$clave          = $_POST['clave'] ?? '';
$claveConfirmar = $_POST['claveConfirmar'] ?? '';
$tipoUsuario    = trim($_POST['tipoUsuario'] ?? '');

$errores = [];

if (!preg_match('/^[A-Za-zÀ-ÿ\s]{2,60}$/u', $nombre)) {
    $errores[] = 'nombre';
}

if (!preg_match('/^[A-Za-zÀ-ÿ\s]{2,60}$/u', $apellido)) {
    $errores[] = 'apellido';
}

if (!preg_match('/^\d{7,8}$/', $dni)) {
    $errores[] = 'dni';
}

if (!preg_match('/^[0-9+\-\s()]{6,20}$/', $telefono)) {
    $errores[] = 'telefono';
}

if (mb_strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'email';
}

if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $clave)) {
    $errores[] = 'clave';
}

if ($clave !== $claveConfirmar) {
    $errores[] = 'claveConfirmar';
}

if (!in_array($tipoUsuario, ['CLIENTE', 'CEO'], true)) {
    $errores[] = 'tipoUsuario';
}

if (!empty($errores)) {
    header("Location: registro.php?invalido=1");
    exit();
}

$token = bin2hex(random_bytes(32));

if ($tipoUsuario == "CEO") {
    $estado = "PENDIENTE";
    $aprobadoAdmin = "NO";
} else {
    $estado = "PENDIENTE";
    $aprobadoAdmin = "SI";
}

// Hash contraseña 
$claveHash = password_hash($clave, PASSWORD_DEFAULT);


$nombreEsc   = mysqli_real_escape_string($link, $nombre);
$apellidoEsc = mysqli_real_escape_string($link, $apellido);
$dniEsc      = mysqli_real_escape_string($link, $dni);
$emailEsc    = mysqli_real_escape_string($link, $email);
$telefonoEsc = mysqli_real_escape_string($link, $telefono);


$consulta = "SELECT * FROM usuarios WHERE emailUsuario='$emailEsc' OR dniUsuario='$dniEsc'";

$resultado = mysqli_query($link, $consulta);

if (mysqli_num_rows($resultado) > 0) {
    header("Location: registro.php?existe=1");
    exit();
}


$vSql = "INSERT INTO usuarios
(nombreUsuario, apellidoUsuario, dniUsuario, emailUsuario, claveUsuario, telefonoUsuario, tipoUsuario, estadoCuenta, tokenValidacion, aprobadoAdmin)
VALUES
('$nombreEsc','$apellidoEsc','$dniEsc','$emailEsc','$claveHash','$telefonoEsc','$tipoUsuario','$estado','$token','$aprobadoAdmin')";

$vResultado = mysqli_query($link, $vSql);


if ($vResultado) {
    // header("Location: login.php");
    // exit();
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';

        $mail->SMTPAuth = true;

        $mail->Username = 'sistemavuelos@gmail.com';

        $mail->Password = 'wgfw hmjr hpge bjtm';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port = 587;

        $mail->setFrom(
            'sistemavuelos@gmail.com',
            'Sistema de Vuelos'
        );

        $mail->addAddress($email);

        $mail->isHTML(true);

        $mail->Subject = 'Validacion de cuenta';

        $linkValidacion =
            "http://localhost/entornosGraficos-SitioWeb/auth/validar.php?token=$token";

        $mail->Body = "
    <h2>Bienvenido</h2>

    <p>
        Haga click para validar su cuenta:
    </p>

    <a href='$linkValidacion'>
        Validar Cuenta
    </a>
    ";

        $mail->send();

        if ($tipoUsuario == "CEO") {
            header("Location: registro.php?ceo=1");
        } else {
            header("Location: registro.php?exito=1");
        }

        exit();
    } catch (Exception $e) {
        echo $mail->ErrorInfo;
    }
} else {
    echo mysqli_error($link);
}