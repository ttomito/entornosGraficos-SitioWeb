<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

include("../includes/conexion.php");

$email = $_POST['email'];

$sql = "

SELECT *

FROM usuarios

WHERE emailUsuario = '$email'

";

$resultado = mysqli_query(
    $link,
    $sql
);

if(mysqli_num_rows($resultado) == 0)
{
    header("Location: recuperar.php?error=1");
    exit();
}

$usuario = mysqli_fetch_assoc(
    $resultado
);

$token = md5(
    uniqid(rand(),true)
);

$sqlUpdate = "

UPDATE usuarios

SET tokenRecuperacion = '$token'

WHERE codUsuario = ".$usuario['codUsuario'];

mysqli_query(
    $link,
    $sqlUpdate
);

$linkRecuperacion =
"http://localhost/EntornosGraficos-SitioWeb/entornosGraficos-SitioWeb/auth/restablecer.php?token=".$token;

$mail = new PHPMailer(true);

try
{
    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username =
    'sistemavuelos@gmail.com';

    $mail->Password =
    'wgfw hmjr hpge bjtm';

    $mail->SMTPSecure =
    PHPMailer::ENCRYPTION_STARTTLS;

    $mail->Port = 587;

    $mail->setFrom(
        'sistemavuelos@gmail.com',
        'AirTickets'
    );

    $mail->addAddress(
        $email
    );

    $mail->isHTML(true);

    $mail->Subject =
    'Recuperación de contraseña';

    $mail->Body = "

    <h2>

        Hola {$usuario['nombreUsuario']}

    </h2>

    <p>

        Hacé clic en el siguiente enlace para cambiar tu contraseña:

    </p>

    <a href='$linkRecuperacion'>

        Recuperar contraseña

    </a>

    ";

    $mail->send();

    header("Location: recuperar.php?ok=1");

    exit();
}
catch(Exception $e)
{
    echo $mail->ErrorInfo;
}