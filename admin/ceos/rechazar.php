<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id = $_GET['id'];

/*
    Obtener datos del CEO
*/

$consulta = "

SELECT *

FROM usuarios

WHERE codUsuario = $id

";

$resultado = mysqli_query(
    $link,
    $consulta
);

$usuario = mysqli_fetch_assoc($resultado);

$email = $usuario['emailUsuario'];

$nombre = $usuario['nombreUsuario'];

/*
    Rechazar CEO
*/

$sql = "

UPDATE usuarios

SET
estadoCuenta = 'RECHAZADA',
aprobadoAdmin = 'NO'

WHERE codUsuario = $id

";

mysqli_query(
    $link,
    $sql
);

/*
    Enviar Mail
*/

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
        'Sistema de Vuelos'
    );

    $mail->addAddress($email);

    $mail->isHTML(true);

    $mail->Subject =
    'Solicitud rechazada';

    $mail->Body = "

    <h2>Hola $nombre</h2>

    <p>

    Tu solicitud como CEO fue rechazada
    por un administrador.

    </p>

    <p>

    Si considerás que se trata de un error,
    comunicate con el administrador.

    </p>

    ";

    $mail->send();
}
catch(Exception $e)
{
}

/*
    Volver
*/

header(
    "Location: listar.php"
);

exit();

?>