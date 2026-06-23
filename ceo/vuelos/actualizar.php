<?php

session_start();

include("../../includes/conexion.php");

$idCEO = $_SESSION['id'];
$idVuelo = $_POST['id'];

if($idCEO <= 0 || $idVuelo <=0){
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

if(mysqli_num_rows($validacion) == 0){
    header("Location: listar.php");
    die("Acceso denegado");
}

$origen = $_POST['origen'];
$destino = $_POST['destino'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];
$precio = $_POST['precio'];
$asientos = $_POST['asientos'];
$imagen = $_POST['imagen'];

if(empty($origen) || empty($destino) || empty($fecha) || empty($hora) || empty($precio) || empty($asientos) || empty($imagen)){
    header("Location: editar.php?alerta=campos_vacios");
    exit();
}

// formato y que la fecha sea hoy o futura
$fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
$hoy      = new DateTime('today');

if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha || $fechaObj < $hoy) {
    header("Location: listar.php?alerta=fecha_invalida");
    exit();
}

// formato de hora HH:MM:SS
if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d):([0-5]\d)$/', $hora)) {
    // Si el input HTML no manda segundos, completarlos
    if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $hora)) {
        $hora .= ':00';
    } else {
        header("Location: listar.php?alerta=hora_invalida");
        exit();
    }
}

// precio (0 a 5.000.000)
if (!is_numeric($precio) || $precio < 0 || $precio > 5000000) {
    header("Location: listar.php?alerta=precio_invalido");
    exit();
}

// asientos de 0 a 500
if (!ctype_digit((string)$asientos) || $asientos < 0 || $asientos > 500) {
    header("Location: listar.php?alerta=asientos_invalidos");
    exit();
}

$sql = "UPDATE vuelos
SET
origenVuelo = '{$_POST['origen']}',
destinoVuelo = '{$_POST['destino']}',
fechaVuelo = '{$_POST['fecha']}',
horaSalida = '{$_POST['hora']}',
precioVuelo = '{$_POST['precio']}',
asientosDisponibles = '{$_POST['asientos']}',
imagenVuelo = '{$_POST['imagen']}'
WHERE codVuelo = $idVuelo";

$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    error_log("Error al actualizar vuelo: " . mysqli_stmt_error($stmt));
    header("Location: editar.php?alerta=error_servidor");
    exit();
} else {
    header("Location: listar.php?alerta=actualizado");
    exit();
}
?>