<?php

session_start();

include("../../includes/conexion.php");

$idCEO = $_SESSION['id'];

$sqlCEO = "SELECT codAerolinea FROM usuarios WHERE codUsuario = $idCEO";

$resultadoCEO = mysqli_query($link,$sqlCEO);

if (!$resultadoCEO) {
    die(mysqli_error($link));
}

$ceo = mysqli_fetch_assoc($resultadoCEO);

$codAerolinea = $ceo['codAerolinea'];
$origen = $_POST['origen'];
$destino = $_POST['destino'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];
$precio = $_POST['precio'];
$asientos = $_POST['asientos'];
$imagen = $_POST['imagen'];

if(empty($codAerolinea) || empty($origen) || empty($destino) || empty($fecha) || empty($hora) || empty($precio) || empty($asientos) || empty($imagen)) {
    header("Location: listar.php?alerta=campos_vacios");
    die(mysqli_error($link));
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

$sql = "INSERT INTO vuelos (codAerolinea, origenVuelo, destinoVuelo, fechaVuelo, horaSalida, precioVuelo, asientosDisponibles, imagenVuelo )
VALUES ('$codAerolinea', '$origen', '$destino', '$fecha', '$hora', '$precio', '$asientos', '$imagen')";

$resultadoVuelo = mysqli_query($link,$sql);

if(!$resultadoVuelo){
    header("Location: listar.php?alerta=error_servidor");
    die(mysqli_error($link));
    exit();
} else {
    header("Location: listar.php?alerta=creado");
    exit();
}

?>