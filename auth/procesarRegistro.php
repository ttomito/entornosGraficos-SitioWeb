<?php

require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/mailer.php';
include '../includes/rutas.php';

// Solo se accede por POST desde el formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registro.php');
    exit();
}

$nombre         = trim($_POST['nombre'] ?? '');
$apellido       = trim($_POST['apellido'] ?? '');
$dni            = trim($_POST['dni'] ?? '');
$email          = mb_strtolower(trim($_POST['email'] ?? ''));
$telefono       = trim($_POST['telefono'] ?? '');
$clave          = $_POST['clave'] ?? '';
$claveConfirmar = $_POST['claveConfirmar'] ?? '';
$tipoUsuario    = trim($_POST['tipoUsuario'] ?? '');

$errores = [];

if (!preg_match('/^[A-Za-zÀ-ÿ\s]{2,60}$/u', $nombre)) {
    $errores[] = 'nombre';
}

if (!preg_match('/^[A-Za-zÀ-ÿ\s]{2,60}$/u', $apellido)) {
    $errores[] = 'apellido';
}

if (!preg_match('/^\d{7,8}$/', $dni)) {
    $errores[] = 'dni';
}

if (!preg_match('/^[0-9+\-\s()]{6,20}$/', $telefono)) {
    $errores[] = 'telefono';
}

if (mb_strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'email';
}

if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $clave)) {
    $errores[] = 'clave';
}

if ($clave !== $claveConfirmar) {
    $errores[] = 'claveConfirmar';
}

if (!in_array($tipoUsuario, ['CLIENTE', 'CEO'], true)) {
    $errores[] = 'tipoUsuario';
}

if (!empty($errores)) {
    header('Location: registro.php?invalido=1');
    exit();
}

$stmt = mysqli_prepare($link, 'SELECT emailUsuario FROM usuarios WHERE emailUsuario = ? OR dniUsuario = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'ss', $email, $dni);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
$yaExiste = mysqli_stmt_num_rows($stmt) > 0;
mysqli_stmt_close($stmt);

if ($yaExiste) {
    header('Location: registro.php?existe=1');
    exit();
}

$token         = bin2hex(random_bytes(32));
$claveHash     = password_hash($clave, PASSWORD_DEFAULT);
$estado        = 'PENDIENTE';
$aprobadoAdmin = ($tipoUsuario === 'CEO') ? 'NO' : 'SI';

$sql = 'INSERT INTO usuarios
        (nombreUsuario, apellidoUsuario, dniUsuario, emailUsuario, claveUsuario,
         telefonoUsuario, tipoUsuario, estadoCuenta, tokenValidacion, aprobadoAdmin)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param(
    $stmt,
    'ssssssssss',
    $nombre,
    $apellido,
    $dni,
    $email,
    $claveHash,
    $telefono,
    $tipoUsuario,
    $estado,
    $token,
    $aprobadoAdmin
);

if (!mysqli_stmt_execute($stmt)) {
    error_log('Error al insertar usuario: ' . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    header('Location: registro.php?error=1');
    exit();
}

mysqli_stmt_close($stmt);

$linkValidacion = ruta.'/auth/validar.php?token=' . urlencode($token);

$cuerpo = '
    <h2>Bienvenido, ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . '</h2>
    <p>Para activar tu cuenta hacé clic en el siguiente enlace:</p>
    <p><a href="' . $linkValidacion . '">Validar cuenta</a></p>
    <p style="font-size:12px;color:#666">
        Si no te registraste en el Sistema de Vuelos, ignorá este mensaje.
    </p>
';

if (!enviarMail($email, 'Validación de cuenta', $cuerpo)) {
    $stmt = mysqli_prepare($link, 'DELETE FROM usuarios WHERE tokenValidacion = ? AND estadoCuenta = "PENDIENTE"');
    mysqli_stmt_bind_param($stmt, 's', $token);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    error_log("No se pudo enviar el mail de validación a $email. Registro revertido.");
    header('Location: registro.php?mailerror=1');
    exit();
}

header('Location: registro.php?' . ($tipoUsuario === 'CEO' ? 'ceo=1' : 'exito=1'));
exit();
