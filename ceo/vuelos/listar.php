<?php

include("../../includes/verificarSessionCeo.php");
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
    <div class="container mt-4">
        <div class="alert alert-danger" role="alert">
            Ocurrió un error al cargar tus datos. Intentá nuevamente más tarde.
        </div>
    </div>
<?php
    include("../../includes/footer.php");
    exit();
}

if ($codAerolinea === null) {
?>
    <div class="container mt-4">
        <div class="alert alert-warning" role="alert">
            <h4>Aerolínea no asignada</h4>
            <p>Un administrador todavía no le asignó una aerolínea. No puede gestionar vuelos hasta que eso ocurra.</p>
        </div>
    </div>
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
    $sql = "SELECT * FROM vuelos WHERE codAerolinea = ? ORDER BY fechaVuelo LIMIT ?, ?";
    $stmt = mysqli_prepare($link, $sql);

    if (!$stmt) {
        error_log("Error al preparar el listado de vuelos: " . mysqli_error($link));
        $errorConsulta = true;
    } else {
        mysqli_stmt_bind_param($stmt, "iii", $codAerolinea, $inicio, $registrosPorPagina);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if (!$resultado) {
            error_log("Error al listar vuelos: " . mysqli_error($link));
            $errorConsulta = true;
        }
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<div class="container mt-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

        <h2 id="main-heading" tabindex="-1">Gestión de Vuelos</h2>
        <a href="crear.php" class="btn btn-success">Nuevo Vuelo</a>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <?php if ($errorConsulta) { ?>

                <div class="alert alert-danger" role="alert">
                    Ocurrió un error al cargar el listado. Intentá nuevamente más tarde.
                </div>

            <?php } elseif (mysqli_num_rows($resultado) === 0) { ?>

                <p class="texto-secundario-accesible">No hay vuelos registrados para esta aerolínea.</p>

            <?php } else { ?>

                <div class="table-responsive">

                    <table class="table table-hover">

                        <caption class="visually-hidden">Listado de vuelos de la aerolínea, ordenados por fecha</caption>

                        <thead>

                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Origen</th>
                                <th scope="col">Destino</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Hora</th>
                                <th scope="col">Precio</th>
                                <th scope="col">Asientos</th>
                                <th scope="col">Acciones</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php while ($fila = mysqli_fetch_assoc($resultado)) {

                                $codVueloInt = (int) $fila['codVuelo'];
                                $origenOut = htmlspecialchars($fila['origenVuelo'], ENT_QUOTES, 'UTF-8');
                                $destinoOut = htmlspecialchars($fila['destinoVuelo'], ENT_QUOTES, 'UTF-8');
                                $fechaOut = htmlspecialchars($fila['fechaVuelo'], ENT_QUOTES, 'UTF-8');
                                $horaOut = htmlspecialchars($fila['horaSalida'], ENT_QUOTES, 'UTF-8');

                            ?>

                                <tr>
                                    <td><?= $codVueloInt ?></td>
                                    <td><?= $origenOut ?></td>
                                    <td><?= $destinoOut ?></td>
                                    <td><time datetime="<?= $fechaOut ?>"><?= $fechaOut ?></time></td>
                                    <td><?= $horaOut ?></td>
                                    <td>$<?= number_format((float)$fila['precioVuelo'], 0, ',', '.') ?></td>
                                    <td><?= (int) $fila['asientosDisponibles'] ?></td>
                                    <td>
                                        <a href="editar.php?id=<?= $codVueloInt ?>"
                                            class="btn btn-warning btn-sm"
                                            role="button">
                                            Editar
                                        </a>

                                        <form action="eliminar.php" method="post" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="id" value="<?= $codVueloInt ?>">

                                            <?php if ($fila['activo'] == 1) { ?>
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return ocultarVuelo(event, this)">
                                                    Ocultar
                                                </button>
                                            <?php } else { ?>
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return activarVuelo(event, this)">
                                                    Activar
                                                </button>
                                            <?php } ?>
                                        </form>
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

                <div class="d-flex justify-content-center mt-4">

                    <nav aria-label="Paginación de vuelos">

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

            <?php } ?>

        </div>

    </div>

</div>

<?php

$alertas = [
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
    ],
    'acceso_denegado' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'No se encontró el vuelo solicitado.'
    ],
    'creado' => [
        'icon'  => 'success',
        'title' => '¡Creado!',
        'text'  => 'Se ha creado el vuelo.'
    ],
    'eliminado' => [
        'icon'  => 'success',
        'title' => '¡Oculto!',
        'text'  => 'Se ha ocultado el vuelo y sus reservas.'
    ],
    'activado' => [
        'icon'  => 'success',
        'title' => '¡Activado!',
        'text'  => 'Se ha activado el vuelo y sus reservas.'
    ],
    'campos_vacios' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'No pueden haber campos vacíos.'
    ],
    'actualizado' => [
        'icon'  => 'success',
        'title' => '¡Modificado!',
        'text'  => 'Se ha actualizado el vuelo.'
    ],
    'fecha_invalida' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'La fecha del vuelo debe ser mayor a hoy.'
    ],
    'hora_invalida' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'La hora no tiene un formato válido.'
    ],
    'precio_invalido' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'El precio debe estar entre 50 y 5.000.000.'
    ],
    'asientos_invalidos' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'Los asientos deben ser un número de 0 a 500.'
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
    function ocultarVuelo(event, boton) {
        event.preventDefault();

        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Desea ocultar este vuelo? Al hacerlo también se desactivarán las reservas asociadas.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, ocultar',
            cancelButtonText: 'Cancelar',
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                boton.closest('form').submit();
            }
        });

        return false;
    }

    function activarVuelo(event, boton) {
        event.preventDefault();

        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Desea activar el vuelo? Al hacerlo también se activarán las reservas asociadas',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, activar',
            cancelButtonText: 'Cancelar',
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                boton.closest('form').submit();
            }
        });

        return false;
    }
</script>

<?php
include("../../includes/footer.php");
?>