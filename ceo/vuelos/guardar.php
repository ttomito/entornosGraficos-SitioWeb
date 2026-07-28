<?php

include("../../includes/verificarSessionCEO.php");
include("../../includes/conexion.php");

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header("Location: crear.php?alerta=error_servidor");
    exit();
}

$idCEO = (int) ($_SESSION['id'] ?? 0);

if ($idCEO <= 0) {
    header("Location: crear.php");
    exit();
}

$sqlCEO = "SELECT codAerolinea FROM usuarios WHERE codUsuario = ?";
$stmtCEO = mysqli_prepare($link, $sqlCEO);

if (!$stmtCEO) {
    error_log("Error al preparar la consulta de CEO: " . mysqli_error($link));
    header("Location: crear.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmtCEO, "i", $idCEO);
mysqli_stmt_execute($stmtCEO);
$resultadoCEO = mysqli_stmt_get_result($stmtCEO);
$ceo = $resultadoCEO ? mysqli_fetch_assoc($resultadoCEO) : null;
mysqli_stmt_close($stmtCEO);

if (!$ceo || $ceo['codAerolinea'] === null) {
    header("Location: crear.php?alerta=sin_aerolinea");
    exit();
}

$codAerolinea = (int) $ceo['codAerolinea'];

$origen = isset($_POST['origen']) ? trim($_POST['origen']) : '';
$destino = isset($_POST['destino']) ? trim($_POST['destino']) : '';
$fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
$hora = isset($_POST['hora']) ? trim($_POST['hora']) : '';
$precio = isset($_POST['precio']) ? trim($_POST['precio']) : '';
$asientos = isset($_POST['asientos']) ? trim($_POST['asientos']) : '';

if ($origen === '' || $destino === '' || $fecha === '' || $hora === '' || $precio === '' || $asientos === '') {
    header("Location: crear.php?alerta=campos_vacios");
    exit();
}

if (mb_strlen($origen) < 3 || mb_strlen($origen) > 50 || !preg_match('/^[A-Za-zÀ-ÿ\s]+$/u', $origen)) {
    header("Location: crear.php?alerta=origen_invalido");
    exit();
}

if (mb_strlen($destino) < 3 || mb_strlen($destino) > 50 || !preg_match('/^[A-Za-zÀ-ÿ\s]+$/u', $destino)) {
    header("Location: crear.php?alerta=destino_invalido");
    exit();
}

$fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
$hoy = new DateTime('today');

if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha || $fechaObj < $hoy) {
    header("Location: crear.php?alerta=fecha_invalida");
    exit();
}

if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d):([0-5]\d)$/', $hora)) {
    if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $hora)) {
        $hora .= ':00';
    } else {
        header("Location: crear.php?alerta=hora_invalida");
        exit();
    }
}

if (!is_numeric($precio) || $precio < 50 || $precio > 5000000) {
    header("Location: crear.php?alerta=precio_invalido");
    exit();
}

if (!ctype_digit((string)$asientos) || $asientos < 0 || $asientos > 500) {
    header("Location: crear.php?alerta=asientos_invalidos");
    exit();
}

$precio = (float)$precio;
$asientos = (int)$asientos;

// imagen obligatoria
$nombreImagen = null;

if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
    header("Location: crear.php?alerta=imagen_requerida");
    exit();
}

if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    header("Location: crear.php?alerta=error_imagen");
    exit();
}

$carpeta = "../../uploads/vuelos/";

if (!is_dir($carpeta)) {
    mkdir($carpeta, 0755, true);
}

$maxTamanio = 3 * 1024 * 1024;
if ($_FILES['imagen']['size'] > $maxTamanio) {
    header("Location: crear.php?alerta=imagen_muy_grande");
    exit();
}

$extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
$extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($extension, $extensionesPermitidas)) {
    header("Location: crear.php?alerta=imagen_invalida");
    exit();
}

if (@getimagesize($_FILES['imagen']['tmp_name']) === false) {
    header("Location: crear.php?alerta=imagen_invalida");
    exit();
}

$nombreImagen = uniqid('vuelo_', true) . '.' . $extension;

if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $carpeta . $nombreImagen)) {
    header("Location: crear.php?alerta=error_imagen");
    exit();
}

$sql = "INSERT INTO vuelos (codAerolinea, origenVuelo, destinoVuelo, fechaVuelo, horaSalida, precioVuelo, asientosDisponibles, imagenVuelo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    error_log("Error al preparar la creación del vuelo: " . mysqli_error($link));
    header("Location: crear.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmt, "issssdis", $codAerolinea, $origen, $destino, $fecha, $hora, $precio, $asientos, $nombreImagen);

$resultado = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$resultado) {
    error_log("Error al crear vuelo: " . mysqli_error($link));
    header("Location: crear.php?alerta=error_servidor");
    exit();
}

header("Location: listar.php?alerta=creado");
exit();