<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$registrosPorPagina = 10;

$pagina = isset($_GET['pagina'])
    ? (int)$_GET['pagina']
    : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $registrosPorPagina;

$idCEO = (int) ($_SESSION['id'] ?? 0);

if ($idCEO <= 0) {
    die("Acceso denegado");
}

$sqlConteo = "
SELECT COUNT(*) AS total
FROM vuelos
WHERE codAerolinea = (
    SELECT codAerolinea
    FROM usuarios
    WHERE codUsuario = $idCEO
)";

$resultadoConteo = mysqli_query($link, $sqlConteo);

$filaConteo = mysqli_fetch_assoc($resultadoConteo);

$totalRegistros = $filaConteo['total'];

$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

$sql = "SELECT
v.codVuelo,
v.origenVuelo,
v.destinoVuelo,
v.fechaVuelo,
v.asientosDisponibles,

COALESCE(SUM(r.cantAsientos),0) AS ocupados

FROM vuelos v
LEFT JOIN reservas r
ON v.codVuelo = r.codVuelo
AND r.estadoReserva = 'CONFIRMADA'
WHERE v.codAerolinea = (SELECT codAerolinea FROM usuarios WHERE codUsuario = $idCEO)
GROUP BY v.codVuelo 
ORDER BY v.fechaVuelo
LIMIT $inicio, $registrosPorPagina";

$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    die(mysqli_error($link));
}

?>

<style>
    .visually-hidden {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }

    /* Foco visible reforzado para SC 2.4.7 */
    a.btn:focus-visible,
    a.page-link:focus-visible,
    button:focus-visible {
        outline: 3px solid #0d6efd;
        outline-offset: 2px;
    }
</style>

<main id="contenido-principal">

    <div class="container mt-5">

        <h2>Ocupación de Vuelos</h2>

        <div class="card card-custom">

            <div class="card-body">

                <?php if (mysqli_num_rows($resultado) > 0) { ?>

                    <div class="table-responsive">

                        <table class="table table-hover">

                            <caption class="visually-hidden">
                                Ocupación de vuelos de la aerolínea: asientos reservados frente a asientos disponibles
                            </caption>

                            <thead>

                                <tr>

                                    <th scope="col">Vuelo</th>
                                    <th scope="col">Origen</th>
                                    <th scope="col">Destino</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Reservas</th>
                                    <th scope="col">Asientos Disponibles</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php while ($fila = mysqli_fetch_assoc($resultado)) {

                                    $codVueloInt = (int) $fila['codVuelo'];
                                    $origenOut = htmlspecialchars($fila['origenVuelo'], ENT_QUOTES, 'UTF-8');
                                    $destinoOut = htmlspecialchars($fila['destinoVuelo'], ENT_QUOTES, 'UTF-8');
                                    $fechaOut = htmlspecialchars($fila['fechaVuelo'], ENT_QUOTES, 'UTF-8');

                                ?>

                                    <tr>
                                        <td>
                                            <?= $codVueloInt ?>
                                        </td>
                                        <td>
                                            <?= $origenOut ?>
                                        </td>
                                        <td>
                                            <?= $destinoOut ?>
                                        </td>
                                        <td>
                                            <time datetime="<?= $fechaOut ?>"><?= $fechaOut ?></time>
                                        </td>
                                        <td>

                                            <?= (int) $fila['ocupados'] ?>

                                        </td>
                                        <td>
                                            <?= (int) $fila['asientosDisponibles'] ?>
                                        </td>
                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                    <div class="d-flex justify-content-center mt-4">

                        <nav aria-label="Paginación de ocupación de vuelos">

                            <ul class="pagination flex-wrap justify-content-center">

                                <?php if ($pagina > 1) { ?>

                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?= $pagina - 1 ?>">
                                            Anterior
                                        </a>
                                    </li>

                                <?php } ?>

                                <?php
                                for ($i = 1; $i <= $totalPaginas; $i++) {
                                ?>

                                    <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                        <a class="page-link" href="?pagina=<?= $i ?>" <?= $i == $pagina ? 'aria-current="page"' : '' ?>>
                                            <?= $i ?>
                                            <?php if ($i == $pagina) { ?><span class="visually-hidden"> (página actual)</span><?php } ?>
                                        </a>
                                    </li>

                                <?php } ?>

                                <?php if ($pagina < $totalPaginas) { ?>

                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?= $pagina + 1 ?>">
                                            Siguiente
                                        </a>
                                    </li>

                                <?php } ?>

                            </ul>

                        </nav>

                    </div>

                <?php } else { ?>

                    <div class="alert alert-info" role="status">
                        No hay vuelos registrados.
                    </div>

                <?php } ?>

            </div>

        </div>

    </div>

</main>

<?php
include("../../includes/footer.php");
?>