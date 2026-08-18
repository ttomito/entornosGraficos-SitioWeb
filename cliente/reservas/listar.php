<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../../login.php");
    exit;
}

$idCliente = (int) $_SESSION['id'];

function fallarBD($mensaje)
{
    echo '<div class="container mt-4">';
    echo '<div class="alert alert-danger" role="alert">';
    echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');
    echo '</div>';
    echo '</div>';

    include("../../includes/footer.php");
    exit;
}

$registrosPorPagina = 10;

$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$sqlConteo = "SELECT COUNT(*) AS total FROM reservas WHERE codUsuario = ?";

$stmtConteo = mysqli_prepare($link, $sqlConteo);

if (!$stmtConteo) {
    fallarBD("No se pudo consultar el historial de reservas. Intentá de nuevo en unos minutos.");
}

mysqli_stmt_bind_param($stmtConteo, "i", $idCliente);
mysqli_stmt_execute($stmtConteo);

$resultadoConteo = mysqli_stmt_get_result($stmtConteo);
$filaConteo      = mysqli_fetch_assoc($resultadoConteo);
$totalRegistros  = (int) $filaConteo['total'];

mysqli_stmt_close($stmtConteo);

$totalPaginas = (int) ceil($totalRegistros / $registrosPorPagina);

if ($totalPaginas > 0 && $pagina > $totalPaginas) {
    $pagina = $totalPaginas;
}

$inicio = ($pagina - 1) * $registrosPorPagina;

$sql = "SELECT r.codReserva,
               r.cantAsientos,
               r.precioFinal,
               r.estadoReserva,
               r.fechaReserva,
               r.activo,
               v.origenVuelo,
               v.destinoVuelo,
               v.fechaVuelo,
               v.imagenVuelo
        FROM reservas r
        LEFT JOIN vuelos v ON v.codVuelo = r.codVuelo
        WHERE r.codUsuario = ?
        ORDER BY r.codReserva DESC
        LIMIT ?, ?";

$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    fallarBD("No se pudo consultar el historial de reservas. Intentá de nuevo en unos minutos.");
}

mysqli_stmt_bind_param($stmt, "iii", $idCliente, $inicio, $registrosPorPagina);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

if (!$resultado) {
    fallarBD("No se pudo consultar el historial de reservas. Intentá de nuevo en unos minutos.");
}

$badgesEstado = [
    'CONFIRMADA' => ['clase' => 'bg-success',            'texto' => 'Confirmada'],
    'PENDIENTE'  => ['clase' => 'bg-warning text-dark',  'texto' => 'Pendiente'],
    'CANCELADA'  => ['clase' => 'bg-danger',             'texto' => 'Cancelada'],
];

$hoy = strtotime(date('Y-m-d'));

?>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">
        <h2>Historial de reservas</h2>
    </div>

    <?php if ($totalRegistros === 0) { ?>

        <div class="alert alert-info" role="status">
            Todavía no tenés reservas registradas.
        </div>

    <?php } else { ?>

        <div class="card card-custom">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">

                        <caption class="visually-hidden">Historial de reservas del usuario</caption>

                        <thead>
                            <tr>
                                <th scope="col">Imagen</th>
                                <th scope="col">Asientos</th>
                                <th scope="col">Origen</th>
                                <th scope="col">Destino</th>
                                <th scope="col">Fecha vuelo</th>
                                <th scope="col">Fecha reserva</th>
                                <th scope="col">Precio final</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acción</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>

                                <?php

                                $hayVuelo = !is_null($fila['origenVuelo']);

                                $origenOut  = $hayVuelo
                                    ? htmlspecialchars($fila['origenVuelo'], ENT_QUOTES, 'UTF-8')
                                    : 'No disponible';

                                $destinoOut = $hayVuelo
                                    ? htmlspecialchars($fila['destinoVuelo'], ENT_QUOTES, 'UTF-8')
                                    : 'No disponible';

                                $tsVuelo = ($hayVuelo && !empty($fila['fechaVuelo']))
                                    ? strtotime($fila['fechaVuelo'])
                                    : false;

                                $fechaVueloOut = $tsVuelo
                                    ? date("d/m/Y", $tsVuelo)
                                    : '—';

                                // Solo lo damos por pasado si conocemos la fecha
                                $vueloPasado = ($tsVuelo !== false && $tsVuelo < $hoy);

                                $fechaReservaOut = !empty($fila['fechaReserva'])
                                    ? date("d/m/Y", strtotime($fila['fechaReserva']))
                                    : '—';

                                $imagenOut = ($hayVuelo && !empty($fila['imagenVuelo']))
                                    ? htmlspecialchars($fila['imagenVuelo'], ENT_QUOTES, 'UTF-8')
                                    : '';

                                $codReservaInt = (int) $fila['codReserva'];

                                $estado = strtoupper(trim((string) $fila['estadoReserva']));

                                $badge = isset($badgesEstado[$estado])
                                    ? $badgesEstado[$estado]
                                    : ['clase' => 'bg-secondary', 'texto' => ucfirst(strtolower($estado))];

                                ?>

                                <tr>

                                    <td>
                                        <?php if ($imagenOut !== '') { ?>
                                            <img
                                                src="../../uploads/vuelos/<?= $imagenOut ?>"
                                                alt="Vuelo de <?= $origenOut ?> a <?= $destinoOut ?>"
                                                title="<?= $origenOut ?> a <?= $destinoOut ?> (<?= $fechaVueloOut ?>)"
                                                class="img-vuelo"
                                                loading="lazy">
                                        <?php } else { ?>
                                            <div class="img-vuelo-placeholder" role="presentation"></div>
                                        <?php } ?>
                                    </td>

                                    <td><?= (int) $fila['cantAsientos'] ?></td>

                                    <td><?= $origenOut ?></td>

                                    <td><?= $destinoOut ?></td>

                                    <td><?= $fechaVueloOut ?></td>

                                    <td><?= $fechaReservaOut ?></td>

                                    <td>
                                        $<?= number_format((float) $fila['precioFinal'], 0, ',', '.') ?>
                                    </td>

                                    <td>
                                        <span class="badge <?= $badge['clase'] ?>">
                                            <?= htmlspecialchars($badge['texto'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php if ($vueloPasado) { ?>
                                        
                                            <span class="text-muted small">Vuelo finalizado</span>

                                        <?php }elseif($fila['activo'] == 0) { ?>

                                            <span class="text-muted small">Vuelo desactivado. Comuníquese a través de contacto.</span>

                                        <?php } else { ?>
                                            <a
                                                href="verReserva.php?codReserva=<?= $codReservaInt ?>"
                                                class="btn btn-primary btn-sm">
                                                Seguir solicitud
                                                <span class="visually-hidden">
                                                    del vuelo <?= $origenOut ?> a <?= $destinoOut ?>,
                                                    reservado el <?= $fechaReservaOut ?>
                                                </span>
                                            </a>
                                        <?php } ?>
                                    </td>

                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>
                </div>

            </div>
        </div>

        <?php if ($totalPaginas > 1) { ?>

            <div class="d-flex justify-content-center mt-4">

                <nav aria-label="Paginación del historial de reservas">

                    <ul class="pagination">

                        <?php if ($pagina > 1) { ?>
                            <li class="page-item">
                                <a class="page-link" href="?pagina=<?= $pagina - 1 ?>">Anterior</a>
                            </li>
                        <?php } ?>

                        <?php

                        // Ventana de páginas: actual ± 2, con puntos suspensivos
                        $desde = max(1, $pagina - 2);
                        $hasta = min($totalPaginas, $pagina + 2);

                        if ($desde > 1) { ?>
                            <li class="page-item">
                                <a class="page-link" href="?pagina=1">1</a>
                            </li>
                            <?php if ($desde > 2) { ?>
                                <li class="page-item disabled">
                                    <span class="page-link">…</span>
                                </li>
                            <?php }
                        } ?>

                        <?php for ($i = $desde; $i <= $hasta; $i++) { ?>
                            <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                                <a
                                    class="page-link"
                                    href="?pagina=<?= $i ?>"
                                    <?= $i === $pagina ? 'aria-current="page"' : '' ?>>
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if ($hasta < $totalPaginas) {
                            if ($hasta < $totalPaginas - 1) { ?>
                                <li class="page-item disabled">
                                    <span class="page-link">…</span>
                                </li>
                            <?php } ?>
                            <li class="page-item">
                                <a class="page-link" href="?pagina=<?= $totalPaginas ?>"><?= $totalPaginas ?></a>
                            </li>
                        <?php } ?>

                        <?php if ($pagina < $totalPaginas) { ?>
                            <li class="page-item">
                                <a class="page-link" href="?pagina=<?= $pagina + 1 ?>">Siguiente</a>
                            </li>
                        <?php } ?>

                    </ul>

                </nav>

            </div>

        <?php } ?>

    <?php } ?>

</div>

<?php

mysqli_stmt_close($stmt);

include("../../includes/footer.php");