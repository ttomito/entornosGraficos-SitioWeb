<?php

include("../../includes/verificarSessionAdmin.php");
include("../../includes/conexion.php");
include("../../includes/mailer.php");
include("../../includes/rutas.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listar.php");
    exit();
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}

$consulta = "SELECT * FROM usuarios WHERE codUsuario = ? AND tipoUsuario = 'CEO' AND estadoCuenta = 'ACTIVA' ";

$stmt = mysqli_prepare($link, $consulta);

if (!$stmt) {
    error_log("Error al preparar la consulta: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$usuario = $resultado ? mysqli_fetch_assoc($resultado) : null;
mysqli_stmt_close($stmt);

if (!$usuario) {
    header("Location: listar.php?alerta=no_encontrada");
    exit();
}

$email = $usuario['emailUsuario'];
$nombre = $usuario['nombreUsuario'];

$sql = "UPDATE usuarios SET aprobadoAdmin = 'SI' WHERE codUsuario = ? AND estadoCuenta = 'ACTIVA' ";

$stmtAprobar = mysqli_prepare($link, $sql);

if (!$stmtAprobar) {
    error_log("Error al preparar la aprobación: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmtAprobar, "i", $id);
$exito = mysqli_stmt_execute($stmtAprobar);
$afectadas = $exito ? mysqli_stmt_affected_rows($stmtAprobar) : 0;
mysqli_stmt_close($stmtAprobar);

if (!$exito) {
    error_log("Error al aprobar CEO: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

if ($afectadas === 0) {
    header("Location: listar.php?alerta=no_encontrada");
    exit();
}

$linkLogin = ruta.'/auth/login.php';
$nombreEscapado = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');

enviarMail(
    $email,
    'Solicitud aprobada',
    "<h2>Hola $nombreEscapado</h2>
    <p>Tu solicitud como CEO fue aprobada por un administrador.</p>
    <p>Ya podés iniciar sesión en el sistema.</p>
    <p>
        <a href='$linkLogin' style='background:#0d6efd;color:white;padding:12px 20px;text-decoration:none;border-radius:6px;display:inline-block;font-weight:bold;'>
            Ingresar al Sistema
        </a>
    </p>
    <p>
        Si el botón no funciona, podés copiar este enlace:
        <br><br>
        $linkLogin
    </p>"
);

header("Location: listar.php?alerta=aprobada");
exit();
