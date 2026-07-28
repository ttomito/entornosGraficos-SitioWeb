<?php

require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/mailer.php';
include '../includes/rutas.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: recuperar.php');
    exit();
}

$email = mb_strtolower(trim($_POST['email'] ?? ''));

if (mb_strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: recuperar.php?invalido=1');
    exit();
}

$stmt = mysqli_prepare(
    $link,
    'SELECT codUsuario, nombreUsuario, estadoCuenta
     FROM usuarios
     WHERE emailUsuario = ?
     LIMIT 1'
);
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario   = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if ($usuario === null || $usuario['estadoCuenta'] === 'RECHAZADA') {
    header('Location: recuperar.php?ok=1');
    exit();
}

$token      = bin2hex(random_bytes(32));
$codUsuario = (int) $usuario['codUsuario'];

$stmt = mysqli_prepare(
    $link,
    'UPDATE usuarios
     SET tokenRecuperacion = ?,
         tokenRecuperacionExpira = NOW() + INTERVAL 60 MINUTE
     WHERE codUsuario = ?'
);
mysqli_stmt_bind_param($stmt, 'si', $token, $codUsuario);
$guardado = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$guardado) {
    // Sin el token en la base, el enlace del mail llegaría muerto
    error_log('Error al guardar el token de recuperación: ' . mysqli_error($link));
    header('Location: recuperar.php?error=1');
    exit();
}


$linkRecuperacion = ruta.'/auth/restablecer.php?token=' . urlencode($token);
$nombreEscapado = htmlspecialchars($usuario['nombreUsuario'], ENT_QUOTES, 'UTF-8');

$cuerpo = '
    <h2>Hola ' . $nombreEscapado . '</h2>
    <p>
        Recibimos un pedido para cambiar la contraseña de tu cuenta.
        Hacé clic en el siguiente enlace, que estará disponible durante 60 minutos:
    </p>
    <p><a href="' . $linkRecuperacion . '">Cambiar mi contraseña</a></p>
    <p style="font-size:12px;color:#666">
        Si no pediste este cambio, ignorá este mensaje: tu contraseña actual sigue siendo válida.
    </p>';

if (!enviarMail($email, 'Recuperación de contraseña', $cuerpo)) {
    $stmt = mysqli_prepare(
        $link,
        'UPDATE usuarios
         SET tokenRecuperacion = NULL, tokenRecuperacionExpira = NULL
         WHERE codUsuario = ?'
    );
    mysqli_stmt_bind_param($stmt, 'i', $codUsuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    error_log("No se pudo enviar el mail de recuperación a $email.");
    header('Location: recuperar.php?error=1');
    exit();
}

header('Location: recuperar.php?ok=1');
exit();
