<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/mailer.php';

// Solo se accede por POST desde el formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contacto.php');
    exit();
}

// Trampa para bots: el campo está oculto por CSS, así que si viene con
// contenido no lo completó una persona. Se responde con el mismo ?ok=1
// del caso exitoso para no darle pistas al robot de que fue detectado.
if (trim($_POST['sitioWeb'] ?? '') !== '') {
    header('Location: contacto.php?ok=1');
    exit();
}

$nombre  = trim($_POST['nombre'] ?? '');
$email   = mb_strtolower(trim($_POST['email'] ?? ''));
$mensaje = trim($_POST['mensaje'] ?? '');

// ---------------------------------------------------------------
// Validación
// Los maxlength del HTML son solo una ayuda visual: cualquiera puede
// mandar un POST sin pasar por el formulario, así que se revalida acá.
// ---------------------------------------------------------------
$errores = [];

if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 60) {
    $errores[] = 'nombre';
}

if (mb_strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'email';
}

if (mb_strlen($mensaje) < 10 || mb_strlen($mensaje) > 2000) {
    $errores[] = 'mensaje';
}

if (!empty($errores)) {
    $_SESSION['contacto_valores'] = compact('nombre', 'email', 'mensaje');
    header('Location: contacto.php?invalido=1');
    exit();
}

// ---------------------------------------------------------------
// Armado del cuerpo
// Todo lo que escribió el visitante se escapa antes de entrar al HTML.
// Sin esto, cualquiera podría mandar un mail con enlaces y botones que
// llegaría a la casilla desde nuestra propia dirección.
// ---------------------------------------------------------------
$nombreEscapado  = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
$emailEscapado   = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$mensajeEscapado = nl2br(htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'));

$cuerpo = '
    <h2>Nuevo mensaje de contacto</h2>
    <p><b>Nombre:</b> ' . $nombreEscapado . '</p>
    <p><b>Email:</b> <a href="mailto:' . $emailEscapado . '">' . $emailEscapado . '</a></p>
    <p><b>Mensaje:</b></p>
    <p>' . $mensajeEscapado . '</p>
    <hr>
    <p style="font-size:12px;color:#666">
        Enviado desde el formulario de contacto el ' . date('d/m/Y H:i') . '.
        Para responder, usá el enlace del email de arriba.
    </p>
';

// Los saltos de línea se quitan del asunto: una cabecera de mail termina
// en el salto de línea, así que dejarlos pasar permitiría inyectar otras.
$asunto = 'Contacto web: ' . preg_replace('/[\r\n]+/', ' ', $nombre);

// Se envía a la casilla del sistema con la firma de tres argumentos de
// enviarMail(). El correo del visitante viaja dentro del cuerpo, como
// enlace mailto, en lugar de ir en la cabecera Reply-To.
if (!enviarMail(MAIL_USERNAME, $asunto, $cuerpo)) {
    $_SESSION['contacto_valores'] = compact('nombre', 'email', 'mensaje');
    error_log("No se pudo enviar el mensaje de contacto de $email.");
    header('Location: contacto.php?error=1');
    exit();
}

header('Location: contacto.php?ok=1');
exit();
