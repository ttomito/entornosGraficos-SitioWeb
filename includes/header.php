<?php

include("rutas.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$rutaActual = $_SERVER['PHP_SELF'];

// Extraemos solo el path de la URL definida en rutas.php
$rutaBase = parse_url(ruta, PHP_URL_PATH);

function esRutaActiva($rutaRelativa)
{
    global $rutaActual, $rutaBase;
    return $rutaActual === $rutaBase . $rutaRelativa ? ' active' : '';
}

function ariaActual($rutaRelativa)
{
    global $rutaActual, $rutaBase;
    return $rutaActual === $rutaBase . $rutaRelativa ? ' aria-current="page"' : '';
}

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
        href="<?php echo ruta . '/assets/css/estilos.css'; ?>">

</head>

<body>

    <a href="#contenido-principal" class="visually-hidden-focusable">
        Saltar al contenido principal
    </a>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm" aria-label="Navegación principal">

        <div class="container">

            <a
                class="navbar-brand fw-bold fs-3 text-white"
                href="<?php echo ruta . '/index.php'; ?>">

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

                            <a class="nav-link<?= esRutaActiva('/Sobrenosotros/pagina.php') ?>"
                                href="<?php echo ruta . '/Sobrenosotros/pagina.php'; ?>" <?= ariaActual('/Sobrenosotros/pagina.php') ?>>

                                Sobre Nosotros

                            </a>

                        </li>



                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/index.php') ?>"
                                href="<?php echo ruta . '/index.php'; ?>" <?= ariaActual('/index.php') ?>>

                                Inicio

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/cliente/vuelos/listar.php') ?>"
                                href="<?php echo ruta . '/cliente/vuelos/listar.php'; ?>" <?= ariaActual('/cliente/vuelos/listar.php') ?>>

                                Vuelos

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/cliente/promociones/listar.php') ?>"
                                href="<?php echo ruta . '/cliente/promociones/listar.php'; ?>" <?= ariaActual('/cliente/promociones/listar.php') ?>>

                                Promociones

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/cliente/novedades/listar.php') ?>"
                                href="<?php echo ruta . '/cliente/novedades/listar.php'; ?>" <?= ariaActual('/cliente/novedades/listar.php') ?>>

                                Novedades

                            </a>

                        </li>

                    <?php
                    } elseif ($_SESSION['tipo'] == 'CLIENTE') {
                    ?>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/Sobrenosotros/pagina.php') ?>"
                                href="<?php echo ruta . '/Sobrenosotros/pagina.php'; ?>" <?= ariaActual('/Sobrenosotros/pagina.php') ?>>

                                Sobre Nosotros

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/cliente/dashboard.php') ?>"
                                href="<?php echo ruta . '/cliente/dashboard.php'; ?>" <?= ariaActual('/cliente/dashboard.php') ?>>

                                Inicio

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/cliente/vuelos/listar.php') ?>"
                                href="<?php echo ruta . '/cliente/vuelos/listar.php'; ?>" <?= ariaActual('/cliente/vuelos/listar.php') ?>>

                                Vuelos

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/cliente/reservas/listar.php') ?>"
                                href="<?php echo ruta . '/cliente/reservas/listar.php'; ?>" <?= ariaActual('/cliente/reservas/listar.php') ?>>

                                Reservas

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/cliente/promociones/listar.php') ?>"
                                href="<?php echo ruta . '/cliente/promociones/listar.php'; ?>" <?= ariaActual('/cliente/promociones/listar.php') ?>>

                                Promociones

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/cliente/novedades/listar.php') ?>"
                                href="<?php echo ruta . '/cliente/novedades/listar.php'; ?>" <?= ariaActual('/cliente/novedades/listar.php') ?>>

                                Novedades

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/perfil/index.php') ?>"
                                href="<?php echo ruta . '/perfil/index.php'; ?>" <?= ariaActual('/perfil/index.php') ?>>

                                Mi Perfil

                            </a>

                        </li>

                    <?php
                    } elseif ($_SESSION['tipo'] == 'CEO') {
                    ?>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/Sobrenosotros/pagina.php') ?>"
                                href="<?php echo ruta . '/Sobrenosotros/pagina.php'; ?>" <?= ariaActual('/Sobrenosotros/pagina.php') ?>>

                                Sobre Nosotros

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/ceo/dashboard.php') ?>"
                                href="<?php echo ruta . '/ceo/dashboard.php'; ?>" <?= ariaActual('/ceo/dashboard.php') ?>>

                                Dashboard

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/ceo/vuelos/listar.php') ?>"
                                href="<?php echo ruta . '/ceo/vuelos/listar.php'; ?>" <?= ariaActual('/ceo/vuelos/listar.php') ?>>

                                Mis Vuelos

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/ceo/promociones/listar.php') ?>"
                                href="<?php echo ruta . '/ceo/promociones/listar.php'; ?>" <?= ariaActual('/ceo/promociones/listar.php') ?>>

                                Promociones

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/perfil/index.php') ?>"
                                href="<?php echo ruta . '/perfil/index.php'; ?>" <?= ariaActual('/perfil/index.php') ?>>

                                Mi Perfil

                            </a>

                        </li>

                    <?php
                    } elseif ($_SESSION['tipo'] == 'ADMIN') {
                    ?>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/Sobrenosotros/pagina.php') ?>"
                                href="<?php echo ruta . '/Sobrenosotros/pagina.php'; ?>" <?= ariaActual('/Sobrenosotros/pagina.php') ?>>

                                Sobre Nosotros

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/admin/dashboard.php') ?>"
                                href="<?php echo ruta . '/admin/dashboard.php'; ?>" <?= ariaActual('/admin/dashboard.php') ?>>

                                Dashboard

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/admin/aerolineas/listar.php') ?>"
                                href="<?php echo ruta . '/admin/aerolineas/listar.php'; ?>" <?= ariaActual('/admin/aerolineas/listar.php') ?>>

                                Aerolíneas

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/admin/ceos/listar.php') ?>"
                                href="<?php echo ruta . '/admin/ceos/listar.php'; ?>" <?= ariaActual('/admin/ceos/listar.php') ?>>

                                CEOs

                            </a>

                        </li>
                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/admin/asignaciones/listar.php') ?>"
                                href="<?php echo ruta . '/admin/asignaciones/listar.php'; ?>" <?= ariaActual('/admin/asignaciones/listar.php') ?>>

                                Asignaciones

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/admin/promociones/listar.php') ?>"
                                href="<?php echo ruta . '/admin/promociones/listar.php'; ?>" <?= ariaActual('/admin/promociones/listar.php') ?>>

                                Promociones

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/admin/novedades/listar.php') ?>"
                                href="<?php echo ruta . '/admin/novedades/listar.php'; ?>" <?= ariaActual('/admin/novedades/listar.php') ?>>

                                Novedades

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link<?= esRutaActiva('/perfil/index.php') ?>"
                                href="<?php echo ruta . '/perfil/index.php'; ?>" <?= ariaActual('/perfil/index.php') ?>>

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

                            <?= htmlspecialchars($_SESSION['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>

                        </span>


                        <a
                            href="<?php echo ruta . '/auth/logout.php'; ?>"
                            class="btn btn-danger">

                            Salir
                            <span class="visually-hidden"> de la cuenta de <?= htmlspecialchars($_SESSION['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>

                        </a>

                    <?php
                    } else {
                    ?>

                        <a
                            href="<?php echo ruta . '/auth/login.php'; ?>"
                            class="btn btn-outline-light me-2">

                            Ingresar

                        </a>

                        <a
                            href="<?php echo ruta . '/auth/registro.php'; ?>"
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