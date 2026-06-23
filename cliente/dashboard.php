<?php

include("../includes/verificarSession.php");
include("../includes/conexion.php");
include("../includes/header.php");

$idCliente = $_SESSION['id'];

$sqlReservas = "

SELECT COUNT(*) total

FROM reservas

WHERE codUsuario = $idCliente

";

$reservas =
mysqli_fetch_assoc(
mysqli_query($link,$sqlReservas)
);

$sqlCompras = "

SELECT COUNT(*) total

FROM reservas

WHERE codUsuario = $idCliente

AND estadoReserva = 'CONFIRMADA'

";

$compras =
mysqli_fetch_assoc(
mysqli_query($link,$sqlCompras)
);

$sqlNovedades = "

SELECT COUNT(*) total

FROM novedades

WHERE CURDATE()
BETWEEN fechaPublicacion
AND fechaExpiracion

";

$novedades =
mysqli_fetch_assoc(
mysqli_query($link,$sqlNovedades)
);

?>



<div class="container mt-5">


<h1>

    Bienvenido

    <?= $_SESSION['nombre'] ?>

</h1>

<p class="text-muted">

    Panel principal del pasajero.

</p>

<div class="row mt-4">

    <div class="col-md-4 mb-4">

        <div class="card dashboard-card">

            <div class="card-body text-center">

                <h5>

                    Mis Reservas

                </h5>

                <h2>

                    <?= $reservas['total'] ?>

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-4 mb-4">

        <div class="card dashboard-card">

            <div class="card-body text-center">

                <h5>

                    Compras Confirmadas

                </h5>

                <h2>

                    <?= $compras['total'] ?>

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-4 mb-4">

        <div class="card dashboard-card">

            <div class="card-body text-center">

                <h5>

                    Novedades Activas

                </h5>

                <h2>

                    <?= $novedades['total'] ?>

                </h2>

            </div>

        </div>

    </div>

</div>
</div>







<?php
include("../includes/footer.php");
?>
