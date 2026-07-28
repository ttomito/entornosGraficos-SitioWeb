<?php

require_once("../includes/conexion.php");

$sql = "SELECT *
        FROM sobre_nosotros
        ORDER BY codSobre DESC
        LIMIT 1";

$resultado = mysqli_query($link, $sql);

$sobre = null;

if (!$resultado) {
    error_log("Sobre nosotros: " . mysqli_error($link));
} else {
    $sobre = mysqli_fetch_assoc($resultado);
}

function textoLargo($valor)
{
    return nl2br(htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8'));
}

$titulo      = htmlspecialchars($sobre['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
$descripcion = textoLargo($sobre['descripcion'] ?? '');
$mision      = textoLargo($sobre['mision'] ?? '');
$vision      = textoLargo($sobre['vision'] ?? '');

$alertasSobreNosotros = [
    'actualizado' => [
        'tipo'  => 'success',
        'texto' => 'El contenido se guardó correctamente.',
    ],
];

$alerta = null;

if (isset($_GET['alerta']) && isset($alertasSobreNosotros[$_GET['alerta']])) {
    $alerta = $alertasSobreNosotros[$_GET['alerta']];
}

include("../includes/header.php");

$esAdmin = (($_SESSION['tipo'] ?? '') === 'ADMIN');

?>

<main id="contenido-principal">

    <div class="container my-5">

        <?php if ($alerta !== null) { ?>
            <div
                id="alertaSobreNosotros"
                class="alert alert-<?= $alerta['tipo'] === 'success' ? 'success' : 'danger' ?>"
                data-tipo="<?= $alerta['tipo'] === 'success' ? 'success' : 'error' ?>"
                role="alert">
                <?= htmlspecialchars($alerta['texto'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php } ?>

        <div class="text-center mb-5">

            <h1 class="display-4 fw-bold">
                <i class="bi bi-airplane" aria-hidden="true"></i>
                <?= $titulo !== '' ? $titulo : 'Sobre Nosotros' ?>
            </h1>

            <?php if ($descripcion !== '') { ?>
                <p class="lead mt-3"><?= $descripcion ?></p>
            <?php } ?>

        </div>

        <?php if (!$sobre) { ?>

            <div class="alert alert-info" role="status">
                Todavía no se cargó el contenido de esta sección.
            </div>

        <?php } else { ?>

            <div class="row">

                <?php if ($mision !== '') { ?>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-lg border-0 h-100 card-hover">
                            <div class="card-body p-4">
                                <h2 class="text-primary fs-3">Nuestra Misión</h2>
                                <p><?= $mision ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php if ($vision !== '') { ?>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-lg border-0 h-100 card-hover">
                            <div class="card-body p-4">
                                <h2 class="text-success fs-3">Nuestra Visión</h2>
                                <p><?= $vision ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>

        <?php } ?>

        <div class="row mt-5">

            <div class="col-md-4 mb-4">
                <div class="card shadow border-0 h-100 card-hover">
                    <div class="card-body text-center">
                        <i class="bi bi-airplane fs-1 text-primary" aria-hidden="true"></i>
                        <h2 class="fs-5">Vuelos Internacionales</h2>
                        <p>Conectamos destinos de todo el mundo.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow border-0 h-100 card-hover">
                    <div class="card-body text-center">
                        <i class="bi bi-shield-lock fs-1 text-success" aria-hidden="true"></i>
                        <h2 class="fs-5">Reservas Seguras</h2>
                        <p>Protegemos toda la información de nuestros usuarios.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow border-0 h-100 card-hover">
                    <div class="card-body text-center">
                        <i class="bi bi-star-fill fs-1 text-warning" aria-hidden="true"></i>
                        <h2 class="fs-5">Experiencia Simple</h2>
                        <p>Diseñamos una plataforma rápida e intuitiva.</p>
                    </div>
                </div>
            </div>

        </div>

        <?php if ($esAdmin) { ?>

            <div class="text-center mt-4">
                <a href="editar.php" class="btn btn-warning">
                    Editar contenido
                </a>
            </div>

        <?php } ?>

    </div>

</main>

<?php if ($alerta !== null) { ?>

    <!-- SweetAlert se carga solo cuando hay algo que mostrar -->
    <script
        src="https://cdn.jsdelivr.net/npm/sweetalert2@11"
        crossorigin="anonymous"></script>

    <script>
        (function() {

            /*
            | El mensaje ya está en el HTML, así que se ve aunque el CDN
            | no cargue. Si SweetAlert está disponible, lo mostramos como
            | popup y ocultamos la versión en línea.
            */

            var alerta = document.getElementById('alertaSobreNosotros');

            if (!alerta || typeof Swal === 'undefined') {
                return;
            }

            var tipo = alerta.getAttribute('data-tipo') === 'success' ? 'success' : 'error';

            var mensaje = alerta.textContent.trim();

            alerta.style.display = 'none';

            Swal.fire({
                icon: tipo,
                title: tipo === 'success' ? '¡Actualizado!' : 'Atención',
                text: mensaje,
                confirmButtonText: 'Aceptar'
            });

        })();
    </script>

<?php } ?>

<?php include("../includes/footer.php"); ?>