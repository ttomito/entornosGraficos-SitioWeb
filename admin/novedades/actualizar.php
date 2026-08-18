<?php

include("../../includes/verificarSessionAdmin.php");
include("../../includes/conexion.php");

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}
$sqlActual = "SELECT imagen FROM novedades WHERE codNovedad = ?";
$stmtActual = mysqli_prepare($link, $sqlActual);

if (!$stmtActual) {
    error_log("Error al preparar la consulta: " . mysqli_error($link));
    header("Location: editar.php?id=$id&alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmtActual, "i", $id);
mysqli_stmt_execute($stmtActual);
$resultadoActual = mysqli_stmt_get_result($stmtActual);
$novedadActual = $resultadoActual ? mysqli_fetch_assoc($resultadoActual) : null;
mysqli_stmt_close($stmtActual);

if (!$novedadActual) {
    header("Location: listar.php?alerta=no_encontrada");
    exit();
}

$imagenActual = $novedadActual['imagen'];

$tituloNovedad = isset($_POST['tituloNovedad']) ? trim($_POST['tituloNovedad']) : '';
$texto = isset($_POST['texto']) ? trim($_POST['texto']) : '';
$publicacion = isset($_POST['publicacion']) ? trim($_POST['publicacion']) : '';
$expiracion = isset($_POST['expiracion']) ? trim($_POST['expiracion']) : '';

if ($tituloNovedad === '' || $texto === '' || $publicacion === '' || $expiracion === '') {
    header("Location: editar.php?id=$id&alerta=campos_vacios");
    exit();
}

if (mb_strlen($tituloNovedad) < 3) {
    header("Location: editar.php?id=$id&alerta=titulo_corto");
    exit();
}

if (mb_strlen($tituloNovedad) > 100) {
    header("Location: editar.php?id=$id&alerta=titulo_largo");
    exit();
}

if (mb_strlen($texto) > 500) {
    header("Location: editar.php?id=$id&alerta=texto_largo");
    exit();
}

// Por defecto conserva la imagen que ya tenía
$nombreImagen = $imagenActual;

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

    $carpeta = "../../uploads/novedades/";

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    $maxTamanio = 3 * 1024 * 1024; // 3MB
    if ($_FILES['imagen']['size'] > $maxTamanio) {
        header("Location: editar.php?id=$id&alerta=imagen_muy_grande");
        exit();
    }

    $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!in_array($extension, $extensionesPermitidas)) {
        header("Location: editar.php?id=$id&alerta=imagen_invalida");
        exit();
    }

    $nombreImagen = uniqid('novedad_', true) . '.' . $extension;

    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $carpeta . $nombreImagen)) {
        header("Location: editar.php?id=$id&alerta=error_imagen");
        exit();
    }

    if (!empty($imagenActual) && file_exists($carpeta . basename($imagenActual))) {
        unlink($carpeta . basename($imagenActual));
    }
} elseif (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
    // Se intentó subir un archivo pero falló por algún otro motivo (tamaño de
    // servidor, subida parcial, etc.) — no seguimos como si no hubiera pasado nada.
    header("Location: editar.php?id=$id&alerta=error_imagen");
    exit();
}

$sql = "UPDATE novedades
        SET textoNovedad = ?, fechaPublicacion = ?, fechaExpiracion = ?, tituloNovedad = ?, imagen = ?
        WHERE codNovedad = ?";

$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    error_log("Error al preparar la actualización: " . mysqli_error($link));
    header("Location: editar.php?id=$id&alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmt, "sssssi", $texto, $publicacion, $expiracion, $tituloNovedad, $nombreImagen, $id);

$resultado = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$resultado) {
    error_log("Error al actualizar novedad (id=$id): " . mysqli_error($link));
    header("Location: editar.php?id=$id&alerta=error_servidor");
    exit();
}

header("Location: listar.php?alerta=actualizada");
exit();