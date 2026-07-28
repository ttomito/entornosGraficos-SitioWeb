<?php

include("../../includes/verificarSessionCEO.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$registrosPorPagina = 10;
$errorConsulta = false;

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$idCEO = (int) ($_SESSION['id'] ?? 0);
$codAerolinea = null;

if ($idCEO > 0) {
    $sqlCEO = "SELECT codAerolinea FROM usuarios WHERE codUsuario = ?";
    $stmtCEO = mysqli_prepare($link, $sqlCEO);

    if (!$stmtCEO) {
        error_log("Error al preparar la consulta de CEO: " . mysqli_error($link));
        $errorConsulta = true;
    } else {
        mysqli_stmt_bind_param($stmtCEO, "i", $idCEO);
        mysqli_stmt_execute($stmtCEO);
        $resultadoCEO = mysqli_stmt_get_result($stmtCEO);
        $ceo = $resultadoCEO ? mysqli_fetch_assoc($resultadoCEO) : null;
        mysqli_stmt_close($stmtCEO);

        if ($ceo && $ceo['codAerolinea'] !== null) {
            $codAerolinea = (int) $ceo['codAerolinea'];
        }
    }
} else {
    $errorConsulta = true;
}

if ($errorConsulta) {
?>

    <main id="contenido-principal">

        <div class="container mt-5">

            <div class="alert alert-danger" role="alert">
                Ocurrió un error al cargar tus datos. Intentá nuevamente más tarde.
            </div>

        </div>

    </main>

<?php
    include("../../includes/footer.php");
    exit();
}

if ($codAerolinea === null) {
?>

    <main id="contenido-principal">

        <div class="container mt-5">

            <div class="card card-custom">

                <div class="card-body p-5 text-center" role="alert">

                    <h2 class="text-danger">
                        Tu cuenta todavía no está vinculada a una aerolínea
                    </h2>

                    <p>
                        Un administrador tiene que asociar tu cuenta a una aerolínea antes de que puedas ver la ocupación de vuelos.
                    </p>

                </div>

            </div>

        </div>

    </main>

<?php
    include("../../includes/footer.php");
    exit();
}

$sqlConteo = "SELECT COUNT(*) AS total FROM vuelos WHERE codAerolinea = ?";
$stmtConteo = mysqli_prepare($link, $sqlConteo);

if (!$stmtConteo) {
    error_log("Error al preparar el conteo: " . mysqli_error($link));
    $errorConsulta = true;
    $totalRegistros = 0;
} else {
    mysqli_stmt_bind_param($stmtConteo, "i", $codAerolinea);
    mysqli_stmt_execute($stmtConteo);
    $resultadoConteo = mysqli_stmt_get_result($stmtConteo);
    $filaConteo = $resultadoConteo ? mysqli_fetch_assoc($resultadoConteo) : null;
    $totalRegistros = $filaConteo ? (int)$filaConteo['total'] : 0;
    mysqli_stmt_close($stmtConteo);
}

$totalPaginas = (int) ceil($totalRegistros / $registrosPorPagina);

if ($totalPaginas > 0 && $pagina > $totalPaginas) {
    $pagina = $totalPaginas;
}

$inicio = ($pagina - 1) * $registrosPorPagina;

$resultado = null;

if (!$errorConsulta) {
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
    WHERE v.codAerolinea = ?
    GROUP BY v.codVuelo
    ORDER BY v.fechaVuelo
    LIMIT ?, ?";

    $stmt = mysqli_prepare($link, $sql);

    if (!$stmt) {
        error_log("Error al preparar el listado de ocupación: " . mysqli_error($link));
        $errorConsulta = true;
    } else {
        mysqli_stmt_bind_param($stmt, "iii", $codAerolinea, $inicio, $registrosPorPagina);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if (!$resultado) {
            error_log("Error al listar ocupación: " . mysqli_error($link));
            $errorConsulta = true;
        }
    }
}

?>

<main id="contenido-principal">

    <div class="container mt-5">

        <h2>Ocupación de Vuelos</h2>

        <div class="card card-custom">

            <div class="card-body">

                <?php if ($errorConsulta) { ?>

                    <div class="alert alert-danger" role="alert">
                        Ocurrió un error al cargar el listado. Intentá nuevamente más tarde.
                    </div>

                <?php } elseif (mysqli_num_rows($resultado) > 0) { ?>

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