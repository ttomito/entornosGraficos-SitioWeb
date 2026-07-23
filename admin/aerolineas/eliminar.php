<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id']) || !isset($_GET['activo'])) {
    header("Location: listar.php");
    exit();
}

// Validaciones
$id = (int)$_GET['id'];
$activo = (int)$_GET['activo'];

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}

if ($activo !== 0 && $activo !== 1) {
    header("Location: listar.php");
    exit();
}

// Determinar nuevo estado
$modifActivo = ($activo == 1) ? 0 : 1;

// Buscar vuelos asociados
$sqlVuelos = "SELECT codVuelo FROM vuelos WHERE codAerolinea = $id";
$resultadoVuelos = mysqli_query($link, $sqlVuelos);

$tieneVuelos = mysqli_num_rows($resultadoVuelos) > 0;

if ($tieneVuelos) {

    while ($vuelo = mysqli_fetch_assoc($resultadoVuelos)) {

        $codVuelo = $vuelo['codVuelo'];

        $sqlActualizarReservas = "
            UPDATE reservas
            SET activo = $modifActivo
            WHERE codVuelo = $codVuelo
        ";

        mysqli_query($link, $sqlActualizarReservas);
    }

    $sqlActualizarVuelos = "
        UPDATE vuelos
        SET activo = $modifActivo
        WHERE codAerolinea = $id
    ";

    mysqli_query($link, $sqlActualizarVuelos);
}

// Actualizar la aerolínea SIEMPRE
$sqlActualizarAerolinea = "UPDATE aerolineas SET activo = $modifActivo WHERE codAerolinea = $id";

mysqli_query($link, $sqlActualizarAerolinea);

if ($modifActivo == 1) {

    if ($tieneVuelos) {
        header("Location: listar.php?alerta=vuelos_activados");
    } else {
        header("Location: listar.php?alerta=activada");
    }

} else {

    if ($tieneVuelos) {
        header("Location: listar.php?alerta=vuelos_desactivados");
    } else {
        header("Location: listar.php?alerta=eliminada");
    }

}

exit();