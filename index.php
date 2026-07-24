<?php

include("includes/conexion.php");



$destinosPopulares = mysqli_query(
    $link,
    "SELECT v.destinoVuelo,
COUNT(r.codReserva) AS cantidad,
MAX(v.imagenVuelo) AS imagenVuelo
FROM reservas r
INNER JOIN vuelos v
ON r.codVuelo = v.codVuelo
WHERE r.fechaReserva >= DATE_SUB(CURDATE(), INTERVAL 3 YEAR)
GROUP BY v.destinoVuelo
ORDER BY cantidad DESC
LIMIT 8"
);

$vuelosHome = mysqli_query(
    $link,
    "SELECT * FROM vuelos WHERE fechaVuelo >= CURDATE() ORDER BY fechaVuelo ASC LIMIT 15"
);

$totalAerolineas = mysqli_fetch_assoc(
    mysqli_query(
        $link,
        "SELECT COUNT(*) total FROM aerolineas"
    )
);

$totalVuelos = mysqli_fetch_assoc(
    mysqli_query(
        $link,
        "SELECT COUNT(*) total FROM vuelos"
    )
);

$totalUsuarios = mysqli_fetch_assoc(
    mysqli_query(
        $link,
        "SELECT COUNT(*) total FROM usuarios"
    )
);

$promociones = mysqli_query(
    $link,
    "SELECT * FROM promociones WHERE estadoPromocion='APROBADA' LIMIT 3"
);

$novedades = mysqli_query(
    $link,
    "SELECT * FROM novedades WHERE CURDATE() BETWEEN fechaPublicacion AND fechaExpiracion LIMIT 3"
);
?>


<?php

include("includes/header.php");

?>



<main id="contenido-principal">

    <section class="py-5 text-center text-white"
        style="
        background:
        linear-gradient(rgba(0,0,0,.6),rgba(0,0,0,.6)),
        url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05');
        background-size:cover;
        background-position:center;
        min-height:500px;
        display:flex;
        align-items:center;
        ">


        <div class="container">

            <h1 class="display-3 fw-bold">

                Encontrá tu próximo destino

            </h1>

            <p class="lead mt-3">

                Reservá vuelos nacionales e internacionales
                de forma rápida, segura y sencilla.

            </p>
            <div class="mt-4 d-grid gap-2 d-sm-flex justify-content-sm-center">

                <a
                    href="cliente/vuelos/listar.php"
                    class="btn btn-warning btn-lg">

                    Buscar vuelos

                </a>

                <a
                    href="cliente/promociones/listar.php"
                    class="btn btn-outline-light btn-lg">

                    Ver promociones

                </a>

            </div>

        </div>

    </section>



    <section class="container my-5" aria-labelledby="titulo-estadisticas">

        <h2 id="titulo-estadisticas" class="visually-hidden">Estadísticas generales</h2>

        <div class="row text-center">

            <div class="col-md-4 mb-3">

                <div class="card shadow-sm card-hover estadistica-card">
                    <div class="card-body">

                        <h3 class="text-primary">

                            <?= $totalAerolineas['total'] ?>

                        </h3>

                        <p class="mb-0">Aerolíneas</p>

                    </div>

                </div>

            </div>

            <div class="col-md-4 mb-3">

                <div class="card shadow-sm card-hover estadistica-card">
                    <div class="card-body">

                        <h3 class="text-success">

                            <?= $totalVuelos['total'] ?>

                        </h3>

                        <p class="mb-0">Vuelos</p>

                    </div>

                </div>

            </div>

            <div class="col-md-4 mb-3">

                <div class="card shadow-sm card-hover estadistica-card">
                    <div class="card-body">

                        <h3 class="text-warning">

                            <?= $totalUsuarios['total'] ?>

                        </h3>

                        <p class="mb-0">Usuarios</p>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section class="container my-5" aria-labelledby="titulo-destinos">

        <h2 id="titulo-destinos" class="fw-bold mb-4">

            Destinos Más Populares

        </h2>

        <div class="netflix-wrapper">

            <button
                type="button"
                class="btn btn-dark netflix-prev"
                onclick="moverDestinos(-1)"
                aria-label="Ver destino anterior">

                <span aria-hidden="true">❮</span>

            </button>

            <div
                id="destinosScroll"
                class="d-flex netflix-scroll pb-3"
                role="region"
                aria-label="Lista de destinos populares"
                tabindex="0">


                <?php

                while (
                    $destino =
                    mysqli_fetch_assoc(
                        $destinosPopulares
                    )
                ) {
                    $nombreDestino = htmlspecialchars($destino['destinoVuelo'], ENT_QUOTES, 'UTF-8');
                ?>

                    <div
                        class="card  netflix-card shadow border-0 card-hover">

                        <img
                            src="<?= htmlspecialchars($destino['imagenVuelo'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="Vista del destino <?= $nombreDestino ?>"
                            class="card-img-top"
                            style="
                        height:180px;
                        object-fit:cover;
                        ">

                        <div class="card-body">

                            <h3 class="h5">

                                <?= $nombreDestino ?>

                            </h3>

                            <p class="text-body-secondary">

                                <?= (int) $destino['cantidad'] ?>

                                reservas

                            </p>

                            <a
                                href="cliente/vuelos/listar.php?destino=<?= urlencode($destino['destinoVuelo']) ?>"
                                class="btn btn-primary btn-sm">

                                Ver vuelos
                                <span class="visually-hidden"> a <?= $nombreDestino ?></span>

                            </a>

                        </div>

                    </div>

                <?php
                }
                ?>

            </div>
            <button
                type="button"
                class="btn btn-dark netflix-next"
                onclick="moverDestinos(1)"
                aria-label="Ver siguiente destino">

                <span aria-hidden="true">❯</span>

            </button>
        </div>



        </div>
    </section>

    <?php if (mysqli_num_rows($promociones) > 0) { ?>

        <section class="container my-5" aria-labelledby="titulo-promos">

            <h2 id="titulo-promos" class="text-center mb-4">

                Promociones Destacadas

            </h2>

            <div class="row">

                <?php while ($promo = mysqli_fetch_assoc($promociones)) {
                    $descripcionPromo = htmlspecialchars($promo['descripcionPromocion'], ENT_QUOTES, 'UTF-8');
                ?>

                    <div class="col-md-4 mb-4">

                        <div class="card h-100 shadow-lg card-hover border-0">
                            <div class="card-body">

                                <span class="badge bg-success">

                                    Promoción

                                </span>

                                <h3 class="h5 mt-3">

                                    <?= $descripcionPromo ?>

                                </h3>

                                <p class="text-danger fs-3 fw-bold">

                                    <?= (int) $promo['descuentoPromocion'] ?>% OFF

                                </p>
                                <a
                                    href="cliente/vuelos/listar.php?promo=<?= (int) $promo['codPromocion'] ?>"
                                    class="btn btn-success mt-3">

                                    Ver vuelos con esta promoción
                                    <span class="visually-hidden"> ("<?= $descripcionPromo ?>")</span>

                                </a>

                            </div>

                        </div>

                    </div>

                <?php } ?>

            </div>

        </section>

    <?php } ?>


    <section class="container my-5" aria-labelledby="titulo-novedades">

        <h2 id="titulo-novedades" class="text-center mb-5 fw-bold">

            Últimas Novedades

        </h2>

        <div class="row">

            <?php

            while ($novedad = mysqli_fetch_assoc($novedades)) {
                $textoNovedad = htmlspecialchars($novedad['textoNovedad'], ENT_QUOTES, 'UTF-8');
            ?>

                <div class="col-md-4 mb-4">

                    <div
                        class="card border-0 shadow-lg h-100 card-hover">

                        <?php if (!empty($novedad['imagen'])) { ?>
                            <img
                                src="uploads/novedades/<?= htmlspecialchars($novedad['imagen'], ENT_QUOTES, 'UTF-8') ?>"
                                class="card-img-top"
                                alt="Imagen relacionada a la novedad: <?= mb_strimwidth($textoNovedad, 0, 80, '...') ?>"
                                style="height: 200px; object-fit: cover;">
                        <?php } ?>

                        <div class="card-body p-4">

                            <span
                                class="badge bg-primary mb-3">

                                Novedad

                            </span>

                            <p class="lead">

                                <?= $textoNovedad ?>

                            </p>

                            <hr>

                            <p class="text-body-secondary small mb-0">

                                Vigente hasta:

                                <time datetime="<?= htmlspecialchars($novedad['fechaExpiracion'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($novedad['fechaExpiracion'], ENT_QUOTES, 'UTF-8') ?>
                                </time>

                            </p>
                            <br>

                            <a
                                href="cliente/novedades/listar.php"
                                class="btn btn-primary">

                                Más información
                                <span class="visually-hidden"> sobre: <?= mb_strimwidth($textoNovedad, 0, 60, '...') ?></span>

                            </a>

                        </div>

                    </div>

                </div>

            <?php
            }
            ?>

        </div>

    </section>
    <section
        class="py-4 bg-dark text-white">

        <div class="container text-center">

            <h2 class="h3">

                <span aria-hidden="true">✈</span>
                Más de 50 destinos disponibles

            </h2>

            <p>

                Promociones exclusivas,
                vuelos nacionales e internacionales.

            </p>

        </div>

    </section>



    <section class="container my-5" aria-labelledby="titulo-proximos-vuelos">

        <h2 id="titulo-proximos-vuelos" class="fw-bold mb-4">

            Próximos Vuelos

        </h2>

        <div class="netflix-wrapper">

            <button
                type="button"
                class="btn btn-dark netflix-prev"
                onclick="moverVuelos(-1)"
                aria-label="Ver vuelo anterior">
                <span aria-hidden="true">❮</span>
            </button>

            <div
                id="vuelosScroll"
                class="d-flex netflix-scroll pb-3"
                role="region"
                aria-label="Lista de próximos vuelos"
                tabindex="0">


                <?php

                while (
                    $vuelo =
                    mysqli_fetch_assoc(
                        $vuelosHome
                    )
                ) {
                    $origen = htmlspecialchars($vuelo['origenVuelo'], ENT_QUOTES, 'UTF-8');
                    $destinoV = htmlspecialchars($vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8');
                ?>

                    <div
                        class="card  netflix-card  shadow border-0 card-hover">

                        <img
                            src="uploads/vuelos/<?= htmlspecialchars($vuelo['imagenVuelo'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="Vuelo de <?= $origen ?> a <?= $destinoV ?>"
                            class="card-img-top"
                            style="
                        height:200px;
                        object-fit:cover;
                        ">

                        <div class="card-body">

                            <h3 class="h5">

                                <?= $origen ?>
                                <span aria-hidden="true">→</span>
                                <span class="visually-hidden">a</span>
                                <?= $destinoV ?>

                            </h3>

                            <p>

                                <span aria-hidden="true">📅</span>
                                <span class="visually-hidden">Fecha:</span>
                                <?= htmlspecialchars($vuelo['fechaVuelo'], ENT_QUOTES, 'UTF-8') ?>

                            </p>

                            <p class="fw-bold text-success">

                                <span class="visually-hidden">Precio:</span>
                                $

                                <?= number_format(
                                    $vuelo['precioVuelo'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </p>

                            <?php

                            if (
                                isset($_SESSION['id'])
                                &&
                                isset($_SESSION['tipo'])
                                &&
                                $_SESSION['tipo'] == 'CLIENTE'
                            ) {
                            ?>

                                <a
                                    href="cliente/reservas/reservar.php?codVuelo=<?= (int) $vuelo['codVuelo'] ?>"
                                    class="btn btn-warning">

                                    Ver disponibilidad
                                    <span class="visually-hidden"> del vuelo <?= $origen ?> a <?= $destinoV ?></span>

                                </a>

                            <?php
                            } elseif (!isset($_SESSION['id'])) {
                            ?>

                                <a
                                    href="auth/login.php"
                                    class="btn btn-primary">

                                    Iniciar sesión
                                    <span class="visually-hidden"> para ver el vuelo <?= $origen ?> a <?= $destinoV ?></span>

                                </a>

                            <?php
                            } else {
                            ?>

                                <button
                                    class="btn btn-secondary"
                                    disabled>

                                    Cuenta de cliente requerida

                                </button>

                            <?php
                            }
                            ?>

                        </div>

                    </div>

                <?php
                }
                ?>

            </div>
            <button
                type="button"
                class="btn btn-dark netflix-next"
                onclick="moverVuelos(1)"
                aria-label="Ver siguiente vuelo">
                <span aria-hidden="true">❯</span>
            </button>
        </div>



        </div>
    </section>


    <?php
    if (!isset($_SESSION['id'])) {
    ?>

        <section class="container my-5">

            <div
                class="card border-0 shadow-lg card-hover"
                style="
                background:
                linear-gradient(
                135deg,
                #0d6efd,
                #0a3d91
                );
                color:white;
                border-radius:20px;
                ">

                <div
                    class="card-body p-5 text-center">

                    <h2 class="fw-bold">

                        ¿Listo para comenzar tu viaje?

                    </h2>

                    <p class="lead text-white">

                        Registrate gratis y comenzá a reservar
                        vuelos nacionales e internacionales.

                    </p>

                    <a
                        href="auth/registro.php"
                        class="btn btn-warning btn-lg ">

                        Crear Cuenta

                    </a>

                </div>

            </div>

        </section>



    <?php
    }
    ?>

</main>

<script>
    function moverDestinos(direccion) {
        document.getElementById(
            'destinosScroll'
        ).scrollBy({
            left: direccion * 300,
            behavior: 'smooth'
        });
    }

    function moverVuelos(direccion) {
        document.getElementById(
            'vuelosScroll'
        ).scrollBy({
            left: direccion * 300,
            behavior: 'smooth'
        });
    }
</script>


<?php

include("includes/footer.php");

?>