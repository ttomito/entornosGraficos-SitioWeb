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

$sqlConteo = "SELECT COUNT(*) AS total FROM aerolineas";
$resultadoConteo = mysqli_query($link, $sqlConteo);

if (!$resultadoConteo) {
    error_log("Error al contar aerolíneas: " . mysqli_error($link));
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
    $sql = "SELECT * FROM aerolineas ORDER BY codAerolinea LIMIT ?, ?";
    $stmt = mysqli_prepare($link, $sql);

    if (!$stmt) {
        error_log("Error al preparar el listado: " . mysqli_error($link));
        $errorConsulta = true;
    } else {
        mysqli_stmt_bind_param($stmt, "ii", $inicio, $registrosPorPagina);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if (!$resultado) {
            error_log("Error al listar aerolíneas: " . mysqli_error($link));
            $errorConsulta = true;
        }
    }
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2 id="titulo-listado">Gestión de Aerolíneas</h2>
        <a href="crear.php" class="btn btn-success">Nueva Aerolínea</a>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <?php if ($errorConsulta) { ?>

                <p class="text-danger">Ocurrió un error al cargar el listado. Intentá nuevamente más tarde.</p>

            <?php } elseif (mysqli_num_rows($resultado) === 0) { ?>

                <p class="text-muted">No hay aerolíneas registradas.</p>

            <?php } else { ?>

                <table class="table table-hover" aria-labelledby="titulo-listado">

                    <caption class="visually-hidden">
                        Listado de aerolíneas registradas, página <?= $pagina ?> de <?= $totalPaginas ?>
                    </caption>

                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">País</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while ($fila = mysqli_fetch_assoc($resultado)) {
                            $nombreEscapado = htmlspecialchars($fila['nombreAerolinea'], ENT_QUOTES, 'UTF-8');
                        ?>

                            <tr>
                                <td><?= htmlspecialchars($fila['codAerolinea'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= $nombreEscapado ?></td>
                                <td><?= htmlspecialchars($fila['descripcionAerolinea'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($fila['codPais'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>

                                    <a href="editar.php?id=<?= $fila['codAerolinea'] ?>"
                                        class="btn btn-warning btn-sm"
                                        aria-label="Editar aerolínea <?= $nombreEscapado ?>">
                                        Editar
                                    </a>

                                    <?php if ($fila['activo'] == 1) { ?>
                                        <form action="eliminar.php" method="post" class="d-inline" data-nombre="<?= $nombreEscapado ?>">
                                            <input type="hidden" name="id" value="<?= (int)$fila['codAerolinea'] ?>">
                                            <button type="submit"
                                                class="btn btn-danger btn-sm"
                                                aria-label="Desactivar aerolínea <?= $nombreEscapado ?>"
                                                onclick="confirmarEliminacion(event, this)">
                                                Desactivar
                                            </button>
                                        </form>
                                    <?php } else { ?>
                                        <form action="eliminar.php" method="post" class="d-inline" data-nombre="<?= $nombreEscapado ?>">
                                            <input type="hidden" name="id" value="<?= (int)$fila['codAerolinea'] ?>">
                                            <button type="submit"
                                                class="btn btn-success btn-sm"
                                                aria-label="Activar aerolínea <?= $nombreEscapado ?>"
                                                onclick="confirmarActivacion(event, this)">
                                                Activar
                                            </button>
                                        </form>
                                    <?php } ?>
                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>

                </table>
                <div class="d-flex justify-content-center mt-4">

                    <nav aria-label="Paginación del listado de aerolíneas">

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

                            for ($i = 1;$i <= $totalPaginas;$i++) {

                            ?>

                                <li
                                    class="page-item <?= $i == $pagina ? 'active' : '' ?>">

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

<?php

$alertas = [
    'eliminada' => [
        'icon'  => 'success',
        'title' => '¡Eliminada!',
        'text'  => 'La aerolínea fue eliminada correctamente.'
    ],
    'activada' => [
        'icon'  => 'success',
        'title' => '¡Activada!',
        'text'  => 'La aerolínea fue activada correctamente.'
    ],
    'vuelos_desactivados' => [
        'icon'  => 'warning',
        'title' => 'Aerolínea desactivada',
        'text'  => 'Se desactivaron los vuelos y reservas asociadas a la aerolínea.'
    ],
    'vuelos_activados' => [
        'icon'  => 'success',
        'title' => 'Aerolínea activada',
        'text'  => 'Se activaron los vuelos y reservas asociadas a la aerolínea.'
    ],
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
    ],
    'no_encontrada' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Aerolínea no encontrada.'
    ],
    'campos_vacios' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'No se pueden ingresar campos vacíos.'
    ],
    'actualizada' => [
        'icon'  => 'success',
        'title' => '¡Actualizada!',
        'text'  => 'La aerolínea fue actualizada.'
    ],
    'creada' => [
        'icon'  => 'success',
        'title' => '¡Creada!',
        'text'  => 'Se ha creado la aerolínea.'
    ]
];

if (isset($_GET['alerta']) && array_key_exists($_GET['alerta'], $alertas)) {
    $alerta = $alertas[$_GET['alerta']];
?>

    <script>
        Swal.fire({
            icon: '<?= $alerta['icon'] ?>',
            title: '<?= $alerta['title'] ?>',
            text: '<?= $alerta['text'] ?>',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'listar.php';
            }
        });
    </script>
<?php }; ?>

<script>
    function confirmarEliminacion(event, boton) {
        event.preventDefault();

        const formulario = boton.closest('form');
        const nombre = formulario.dataset.nombre;

        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Desea ocultar la aerolínea "${nombre}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, ocultar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                formulario.submit();
            }
        });
    }

    function confirmarActivacion(event, boton) {
        event.preventDefault();

        const formulario = boton.closest('form');
        const nombre = formulario.dataset.nombre;

        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Desea activar la aerolínea "${nombre}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, activar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                formulario.submit();
            }
        });
    }
</script>

<?php
include("../../includes/footer.php");
?>