<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

include("../includes/conexion.php");

$email = trim($_POST['email'] ?? '');

if (mb_strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: recuperar.php?invalido=1");
    exit();
}

$emailEsc = mysqli_real_escape_string($link, $email);

$sql = "SELECT * FROM usuarios WHERE emailUsuario = '$emailEsc'";

$resultado = mysqli_query($link, $sql);

if (mysqli_num_rows($resultado) == 0) {
    header("Location: recuperar.php?ok=1");
    exit();
}

$usuario = mysqli_fetch_assoc(
    $resultado
);


$token = bin2hex(random_bytes(32));

$codUsuario = (int) $usuario['codUsuario'];

$sqlUpdate = "UPDATE usuarios SET tokenRecuperacion = '$token', tokenRecuperacionExpira = NOW() + INTERVAL 60 MINUTE WHERE codUsuario = $codUsuario";

mysqli_query($link, $sqlUpdate);

$linkRecuperacion =
    "http://localhost/entornosGraficos-SitioWeb/auth/restablecer.php?token=".$token;

$mail = new PHPMailer(true);

try {
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

        Hacé clic en el siguiente enlace para cambiar tu contraseña. Va a estar
        disponible durante 60 minutos:

    </p>

    <a href='$linkRecuperacion'>

        Recuperar contraseña

    </a>

    ";

    $mail->send();

    header("Location: recuperar.php?ok=1");

    exit();
} catch (Exception $e) {
    echo $mail->ErrorInfo;
}