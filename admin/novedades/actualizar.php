<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id = $_POST['id'];

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}

$tituloNovedad = $_POST['tituloNovedad'];
$texto = $_POST['texto'];
$publicacion = $_POST['publicacion'];
$expiracion = $_POST['expiracion'];
$imagenActual = $_POST['imagenActual'];

if (empty($tituloNovedad) || empty($texto) || empty($publicacion) || empty($expiracion)) {
    header("Location: listar.php?id=$id&alerta=campos_vacios");
    exit();
}

// imagen
$nombreImagen = $imagenActual; // por defecto conserva la actual

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

    $carpeta = "../../uploads/novedades/";

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    // tamaño máx 3MB
    $maxTamanio = 3 * 1024 * 1024;
    if ($_FILES['imagen']['size'] > $maxTamanio) {
        header("Location: listar.php?id=$id&alerta=imagen_muy_grande");
        exit();
    }

    $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!in_array($extension, $extensionesPermitidas)) {
        header("Location: listar.php?id=$id&alerta=imagen_invalida");
        exit();
    }

    $nombreImagen = uniqid('novedad_', true) . '.' . $extension;

    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $carpeta . $nombreImagen)) {
        header("Location: listar.php?id=$id&alerta=error_imagen");
        exit();
    }

    // borramos la anterior si existía
    if (!empty($imagenActual) && file_exists($carpeta . $imagenActual)) {
        unlink($carpeta . $imagenActual);
    }
}

$sql = "UPDATE novedades 
        SET textoNovedad = '$texto', fechaPublicacion = '$publicacion', fechaExpiracion = '$expiracion', tituloNovedad = '$tituloNovedad', imagen = '$nombreImagen'
        WHERE codNovedad = $id";

$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    header("Location: listar.php?alerta=error_servidor");
    die("Error en la consulta: " . mysqli_error($link));
    exit();
} else {
    header("Location: listar.php?alerta=actualizada");
    exit();
}

?>