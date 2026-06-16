<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

include("../includes/conexion.php");

$nombre = $_POST['nombre'];

$email = $_POST['email'];

$telefono = $_POST['telefono'];

$clave = $_POST['clave'];

$tipoUsuario = $_POST['tipoUsuario'];
$token = bin2hex(random_bytes(32));

if($tipoUsuario == "CEO")
{
    $estado = "PENDIENTE";
}
else
{
    // $estado = "ACTIVA";
        $estado = "PENDIENTE";

}

$consulta = "
SELECT *
FROM usuarios
WHERE emailUsuario = '$email'
";

$resultado = mysqli_query($link,$consulta);

if(mysqli_num_rows($resultado) > 0)
{
    echo "
    <h2>Ya existe una cuenta con ese correo.</h2>
    <a href='registro.php'>Volver al registro</a>
    ";

    exit();
}

$vSql = "

INSERT INTO usuarios
(
    nombreUsuario,
    emailUsuario,
    claveUsuario,
    telefonoUsuario,
    tipoUsuario,
    estadoCuenta,
    tokenValidacion
)
VALUES
(
    '$nombre',
    '$email',
    '$clave',
    '$telefono',
    '$tipoUsuario',
    '$estado',
    '$token'
)

";

$vResultado = mysqli_query(
    $link,
    $vSql
);


if($vResultado)
{
    // header("Location: login.php");
    // exit();
    $mail = new PHPMailer(true);
    try
{
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
    "http://localhost/EntornosGraficos-SitioWeb/entornosGraficos-SitioWeb/auth/validar.php?token=$token";

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

    echo "
    <h2>Registro exitoso</h2>

    <p>
        Revisá tu correo para activar la cuenta.
    </p>
    ";
}
catch(Exception $e)
{
    echo $mail->ErrorInfo;
}
}
else
{
    echo mysqli_error($link);
}