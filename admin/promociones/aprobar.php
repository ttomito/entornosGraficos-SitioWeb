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
    AND u.tipoUsuario = 'CEO'";

$stmt = mysqli_prepare($link, $consulta);

if (!$stmt) {
    error_log("Error al preparar la consulta: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$filas = $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : [];
mysqli_stmt_close($stmt);

if (empty($filas)) {
    header("Location: listar.php?alerta=no_encontrada");
    exit();
}

$codAerolinea = (int)$filas[0]['codAerolinea'];

mysqli_begin_transaction($link);
$exito = true;

// Denegar cualquier otra promoción aprobada de la misma aerolínea
$sqlDenegar = "UPDATE promociones
    SET estadoPromocion = 'DENEGADA'
    WHERE codAerolinea = ?
    AND estadoPromocion = 'APROBADA'";

$stmtDenegar = mysqli_prepare($link, $sqlDenegar);

if (!$stmtDenegar) {
    $exito = false;
} else {
    mysqli_stmt_bind_param($stmtDenegar, "i", $codAerolinea);
    $exito = mysqli_stmt_execute($stmtDenegar);
    mysqli_stmt_close($stmtDenegar);
}

// Aprobar la promoción actual, solo si sigue pendiente
if ($exito) {
    $sqlAprobar = "UPDATE promociones
        SET estadoPromocion = 'APROBADA'
        WHERE codPromocion = ?
        AND estadoPromocion = 'PENDIENTE'";

    $stmtAprobar = mysqli_prepare($link, $sqlAprobar);

    if (!$stmtAprobar) {
        $exito = false;
    } else {
        mysqli_stmt_bind_param($stmtAprobar, "i", $id);
        $exito = mysqli_stmt_execute($stmtAprobar);

        if ($exito && mysqli_stmt_affected_rows($stmtAprobar) === 0) {
            // Ya no estaba pendiente
            $exito = false;
        }

        mysqli_stmt_close($stmtAprobar);
    }
}

if (!$exito) {
    mysqli_rollback($link);
    error_log("Error al aprobar promoción (id=$id): " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_commit($link);

// Enviar el aviso a cada CEO de la aerolínea
foreach ($filas as $datos) {
    if (empty($datos['emailUsuario'])) {
        continue;
    }

    enviarMail(
        $datos['emailUsuario'],
        'Promoción aprobada',
        "<h2>Hola " . htmlspecialchars($datos['nombreUsuario'], ENT_QUOTES, 'UTF-8') . "</h2>
        <p>Tu promoción fue aprobada por el administrador.</p>
        <p>Ya se encuentra disponible en el sistema.</p>"
    );
}

header("Location: listar.php?alerta=aprobada");
exit();
