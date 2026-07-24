<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 
require '../../vendor/autoload.php';
 
include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
 
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
 
if ($id <= 0) {
    header("Location: listar.php");
    exit();
}
 
 
$consulta = "
 
SELECT
p.*,
u.nombreUsuario,
u.emailUsuario
 
FROM promociones p
 
INNER JOIN usuarios u
ON p.codAerolinea = u.codAerolinea
 
WHERE p.codPromocion = ?
 
";
 
$stmt = mysqli_prepare($link, $consulta);
 
if (!$stmt) {
    error_log("Error al preparar la consulta: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}
 
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
 
$resultado = mysqli_stmt_get_result($stmt);
$datos = $resultado ? mysqli_fetch_assoc($resultado) : null;
mysqli_stmt_close($stmt);
 
if (!$datos) {
    header("Location: listar.php?alerta=no_encontrada");
    exit();
}
 
$codAerolinea = (int)$datos['codAerolinea'];
$nombre = $datos['nombreUsuario'];
$email = $datos['emailUsuario'];
 
/*
| Denegar promociones aprobadas anteriormente para esa aerolínea
*/
 
$sqlDenegar = "
 
UPDATE promociones
 
SET estadoPromocion = 'DENEGADA'
 
WHERE codAerolinea = ?
 
AND estadoPromocion = 'APROBADA'
 
";
 
$stmtDenegar = mysqli_prepare($link, $sqlDenegar);
 
if ($stmtDenegar) {
    mysqli_stmt_bind_param($stmtDenegar, "i", $codAerolinea);
    mysqli_stmt_execute($stmtDenegar);
    mysqli_stmt_close($stmtDenegar);
}
 
/*
| Aprobar la promoción actual
*/
 
$sqlAprobar = "
 
UPDATE promociones
 
SET estadoPromocion = 'APROBADA'
 
WHERE codPromocion = ?
 
";
 
$stmtAprobar = mysqli_prepare($link, $sqlAprobar);
 
if (!$stmtAprobar) {
    error_log("Error al preparar la aprobación: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}
 
mysqli_stmt_bind_param($stmtAprobar, "i", $id);
$aprobacionExitosa = mysqli_stmt_execute($stmtAprobar);
mysqli_stmt_close($stmtAprobar);
 
if (!$aprobacionExitosa) {
    error_log("Error al aprobar promoción: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}
 

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