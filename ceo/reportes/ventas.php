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
                        Un administrador tiene que asociar tu cuenta a una aerolínea antes de que puedas ver el reporte de ventas.
                    </p>

                </div>

            </div>

        </div>

    </main>

<?php
    include("../../includes/footer.php");
    exit();
}

$sqlConteo = "SELECT COUNT(*) AS total
    FROM reservas r
    INNER JOIN vuelos v ON r.codVuelo = v.codVuelo
    WHERE r.estadoReserva = 'CONFIRMADA'
    AND v.codAerolinea = ?";

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
$totalVendido = 0;

if (!$errorConsulta) {
    $sql = "SELECT
        r.codReserva,
        u.nombreUsuario,
        v.origenVuelo,
        v.destinoVuelo,
        r.fechaReserva,
        r.precioFinal
    FROM reservas r
    INNER JOIN usuarios u ON r.codUsuario = u.codUsuario
    INNER JOIN vuelos v ON r.codVuelo = v.codVuelo
    WHERE r.estadoReserva = 'CONFIRMADA'
    AND v.codAerolinea = ?
    ORDER BY r.fechaReserva DESC
    LIMIT ?, ?";

    $stmt = mysqli_prepare($link, $sql);

    if (!$stmt) {
        error_log("Error al preparar el listado de ventas: " . mysqli_error($link));
        $errorConsulta = true;
    } else {
        mysqli_stmt_bind_param($stmt, "iii", $codAerolinea, $inicio, $registrosPorPagina);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if (!$resultado) {
            error_log("Error al listar ventas: " . mysqli_error($link));
            $errorConsulta = true;
        }
    }

    if (!$errorConsulta) {
        $sqlTotal = "SELECT SUM(r.precioFinal) AS total
            FROM reservas r
            INNER JOIN vuelos v ON r.codVuelo = v.codVuelo
            WHERE r.estadoReserva = 'CONFIRMADA'
            AND v.codAerolinea = ?";

        $stmtTotal = mysqli_prepare($link, $sqlTotal);

        if (!$stmtTotal) {
            error_log("Error al preparar el total: " . mysqli_error($link));
            $errorConsulta = true;
        } else {
            mysqli_stmt_bind_param($stmtTotal, "i", $codAerolinea);
            mysqli_stmt_execute($stmtTotal);
            $resultadoTotal = mysqli_stmt_get_result($stmtTotal);
            $filaTotal = $resultadoTotal ? mysqli_fetch_assoc($resultadoTotal) : null;
            $totalVendido = $filaTotal && $filaTotal['total'] !== null ? (float)$filaTotal['total'] : 0;
            mysqli_stmt_close($stmtTotal);
        }
    }
}

?>

<main id="contenido-principal">

    <div class="container mt-5">

        <h2>Reporte de Ventas</h2>

        <?php if ($errorConsulta) { ?>

            <div class="alert alert-danger" role="alert">
                Ocurrió un error al cargar el reporte. Intentá nuevamente más tarde.
            </div>

        <?php } else { ?>

            <div class="alert alert-info">
                Total vendido: <strong>$<?= number_format($totalVendido, 0, ',', '.') ?></strong>
            </div>

            <div class="card card-custom">

                <div class="card-body">

                    <?php if (mysqli_num_rows($resultado) > 0) { ?>

                        <div class="table-responsive">

                            <table class="table table-hover">

                                <caption class="visually-hidden">
                                    Listado de reservas confirmadas de la aerolínea
                                </caption>

                                <thead>

                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Cliente</th>
                                        <th scope="col">Origen</th>
                                        <th scope="col">Destino</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Importe</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php while ($fila = mysqli_fetch_assoc($resultado)) {

                                        $codReservaInt = (int) $fila['codReserva'];
                                        $nombreOut = htmlspecialchars($fila['nombreUsuario'], ENT_QUOTES, 'UTF-8');
                                        $origenOut = htmlspecialchars($fila['origenVuelo'], ENT_QUOTES, 'UTF-8');
                                        $destinoOut = htmlspecialchars($fila['destinoVuelo'], ENT_QUOTES, 'UTF-8');
                                        $fechaOut = htmlspecialchars($fila['fechaReserva'], ENT_QUOTES, 'UTF-8');

                                    ?>

                                        <tr>
                                            <td><?= $codReservaInt ?></td>
                                            <td><?= $nombreOut ?></td>
                                            <td><?= $origenOut ?></td>
                                            <td><?= $destinoOut ?></td>
                                            <td><time datetime="<?= $fechaOut ?>"><?= $fechaOut ?></time></td>
                                            <td>$<?= number_format((float)$fila['precioFinal'], 0, ',', '.') ?></td>
                                        </tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

                        <div class="d-flex justify-content-center mt-4">

                            <nav aria-label="Paginación de ventas">

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
                            No hay ventas registradas.
                        </div>

                    <?php } ?>

                </div>

            </div>

        <?php } ?>

    </div>

</main>

<?php
include("../../includes/footer.php");
?>