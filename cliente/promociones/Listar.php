<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

$registrosPorPagina = 10;

$filtroDescripcion = $_GET['descripcion'] ?? '';
$filtroAerolinea   = $_GET['aerolinea'] ?? '';

$pagina = isset($_GET['pagina'])
    ? (int)$_GET['pagina'] :
    1;

if ($pagina < 1) {
    $pagina = 1;
}

$inicio =
    ($pagina - 1)
    *
    $registrosPorPagina;

$filtroDescripcionEsc = mysqli_real_escape_string($link, addcslashes($filtroDescripcion, '%_'));
$filtroAerolineaEsc   = mysqli_real_escape_string($link, addcslashes($filtroAerolinea, '%_'));

function urlPagina($n, $filtroDescripcion, $filtroAerolinea)
{
    $query = http_build_query([
        'pagina'      => $n,
        'descripcion' => $filtroDescripcion,
        'aerolinea'   => $filtroAerolinea,
    ]);

    return '?' . htmlspecialchars($query, ENT_QUOTES, 'UTF-8');
}

$sqlConteo = "SELECT COUNT(*) AS total
FROM promociones p
INNER JOIN aerolineas a
ON p.codAerolinea = a.codAerolinea
WHERE p.estadoPromocion = 'APROBADA'
AND p.fechaLimitePromocion >= CURDATE()";

if ($filtroDescripcion != '') {
    $sqlConteo .= " AND p.descripcionPromocion LIKE '%$filtroDescripcionEsc%'";
}

if ($filtroAerolinea != '') {
    $sqlConteo .= " AND a.nombreAerolinea LIKE '%$filtroAerolineaEsc%'";
}

$resultadoConteo = mysqli_query($link, $sqlConteo);
$filaConteo = mysqli_fetch_assoc($resultadoConteo);
$totalRegistros = $filaConteo['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);


$sql = "SELECT p.*, a.nombreAerolinea
FROM promociones p
INNER JOIN aerolineas a
ON p.codAerolinea = a.codAerolinea
WHERE p.estadoPromocion = 'APROBADA'
AND p.fechaLimitePromocion >= CURDATE()";

if ($filtroDescripcion != '') {
    $sql .= " AND p.descripcionPromocion LIKE '%$filtroDescripcionEsc%'";
}

if ($filtroAerolinea != '') {
    $sql .= " AND a.nombreAerolinea LIKE '%$filtroAerolineaEsc%'";
}

$sql .= " ORDER BY p.codPromocion DESC LIMIT $inicio, $registrosPorPagina;";
$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($link));
}


?>


<div class="container mt-4">
    <div class="row align-items-start mb-4">
        <div class="col-md-4">

            <h2>

                Promociones disponibles

            </h2>

        </div>

        <div class="col-md-8">

            <p class="text-muted mb-3">

                Busque promociones por descripción o por nombre de la aerolínea.

            </p>

            <form method="GET" role="search" aria-label="Buscar promociones">

                <div class="row g-3">

                    <div class="col-md-5">

                        <label for="descripcion" class="form-label">

                            Descripción

                        </label>

                        <input
                            type="text"
                            id="descripcion"
                            name="descripcion"
                            class="form-control"
                            placeholder="Ej.: Europa, Dubái, Bariloche..."
                            value="<?= htmlspecialchars($filtroDescripcion, ENT_QUOTES, 'UTF-8') ?>">

                    </div>

                    <div class="col-md-5">

                        <label for="aerolinea" class="form-label">

                            Aerolínea

                        </label>

                        <input
                            type="text"
                            id="aerolinea"
                            name="aerolinea"
                            class="form-control"
                            placeholder="Ej.: Emirates, LATAM, Iberia..."
                            value="<?= htmlspecialchars($filtroAerolinea, ENT_QUOTES, 'UTF-8') ?>">

                    </div>

                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            class="btn btn-primary w-100">

                            Buscar

                        </button>

                    </div>

                </div>

            </form>


        </div>



    </div>

    <?php
    if (
        !empty($filtroDescripcion)
        ||
        !empty($filtroAerolinea)
    ) {
    ?>

        <div class="alert alert-info d-flex justify-content-between align-items-center" role="status">

            <span>

                Mostrando promociones según los filtros seleccionados.

            </span>

            <a
                href="listar.php"
                class="btn btn-sm btn-outline-primary">

                Limpiar filtros

            </a>

        </div>

    <?php
    }
    ?>

    <?php
    if (mysqli_num_rows($resultado) > 0) { ?>
        <div class="card card-custom">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover">

                        <caption class="visually-hidden">Listado de promociones disponibles</caption>

                        <thead>

                            <tr>
                                <th scope="col">Promocion</th>
                                <th scope="col">Descripcion</th>
                                <th scope="col">Fecha limite</th>
                                <th scope="col">Aerolinea</th>
                                <th scope="col">Acción</th>
                            </tr>

                        </thead>

                        <tbody>
                            <?php $hoy = new DateTime() ?>
                            <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>
                                <?php
                                $fechaLimite = new DateTime($fila['fechaLimitePromocion']);
                                if ($fila['estadoPromocion'] == "APROBADA" && $fechaLimite >= $hoy) {
                                    $descripcion = htmlspecialchars($fila['descripcionPromocion'], ENT_QUOTES, 'UTF-8');
                                    $nombreAerolinea = htmlspecialchars($fila['nombreAerolinea'], ENT_QUOTES, 'UTF-8');
                                    $fechaLimiteTexto = htmlspecialchars($fila['fechaLimitePromocion'], ENT_QUOTES, 'UTF-8');
                                ?>

                                    <tr>
                                        <td>
                                            <?= (int) $fila['descuentoPromocion'] ?>%
                                        </td>
                                        <td>
                                            <?= $descripcion ?>
                                        </td>
                                        <td>
                                            <time datetime="<?= $fechaLimiteTexto ?>"><?= $fechaLimiteTexto ?></time>
                                        </td>

                                        <td>

                                            <?= $nombreAerolinea ?>

                                        </td>
                                        <td>

                                            <a
                                                href="../vuelos/listar.php?promo=<?= (int) $fila['codPromocion'] ?>"
                                                class="btn btn-primary btn-sm">

                                                Ver vuelos
                                                <span class="visually-hidden"> con la promoción "<?= $descripcion ?>" de <?= $nombreAerolinea ?></span>

                                            </a>

                                        </td>

                                    </tr>

                                <?php } ?>
                            <?php } ?>

                        </tbody>

                    </table>

                </div>

                <div class="d-flex justify-content-center mt-4">

                    <nav aria-label="Paginación de promociones">

                        <ul class="pagination flex-wrap justify-content-center">

                            <?php if ($pagina > 1) { ?>

                                <li class="page-item">
                                    <a class="page-link" href="<?= urlPagina($pagina - 1, $filtroDescripcion, $filtroAerolinea) ?>">
                                        Anterior
                                    </a>
                                </li>

                            <?php } ?>

                            <?php
                            for ($i = 1; $i <= $totalPaginas; $i++) {
                            ?>

                                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= urlPagina($i, $filtroDescripcion, $filtroAerolinea) ?>"
                                        <?= $i == $pagina ? 'aria-current="page"' : '' ?>>
                                        <?= $i ?>
                                        <?php if ($i == $pagina) { ?>
                                            <span class="visually-hidden"> (página actual)</span>
                                        <?php } ?>
                                    </a>
                                </li>

                            <?php } ?>

                            <?php if ($pagina < $totalPaginas) { ?>

                                <li class="page-item">
                                    <a class="page-link" href="<?= urlPagina($pagina + 1, $filtroDescripcion, $filtroAerolinea) ?>">
                                        Siguiente
                                    </a>
                                </li>

                            <?php } ?>

                        </ul>

                    </nav>

                </div>
            </div>

        </div>
    <?php } else { ?>

        <div class="alert alert-info" role="status">
            No hay promociones disponibles<?= ($filtroDescripcion || $filtroAerolinea) ? ' para los filtros aplicados' : '' ?>.
        </div>

    <?php } ?>



</div>
<?php include("../../includes/footer.php"); ?>