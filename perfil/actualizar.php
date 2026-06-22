<?php

include("../includes/verificarSession.php");
include("../includes/conexion.php");

$id = $_SESSION['id'];

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

$nombre = $_POST['nombre'];
$telefono = $_POST['telefono'];
$clave = $_POST['clave'];



if($clave != ""){
    if (!empty($clave)){
        $sql = "UPDATE usuarios SET nombreUsuario = '$nombre', telefonoUsuario = '$telefono', claveUsuario = '$clave' WHERE codUsuario = $id ";
    }
}
else {
    if(empty($clave)){
        $sql = "UPDATE usuarios SET nombreUsuario = '$nombre', telefonoUsuario = '$telefono' WHERE codUsuario = $id";
    }
}

$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    error_log("Error al actualizar perfil: " . mysqli_stmt_error($stmt));
    header("Location: index.php?alerta=error_servidor");
    exit();
} else {
    header("Location: index.php?alerta=actualizado");
    $_SESSION['nombre'] = $nombre;
    exit();
}



?>