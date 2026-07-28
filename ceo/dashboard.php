<?php

include("../includes/verificarSessionCeo.php");
include("../includes/conexion.php");
include("../includes/header.php");

$idCEO = (int) ($_SESSION['id'] ?? 0);
$errorConsulta = false;

$nombreAerolinea = null;
$codAerolinea = null;
$totalVuelos = 0;
$totalPromociones = 0;

if ($idCEO > 0) {
    $sqlAerolinea = "SELECT a.codAerolinea, a.nombreAerolinea
        FROM usuarios u
        LEFT JOIN aerolineas a ON u.codAerolinea = a.codAerolinea
        WHERE u.codUsuario = ?";

    $stmtAerolinea = mysqli_prepare($link, $sqlAerolinea);

    if (!$stmtAerolinea) {
        error_log("Error al preparar la consulta de aerolínea: " . mysqli_error($link));
        $errorConsulta = true;
    } else {
        mysqli_stmt_bind_param($stmtAerolinea, "i", $idCEO);
        mysqli_stmt_execute($stmtAerolinea);
        $resultadoAerolinea = mysqli_stmt_get_result($stmtAerolinea);
        $datosAerolinea = $resultadoAerolinea ? mysqli_fetch_assoc($resultadoAerolinea) : null;
        mysqli_stmt_close($stmtAerolinea);

        if ($datosAerolinea && $datosAerolinea['codAerolinea'] !== null) {
            $codAerolinea = (int) $datosAerolinea['codAerolinea'];
            $nombreAerolinea = $datosAerolinea['nombreAerolinea'];
        }
    }
} else {
    $errorConsulta = true;
}

if (!$errorConsulta && $codAerolinea !== null) {

    $sqlVuelos = "SELECT COUNT(*) AS total FROM vuelos WHERE codAerolinea = ?";
    $stmtVuelos = mysqli_prepare($link, $sqlVuelos);

    if (!$stmtVuelos) {
        error_log("Error al preparar el conteo de vuelos: " . mysqli_error($link));
        $errorConsulta = true;
    } else {
        mysqli_stmt_bind_param($stmtVuelos, "i", $codAerolinea);
        mysqli_stmt_execute($stmtVuelos);
        $resultadoVuelos = mysqli_stmt_get_result($stmtVuelos);
        $filaVuelos = $resultadoVuelos ? mysqli_fetch_assoc($resultadoVuelos) : null;
        $totalVuelos = $filaVuelos ? (int) $filaVuelos['total'] : 0;
        mysqli_stmt_close($stmtVuelos);
    }

    $sqlPromociones = "SELECT COUNT(*) AS total FROM promociones WHERE codAerolinea = ?";
    $stmtPromociones = mysqli_prepare($link, $sqlPromociones);

    if (!$stmtPromociones) {
        error_log("Error al preparar el conteo de promociones: " . mysqli_error($link));
        $errorConsulta = true;
    } else {
        mysqli_stmt_bind_param($stmtPromociones, "i", $codAerolinea);
        mysqli_stmt_execute($stmtPromociones);
        $resultadoPromociones = mysqli_stmt_get_result($stmtPromociones);
        $filaPromociones = $resultadoPromociones ? mysqli_fetch_assoc($resultadoPromociones) : null;
        $totalPromociones = $filaPromociones ? (int) $filaPromociones['total'] : 0;
        mysqli_stmt_close($stmtPromociones);
    }
}

$nombreOut = htmlspecialchars($_SESSION['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
$nombreAerolineaOut = $nombreAerolinea !== null ? htmlspecialchars($nombreAerolinea, ENT_QUOTES, 'UTF-8') : null;

?>

<main id="contenido-principal">

    <div class="container mt-5">

        <h2>Bienvenido <?= $nombreOut ?></h2>

        <p class="text-muted">Panel de gestión de la aerolínea.</p>

        <?php if ($errorConsulta) { ?>

            <div class="alert alert-danger" role="alert">
                Ocurrió un error al cargar tus datos. Intentá nuevamente más tarde.
            </div>

        <?php } else { ?>

            <!-- AEROLÍNEA -->

            <div class="card card-custom mb-4">

                <div class="card-body">

                    <h5>Aerolínea asignada</h5>
                    <hr>
                    <?php if ($nombreAerolineaOut) { ?>
                        <h3 class="text-success"><?= $nombreAerolineaOut ?></h3>
                    <?php } else { ?>
                        <h3 class="text-danger">Sin aerolínea asignada</h3>
                        <p>Contacte al administrador para que le asigne una aerolínea.</p>
                    <?php } ?>

                </div>

            </div>


            <div class="row">

                <div class="col-md-6 mb-4">

                    <div class="card dashboard-card">

                        <div class="card-body">

                            <h5>Vuelos</h5>
                            <h2><?= $totalVuelos ?></h2>

                        </div>

                    </div>

                </div>

                <div class="col-md-6 mb-4">

                    <div class="card dashboard-card">

                        <div class="card-body">

                            <h5>Promociones</h5>
                            <h2><?= $totalPromociones ?></h2>

                        </div>

                    </div>

                </div>

            </div>

            <?php if ($codAerolinea !== null) { ?>
                <a href="reportes/ventas.php" class="btn btn-primary">Reporte Ventas</a>
                <a href="reportes/ocupacion.php" class="btn btn-success">Ocupación Vuelos</a>
            <?php } ?>

        <?php } ?>

    </div>

</main>

<?php
include("../includes/footer.php");
?>