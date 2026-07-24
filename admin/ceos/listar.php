<?php

include("../../includes/conexion.php");
include("../../includes/verificarSession.php");
include("../../includes/header.php");

?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

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



$sqlConteo = "

SELECT COUNT(*) AS total

FROM usuarios

WHERE tipoUsuario = 'CEO'

";

$resultadoConteo =
    mysqli_query($link, $sqlConteo);

$filaConteo =
    mysqli_fetch_assoc($resultadoConteo);

$totalRegistros =
    $filaConteo['total'];

$totalPaginas =
    ceil(
        $totalRegistros
            /
            $registrosPorPagina
    );



$sql = "

SELECT *

FROM usuarios

WHERE tipoUsuario = 'CEO'

ORDER BY
estadoCuenta,
nombreUsuario

LIMIT $inicio,
$registrosPorPagina

";

$resultado =
    mysqli_query($link, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($link));
}

?>

<div class="container mt-4">
    <div class="d-flex justify-content-between mb-4">

        <h2 id="titulo-listado-ceos">

            Gestión de CEOs

        </h2>

    </div>


    <div class="card card-custom">

        <div class="card-body">

            <?php if (mysqli_num_rows($resultado) == 0) { ?>

                <p class="text-muted">

                    No hay CEOs registrados.

                </p>

            <?php } else { ?>

                <table class="table table-hover" aria-labelledby="titulo-listado-ceos">

                    <caption class="visually-hidden">
                        Listado de CEOs, página <?= $pagina ?> de <?= $totalPaginas ?>
                    </caption>

                    <thead>

                        <tr>

                            <th scope="col">ID</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Email</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($fila = mysqli_fetch_assoc($resultado)) {
                            $nombreEscapado = htmlspecialchars($fila['nombreUsuario'], ENT_QUOTES, 'UTF-8');
                        ?>

                            <tr>

                                <td>

                                    <?= htmlspecialchars($fila['codUsuario'], ENT_QUOTES, 'UTF-8') ?>

                                </td>

                                <td>

                                    <?= $nombreEscapado ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($fila['emailUsuario'], ENT_QUOTES, 'UTF-8') ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($fila['estadoCuenta'], ENT_QUOTES, 'UTF-8') ?>

                                </td>

                                <td>
                                    <?php

                                    if ($fila['estadoCuenta'] == 'PENDIENTE') {
                                    ?>

                                        <a
                                            href="aprobar.php?id=<?= $fila['codUsuario'] ?>"
                                            class="btn btn-success btn-sm"
                                            aria-label="Aprobar a <?= $nombreEscapado ?>"
                                            data-nombre="<?= $nombreEscapado ?>"
                                            onclick="confirmarAprobacion(event, this)">

                                            Aprobar

                                        </a>

                                        <a
                                            href="rechazar.php?id=<?= $fila['codUsuario'] ?>"
                                            class="btn btn-danger btn-sm"
                                            aria-label="Rechazar a <?= $nombreEscapado ?>"
                                            data-nombre="<?= $nombreEscapado ?>"
                                            onclick="confirmarRechazo(event, this)">

                                            Rechazar

                                        </a>

                                    <?php
                                    } else {
                                    ?>

                                        <span class="text-muted">

                                            Sin acciones

                                        </span>

                                    <?php
                                    }
                                    ?>
                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>
                <div class="d-flex justify-content-center mt-4">

                    <nav aria-label="Paginación del listado de CEOs">

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

<script>
    function confirmarAprobacion(event, elemento) {
        event.preventDefault();

        const nombre = elemento.dataset.nombre;

        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Desea aprobar a "${nombre}" como CEO?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = elemento.href;
            }
        });
    }

    function confirmarRechazo(event, elemento) {
        event.preventDefault();

        const nombre = elemento.dataset.nombre;

        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Desea rechazar a "${nombre}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, rechazar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = elemento.href;
            }
        });
    }
</script>

<?php
include("../../includes/footer.php");
?>