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

$sqlConteo = "SELECT COUNT(*) AS total FROM novedades";
$resultadoConteo = mysqli_query($link, $sqlConteo);

if (!$resultadoConteo) {
    error_log("Error al contar novedades: " . mysqli_error($link));
    $errorConsulta = true;
    $totalRegistros = 0;
} else {
    $filaConteo = mysqli_fetch_assoc($resultadoConteo);
    $totalRegistros = (int)$filaConteo['total'];
}

$totalPaginas = (int)ceil($totalRegistros / $registrosPorPagina);

if ($totalPaginas > 0 && $pagina > $totalPaginas) {
    $pagina = $totalPaginas;
}

$inicio = ($pagina - 1) * $registrosPorPagina;

$resultado = null;

if (!$errorConsulta) {
    $sql = "SELECT * FROM novedades ORDER BY codNovedad DESC LIMIT ?, ?";
    $stmt = mysqli_prepare($link, $sql);

    if (!$stmt) {
        error_log("Error al preparar el listado de novedades: " . mysqli_error($link));
        $errorConsulta = true;
    } else {
        mysqli_stmt_bind_param($stmt, "ii", $inicio, $registrosPorPagina);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if (!$resultado) {
            error_log("Error al listar novedades: " . mysqli_error($link));
            $errorConsulta = true;
        }
    }
}

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>Gestión de Novedades</h2>

        <a href="crear.php" class="btn btn-success">Nueva Novedad</a>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <?php if ($errorConsulta) { ?>

                <p class="text-danger">

                    Ocurrió un error al cargar el listado. Intentá nuevamente más tarde.

                </p>

            <?php } elseif (mysqli_num_rows($resultado) == 0) { ?>

                <p class="text-muted">

                    No hay novedades registradas.

                </p>

            <?php } else { ?>

                <table class="table table-hover">
                    <thead>

                        <tr>

                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Novedad</th>
                            <th>Publicación</th>
                            <th>Expiración</th>
                            <th>Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($fila = mysqli_fetch_assoc($resultado)) {
                            $tituloEscapado = htmlspecialchars($fila['tituloNovedad'] ?? '', ENT_QUOTES, 'UTF-8');
                            $textoEscapado = htmlspecialchars($fila['textoNovedad'] ?? '', ENT_QUOTES, 'UTF-8');
                        ?>

                            <tr>

                                <td><?= (int)$fila['codNovedad'] ?></td>
                                <td>
                                    <?php if (!empty($fila['imagen'])) { ?>
                                        <img
                                            src="../../uploads/novedades/<?= htmlspecialchars($fila['imagen'], ENT_QUOTES, 'UTF-8') ?>"
                                            alt="Imagen relacionada a la novedad: <?= $tituloEscapado ?>"
                                            title="<?= $tituloEscapado ?>"
                                            style="height: 60px; width: 90px; object-fit: cover; border-radius: 4px;">
                                    <?php } else { ?>
                                        <span class="text-muted">Sin imagen</span>
                                    <?php } ?>
                                </td>
                                <td><?= $textoEscapado ?></td>
                                <td><?= htmlspecialchars($fila['fechaPublicacion'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($fila['fechaExpiracion'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>

                                    <a href="editar.php?id=<?= (int)$fila['codNovedad'] ?>" class="btn btn-warning btn-sm">
                                        Editar
                                    </a>

                                    <form action="eliminar.php" method="post" class="d-inline" data-titulo="<?= $tituloEscapado ?>">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="id" value="<?= (int)$fila['codNovedad'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="eliminarNovedad(event, this)">
                                            Eliminar
                                        </button>
                                    </form>

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

                <div class="d-flex justify-content-center mt-4">

                    <nav>

                        <ul class="pagination">

                            <?php if ($pagina > 1) { ?>

                                <li class="page-item">

                                    <a
                                        class="page-link"
                                        href="?pagina=<?= $pagina - 1 ?>">

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
                                        href="?pagina=<?= $i ?>">

                                        <?= $i ?>

                                    </a>

                                </li>

                            <?php } ?>

                            <?php if ($pagina < $totalPaginas) { ?>

                                <li class="page-item">

                                    <a
                                        class="page-link"
                                        href="?pagina=<?= $pagina + 1 ?>">

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
        'text'  => 'La novedad fue eliminada correctamente.'
    ],
    'actualizada' => [
        'icon'  => 'success',
        'title' => '¡Actualizada!',
        'text'  => 'La novedad fue actualizada correctamente.'
    ],
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
    ],
    'creada' => [
        'icon'  => 'success',
        'title' => '¡Creada!',
        'text'  => 'La novedad fue creada correctamente.'
    ],
    'campos_vacios' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'Los campos no pueden ser vacíos.'
    ],
    'imagen_invalida' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'Formato de la imagen inválido.'
    ],
    'imagen_muy_grande' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'La imagen puede pesar hasta 3MB.'
    ],
    'error_imagen' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'Ocurrió un error con la imagen.'
    ],
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
    function eliminarNovedad(event, boton) {
        event.preventDefault();

        const formulario = boton.closest('form');
        const titulo = formulario.dataset.titulo;

        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Desea eliminar la novedad "${titulo}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
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