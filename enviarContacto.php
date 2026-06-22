<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$nombre = $_POST['nombre'];
$email = $_POST['email'];
$mensaje = $_POST['mensaje'];

$mail = new PHPMailer(true);

try
{
    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username = 'sistemavuelos@gmail.com';

    $mail->Password = 'wgfw hmjr hpge bjtm';

    $mail->SMTPSecure =
    PHPMailer::ENCRYPTION_STARTTLS;

    $mail->Port = 587;

    $mail->setFrom(
        'sistemavuelos@gmail.com',
        'AirTickets'
    );

    /*
        Correo que recibe los mensajes
    */

    $mail->addAddress(
        'sistemavuelos@gmail.com'
    );

    $mail->isHTML(true);

    $mail->Subject =
    'Nuevo mensaje de contacto';

    $mail->Body = "

        <h2>Nuevo mensaje recibido</h2>

        <p>

            <b>Nombre:</b> $nombre

        </p>

        <p>

            <b>Email:</b> $email

        </p>

        <p>

            <b>Mensaje:</b>

        </p>

        <p>

            $mensaje

        </p>

    ";

    $mail->send();

    header("Location: contacto.php?ok=1");
}
catch(Exception $e)
{
    header("Location: contacto.php?error=1");
}

?>