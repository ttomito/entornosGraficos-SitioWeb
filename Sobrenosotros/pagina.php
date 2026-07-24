<?php

include("../includes/conexion.php");
include("../includes/header.php");

$sql = "

SELECT *

FROM sobre_nosotros

LIMIT 1

";

$resultado =
    mysqli_query(
        $link,
        $sql
    );

$sobre =
    mysqli_fetch_assoc(
        $resultado
    ) ?: [];

$titulo = htmlspecialchars($sobre['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
$descripcion = htmlspecialchars($sobre['descripcion'] ?? '', ENT_QUOTES, 'UTF-8');
$mision = htmlspecialchars($sobre['mision'] ?? '', ENT_QUOTES, 'UTF-8');
$vision = htmlspecialchars($sobre['vision'] ?? '', ENT_QUOTES, 'UTF-8');

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container my-5">

    <div class="text-center mb-5">

        <h1 class="display-4 fw-bold">

            <i class="bi bi-airplane" aria-hidden="true"></i> <?= $titulo ?>

        </h1>

        <p class="lead mt-3">

            <?= $descripcion ?>

        </p>

    </div>

    <div class="row">

        <div class="col-md-6 mb-4">

            <div class="card shadow-lg border-0 h-100 card-hover">

                <div class="card-body p-4">

                    <h2 class="text-primary fs-3">

                        Nuestra Misión

                    </h2>

                    <p>

                        <?= $mision ?>

                    </p>

                </div>

            </div>

        </div>

        <div class="col-md-6 mb-4">

            <div class="card shadow-lg border-0 h-100 card-hover">

                <div class="card-body p-4">

                    <h2 class="text-success fs-3">

                        Nuestra Visión

                    </h2>

                    <p>

                        <?= $vision ?>

                    </p>

                </div>

            </div>

        </div>

    </div>

    <div class="row mt-5">

        <div class="col-md-4">

            <div class="card shadow border-0 card-hover">

                <div class="card-body text-center">

                    <i class="bi bi-airplane fs-1 text-primary" aria-hidden="true"></i>
                    <h2 class="fs-5">Vuelos Internacionales</h2>

                    <p>

                        Conectamos destinos de todo el mundo.

                    </p>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow border-0 card-hover">

                <div class="card-body text-center">

                    <i class="bi bi-shield-lock fs-1 text-success" aria-hidden="true"></i>
                    <h2 class="fs-5">Reservas Seguras</h2>

                    <p>

                        Protegemos toda la información de nuestros usuarios.

                    </p>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow border-0 card-hover">

                <div class="card-body text-center">

                    <i class="bi bi-star-fill fs-1 text-warning" aria-hidden="true"></i>
                    <h2 class="fs-5">Experiencia Simple</h2>

                    <p>

                        Diseñamos una plataforma rápida e intuitiva.

                    </p>

                </div>

            </div>

        </div>

    </div>

    <?php if (
        isset($_SESSION['tipo'])
        &&
        $_SESSION['tipo'] == 'ADMIN'
    ) { ?>

        <div class="text-center mt-4">

            <a
                href="editar.php"
                class="btn btn-warning">

                Editar contenido

            </a>

        </div>

    <?php } ?>

</div>

<?php
$alertasSobreNosotros = [
    'actualizado' => [
        'icon'  => 'success',
        'title' => '¡Actualizado!',
        'text'  => 'El contenido se guardó correctamente.'
    ]
];

if (isset($_GET['alerta']) && array_key_exists($_GET['alerta'], $alertasSobreNosotros)) {
    $alertaSobreNosotros = $alertasSobreNosotros[$_GET['alerta']];
?>

    <script>
        Swal.fire({
            icon: '<?= $alertaSobreNosotros['icon'] ?>',
            title: '<?= $alertaSobreNosotros['title'] ?>',
            text: '<?= $alertaSobreNosotros['text'] ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php } ?>

<?php include("../includes/footer.php"); ?>