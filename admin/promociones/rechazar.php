<?php

include("../../includes/verificarSessionAdmin.php");
include("../../includes/conexion.php");
include("../../includes/mailer.php");

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

$consulta = "SELECT p.*, u.nombreUsuario, u.emailUsuario
    FROM promociones p
    INNER JOIN usuarios u ON p.codAerolinea = u.codAerolinea
    WHERE p.codPromocion = ?
    AND u.tipoUsuario = 'CEO' ";

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

$nombre = $datos['nombreUsuario'];
$email = $datos['emailUsuario'];

// Solo se rechaza si sigue pendiente
$sql = "UPDATE promociones
    SET estadoPromocion = 'DENEGADA'
    WHERE codPromocion = ?
    AND estadoPromocion = 'PENDIENTE' ";

$stmtRechazar = mysqli_prepare($link, $sql);

if (!$stmtRechazar) {
    error_log("Error al preparar el rechazo: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmtRechazar, "i", $id);
$rechazoExitoso = mysqli_stmt_execute($stmtRechazar);
$afectadas = $rechazoExitoso ? mysqli_stmt_affected_rows($stmtRechazar) : 0;
mysqli_stmt_close($stmtRechazar);

if (!$rechazoExitoso) {
    error_log("Error al rechazar promoción: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

if ($afectadas === 0) {
    // Ya no estaba pendiente
    header("Location: listar.php?alerta=no_encontrada");
    exit();
}

enviarMail(
    $email,
    'Promoción rechazada',
    "<h2>Hola " . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . "</h2>
    <p>Tu promoción fue rechazada por el administrador.</p>
    <p>Podés modificarla y volver a enviarla para revisión.</p>"
);

header("Location: listar.php?alerta=rechazada");
exit();