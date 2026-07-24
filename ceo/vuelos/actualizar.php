<?php

session_start();

include("../../includes/conexion.php");

$idCEO = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0;
$idVuelo = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($idCEO <= 0 || $idVuelo <= 0) {
    header("Location: listar.php");
    exit();
}

/*
| Validar que el vuelo exista y pertenezca a este CEO
*/

$sqlValidacion = "SELECT v.*
FROM vuelos v
INNER JOIN usuarios u
ON v.codAerolinea = u.codAerolinea
WHERE v.codVuelo = ?
AND u.codUsuario = ?";

$stmtValidacion = mysqli_prepare($link, $sqlValidacion);

if (!$stmtValidacion) {
    error_log("Error al preparar la validación: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmtValidacion, "ii", $idVuelo, $idCEO);
mysqli_stmt_execute($stmtValidacion);

$resultadoValidacion = mysqli_stmt_get_result($stmtValidacion);
$vueloActual = $resultadoValidacion ? mysqli_fetch_assoc($resultadoValidacion) : null;
mysqli_stmt_close($stmtValidacion);

if (!$vueloActual) {
    header("Location: listar.php?alerta=acceso_denegado");
    exit();
}

$imagenActual = $vueloActual['imagenVuelo'];

$origen = isset($_POST['origen']) ? trim($_POST['origen']) : '';
$destino = isset($_POST['destino']) ? trim($_POST['destino']) : '';
$fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
$hora = isset($_POST['hora']) ? trim($_POST['hora']) : '';
$precio = isset($_POST['precio']) ? trim($_POST['precio']) : '';
$asientos = isset($_POST['asientos']) ? trim($_POST['asientos']) : '';

if (empty($origen) || empty($destino) || empty($fecha) || empty($hora) || empty($precio) || empty($asientos)) {
    header("Location: editar.php?id=$idVuelo&alerta=campos_vacios");
    exit();
}

if (mb_strlen($origen) < 3 || mb_strlen($origen) > 50 || !preg_match('/^[A-Za-zÀ-ÿ\s]+$/u', $origen)) {
    header("Location: editar.php?id=$idVuelo&alerta=origen_invalido");
    exit();
}

if (mb_strlen($destino) < 3 || mb_strlen($destino) > 50 || !preg_match('/^[A-Za-zÀ-ÿ\s]+$/u', $destino)) {
    header("Location: editar.php?id=$idVuelo&alerta=destino_invalido");
    exit();
}

$fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
$hoy = new DateTime('today');

if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha || $fechaObj < $hoy) {
    header("Location: editar.php?id=$idVuelo&alerta=fecha_invalida");
    exit();
}

if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d):([0-5]\d)$/', $hora)) {
    if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $hora)) {
        $hora .= ':00';
    } else {
        header("Location: editar.php?id=$idVuelo&alerta=hora_invalida");
        exit();
    }
}


if (!is_numeric($precio) || $precio < 50 || $precio > 5000000) {
    header("Location: editar.php?id=$idVuelo&alerta=precio_invalido");
    exit();
}

if (!ctype_digit((string)$asientos) || $asientos < 0 || $asientos > 500) {
    header("Location: editar.php?id=$idVuelo&alerta=asientos_invalidos");
    exit();
}

$precio = (float)$precio;
$asientos = (int)$asientos;

$nombreImagen = $imagenActual;

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

    $carpeta = "../../uploads/vuelos/";

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    $maxTamanio = 3 * 1024 * 1024; // 3MB
    if ($_FILES['imagen']['size'] > $maxTamanio) {
        header("Location: editar.php?id=$idVuelo&alerta=imagen_muy_grande");
        exit();
    }

    $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $extensionesPermitidas)) {
        header("Location: editar.php?id=$idVuelo&alerta=imagen_invalida");
        exit();
    }

    if (@getimagesize($_FILES['imagen']['tmp_name']) === false) {
        header("Location: editar.php?id=$idVuelo&alerta=imagen_invalida");
        exit();
    }

    $nombreImagen = uniqid('vuelo_', true) . '.' . $extension;

    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $carpeta . $nombreImagen)) {
        header("Location: editar.php?id=$idVuelo&alerta=error_imagen");
        exit();
    }

    $nombreImagenAnteriorSeguro = basename($imagenActual);

    if (!empty($nombreImagenAnteriorSeguro) && file_exists($carpeta . $nombreImagenAnteriorSeguro)) {
        unlink($carpeta . $nombreImagenAnteriorSeguro);
    }
}

$sql = "UPDATE vuelos
SET
origenVuelo = ?,
destinoVuelo = ?,
fechaVuelo = ?,
horaSalida = ?,
precioVuelo = ?,
asientosDisponibles = ?,
imagenVuelo = ?
WHERE codVuelo = ?";

$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    error_log("Error al preparar la actualización: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmt, "ssssdisi", $origen, $destino, $fecha, $hora, $precio, $asientos, $nombreImagen, $idVuelo);

$resultado = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$resultado) {
    error_log("Error al actualizar vuelo: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

header("Location: listar.php?alerta=actualizado");
exit();
