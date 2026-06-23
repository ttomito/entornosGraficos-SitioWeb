<?php

include("../includes/verificarSession.php");
include("../includes/header.php");
include("../includes/conexion.php");

$idCEO = $_SESSION['id'];

//Obtener aerolínea asignada

$sqlAerolinea = "SELECT a.nombreAerolinea FROM usuarios u LEFT JOIN aerolineas a ON u.codAerolinea = a.codAerolinea WHERE u.codUsuario = $idCEO ";

$resultadoAerolinea = mysqli_query($link, $sqlAerolinea);

if (!$resultadoAerolinea) {
    die(mysqli_error($link));
}

$datosAerolinea = mysqli_fetch_assoc($resultadoAerolinea);

$nombreAerolinea = $datosAerolinea['nombreAerolinea'];




//Total vuelos de la aerolínea

$sqlVuelos = "SELECT COUNT(*) AS total
FROM vuelos
WHERE codAerolinea = (
    SELECT codAerolinea
    FROM usuarios
    WHERE codUsuario = $idCEO
)";

$resultadoVuelos = mysqli_query($link, $sqlVuelos);

$totalVuelos = mysqli_fetch_assoc($resultadoVuelos);

//Total promociones

$sqlPromociones = "SELECT COUNT(*) AS total
FROM promociones
WHERE codAerolinea =
(
    SELECT codAerolinea
    FROM usuarios
    WHERE codUsuario = $idCEO
)";

$resultadoPromociones = mysqli_query($link, $sqlPromociones);
$totalPromociones = mysqli_fetch_assoc($resultadoPromociones);

?>

<div class="container mt-5">

            <h2>Bienvenido <?= $_SESSION['nombre'] ?></h2>

            <p class="text-muted">Panel de gestión de la aerolínea.</p>

            <!-- AEROLÍNEA -->

            <div class="card card-custom mb-4">

                <div class="card-body">

                    <h5>Aerolínea asignada</h5>
                    <hr>
                    <?php
                        if($nombreAerolinea){
                    ?>
                        <h3 class="text-success"><?= $nombreAerolinea ?></h3>
                    <?php
                    }
                        else{
                    ?>
                        <h3 class="text-danger">Sin aerolínea asignada</h3>
                        <p>Contacte al administrador para que le asigne una aerolínea.</p>
                    <?php
                    }
                    ?>

                </div>

            </div>

            <!-- TARJETAS -->

            <div class="row">

                <div class="col-md-6 mb-4">

                    <div class="card dashboard-card">

                        <div class="card-body">

                            <h5>Vuelos</h5>
                            <h2><?= $totalVuelos['total'] ?></h2>

                        </div>

                    </div>

                </div>

                <div class="col-md-6 mb-4">

                    <div class="card dashboard-card">

                        <div class="card-body">

                            <h5>Promociones</h5>
                            <h2><?= $totalPromociones['total'] ?></h2>

                        </div>

                    </div>

                </div>

            </div>
            
            <a href="../ceo/reportes/ventas.php" class="btn btn-primary">Reporte Ventas</a>
            <a href="../ceo/reportes/ocupacion.php" class="btn btn-success">Ocupación Vuelos</a>

        </div>

    </div>

</div>

<?php
include("../includes/footer.php");
?>