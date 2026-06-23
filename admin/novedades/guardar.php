<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$tituloNovedad = $_POST['tituloNovedad'];
$texto = $_POST['texto'];
$publicacion = $_POST['publicacion'];
$expiracion = $_POST['expiracion'];

if (empty($texto) || empty($publicacion) || empty($expiracion) || empty($tituloNovedad)) {
    header("Location: crear.php?alerta=campos_vacios");
    exit();
}

// imagen
$nombreImagen = null;

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

    $carpeta = "../../uploads/novedades/";

    // Crear la carpeta si no existe
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    //  tamaño (máx 3MB)
    $maxTamanio = 3 * 1024 * 1024; // 3MB en bytes
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

    // Nombre único para evitar colisiones
    $nombreImagen = uniqid('novedad_', true) . '.' . $extension;

    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $carpeta . $nombreImagen)) {
        header("Location: listar.php?alerta=error_imagen");
        exit();
    }
}

$sql = "INSERT INTO novedades (textoNovedad, fechaPublicacion, fechaExpiracion, tituloNovedad, imagen)
        VALUES ('$texto', '$publicacion', '$expiracion', '$tituloNovedad', '$nombreImagen')";

$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    header("Location: listar.php?alerta=error_servidor");
    die("Error en la consulta: " . mysqli_error($link));
    exit();
} else {
    header("Location: listar.php?alerta=creada");
    exit();
}

?>