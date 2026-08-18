<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once(__DIR__ . "/conexion.php");

define('RUTA_LOGIN', '/entornosGraficos-SitioWeb/auth/login.php');
define('RUTA_INICIO', '/entornosGraficos-SitioWeb/index.php');

function cerrarSesionYRedirigir()
{
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    header("Location: " . RUTA_LOGIN);
    exit();
}

if (!isset($_SESSION['id'])) {
    header("Location: " . RUTA_LOGIN);
    exit();
}

$id = (int) $_SESSION['id'];

$sql = "SELECT * FROM usuarios WHERE codUsuario = ?";

$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    error_log("Error al verificar sesión: " . mysqli_error($link));
    cerrarSesionYRedirigir();
}

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

$usuarioSesion = $resultado
    ? mysqli_fetch_assoc($resultado)
    : null;

mysqli_stmt_close($stmt);

if (!$usuarioSesion) {
    cerrarSesionYRedirigir();
}

if (isset($usuarioSesion['tipo'])) {
    $_SESSION['tipo'] = $usuarioSesion['tipo'];
}

if (isset($usuarioSesion['nombre'])) {
    $_SESSION['nombre'] = $usuarioSesion['nombre'];
}

// Token CSRF único por sesión
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function exigirTipo(...$tiposPermitidos)
{
    $tipoActual = $_SESSION['tipo'] ?? null;

    if (!in_array($tipoActual, $tiposPermitidos, true)) {
        header("Location: " . RUTA_INICIO);
        exit();
    }
}
