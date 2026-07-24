<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

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

AND aprobadoAdmin = 'SI'

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

SELECT
u.*,
a.nombreAerolinea

FROM usuarios u

LEFT JOIN aerolineas a
ON u.codAerolinea = a.codAerolinea

WHERE u.tipoUsuario = 'CEO'

AND u.aprobadoAdmin = 'SI'

ORDER BY
u.nombreUsuario

LIMIT $inicio,
$registrosPorPagina

";

$resultado =
    mysqli_query($link, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($link));
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2 id="titulo-asignacion">

            Asignación de Aerolíneas

        </h2>
    </div>
    <div class="card card-custom">

        <div class="card-body">


            <?php if (mysqli_num_rows($resultado) == 0) { ?>

                <p class="text-muted">

                    No hay CEOs aprobados.

                </p>

            <?php } else { ?>
                <table class="table table-hover" aria-labelledby="titulo-asignacion">

                    <caption class="visually-hidden">
                        Listado de CEOs aprobados y su aerolínea asignada, página <?= $pagina ?> de <?= $totalPaginas ?>
                    </caption>

                    <thead>

                        <tr>

                            <th scope="col">CEO</th>
                            <th scope="col">Email</th>
                            <th scope="col">Aerolínea</th>
                            <th scope="col">Acción</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($fila = mysqli_fetch_assoc($resultado)) {
                            $nombreEscapado = htmlspecialchars($fila['nombreUsuario'], ENT_QUOTES, 'UTF-8');
                            $aerolineaEscapada = htmlspecialchars($fila['nombreAerolinea'] ?? 'Sin asignar', ENT_QUOTES, 'UTF-8');
                        ?>

                            <tr>

                                <td><?= $nombreEscapado ?></td>

                                <td><?= htmlspecialchars($fila['emailUsuario'], ENT_QUOTES, 'UTF-8') ?></td>

                                <td>

                                    <?= $aerolineaEscapada ?>

                                </td>

                                <td>

                                    <a
                                        href="asignar.php?id=<?= $fila['codUsuario'] ?>"
                                        class="btn btn-primary btn-sm"
                                        aria-label="Asignar aerolínea a <?= $nombreEscapado ?>">

                                        Asignar

                                    </a>

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>
                <div class="d-flex justify-content-center mt-4">

                    <nav aria-label="Paginación de CEOs aprobados">

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

<?php
$alertasAsignacion = [
    'asignada' => [
        'icon'  => 'success',
        'title' => '¡Asignada!',
        'text'  => 'La aerolínea fue asignada correctamente.'
    ],
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error al guardar la asignación. Intente nuevamente.'
    ]
];

if (isset($_GET['alerta']) && array_key_exists($_GET['alerta'], $alertasAsignacion)) {
    $alertaAsignacion = $alertasAsignacion[$_GET['alerta']];
?>

    <script>
        Swal.fire({
            icon: '<?= $alertaAsignacion['icon'] ?>',
            title: '<?= $alertaAsignacion['title'] ?>',
            text: '<?= $alertaAsignacion['text'] ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php } ?>

<?php
include("../../includes/footer.php");
?>