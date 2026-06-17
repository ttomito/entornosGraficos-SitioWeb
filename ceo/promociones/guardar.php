<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");

$idCEO = $_SESSION['id'];

$descripcion = $_POST['descripcion'];

$descuento = $_POST['descuento'];

$sqlCEO = "

SELECT codAerolinea

FROM usuarios

WHERE codUsuario = $idCEO

";

$resultadoCEO = mysqli_query(
    $link,
    $sqlCEO
);

$ceo = mysqli_fetch_assoc(
    $resultadoCEO
);

$codAerolinea = $ceo['codAerolinea'];

$sql = "

INSERT INTO promociones
(
    codAerolinea,
    descripcionPromocion,
    descuentoPromocion,
    estadoPromocion
)
VALUES
(
    $codAerolinea,
    '$descripcion',
    $descuento,
    'PENDIENTE'
)

";

mysqli_query(
    $link,
    $sql
);

header(
    "Location: listar.php"
);

exit();