<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id = $_GET['id'];

/*
    Obtener CEO
*/

$consulta = "

SELECT
p.*,
u.nombreUsuario,
u.emailUsuario

FROM promociones p

INNER JOIN usuarios u
ON p.codAerolinea = u.codAerolinea

WHERE p.codPromocion = $id

";

$resultado = mysqli_query(
    $link,
    $consulta
);

$datos = mysqli_fetch_assoc(
    $resultado
);

$nombre = $datos['nombreUsuario'];

$email = $datos['emailUsuario'];

/*
    Rechazar promoción
*/

$sql = "

UPDATE promociones

SET estadoPromocion = 'DENEGADA'

WHERE codPromocion = $id

";

mysqli_query(
    $link,
    $sql
);

/*
    Mail
*/

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
        'Sistema de Vuelos'
    );

    $mail->addAddress($email);

    $mail->isHTML(true);

    $mail->Subject =
    'Promoción rechazada';

    $mail->Body = "

    <h2>Hola $nombre</h2>

    <p>

    Tu promoción fue rechazada por el administrador.

    </p>

    <p>

    Podés modificarla y volver a enviarla para revisión.

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