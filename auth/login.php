<?php
include("../includes/header.php");
?>

<div class="container">

    <div class="row justify-content-center mt-5">

        <div class="col-md-5">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2 class="text-center mb-4" id="tituloLogin">

                        Iniciar Sesión

                    </h2>
                    <?php

                    if (isset($_GET['error'])) {
                    ?>

                        <div class="alert alert-danger text-center" role="alert">

                            Usuario o contraseña incorrectos.

                        </div>

                    <?php
                    }

                    if (isset($_GET['pendiente'])) {
                    ?>

                        <div class="alert alert-warning text-center" role="alert">

                            Debés validar tu correo electrónico antes de iniciar sesión.

                        </div>

                    <?php
                    }
                    ?>

                    <?php

                    if (isset($_GET['esperando'])) {
                    ?>

                        <div class="alert alert-info text-center" role="alert">

                            Tu cuenta está pendiente de aprobación por un administrador.

                        </div>

                    <?php
                    }

                    if (isset($_GET['actualizada'])) {
                    ?>

                        <div class="alert alert-success text-center" role="alert">

                            Tu contraseña fue actualizada. Ya podés iniciar sesión.

                        </div>

                    <?php
                    }
                    ?>

                    <p class="text-muted" style="font-size: 0.9rem;">
                        Todos los campos son obligatorios.
                    </p>

                    <form
                        id="formLogin"
                        action="procesarLogin.php"
                        method="post"
                        aria-labelledby="tituloLogin"
                        novalidate>

                        <div class="mb-3">

                            <label for="email" class="form-label">Email</label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                maxlength="100"
                                required
                                aria-required="true"
                                autocomplete="email"
                                aria-describedby="emailAyuda">

                            <small id="emailAyuda" class="form-text text-muted">Ejemplo: nombre@dominio.com</small>

                        </div>

                        <div class="mb-3">

                            <label for="clave" class="form-label">Contraseña</label>

                            <div class="input-group">

                                <input
                                    type="password"
                                    id="clave"
                                    name="clave"
                                    class="form-control"
                                    minlength="8"
                                    pattern="(?=.*[A-Za-z])(?=.*\d).{8,}"
                                    required
                                    aria-required="true"
                                    autocomplete="current-password"
                                    aria-describedby="claveAyuda">

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary toggle-clave"
                                    data-target="clave"
                                    aria-pressed="false"
                                    aria-label="Mostrar contraseña">

                                    <svg class="icon-mostrar" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>

                                    <svg class="icon-ocultar" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false" style="display:none;">
                                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.6 21.6 0 0 1 5.06-6.06M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 7 11 7a21.7 21.7 0 0 1-3.22 4.36M14.12 14.12a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>

                                </button>

                            </div>

                            <small id="claveAyuda" class="form-text text-muted">Ingresá la contraseña con la que te registraste.</small>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100">

                            Ingresar

                        </button>

                        <a
                            href="recuperar.php"
                            class="d-inline-block mt-3">

                            ¿Olvidaste tu contraseña?

                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    (function() {
        var formulario = document.getElementById('formLogin');

        // mostrar / ocultar contraseña
        document.querySelectorAll('.toggle-clave').forEach(function(boton) {
            boton.addEventListener('click', function() {
                var idCampo = boton.getAttribute('data-target');
                var campo = document.getElementById(idCampo);
                var iconoMostrar = boton.querySelector('.icon-mostrar');
                var iconoOcultar = boton.querySelector('.icon-ocultar');
                var seVeAhora = campo.type === 'password';

                campo.type = seVeAhora ? 'text' : 'password';
                boton.setAttribute('aria-pressed', seVeAhora ? 'true' : 'false');
                boton.setAttribute('aria-label', seVeAhora ? 'Ocultar contraseña' : 'Mostrar contraseña');
                iconoMostrar.style.display = seVeAhora ? 'none' : 'inline-block';
                iconoOcultar.style.display = seVeAhora ? 'inline-block' : 'none';
            });
        });

        // validación nativa antes de enviar
        formulario.addEventListener('submit', function(evento) {
            if (!formulario.checkValidity()) {
                evento.preventDefault();
                formulario.reportValidity();
            }
        });
    })();
</script>

<?php
include("../includes/footer.php");
?>