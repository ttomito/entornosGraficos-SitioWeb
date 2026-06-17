<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id = $_GET['id'];

/*
    Obtener datos promoción y CEO
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

$codAerolinea = $datos['codAerolinea'];

$nombre = $datos['nombreUsuario'];

$email = $datos['emailUsuario'];

/*
    Denegar promociones aprobadas anteriores
*/

$sql = "

UPDATE promociones

SET estadoPromocion = 'DENEGADA'

WHERE codAerolinea = $codAerolinea

AND estadoPromocion = 'APROBADA'

";

mysqli_query(
    $link,
    $sql
);

/*
    Aprobar promoción actual
*/

$sql = "

UPDATE promociones

SET estadoPromocion = 'APROBADA'

WHERE codPromocion = $id

";

mysqli_query(
    $link,
    $sql
);

/*
    Mail al CEO
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
    'Promoción aprobada';

    $mail->Body = "

    <h2>Hola $nombre</h2>

    <p>

    Tu promoción fue aprobada por el administrador.

    </p>

    <p>

    Ya se encuentra disponible en el sistema.

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