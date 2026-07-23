<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");
include("../../includes/conexion.php");

$codNovedad = isset($_GET['codNovedad']) ? (int) $_GET['codNovedad'] : 0;

$novedad = null;

if ($codNovedad > 0) {

    $sql = "SELECT * FROM novedades WHERE codNovedad = $codNovedad";

    $resultado = mysqli_query($link, $sql);

    if ($resultado) {
        $novedad = mysqli_fetch_assoc($resultado);
    }
}


if (!$novedad) {
?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5 text-center" role="alert">

                    <h2 class="text-danger">

                        Novedad no encontrada

                    </h2>

                    <p>

                        La novedad que buscás no existe o ya no está disponible.

                    </p>

                    <a href="listar.php" class="btn btn-secondary">

                        <span aria-hidden="true"></span>Volver

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

    include("../../includes/footer.php");
    exit();
}

$titulo = htmlspecialchars($novedad['tituloNovedad'], ENT_QUOTES, 'UTF-8');
$texto = htmlspecialchars($novedad['textoNovedad'], ENT_QUOTES, 'UTF-8');
$fechaPubTexto = htmlspecialchars($novedad['fechaPublicacion'], ENT_QUOTES, 'UTF-8');
$fechaExpTexto = htmlspecialchars($novedad['fechaExpiracion'], ENT_QUOTES, 'UTF-8');

$error = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : null;

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2><?= $titulo ?></h2>

                    <?php if ($error !== null) { ?>

                        <div class="alert alert-danger" role="alert">

                            <?= $error ?>

                        </div>

                    <?php } ?>

                    <div class="mb-3">
                        Fecha de publicación: <time datetime="<?= $fechaPubTexto ?>"><?= $fechaPubTexto ?></time>
                    </div>

                    <div class="mb-3">
                        Fecha de expiración: <time datetime="<?= $fechaExpTexto ?>"><?= $fechaExpTexto ?></time>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1">Novedad:</p>
                        <p><?= nl2br($texto) ?></p>
                    </div>

                    <div class="mt-4">

                        <a href="listar.php" class="btn btn-secondary">

                            <span aria-hidden="true"></span>Volver

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include("../../includes/footer.php"); ?>