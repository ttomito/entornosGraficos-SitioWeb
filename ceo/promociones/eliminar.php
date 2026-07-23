<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$id = $_GET['id'];

$idCEO = $_SESSION['id'];

$sql = "DELETE p
FROM promociones p
INNER JOIN usuarios u
ON p.codAerolinea = u.codAerolinea
WHERE p.codPromocion = $id
AND u.codUsuario = $idCEO";

mysqli_query(
    $link,
    $sql
);

header(
    "Location: listar.php"
);

exit();