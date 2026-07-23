<?php

session_start();

include("../../includes/conexion.php");

$idCEO = $_SESSION['id'];
$idVuelo = $_POST['id'];

if ($idCEO <= 0 || $idVuelo <= 0) {
    header("Location: listar.php");
    die("Acceso denegado");
}

$sqlValidacion = "SELECT v.*
FROM vuelos v
INNER JOIN usuarios u
ON v.codAerolinea = u.codAerolinea
WHERE v.codVuelo = $idVuelo
AND u.codUsuario = $idCEO";

$validacion = mysqli_query($link, $sqlValidacion);

if (mysqli_num_rows($validacion) == 0) {
    header("Location: listar.php");
    die("Acceso denegado");
}

$origen = $_POST['origen'];
$destino = $_POST['destino'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];
$precio = $_POST['precio'];
$asientos = $_POST['asientos'];
$imagenActual = $_POST['imagenActual'];

if (empty($origen) || empty($destino) || empty($fecha) || empty($hora) || empty($precio) || empty($asientos)) {
    header("Location: editar.php?alerta=campos_vacios");
    exit();
}

$fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
$hoy = new DateTime('today');

if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha || $fechaObj < $hoy) {
    header("Location: editar.php?alerta=fecha_invalida");
    exit();
}

if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d):([0-5]\d)$/', $hora)) {
    if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $hora)) {
        $hora .= ':00';
    } else {
        header("Location: editar.php?alerta=hora_invalida");
        exit();
    }
}

if (!is_numeric($precio) || $precio < 0 || $precio > 5000000) {
    header("Location: editar.php?alerta=precio_invalido");
    exit();
}

if (!ctype_digit((string)$asientos) || $asientos < 0 || $asientos > 500) {
    header("Location: editar.php?alerta=asientos_invalidos");
    exit();
}

$nombreImagen = $imagenActual;

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

    $carpeta = "../../uploads/vuelos/";

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    $maxTamanio = 3 * 1024 * 1024; // 3MB
    if ($_FILES['imagen']['size'] > $maxTamanio) {
        header("Location: listar.php?alerta=imagen_muy_grande");
        exit();
    }

    $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!in_array($extension, $extensionesPermitidas)) {
        header("Location: listar.php?alerta=imagen_invalida");
        exit();
    }

    $nombreImagen = uniqid('vuelo_', true) . '.' . $extension;

    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $carpeta . $nombreImagen)) {
        header("Location: listar.php?alerta=error_imagen");
        exit();
    }

    if (!empty($imagenActual) && file_exists($carpeta . $imagenActual)) {
        unlink($carpeta . $imagenActual);
    }
}

$sql = "UPDATE vuelos
SET
origenVuelo = '$origen',
destinoVuelo = '$destino',
fechaVuelo = '$fecha',
horaSalida = '$hora',
precioVuelo = '$precio',
asientosDisponibles = '$asientos',
imagenVuelo = '$nombreImagen'
WHERE codVuelo = $idVuelo";

$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    header("Location: listar.php?alerta=error_servidor");
    exit();
} else {
    header("Location: listar.php?alerta=actualizado");
    exit();
}

?>