<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$rutaActual = $_SERVER['PHP_SELF'];

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>

        AirTickets - Sistema de Reservas Aéreas

    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

        <link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="/entornosGraficos-SitioWeb/assets/css/estilos.css">

</head>

<body>

    <a href="#contenido-principal" class="visually-hidden-focusable">
        Saltar al contenido principal
    </a>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm" aria-label="Navegación principal">

        <div class="container">

            <a
                class="navbar-brand fw-bold fs-3"
                href="/entornosGraficos-SitioWeb/index.php"
                style="color: white;">

                AirTickets

            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menuNavbar"
                aria-controls="menuNavbar"
                aria-expanded="false"
                aria-label="Abrir menú de navegación">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div
                class="collapse navbar-collapse"
                id="menuNavbar">

                <ul class="navbar-nav ms-auto">

                    <?php

                    if (!isset($_SESSION['tipo'])) {
                    ?>

                        <li class="nav-item">

                            <a class="nav-link<?= $paginaActual == 'pagina.php' ? ' active' : '' ?>"
                                href="/entornosGraficos-SitioWeb/Sobrenosotros/pagina.php"
                                <?= $paginaActual == 'pagina.php' ? ' aria-current="page"' : '' ?>>

                                Sobre Nosotros

                            </a>

                        </li>



                        <li class="nav-item">

                            <a class="nav-link<?= $paginaActual == 'index.php' ? ' active' : '' ?>"
                                href="/entornosGraficos-SitioWeb/index.php"
                                <?= $paginaActual == 'index.php' ? ' aria-current="page"' : '' ?>>

                                Inicio

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link" href="/entornosGraficos-SitioWeb/cliente/vuelos/listar.php">

                                Vuelos

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link" href="/entornosGraficos-SitioWeb/cliente/promociones/listar.php">

                                Promociones

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link" href="/entornosGraficos-SitioWeb/cliente/novedades/listar.php">

                                Novedades

                            </a>

                        </li>

                    <?php
                    } elseif ($_SESSION['tipo'] == 'CLIENTE') {
                    ?>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/Sobrenosotros/pagina.php">

                                Sobre Nosotros

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/cliente/dashboard.php">

                                Inicio

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/cliente/vuelos/listar.php">

                                Vuelos

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/cliente/reservas/listar.php">

                                Reservas

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/cliente/promociones/listar.php">

                                Promociones

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/cliente/novedades/listar.php">

                                Novedades

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/perfil/index.php">

                                Mi Perfil

                            </a>

                        </li>

                    <?php
                    } elseif ($_SESSION['tipo'] == 'CEO') {
                    ?>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/Sobrenosotros/pagina.php">

                                Sobre Nosotros

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/ceo/dashboard.php">

                                Dashboard

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/ceo/vuelos/listar.php">

                                Mis Vuelos

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/ceo/promociones/listar.php">

                                Promociones

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/perfil/index.php">

                                Mi Perfil

                            </a>

                        </li>

                    <?php
                    } elseif ($_SESSION['tipo'] == 'ADMIN') {
                    ?>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/Sobrenosotros/pagina.php">

                                Sobre Nosotros

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/admin/dashboard.php">

                                Dashboard

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/admin/aerolineas/listar.php">

                                Aerolíneas

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/admin/ceos/listar.php">

                                CEOs

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/admin/promociones/listar.php">

                                Promociones

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/admin/novedades/listar.php">

                                Novedades

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                                href="/entornosGraficos-SitioWeb/perfil/index.php">

                                Mi Perfil

                            </a>

                        </li>

                    <?php
                    }
                    ?>

                </ul>

                <div class="ms-lg-3 mt-3 mt-lg-0">

                    <?php

                    if (isset($_SESSION['id'])) {
                    ?>

                        <span class="text-white me-3">

                            Hola,

                            <?= htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8') ?>

                        </span>

                        
                        <a
                            href="/entornosGraficos-SitioWeb/auth/logout.php"
                            class="btn btn-danger">

                            Salir
                            <span class="visually-hidden"> de la cuenta de <?= htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8') ?></span>

                        </a>

                    <?php
                    } else {
                    ?>

                        <a
                            href="/entornosGraficos-SitioWeb/auth/login.php"
                            class="btn btn-outline-light me-2">

                            Ingresar

                        </a>

                        <a
                            href="/entornosGraficos-SitioWeb/auth/registro.php"
                            class="btn btn-warning">

                            Registrarse

                        </a>

                    <?php
                    }

                    ?>

                </div>

            </div>

        </div>

    </nav>