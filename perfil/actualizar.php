<?php

include("../includes/verificarSession.php");
include("../includes/conexion.php");

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header("Location: index.php?alerta=error_servidor");
    exit();
}

$id = (int)($_SESSION['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
$dni = isset($_POST['dni']) ? trim($_POST['dni']) : '';
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
$clave = isset($_POST['clave']) ? $_POST['clave'] : '';
$claveConfirmacion = isset($_POST['clave_confirmacion']) ? $_POST['clave_confirmacion'] : '';

if ($nombre === '' || mb_strlen($nombre) > 60) {
    header("Location: index.php?alerta=datos_invalidos");
    exit();
}

if ($apellido === '' || mb_strlen($apellido) > 60) {
    header("Location: index.php?alerta=datos_invalidos");
    exit();
}

if (!preg_match('/^\d{7,8}$/', $dni)) {
    header("Location: index.php?alerta=dni_invalido");
    exit();
}

if (!preg_match('/^[\d\s\-\+\(\)]{6,20}$/', $telefono)) {
    header("Location: index.php?alerta=telefono_invalido");
    exit();
}

if ($clave !== '' && strlen($clave) < 8) {
    header("Location: index.php?alerta=clave_corta");
    exit();
}

if ($clave !== '' && $clave !== $claveConfirmacion) {
    header("Location: index.php?alerta=clave_no_coincide");
    exit();
}

// Debe contener al menos una letra y un número (igual que el pattern del input en index.php),
// además de respetar el mismo juego de caracteres permitidos.
if ($clave !== '' && !preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*()_+\-=\[\]{};:\'",.<>\/?`~\\\\]{8,}$/', $clave)) {
    header("Location: index.php?alerta=clave_invalida");
    exit();
}

$sqlVerificar = "SELECT codUsuario FROM usuarios WHERE dniUsuario = ? AND codUsuario <> ?";
$stmtVerificar = mysqli_prepare($link, $sqlVerificar);

if (!$stmtVerificar) {
    error_log("Error al preparar la verificación de DNI: " . mysqli_error($link));
    header("Location: index.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmtVerificar, "si", $dni, $id);
mysqli_stmt_execute($stmtVerificar);
mysqli_stmt_store_result($stmtVerificar);

if (mysqli_stmt_num_rows($stmtVerificar) > 0) {
    mysqli_stmt_close($stmtVerificar);
    header("Location: index.php?alerta=dni");
    exit();
}

mysqli_stmt_close($stmtVerificar);

if ($clave !== '') {

    $claveHasheada = password_hash($clave, PASSWORD_DEFAULT);

    $sql = "UPDATE usuarios SET
        nombreUsuario = ?,
        apellidoUsuario = ?,
        dniUsuario = ?,
        telefonoUsuario = ?,
        claveUsuario = ?
        WHERE codUsuario = ?";

    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssi", $nombre, $apellido, $dni, $telefono, $claveHasheada, $id);
    }
} else {

    $sql = "UPDATE usuarios SET
        nombreUsuario = ?,
        apellidoUsuario = ?,
        dniUsuario = ?,
        telefonoUsuario = ?
        WHERE codUsuario = ?";

    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssi", $nombre, $apellido, $dni, $telefono, $id);
    }
}

if (!$stmt) {
    error_log("Error al preparar la actualización de perfil: " . mysqli_error($link));
    header("Location: index.php?alerta=error_servidor");
    exit();
}

$resultado = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$resultado) {
    error_log("Error al actualizar perfil (id=$id): " . mysqli_error($link));
    header("Location: index.php?alerta=error_servidor");
    exit();
}

$_SESSION['nombre'] = $nombre . " " . $apellido;
header("Location: index.php?alerta=actualizado");
exit();