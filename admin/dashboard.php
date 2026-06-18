<?php

include("../includes/verificarSession.php");

include("../includes/header.php");

include("../includes/conexion.php");



$sqlAerolineas = "
SELECT COUNT(*) AS total
FROM aerolineas
";

$resultadoAerolineas =
mysqli_query(
    $link,
    $sqlAerolineas
);

$totalAerolineas =
mysqli_fetch_assoc(
    $resultadoAerolineas
);

$sqlPendientes = "

SELECT COUNT(*) AS total

FROM promociones

WHERE estadoPromocion = 'PENDIENTE'

";

$resultadoPendientes =
mysqli_query(
    $link,
    $sqlPendientes
);

$totalPendientes =
mysqli_fetch_assoc(
    $resultadoPendientes
);

$sqlAprobadas = "

SELECT COUNT(*) AS total

FROM promociones

WHERE estadoPromocion = 'APROBADA'

";

$resultadoAprobadas =
mysqli_query(
    $link,
    $sqlAprobadas
);

$totalAprobadas =
mysqli_fetch_assoc(
    $resultadoAprobadas
);


$sqlUsuarios = "
SELECT COUNT(*) AS total
FROM usuarios
";

$resultadoUsuarios =
mysqli_query(
    $link,
    $sqlUsuarios
);

$totalUsuarios =
mysqli_fetch_assoc(
    $resultadoUsuarios
);
?>

<div class="container mt-5">

            <h2>

                Bienvenido
                <?php echo $_SESSION['nombre']; ?>
            </h2>

            <p class="text-muted">

                Panel de administración del sistema.

            </p>

            <div class="row mt-4">

                <div class="col-md-4 mb-4">

                    <div class="card dashboard-card">

                        <div class="card-body">

                            <h5>

                                Aerolíneas

                            </h5>

                            <h2>

    <?php
    echo $totalAerolineas['total'];
    ?>

</h2>

                        </div>

                    </div>

                </div>

              <div class="col-md-4 mb-4">

    <div class="card dashboard-card">

        <div class="card-body">

            <h5>

                Promociones

            </h5>

            <h3>

                <?= $totalPendientes['total'] ?>

            </h3>

            <small class="text-warning">

                Pendientes

            </small>

            <hr>

            <h4>

                <?= $totalAprobadas['total'] ?>

            </h4>

            <small class="text-success">

                Aprobadas

            </small>

        </div>

    </div>

</div>

                <div class="col-md-4 mb-4">

                    <div class="card dashboard-card">

                        <div class="card-body">

                            <h5>

                                Usuarios

                            </h5>

                           <h2>

    <?php
    echo $totalUsuarios['total'];
    ?>

</h2>

                        </div>

                    </div>

                </div>

            </div>
        </div>

    </div>

</div>

<?php

include("../includes/footer.php");

?>