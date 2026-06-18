<?php

include("includes/header.php");

?>

<div class="container mt-5">

    <h1>

        Mapa del Sitio

    </h1>

    <p class="text-muted">

        Accesos disponibles para el usuario actual.

    </p>

    <div class="card card-custom">

        <div class="card-body">

            <ul>

<?php

if(!isset($_SESSION['tipo']))
{
?>

                <li>
                    <a href="index.php">
                        Inicio
                    </a>
                </li>

                <li>
                    <a href="auth/login.php">
                        Iniciar Sesión
                    </a>
                </li>

                <li>
                    <a href="auth/registro.php">
                        Registrarse
                    </a>
                </li>
                <li>
                    <a href="auth/recuperar.php">
                        Recuperar Contraseña
                    </a>
                </li>
                 <li>
                    <a href="cliente/vuelos/listar.php">
                        Vuelos
                    </a>
                </li>
                 <li>
                    <a href="cliente/promociones/listar.php">
                        Promociones
                    </a>
                </li>
                 <li>
                    <a href="cliente/novedades/listar.php">
                        Novedades
                    </a>

<?php
}
elseif($_SESSION['tipo'] == 'CLIENTE')
{
?>


                <li>
                    <a href="cliente/dashboard.php">
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="cliente/vuelos/listar.php">
                        Vuelos
                    </a>
                </li>

                <li>
                    <a href="cliente/reservas/listar.php">
                        Reservas
                    </a>
                </li>

                <li>
                    <a href="cliente/promociones/listar.php">
                        Promociones
                    </a>
                </li>

                <li>
                    <a href="cliente/novedades/listar.php">
                        Novedades
                    </a>
                </li>

                <li>
                    <a href="perfil/index.php">
                        Mi Perfil
                    </a>
                </li>

<?php
}
elseif($_SESSION['tipo'] == 'CEO')
{
?>

                <li>
                    <a href="ceo/dashboard.php">
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="ceo/vuelos/listar.php">
                        Mis Vuelos
                    </a>
                </li>

                <li>
                    <a href="ceo/promociones/listar.php">
                        Promociones
                    </a>
                </li>

                <li>
                    <a href="ceo/reportes/ventas.php">
                        Reporte de Ventas
                    </a>
                </li>

                <li>
                    <a href="ceo/reportes/ocupacion.php">
                        Reporte de Ocupación
                    </a>
                </li>

                <li>
                    <a href="perfil/index.php">
                        Mi Perfil
                    </a>
                </li>

<?php
}
elseif($_SESSION['tipo'] == 'ADMIN')
{
?>

                <li>
                    <a href="admin/dashboard.php">
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="admin/aerolineas/listar.php">
                        Aerolíneas
                    </a>
                </li>

                <li>
                    <a href="admin/ceos/listar.php">
                        CEOs
                    </a>
                </li>

                <li>
                    <a href="admin/asignaciones/listar.php">
                        Asignaciones
                    </a>
                </li>

                <li>
                    <a href="admin/promociones/listar.php">
                        Promociones
                    </a>
                </li>

                <li>
                    <a href="admin/novedades/listar.php">
                        Novedades
                    </a>
                </li>

                <li>
                    <a href="admin/reportes/usuarios.php">
                        Reporte Usuarios
                    </a>
                </li>

                <li>
                    <a href="admin/reportes/vuelos.php">
                        Reporte Vuelos
                    </a>
                </li>

                <li>
                    <a href="admin/reportes/ventas.php">
                        Reporte Ventas
                    </a>
                </li>

                <li>
                    <a href="perfil/index.php">
                        Mi Perfil
                    </a>
                </li>

<?php
}
?>

            </ul>

        </div>

    </div>

</div>

<?php
include("includes/footer.php");
?>