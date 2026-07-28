<?php

include_once("../../includes/header.php");
include_once("../../includes/conexion.php");

$registrosPorPagina = 6;

$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}


$sqlConteo = "SELECT COUNT(*) AS total FROM novedades WHERE fechaExpiracion >= CURDATE()";

$resultadoConteo = mysqli_query($link, $sqlConteo);

$filaConteo = mysqli_fetch_assoc($resultadoConteo);

$totalRegistros = (int) $filaConteo['total'];

$totalPaginas = max(1, (int) ceil($totalRegistros / $registrosPorPagina));

if ($pagina > $totalPaginas) {
    $pagina = $totalPaginas;
}

$inicio = ($pagina - 1) * $registrosPorPagina;

$sql = "SELECT *
        FROM novedades
        WHERE fechaExpiracion >= CURDATE()
        ORDER BY codNovedad DESC
        LIMIT ? OFFSET ?";

$sentencia = mysqli_prepare($link, $sql);

mysqli_stmt_bind_param($sentencia, "ii", $registrosPorPagina, $inicio);

mysqli_stmt_execute($sentencia);

$resultado = mysqli_stmt_get_result($sentencia);

$estaLogueado = isset($_SESSION['id']);

$esCliente = isset($_SESSION['tipo'])
    && $_SESSION['tipo'] === 'CLIENTE';

?>

<main id="contenido-principal">

    <div class="container mt-4">

        <div class="d-flex justify-content-between mb-4">

            <h2>

                Novedades

            </h2>

        </div>

        <?php if ($totalRegistros === 0) { ?>

            <div class="alert alert-info" role="status">

                No hay novedades disponibles en este momento.

            </div>

        <?php } ?>

        <div class="row">

            <?php

            while ($fila = mysqli_fetch_assoc($resultado)) {

                $titulo = htmlspecialchars(
                    $fila['tituloNovedad'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                );

                $textoOriginal = $fila['textoNovedad'] ?? '';

                $textoCorto = mb_substr($textoOriginal, 0, 150);

                if (mb_strlen($textoOriginal) > 150) {
                    $textoCorto = rtrim($textoCorto) . '...';
                }

                $textoCorto = htmlspecialchars(
                    $textoCorto,
                    ENT_QUOTES,
                    'UTF-8'
                );

                $fechaPublicacion = new DateTime($fila['fechaPublicacion']);

                $fechaExpiracion = new DateTime($fila['fechaExpiracion']);

            ?>

                <div class="col-md-4 mb-4">

                    <div class="card shadow-lg border-0 h-100 card-hover">

                        <?php if (!empty($fila['imagen'])) { ?>

                            <img
                                src="../../uploads/novedades/<?= htmlspecialchars($fila['imagen'], ENT_QUOTES, 'UTF-8') ?>"
                                class="card-img-top"
                                alt="<?= $titulo ?>"
                                title="Imagen de la novedad: <?= $titulo ?>"
                                loading="lazy"
                                style="height: 200px; object-fit: cover;">

                        <?php } ?>

                        <div class="card-body">

                            <span class="badge bg-primary mb-3">

                                Novedad

                            </span>

                            <h3 class="h5">

                                <?= $titulo ?>

                            </h3>

                            <p>

                                <?= $textoCorto ?>

                            </p>

                            <hr>

                            <small>

                                Publicado:

                                <time datetime="<?= $fechaPublicacion->format('Y-m-d') ?>">
                                    <?= $fechaPublicacion->format('d/m/Y') ?>
                                </time>

                            </small>

                            <br>

                            <small>

                                Expira:

                                <time datetime="<?= $fechaExpiracion->format('Y-m-d') ?>">
                                    <?= $fechaExpiracion->format('d/m/Y') ?>
                                </time>

                            </small>

                            <br><br>

                            <?php if ($esCliente) { ?>

                                <a
                                    href="verNovedad.php?codNovedad=<?= (int) $fila['codNovedad'] ?>"
                                    class="btn btn-primary">

                                    Ver Novedad
                                    <span class="visually-hidden"> sobre: <?= $titulo ?></span>

                                </a>

                            <?php } elseif ($estaLogueado) { ?>

                                <span class="text-muted small">

                                    Contenido disponible para clientes.

                                </span>

                            <?php } else { ?>

                                <a
                                    href=<?php echo ruta."/auth/login.php" ?>
                                    class="btn btn-warning">

                                    Iniciar Sesión
                                    <span class="visually-hidden"> para ver la novedad: <?= $titulo ?></span>

                                </a>

                            <?php } ?>

                        </div>

                    </div>

                </div>

            <?php
            }
            ?>

        </div>

        <?php if ($totalPaginas > 1) { ?>

            <div class="d-flex justify-content-center mt-4">

                <nav aria-label="Paginación de novedades">

                    <ul class="pagination flex-wrap justify-content-center">

                        <?php if ($pagina > 1) { ?>

                            <li class="page-item">

                                <a
                                    class="page-link"
                                    href="?pagina=<?= $pagina - 1 ?>">

                                    Anterior

                                </a>

                            </li>

                        <?php } ?>

                        <?php for ($i = 1; $i <= $totalPaginas; $i++) { ?>

                            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">

                                <a
                                    class="page-link"
                                    href="?pagina=<?= $i ?>"
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

</main>

<?php

mysqli_stmt_close($sentencia);

include_once("../../includes/footer.php");
