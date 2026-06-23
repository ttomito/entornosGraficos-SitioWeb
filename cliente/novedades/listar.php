<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

$sql = "

SELECT *

FROM novedades

ORDER BY fechaPublicacion DESC

";

$resultado = mysqli_query(
    $link,
    $sql
);



$sql = "

SELECT *

FROM novedades

ORDER BY codNovedad DESC

";

$resultado = mysqli_query(
    $link,
    $sql
);

$hoy = new DateTime();

?>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>Novedades</h2>

    </div>

    <div class="row">

        <?php

        while ($fila = mysqli_fetch_assoc($resultado)) {
            $fechaPublicacion =
                new DateTime(
                    $fila['fechaPublicacion']
                );

            $fechaExpiracion =
                new DateTime(
                    $fila['fechaExpiracion']
                );

            if (
                $fechaExpiracion >= $hoy
            ) {
        ?>

                <div class="col-md-4 mb-4">

                    <div
                        class="card shadow-lg border-0 h-100 card-hover">

                        <?php if (!empty($fila['imagen'])) { ?>
                            <img
                                src="../../uploads/novedades/<?php echo $fila['imagen'] ?>"
                                class="card-img-top"
                                alt="Imagen de novedad"
                                style="height: 200px; object-fit: cover;">
                        <?php } ?>

                        <div class="card-body">

                            <span class="badge bg-primary mb-3">Novedad</span>

                            <h4>

                                <?= $fila['tituloNovedad'] ?>

                            </h4>

                            <p>

                                <?= substr(
                                    $fila['textoNovedad'],
                                    0,
                                    150
                                ) ?>

                                ...

                            </p>

                            <hr>

                            <small>

                                Publicado:

                                <?= $fila['fechaPublicacion'] ?>

                            </small>

                            <br>

                            <small>

                                Expira:

                                <?= $fila['fechaExpiracion'] ?>

                            </small>

                            <br><br>

                            <?php

                            if (
                                isset($_SESSION['tipo'])
                                &&
                                $_SESSION['tipo'] == 'CLIENTE'
                            ) {
                            ?>

                                <a
                                    href="../novedades/verNovedad.php?codNovedad=<?= $fila['codNovedad'] ?>"
                                    class="btn btn-primary">

                                    Ver Novedad

                                </a>

                            <?php
                            } else {
                            ?>

                                <a
                                    href="/entornosGraficos-SitioWeb/auth/login.php"
                                    class="btn btn-warning">

                                    Iniciar Sesión

                                </a>

                            <?php
                            }
                            ?>

                        </div>

                    </div>

                </div>

        <?php
            }
        }
        ?>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>

<?php include("../../includes/footer.php"); ?>