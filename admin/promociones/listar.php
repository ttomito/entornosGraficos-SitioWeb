<?php

include("../../includes/verificarSessionAdmin.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$registrosPorPagina = 10;
$errorConsulta = false;

$pagina = isset($_GET['pagina'])
    ? (int)$_GET['pagina']
    : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$sqlConteo = "SELECT COUNT(*) AS total FROM promociones";
$resultadoConteo = mysqli_query($link, $sqlConteo);

if (!$resultadoConteo) {
    error_log("Error al contar promociones: " . mysqli_error($link));
    $errorConsulta = true;
    $totalRegistros = 0;
} else {
    $filaConteo = mysqli_fetch_assoc($resultadoConteo);
    $totalRegistros = (int)$filaConteo['total'];
}

$totalPaginas = (int)ceil($totalRegistros / $registrosPorPagina);

// Evita pedir una página que no existe
if ($totalPaginas > 0 && $pagina > $totalPaginas) {
    $pagina = $totalPaginas;
}

$inicio = ($pagina - 1) * $registrosPorPagina;

$resultado = null;

if (!$errorConsulta) {
    $sql = "
        SELECT
            p.*,
            a.nombreAerolinea
        FROM promociones p
        INNER JOIN aerolineas a ON p.codAerolinea = a.codAerolinea
        ORDER BY p.estadoPromocion, p.codPromocion DESC
        LIMIT ?, ?
    ";

    $stmt = mysqli_prepare($link, $sql);

    if (!$stmt) {
        error_log("Error al preparar el listado de promociones: " . mysqli_error($link));
        $errorConsulta = true;
    } else {
        mysqli_stmt_bind_param($stmt, "ii", $inicio, $registrosPorPagina);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if (!$resultado) {
            error_log("Error al listar promociones: " . mysqli_error($link));
            $errorConsulta = true;
        }
    }
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2 id="titulo-promociones">

            Aprobación de Promociones

        </h2>

    </div>

    <div class="card card-custom">

        <div class="card-body">


            <?php if ($errorConsulta) { ?>

                <p class="text-danger">Ocurrió un error al cargar el listado. Intentá nuevamente más tarde.</p>

            <?php } elseif (mysqli_num_rows($resultado) === 0) { ?>

                <p class="text-muted">

                    No hay promociones registradas.

                </p>

            <?php } else { ?>

                <table class="table table-hover" aria-labelledby="titulo-promociones">

                    <caption class="visually-hidden">
                        Listado de promociones, página <?= $pagina ?> de <?= $totalPaginas ?>
                    </caption>

                    <thead>

                        <tr>

                            <th scope="col">ID</th>
                            <th scope="col">Aerolínea</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Descuento</th>
                            <th scope="col">Fecha límite</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($fila = mysqli_fetch_assoc($resultado)) {
                            $aerolineaEscapada = htmlspecialchars($fila['nombreAerolinea'], ENT_QUOTES, 'UTF-8');
                        ?>

                            <tr>

                                <td><?= htmlspecialchars($fila['codPromocion'], ENT_QUOTES, 'UTF-8') ?></td>

                                <td><?= $aerolineaEscapada ?></td>

                                <td><?= htmlspecialchars($fila['descripcionPromocion'], ENT_QUOTES, 'UTF-8') ?></td>

                                <td><?= htmlspecialchars($fila['descuentoPromocion'], ENT_QUOTES, 'UTF-8') ?>%</td>

                                <td><?= htmlspecialchars($fila['fechaLimitePromocion'], ENT_QUOTES, 'UTF-8') ?></td>

                                <td><?= htmlspecialchars($fila['estadoPromocion'], ENT_QUOTES, 'UTF-8') ?></td>

                                <td>

                                    <?php
                                    if ($fila['estadoPromocion'] == 'PENDIENTE') {
                                    ?>

                                        <form action="aprobar.php" method="post" class="d-inline" data-aerolinea="<?= $aerolineaEscapada ?>">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="id" value="<?= (int)$fila['codPromocion'] ?>">
                                            <button type="submit"
                                                class="btn btn-success btn-sm"
                                                aria-label="Aprobar promoción #<?= (int)$fila['codPromocion'] ?> de <?= $aerolineaEscapada ?>"
                                                onclick="confirmarAprobacion(event, this)">
                                                Aprobar
                                            </button>
                                        </form>

                                        <form action="rechazar.php" method="post" class="d-inline" data-aerolinea="<?= $aerolineaEscapada ?>">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="id" value="<?= (int)$fila['codPromocion'] ?>">
                                            <button type="submit"
                                                class="btn btn-danger btn-sm"
                                                aria-label="Rechazar promoción #<?= (int)$fila['codPromocion'] ?> de <?= $aerolineaEscapada ?>"
                                                onclick="confirmarRechazo(event, this)">
                                                Rechazar
                                            </button>
                                        </form>

                                    <?php
                                    }
                                    ?>

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-4">

                    <nav aria-label="Paginación de promociones">

                        <ul class="pagination">

                            <?php if ($pagina > 1) { ?>

                                <li class="page-item">

                                    <a
                                        class="page-link"
                                        href="?pagina=<?= $pagina - 1 ?>"
                                        aria-label="Ir a la página anterior">

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

                                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">

                                    <a
                                        class="page-link"
                                        href="?pagina=<?= $i ?>"
                                        aria-label="Ir a la página <?= $i ?>"
                                        <?= $i == $pagina ? 'aria-current="page"' : '' ?>>

                                        <?= $i ?>

                                    </a>

                                </li>

                            <?php } ?>

                            <?php if ($pagina < $totalPaginas) { ?>

                                <li class="page-item">

                                    <a
                                        class="page-link"
                                        href="?pagina=<?= $pagina + 1 ?>"
                                        aria-label="Ir a la página siguiente">

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

</div>

<script>
    function confirmarAprobacion(event, boton) {
        event.preventDefault();

        const formulario = boton.closest('form');
        const aerolinea = formulario.dataset.aerolinea;

        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Desea aprobar la promoción de "${aerolinea}"? Esto denegará cualquier otra promoción aprobada de la misma aerolínea.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                formulario.submit();
            }
        });
    }

    function confirmarRechazo(event, boton) {
        event.preventDefault();

        const formulario = boton.closest('form');
        const aerolinea = formulario.dataset.aerolinea;

        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Desea rechazar la promoción de "${aerolinea}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, rechazar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                formulario.submit();
            }
        });
    }
</script>

<?php
$alertasPromociones = [
    'aprobada' => [
        'icon'  => 'success',
        'title' => '¡Aprobada!',
        'text'  => 'La promoción fue aprobada y se notificó al CEO por correo.'
    ],
    'rechazada' => [
        'icon'  => 'success',
        'title' => '¡Rechazada!',
        'text'  => 'La promoción fue rechazada y se notificó al CEO por correo.'
    ],
    'no_encontrada' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'No se encontró la promoción o el CEO asociado.'
    ],
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error al procesar la promoción. Intente nuevamente.'
    ]
];

if (isset($_GET['alerta']) && array_key_exists($_GET['alerta'], $alertasPromociones)) {
    $alertaPromocion = $alertasPromociones[$_GET['alerta']];
?>

    <script>
        Swal.fire({
            icon: '<?= $alertaPromocion['icon'] ?>',
            title: '<?= $alertaPromocion['title'] ?>',
            text: '<?= $alertaPromocion['text'] ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php } ?>

<?php
include("../../includes/footer.php");
?>