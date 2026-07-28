<?php

include_once("../../includes/verificarSession.php");

exigirTipo('CLIENTE');

include_once("../../includes/header.php");
include_once("../../includes/conexion.php");

$codNovedad = isset($_GET['codNovedad']) ? (int) $_GET['codNovedad']  : 0;

$novedad = null;

if ($codNovedad > 0) {

    $sql = "SELECT * FROM novedades WHERE codNovedad = ? AND fechaExpiracion >= CURDATE()";

    $sentencia = mysqli_prepare($link, $sql);

    mysqli_stmt_bind_param($sentencia, "i", $codNovedad);

    mysqli_stmt_execute($sentencia);

    $resultado = mysqli_stmt_get_result($sentencia);

    if ($resultado) {
        $novedad = mysqli_fetch_assoc($resultado);
    }

    mysqli_stmt_close($sentencia);
}

if (!$novedad) {

    http_response_code(404);

?>

    <main id="contenido-principal">

        <div class="container mt-5">

            <div class="row justify-content-center">

                <div class="col-md-8">

                    <div class="card card-custom">

                        <div class="card-body p-5 text-center">

                            <h2 class="text-danger">

                                Novedad no encontrada

                            </h2>

                            <p>

                                La novedad que buscás no existe o ya no está disponible.

                            </p>

                            <a href="listar.php" class="btn btn-secondary">

                                Volver al listado

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>

<?php

    include_once("../../includes/footer.php");
    exit();
}

$titulo = htmlspecialchars($novedad['tituloNovedad'] ?? '', ENT_QUOTES, 'UTF-8');
$texto = htmlspecialchars($novedad['textoNovedad'] ?? '', ENT_QUOTES, 'UTF-8');
$fechaPublicacion = new DateTime($novedad['fechaPublicacion']);
$fechaExpiracion = new DateTime($novedad['fechaExpiracion']);


$mensajesError = [
    'noDisponible' => 'La novedad ya no está disponible.',
    'sinPermiso'   => 'No tenés permiso para realizar esa acción.',
];

$error = null;

if (isset($_GET['error']) && isset($mensajesError[$_GET['error']])) {
    $error = $mensajesError[$_GET['error']];
}

?>

<main id="contenido-principal">

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card card-custom">

                    <?php if (!empty($novedad['imagen'])) { ?>

                        <img
                            src="../../uploads/novedades/<?= htmlspecialchars($novedad['imagen'], ENT_QUOTES, 'UTF-8') ?>"
                            class="card-img-top"
                            alt="<?= $titulo ?>"
                            title="Imagen de la novedad: <?= $titulo ?>">

                    <?php } ?>

                    <div class="card-body p-5">

                        <h2><?= $titulo ?></h2>

                        <?php if ($error !== null) { ?>

                            <div class="alert alert-danger" role="alert">

                                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>

                            </div>

                        <?php } ?>

                        <dl class="mb-4">

                            <dt>Fecha de publicación</dt>

                            <dd>
                                <time datetime="<?= $fechaPublicacion->format('Y-m-d') ?>">
                                    <?= $fechaPublicacion->format('d/m/Y') ?>
                                </time>
                            </dd>

                            <dt>Fecha de expiración</dt>

                            <dd>
                                <time datetime="<?= $fechaExpiracion->format('Y-m-d') ?>">
                                    <?= $fechaExpiracion->format('d/m/Y') ?>
                                </time>
                            </dd>

                        </dl>

                        <div class="mb-3">

                            <h3 class="h6">Novedad</h3>

                            <p><?= nl2br($texto) ?></p>

                        </div>

                        <div class="mt-4">

                            <a href="listar.php" class="btn btn-secondary">

                                Volver al listado

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<?php include_once("../../includes/footer.php"); ?>