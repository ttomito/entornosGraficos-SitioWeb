<?php

require_once("../includes/verificarSession.php");
require_once("../includes/conexion.php");

if (($_SESSION['tipo'] ?? '') !== 'CLIENTE') {
    header("Location: ".ruta."/index.php");
    exit();
}

$idCliente = (int) $_SESSION['id'];

$totalReservas    = null;
$totalConfirmadas = null;
$totalNovedades   = null;

$sqlReservas = "SELECT COUNT(*) AS total,
                COALESCE(SUM(estadoReserva = 'CONFIRMADA'), 0) AS confirmadas
                FROM reservas
                WHERE codUsuario = ? AND activo = 1";

$stmtReservas = mysqli_prepare($link, $sqlReservas);

if ($stmtReservas) {

    mysqli_stmt_bind_param($stmtReservas, "i", $idCliente);
    mysqli_stmt_execute($stmtReservas);

    $resReservas = mysqli_stmt_get_result($stmtReservas);
    $filaReservas = $resReservas ? mysqli_fetch_assoc($resReservas) : null;

    if ($filaReservas) {
        $totalReservas    = (int) $filaReservas['total'];
        $totalConfirmadas = (int) $filaReservas['confirmadas'];
    }

    mysqli_stmt_close($stmtReservas);
} else {
    error_log("Dashboard - reservas: " . mysqli_error($link));
}

$sqlNovedades = "SELECT COUNT(*) AS total
                 FROM novedades
                 WHERE CURDATE() BETWEEN fechaPublicacion AND fechaExpiracion";

$resNovedades = mysqli_query($link, $sqlNovedades);

if ($resNovedades) {

    $filaNovedades = mysqli_fetch_assoc($resNovedades);

    if ($filaNovedades) {
        $totalNovedades = (int) $filaNovedades['total'];
    }
} else {
    error_log("Dashboard - novedades: " . mysqli_error($link));
}

function mostrarTotal($valor)
{
    return $valor === null ? '—' : number_format($valor, 0, ',', '.');
}

$nombreOut = htmlspecialchars($_SESSION['nombre'] ?? '', ENT_QUOTES, 'UTF-8');

include("../includes/header.php");

?>

<main id="contenido-principal">

    <div class="container mt-5">

        <h1>Bienvenido <?= $nombreOut ?></h1>

        <p class="text-muted">Panel principal del pasajero.</p>

        <div class="row mt-4">

            <div class="col-md-4 mb-4">

                <a href="reservas/listar.php" class="text-decoration-none text-reset">

                    <div class="card dashboard-card h-100">

                        <div class="card-body text-center">

                            <h2 class="h5">Mis Reservas</h2>

                            <p class="display-6 mb-0"><?= mostrarTotal($totalReservas) ?></p>

                        </div>

                    </div>

                </a>

            </div>

            <div class="col-md-4 mb-4">

                <a href="reservas/listar.php" class="text-decoration-none text-reset">

                    <div class="card dashboard-card h-100">

                        <div class="card-body text-center">

                            <h2 class="h5">Compras Confirmadas</h2>

                            <p class="display-6 mb-0"><?= mostrarTotal($totalConfirmadas) ?></p>

                        </div>

                    </div>

                </a>

            </div>

            <div class="col-md-4 mb-4">

                <a href="novedades/listar.php" class="text-decoration-none text-reset">

                    <div class="card dashboard-card h-100">

                        <div class="card-body text-center">

                            <h2 class="h5">Novedades Activas</h2>

                            <p class="display-6 mb-0"><?= mostrarTotal($totalNovedades) ?></p>

                        </div>

                    </div>

                </a>

            </div>

        </div>

        <div class="d-flex flex-wrap gap-2 mt-2">

            <a href="vuelos/listar.php" class="btn btn-primary">
                Buscar vuelos
            </a>

            <a href="reservas/listar.php" class="btn btn-outline-secondary">
                Ver mis reservas
            </a>

        </div>

    </div>

</main>

<?php
include("../includes/footer.php");
?>