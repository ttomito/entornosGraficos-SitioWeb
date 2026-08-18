<?php

include("../../includes/verificarSessionAdmin.php");
include("../../includes/conexion.php");

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header("Location: crear.php?alerta=error_servidor");
    exit();
}

$tituloNovedad = isset($_POST['tituloNovedad']) ? trim($_POST['tituloNovedad']) : '';
$texto = isset($_POST['texto']) ? trim($_POST['texto']) : '';
$publicacion = isset($_POST['publicacion']) ? trim($_POST['publicacion']) : '';
$expiracion = isset($_POST['expiracion']) ? trim($_POST['expiracion']) : '';

if ($texto === '' || $publicacion === '' || $expiracion === '' || $tituloNovedad === '') {
    header("Location: crear.php?alerta=campos_vacios");
    exit();
}

if (mb_strlen($tituloNovedad) < 3) {
    header("Location: crear.php?alerta=titulo_corto");
    exit();
}

if (mb_strlen($tituloNovedad) > 100) {
    header("Location: crear.php?alerta=titulo_largo");
    exit();
}

if (mb_strlen($texto) > 500) {
    header("Location: crear.php?alerta=texto_largo");
    exit();
}

// La imagen es obligatoria: si no llegó ningún archivo, no seguimos.
if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
    header("Location: crear.php?alerta=imagen_requerida");
    exit();
}

if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    header("Location: crear.php?alerta=error_imagen");
    exit();
}

$carpeta = "../../uploads/novedades/";

if (!is_dir($carpeta)) {
    mkdir($carpeta, 0755, true);
}

$maxTamanio = 3 * 1024 * 1024; // 3MB
if ($_FILES['imagen']['size'] > $maxTamanio) {
    header("Location: crear.php?alerta=imagen_muy_grande");
    exit();
}

$extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
$extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

if (!in_array($extension, $extensionesPermitidas)) {
    header("Location: crear.php?alerta=imagen_invalida");
    exit();
}

// Nombre único
$nombreImagen = uniqid('novedad_', true) . '.' . $extension;

if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $carpeta . $nombreImagen)) {
    header("Location: crear.php?alerta=error_imagen");
    exit();
}

$sql = "INSERT INTO novedades (textoNovedad, fechaPublicacion, fechaExpiracion, tituloNovedad, imagen)
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    error_log("Error al preparar la consulta: " . mysqli_error($link));
    header("Location: crear.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmt, "sssss", $texto, $publicacion, $expiracion, $tituloNovedad, $nombreImagen);

$resultado = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$resultado) {
    error_log("Error al crear novedad: " . mysqli_error($link));
    header("Location: crear.php?alerta=error_servidor");
    exit();
}

header("Location: listar.php?alerta=creada");
exit();