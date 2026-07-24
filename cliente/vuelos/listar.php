<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

$registrosPorPagina = 10;

$pagina = isset($_GET['pagina'])
    ? (int)$_GET['pagina']
    : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$inicio =
    ($pagina - 1)
    *
    $registrosPorPagina;

$origenFiltro  = $_GET['origen'] ?? '';
$destinoFiltro = $_GET['destino'] ?? '';
$fechaFiltro   = $_GET['fecha'] ?? '';
$promoFiltro   = $_GET['promo'] ?? '';

$origenEsc  = mysqli_real_escape_string($link, addcslashes($origenFiltro, '%_'));
$destinoEsc = mysqli_real_escape_string($link, addcslashes($destinoFiltro, '%_'));

$fechaEsc = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFiltro) ? mysqli_real_escape_string($link, $fechaFiltro) : '';

$promoId = (int) $promoFiltro;

// Nota: cada fragmento arranca con un espacio explícito, para no depender
// de saltos de línea u otros espacios "accidentales" al concatenar.
function condicionesVuelos($promoId, $origenEsc, $destinoEsc, $fechaEsc, $incluirPromo = true, $incluirFecha = true)
{
    $cond = '';

    if ($incluirPromo && $promoId > 0) {
        $cond .= " AND v.codAerolinea = (SELECT codAerolinea FROM promociones WHERE codPromocion = $promoId)";
        $cond .= " AND LOWER(TRIM(v.destinoVuelo)) IN (SELECT LOWER(TRIM(destinoVuelo)) FROM promocionesDestinos WHERE codPromocion = $promoId)";
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


$sql = "SELECT v.*, a.nombreAerolinea,
COALESCE(MAX(p.descuentoPromocion), 0) AS descuento
FROM vuelos v
INNER JOIN aerolineas a
ON v.codAerolinea = a.codAerolinea
LEFT JOIN promocionesDestinos pd
ON LOWER(TRIM(pd.destinoVuelo)) = LOWER(TRIM(v.destinoVuelo))
LEFT JOIN promociones p
ON p.codPromocion = pd.codPromocion
AND p.codAerolinea = v.codAerolinea
AND p.estadoPromocion = 'APROBADA'
AND p.fechaLimitePromocion >= CURDATE()
WHERE 1=1";

$sql .= condicionesVuelos($promoId, $origenEsc, $destinoEsc, $fechaEsc);

$sqlConteo = "SELECT COUNT(*) AS total FROM vuelos v WHERE 1=1";
$sqlConteo .= condicionesVuelos($promoId, $origenEsc, $destinoEsc, $fechaEsc);

$resultadoConteo = mysqli_query($link, $sqlConteo);

$filaConteo = mysqli_fetch_assoc($resultadoConteo);
$totalRegistros = $filaConteo['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

$sql .= " GROUP BY v.codVuelo ORDER BY v.codVuelo DESC LIMIT $inicio, $registrosPorPagina";

$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    die(mysqli_error($link));
}


if (mysqli_num_rows($resultado) == 0 && $fechaEsc !== '') {

    $sql = "SELECT v.*, a.nombreAerolinea, COALESCE(MAX(p.descuentoPromocion), 0)
    AS descuento
    FROM vuelos v
    INNER JOIN aerolineas a
    ON v.codAerolinea = a.codAerolinea
    LEFT JOIN promocionesDestinos pd
    ON LOWER(TRIM(pd.destinoVuelo)) = LOWER(TRIM(v.destinoVuelo))
    LEFT JOIN promociones p
    ON p.codPromocion = pd.codPromocion
    AND p.codAerolinea = v.codAerolinea
    AND p.estadoPromocion = 'APROBADA'
    AND p.fechaLimitePromocion >= CURDATE()
    WHERE fechaVuelo >= CURDATE()";

    $sql .= condicionesVuelos($promoId, $origenEsc, $destinoEsc, $fechaEsc, false, false);
    $sql .= " GROUP BY v.codVuelo ORDER BY ABS(DATEDIFF(fechaVuelo,'$fechaEsc')) LIMIT $inicio,$registrosPorPagina";
    $resultado = mysqli_query($link, $sql);
    $fechaFlexible = true;
} else {

    $fechaFlexible = false;
}
?>

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
        !empty($promoFiltro) ||
        !empty($origenFiltro) ||
        !empty($destinoFiltro) ||
        !empty($fechaFiltro)
    ) {
    ?>

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

    <?php
    }
    ?>
    <?php

    if (isset($fechaFlexible) && $fechaFlexible) {
    ?>

        <div class="alert alert-warning" role="status">

            No se encontraron vuelos para la fecha seleccionada.

            Se muestran los vuelos disponibles para las fechas más cercanas.

        </div>

    <?php
    }
    ?>

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

                        $hoy = new DateTime();

                        $cantidadResultados = mysqli_num_rows($resultado);
                        while ($fila = mysqli_fetch_assoc($resultado)) {
                            $fechaVuelo = new DateTime($fila['fechaVuelo']);

                            if (
                                $fila['asientosDisponibles'] > 0
                                &&
                                $fechaVuelo >= $hoy
                            ) {

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
                                $fechaVueloOut = htmlspecialchars($fila['fechaVuelo'], ENT_QUOTES, 'UTF-8');
                                $imagenOut = htmlspecialchars($fila['imagenVuelo'], ENT_QUOTES, 'UTF-8');
                        ?>

                                <tr>

                                    <td>

                                        <img
                                            src="../../uploads/vuelos/<?= $imagenOut ?>"
                                            alt="Vuelo desde <?= $origenOut ?> hacia <?= $destinoOut ?>"

                                            style="
                                        width:120px;
                                        height:80px;
                                        object-fit:cover;
                                        border-radius:7px;">

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

                                        <time datetime="<?= $fechaVueloOut ?>"><?= $fechaVueloOut ?></time>

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

                                                $<?= number_format(
                                                        $precioFinal,
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) ?>

                                            </span>

                                        <?php } else { ?>

                                            $<?= number_format(
                                                    $fila['precioVuelo'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ) ?>

                                        <?php } ?>

                                    </td>

                                    <td>

                                        <?php if ($fila['descuento'] > 0) { ?>

                                            <span class="badge bg-danger">

                                                <span aria-hidden="true">🔥</span> <?= (int) $fila['descuento'] ?>% OFF

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

                                        if (
                                            isset($_SESSION['tipo'])
                                            &&
                                            $_SESSION['tipo'] == 'CLIENTE'
                                        ) {

                                            $idUsuario = (int) $_SESSION['id'];
                                            $codVueloInt = (int) $fila['codVuelo'];

                                            $sqlReserva = "SELECT * FROM reservas
                                        WHERE codUsuario = $idUsuario
                                        AND codVuelo = $codVueloInt
                                        AND estadoReserva != 'CANCELADA'
                                        LIMIT 1";

                                            $resultadoReserva =
                                                mysqli_query(
                                                    $link,
                                                    $sqlReserva
                                                );

                                            if (
                                                mysqli_num_rows(
                                                    $resultadoReserva
                                                ) > 0
                                            ) {
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
                                                    href="/entornosGraficos-SitioWeb/auth/login.php"
                                                    class="btn btn-warning btn-sm">

                                                    Iniciar sesión
                                                    <span class="visually-hidden"> para ver el vuelo <?= $origenOut ?> a <?= $destinoOut ?></span>

                                                </a>

                                            <?php
                                            } else {
                                            ?>

                                                <button
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

                        <?php
                            }
                        }
                        ?>

                    </tbody>


                </table>

            </div>

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

                        <?php

                        for (
                            $i = 1;
                            $i <= $totalPaginas;
                            $i++
                        ) {
                        ?>

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

                        <?php
                        }
                        ?>

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

        </div>

    </div>

</div>
<?php include("../../includes/footer.php"); ?>