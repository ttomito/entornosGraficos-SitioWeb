<?php

include_once("../../includes/header.php");
include_once("../../includes/conexion.php");

$registrosPorPagina = 10;

$pagina = isset($_GET['pagina'])
    ? (int)$_GET['pagina']
    : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$origenFiltro  = $_GET['origen'] ?? '';
$destinoFiltro = $_GET['destino'] ?? '';
$fechaFiltro   = $_GET['fecha'] ?? '';
$promoFiltro   = $_GET['promo'] ?? '';

$origenEsc  = mysqli_real_escape_string($link, addcslashes($origenFiltro, '%_'));
$destinoEsc = mysqli_real_escape_string($link, addcslashes($destinoFiltro, '%_'));

$fechaEsc = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFiltro) ? mysqli_real_escape_string($link, $fechaFiltro) : '';

$promoId = (int) $promoFiltro;

function abortarConError($link, $contexto)
{
    error_log("Vuelos - $contexto: " . mysqli_error($link));

    echo '<main id="contenido-principal"><div class="container mt-4">'
        . '<div class="alert alert-danger" role="alert">'
        . 'No se pudieron cargar los vuelos en este momento. '
        . 'Intentá nuevamente más tarde.'
        . '</div></div></main>';

    include_once("../../includes/footer.php");
    exit();
}

$whereBase = "WHERE v.activo = 1
               AND v.fechaVuelo >= CURDATE()
               AND v.asientosDisponibles > 0";

function condicionesVuelos($promoId, $origenEsc, $destinoEsc, $fechaEsc, $incluirPromo = true, $incluirFecha = true)
{
    $cond = '';

    if ($incluirPromo && $promoId > 0) {
        $cond .= " AND v.codAerolinea = (SELECT codAerolinea FROM promociones WHERE codPromocion = $promoId)";
        $cond .= " AND LOWER(TRIM(v.destinoVuelo)) IN (SELECT LOWER(TRIM(destinoVuelo)) FROM promociones_destinos WHERE codPromocion = $promoId)";
    }

    if ($origenEsc !== '') {
        $cond .= " AND v.origenVuelo LIKE '%$origenEsc%'";
    }

    if ($destinoEsc !== '') {
        $cond .= " AND v.destinoVuelo LIKE '%$destinoEsc%'";
    }

    if ($incluirFecha && $fechaEsc !== '') {
        $cond .= " AND v.fechaVuelo = '$fechaEsc'";
    }

    return $cond;
}

function urlPaginaVuelos($n, $origen, $destino, $fecha, $promo)
{
    $params = ['pagina' => $n];

    if ($origen !== '')  $params['origen'] = $origen;
    if ($destino !== '') $params['destino'] = $destino;
    if ($fecha !== '')   $params['fecha'] = $fecha;
    if ($promo !== '')   $params['promo'] = $promo;

    return '?' . htmlspecialchars(http_build_query($params), ENT_QUOTES, 'UTF-8');
}

function sqlListado($whereBase, $condiciones)
{
    return "SELECT v.*, a.nombreAerolinea,
            COALESCE(MAX(p.descuentoPromocion), 0) AS descuento
            FROM vuelos v
            INNER JOIN aerolineas a
            ON v.codAerolinea = a.codAerolinea
            AND a.activo = 1
            LEFT JOIN promociones_destinos pd
            ON LOWER(TRIM(pd.destinoVuelo)) = LOWER(TRIM(v.destinoVuelo))
            LEFT JOIN promociones p
            ON p.codPromocion = pd.codPromocion
            AND p.codAerolinea = v.codAerolinea
            AND p.estadoPromocion = 'APROBADA'
            AND p.fechaLimitePromocion >= CURDATE()"
        . $whereBase
        . $condiciones;
}

// cantidad de vuelos que cumplen las condiciones

function contarVuelos($link, $whereBase, $condiciones)
{
    $sqlConteo = "SELECT COUNT(*) AS total FROM vuelos v "
        . $whereBase
        . $condiciones;

    $resultadoConteo = mysqli_query($link, $sqlConteo);

    if (!$resultadoConteo) {
        abortarConError($link, "conteo");
    }

    $filaConteo = mysqli_fetch_assoc($resultadoConteo);

    return (int) $filaConteo['total'];
}

// busqueda exacta

$condiciones = condicionesVuelos($promoId, $origenEsc, $destinoEsc, $fechaEsc);

$totalRegistros = contarVuelos($link, $whereBase, $condiciones);

$fechaFlexible = false;


if ($totalRegistros === 0 && $fechaEsc !== '') {

    $condiciones = condicionesVuelos($promoId, $origenEsc, $destinoEsc, $fechaEsc, true, false);
    $totalRegistros = contarVuelos($link, $whereBase, $condiciones);
    $fechaFlexible = ($totalRegistros > 0);
}

$totalPaginas = max(1,(int) ceil($totalRegistros / $registrosPorPagina));

if ($pagina > $totalPaginas) {
    $pagina = $totalPaginas;
}

$inicio = ($pagina - 1) * $registrosPorPagina;

$sql = sqlListado($whereBase, $condiciones);

$sql .= " GROUP BY v.codVuelo";

$sql .= $fechaFlexible
    ? " ORDER BY ABS(DATEDIFF(v.fechaVuelo, '$fechaEsc'))"
    : " ORDER BY v.codVuelo DESC";

$sql .= " LIMIT $inicio, $registrosPorPagina";

$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    abortarConError($link, "listado");
}

$hayFiltros = (
    !empty($promoFiltro) ||
    !empty($origenFiltro) ||
    !empty($destinoFiltro) ||
    !empty($fechaFiltro)
);

?>

<main id="contenido-principal">

    <div class="container mt-4">

        <div class="row align-items-start mb-4">

            <div class="col-md-4">

                <h2>

                    Vuelos disponibles

                </h2>
            </div>

            <div class="col-md-8">

                <form method="GET" role="search" aria-label="Buscar vuelos">

                    <p class="text-muted mb-3">

                        Ingrese la ciudad de origen y destino. Luego seleccione la fecha del viaje.

                    </p>

                    <div class="row g-3">

                        <div class="col-md-4">

                            <label for="origen" class="form-label">

                                Origen

                            </label>

                            <input
                                type="text"
                                id="origen"
                                name="origen"
                                class="form-control"
                                maxlength="100"
                                placeholder="Ej.: Rosario o Buenos Aires"
                                value="<?= htmlspecialchars($origenFiltro, ENT_QUOTES, 'UTF-8') ?>">


                        </div>

                        <div class="col-md-4">

                            <label for="destino" class="form-label">

                                Destino

                            </label>

                            <input
                                type="text"
                                id="destino"
                                name="destino"
                                class="form-control"
                                maxlength="100"
                                placeholder="Ej.: Madrid o Lima"
                                value="<?= htmlspecialchars($destinoFiltro, ENT_QUOTES, 'UTF-8') ?>">


                        </div>

                        <div class="col-md-2">

                            <label for="fecha" class="form-label">

                                Fecha

                            </label>

                            <input
                                type="date"
                                id="fecha"
                                name="fecha"
                                class="form-control"
                                min="<?= date('Y-m-d') ?>"
                                value="<?= htmlspecialchars($fechaFiltro, ENT_QUOTES, 'UTF-8') ?>">

                        </div>

                        <div class="col-md-2 d-flex align-items-end">

                            <button
                                type="submit"
                                class="btn btn-primary w-100">

                                Buscar

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        <?php if ($hayFiltros) { ?>

            <div class="alert alert-info d-flex justify-content-between align-items-center" role="status">

                <span>

                    Mostrando resultados según los filtros seleccionados.

                </span>

                <a
                    href="listar.php"
                    class="btn btn-sm btn-outline-primary">

                    Limpiar filtros

                </a>

            </div>

        <?php } ?>

        <?php if ($fechaFlexible) { ?>

            <div class="alert alert-warning" role="status">

                No se encontraron vuelos para la fecha seleccionada.

                Se muestran los vuelos disponibles para las fechas más cercanas.

            </div>

        <?php } ?>

        <?php if ($totalRegistros === 0) { ?>

            <div class="alert alert-info" role="status">

                No hay vuelos disponibles<?= $hayFiltros ? ' para la búsqueda realizada' : ' en este momento' ?>.

                <?php if ($hayFiltros) { ?>
                    Probá ampliando los criterios o
                    <a href="listar.php" class="alert-link">limpiando los filtros</a>.
                <?php } ?>

            </div>

        <?php } else { ?>

            <div class="card card-custom">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover">

                            <caption class="visually-hidden">Listado de vuelos disponibles</caption>

                            <thead>

                                <tr>

                                    <th scope="col">Imagen</th>
                                    <th scope="col">Aerolínea</th>
                                    <th scope="col">Origen</th>
                                    <th scope="col">Destino</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Precio</th>
                                    <th scope="col">Promoción</th>
                                    <th scope="col">Asientos</th>
                                    <th scope="col">Acción</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php

                            
                                while ($fila = mysqli_fetch_assoc($resultado)) {

                                    $precioFinal = $fila['precioVuelo'];

                                    if ($fila['descuento'] > 0) {
                                        $precioFinal =
                                            $fila['precioVuelo']
                                            -
                                            (
                                                $fila['precioVuelo']
                                                *
                                                $fila['descuento']
                                                / 100
                                            );
                                    }

                                    $origenOut = htmlspecialchars($fila['origenVuelo'], ENT_QUOTES, 'UTF-8');
                                    $destinoOut = htmlspecialchars($fila['destinoVuelo'], ENT_QUOTES, 'UTF-8');
                                    $nombreAerolineaOut = htmlspecialchars($fila['nombreAerolinea'], ENT_QUOTES, 'UTF-8');

                                    $imagenOut = htmlspecialchars($fila['imagenVuelo'] ?? '', ENT_QUOTES, 'UTF-8');

                                    $fechaVuelo = new DateTime($fila['fechaVuelo']);

                                ?>

                                    <tr>

                                        <td>

                                            <?php if ($imagenOut !== '') { ?>

                                                <img
                                                    src="../../uploads/vuelos/<?= $imagenOut ?>"
                                                    alt="Vuelo desde <?= $origenOut ?> hacia <?= $destinoOut ?>"
                                                    title="Imagen del vuelo <?= $origenOut ?> - <?= $destinoOut ?>"
                                                    loading="lazy"
                                                    style="
                                                        width:120px;
                                                        height:80px;
                                                        object-fit:cover;
                                                        border-radius:7px;">

                                            <?php } else { ?>

                                                <span class="text-muted small">Sin imagen</span>

                                            <?php } ?>

                                        </td>

                                        <td>

                                            <?= $nombreAerolineaOut ?>

                                        </td>

                                        <td>

                                            <?= $origenOut ?>

                                        </td>

                                        <td>

                                            <?= $destinoOut ?>

                                        </td>

                                        <td>

                                            <time datetime="<?= $fechaVuelo->format('Y-m-d') ?>">
                                                <?= $fechaVuelo->format('d/m/Y') ?>
                                            </time>

                                        </td>

                                        <td>

                                            <?php if ($fila['descuento'] > 0) { ?>

                                                <span class="visually-hidden">Precio original: </span>
                                                <del class="text-danger">

                                                    $<?= number_format(
                                                            $fila['precioVuelo'],
                                                            0,
                                                            ',',
                                                            '.'
                                                        ) ?>

                                                </del>

                                                <br>

                                                <span class="visually-hidden">Precio con descuento: </span>
                                                <span class="fw-bold text-success">

                                                    $<?= number_format($precioFinal, 0, ',','.') ?>

                                                </span>

                                            <?php } else { ?>

                                                $<?= number_format($fila['precioVuelo'], 0, ',', '.') ?>

                                            <?php } ?>

                                        </td>

                                        <td>

                                            <?php if ($fila['descuento'] > 0) { ?>

                                                <span class="badge bg-danger">

                                                    <span aria-hidden="true"></span> <?= (int) $fila['descuento'] ?>% OFF

                                                </span>

                                            <?php } else { ?>

                                                <span class="badge bg-secondary">

                                                    Sin promo

                                                </span>

                                            <?php } ?>

                                        </td>

                                        <td>

                                            <?= (int) $fila['asientosDisponibles'] ?>

                                        </td>

                                        <td>

                                            <?php

                                            if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'CLIENTE') {

                                                $idUsuario = (int) $_SESSION['id'];
                                                $codVueloInt = (int) $fila['codVuelo'];

                                                $sqlReserva = "SELECT codReserva FROM reservas
                                                    WHERE codUsuario = $idUsuario
                                                    AND codVuelo = $codVueloInt
                                                    AND estadoReserva != 'CANCELADA'
                                                    LIMIT 1";

                                                $resultadoReserva = mysqli_query($link,$sqlReserva);

                                                $yaReservado = $resultadoReserva && mysqli_num_rows($resultadoReserva) > 0;

                                                if ($yaReservado) {
                                            ?>

                                                    <a
                                                        href="../reservas/listar.php"
                                                        class="btn btn-primary btn-sm">

                                                        Ver reservas
                                                        <span class="visually-hidden"> del vuelo <?= $origenOut ?> a <?= $destinoOut ?></span>

                                                    </a>

                                                <?php
                                                } else {
                                                ?>

                                                    <a
                                                        href="../reservas/reservar.php?codVuelo=<?= $codVueloInt ?>"
                                                        class="btn btn-success btn-sm">

                                                        Reservar
                                                        <span class="visually-hidden"> vuelo <?= $origenOut ?> a <?= $destinoOut ?></span>

                                                    </a>

                                                <?php
                                                }
                                            } else {

                                                if (!isset($_SESSION['id'])) {
                                                ?>

                                                    <a
                                                        href= <?php echo ruta.'/auth/login.php' ?>
                                                        class="btn btn-warning btn-sm">

                                                        Iniciar sesión
                                                        <span class="visually-hidden"> para ver el vuelo <?= $origenOut ?> a <?= $destinoOut ?></span>

                                                    </a>

                                                <?php
                                                } else {
                                                ?>

                                                    <button
                                                        type="button"
                                                        class="btn btn-secondary btn-sm"
                                                        disabled>

                                                        Cuenta de cliente requerida

                                                    </button>

                                            <?php
                                                }
                                            }
                                            ?>

                                        </td>

                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                    <?php if ($totalPaginas > 1) { ?>

                        <div class="d-flex justify-content-center mt-4">

                            <nav aria-label="Paginación de vuelos">

                                <ul class="pagination flex-wrap justify-content-center">

                                    <?php if ($pagina > 1) { ?>

                                        <li class="page-item">

                                            <a
                                                class="page-link"
                                                href="<?= urlPaginaVuelos($pagina - 1, $origenFiltro, $destinoFiltro, $fechaFiltro, $promoFiltro) ?>">

                                                Anterior

                                            </a>

                                        </li>

                                    <?php } ?>

                                    <?php for ($i = 1; $i <= $totalPaginas; $i++) { ?>

                                        <li
                                            class="page-item <?= $i == $pagina ? 'active' : '' ?>">

                                            <a
                                                class="page-link"
                                                href="<?= urlPaginaVuelos($i, $origenFiltro, $destinoFiltro, $fechaFiltro, $promoFiltro) ?>"
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

                                            <a
                                                class="page-link"
                                                href="<?= urlPaginaVuelos($pagina + 1, $origenFiltro, $destinoFiltro, $fechaFiltro, $promoFiltro) ?>">
                                                Siguiente

                                            </a>

                                        </li>

                                    <?php } ?>

                                </ul>

                            </nav>

                        </div>

                    <?php } ?>

                </div>

            </div>

        <?php } ?>

    </div>

</main>

<?php include_once("../../includes/footer.php"); ?>