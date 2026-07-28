<?php

session_start();

require_once __DIR__ . '/../includes/conexion.php';

// Solo se accede por POST desde el formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$email = mb_strtolower(trim($_POST['email'] ?? ''));
$clave = $_POST['clave'] ?? '';

$emailValido = (mb_strlen($email) <= 100) && filter_var($email, FILTER_VALIDATE_EMAIL);
$claveValida = $clave !== '';

if (!$emailValido || !$claveValida) {
    header('Location: login.php?error=1');
    exit();
}

$sql = 'SELECT codUsuario, nombreUsuario, apellidoUsuario, claveUsuario,
               tipoUsuario, estadoCuenta, aprobadoAdmin
        FROM usuarios
        WHERE emailUsuario = ?
        LIMIT 1';

$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario   = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if ($usuario === null) {
    password_verify($clave, '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30M1p1DR.0u6');
    header('Location: login.php?error=1');
    exit();
}

if (!password_verify($clave, $usuario['claveUsuario'])) {
    header('Location: login.php?error=1');
    exit();
}

if ($usuario['estadoCuenta'] === 'RECHAZADA') {
    header('Location: login.php?rechazada=1');
    exit();
}

if (in_array($usuario['tipoUsuario'], ['CLIENTE', 'CEO'], true)
    && $usuario['estadoCuenta'] !== 'ACTIVA') {
    header('Location: login.php?pendiente=1');
    exit();
}

if ($usuario['tipoUsuario'] === 'CEO' && $usuario['aprobadoAdmin'] === 'NO') {
    header('Location: login.php?esperando=1');
    exit();
}

session_regenerate_id(true);

$_SESSION['id']     = $usuario['codUsuario'];
$_SESSION['nombre'] = $usuario['nombreUsuario'] . ' ' . $usuario['apellidoUsuario'];
$_SESSION['tipo']   = $usuario['tipoUsuario'];

switch ($usuario['tipoUsuario']) {
    case 'ADMIN':
        header('Location: ../admin/dashboard.php');
        break;
    case 'CEO':
        header('Location: ../ceo/dashboard.php');
        break;
    default:
        header('Location: ../cliente/dashboard.php');
        break;
}

exit();