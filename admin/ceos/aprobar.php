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
    Aprobar CEO
*/

$sql = "

UPDATE usuarios

SET
estadoCuenta = 'ACTIVA',
aprobadoAdmin = 'SI'

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
    'Solicitud aprobada';

   $linkLogin = "http://localhost/entornosGraficos-SitioWeb/auth/login.php";

$mail->Body = "

<h2>Hola $nombre</h2>

<p>

Tu solicitud como CEO fue aprobada por un administrador.

</p>

<p>

Ya podés iniciar sesión en el sistema.

</p>

<p>

<a
href='$linkLogin'
style='
background:#0d6efd;
color:white;
padding:12px 20px;
text-decoration:none;
border-radius:6px;
display:inline-block;
font-weight:bold;
'>

Ingresar al Sistema

</a>

</p>

<p>

Si el botón no funciona, podés copiar este enlace:

<br><br>

$linkLogin

</p>

";

    $mail->send();
}
catch(Exception $e)
{
}

header(
    "Location: listar.php"
);

exit();

?>