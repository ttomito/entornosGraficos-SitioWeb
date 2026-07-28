<?php

include("../../includes/verificarSessionCEO.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$registrosPorPagina = 10;
$errorConsulta = false;

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina']: 1;

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

            <div class="row justify-content-center">

                <div class="col-md-8">

                    <div class="alert alert-danger" role="alert">
                        Ocurrió un error al cargar tus datos. Intentá nuevamente más tarde.
                    </div>

                </div>

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

            <div class="row justify-content-center">

                <div class="col-md-8">

                    <div class="card card-custom">

                        <div class="card-body p-5 text-center" role="alert">

                            <h2 class="text-danger">

                                Tu cuenta todavía no está vinculada a una aerolínea

                            </h2>

                            <p>

                                Un administrador tiene que asociar tu cuenta a una aerolínea antes de que puedas gestionar promociones.

                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>

<?php

    include("../../includes/footer.php");
    exit();
}

$sqlConteo = "SELECT COUNT(*) AS total FROM promociones WHERE codAerolinea = ?";
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
    $sql = "SELECT * FROM promociones WHERE codAerolinea = ? ORDER BY codPromocion DESC LIMIT ?, ?";
    $stmt = mysqli_prepare($link, $sql);

    if (!$stmt) {
        error_log("Error al preparar el listado de promociones: " . mysqli_error($link));
        $errorConsulta = true;
    } else {
        mysqli_stmt_bind_param($stmt, "iii", $codAerolinea, $inicio, $registrosPorPagina);
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

<main id="contenido-principal">

    <div class="container mt-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

            <h2>Gestión de Promociones</h2>
            <a href="crear.php" class="btn btn-success">Nueva Promoción</a>

        </div>

        <?php if ($errorConsulta) { ?>

            <div class="alert alert-danger" role="alert">
                Ocurrió un error al cargar el listado. Intentá nuevamente más tarde.
            </div>

        <?php } elseif (mysqli_num_rows($resultado) > 0) { ?>

            <div class="card card-custom">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover">

                            <caption class="visually-hidden">
                                Listado de promociones de la aerolínea
                            </caption>

                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Descripción</th>
                                    <th scope="col">Descuento</th>
                                    <th scope="col">Fecha límite</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php while ($fila = mysqli_fetch_assoc($resultado)) {

                                    $codPromocionInt = (int) $fila['codPromocion'];
                                    $descripcionOut = htmlspecialchars($fila['descripcionPromocion'], ENT_QUOTES, 'UTF-8');
                                    $fechaLimiteOut = htmlspecialchars($fila['fechaLimitePromocion'], ENT_QUOTES, 'UTF-8');
                                    $estadoOut = htmlspecialchars($fila['estadoPromocion'], ENT_QUOTES, 'UTF-8');

                                ?>

                                    <tr>
                                        <td><?= $codPromocionInt ?></td>
                                        <td><?= $descripcionOut ?></td>
                                        <td><?= (int) $fila['descuentoPromocion'] ?>%</td>
                                        <td><time datetime="<?= $fechaLimiteOut ?>"><?= $fechaLimiteOut ?></time></td>
                                        <td><?= $estadoOut ?></td>

                                        <td>
                                            <a href="editar.php?id=<?= $codPromocionInt ?>" class="btn btn-warning btn-sm">
                                                Editar
                                                <span class="visually-hidden"> promoción "<?= $descripcionOut ?>"</span>
                                            </a>

                                            <form action="eliminar.php" method="post" class="d-inline" data-descripcion="<?= $descripcionOut ?>">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="id" value="<?= $codPromocionInt ?>">
                                                <button type="submit" class="btn btn-danger btn-sm eliminar-promocion">
                                                    Eliminar
                                                    <span class="visually-hidden"> promoción "<?= $descripcionOut ?>"</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                    <div class="d-flex justify-content-center mt-4">

                        <nav aria-label="Paginación de promociones">

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
                </div>

            </div>

        <?php } else { ?>

            <div class="alert alert-info" role="status">
                No hay promociones registradas.
            </div>

        <?php } ?>

    </div>

</main>

<?php

$alertas = [
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
    ],
    'no_encontrada' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'No se encontró la promoción.'
    ],
    'creada' => [
        'icon'  => 'success',
        'title' => '¡Creado!',
        'text'  => 'Se ha creado la promoción.'
    ],
    'eliminado' => [
        'icon'  => 'success',
        'title' => '¡Eliminada!',
        'text'  => 'Se ha eliminado la promoción.'
    ],
    'modificada' => [
        'icon'  => 'success',
        'title' => '¡Modificado!',
        'text'  => 'Se ha actualizado la promoción.'
    ],
    'fecha_invalida' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'La fecha límite debe ser posterior a hoy.'
    ],
    'descuento_invalido' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'El descuento debe estar entre 1% y 100%.'
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
    document.querySelectorAll('.eliminar-promocion').forEach(function(boton) {
        boton.addEventListener('click', function(evento) {

            const formulario = boton.closest('form');
            const descripcion = formulario.dataset.descripcion;

            if (typeof Swal === 'undefined') {
                if (confirm('¿Eliminar la promoción "' + descripcion + '"?')) {
                    formulario.submit();
                }
                return;
            }

            evento.preventDefault();

            Swal.fire({
                title: '¿Eliminar promoción?',
                text: '¿Desea eliminar la promoción "' + descripcion + '"? Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((resultado) => {
                if (resultado.isConfirmed) {
                    formulario.submit();
                }
            });
        });
    });
</script>

<?php
include("../../includes/footer.php");
?>