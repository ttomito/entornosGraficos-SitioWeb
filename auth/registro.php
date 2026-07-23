<?php

include("../includes/header.php");

?>

<div class="container my-5">

    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2 class="text-center mb-3">

                        Crear Cuenta

                    </h2>
                    <?php

                    if (isset($_GET['exito'])) {
                    ?>

                        <div class="alert alert-success text-center" role="alert">

                            Registro exitoso.
                            Revisá tu correo electrónico para activar la cuenta.

                        </div>

                    <?php
                    }

                    if (isset($_GET['ceo'])) {
                    ?>

                        <div class="alert alert-info text-center" role="alert">

                            Registro exitoso.

                            Revisá tu correo electrónico para validar la cuenta.

                            Luego un administrador deberá aprobar tu solicitud.

                        </div>

                    <?php
                    }

                    if (isset($_GET['existe'])) {
                    ?>

                        <div class="alert alert-danger text-center" role="alert">

                            Ya existe una cuenta asociada a ese correo.

                        </div>

                    <?php
                    }

                    if (isset($_GET['invalido'])) {
                    ?>

                        <div class="alert alert-danger text-center" role="alert">

                            Revisá los datos ingresados: alguno de los campos no cumple el formato requerido.

                        </div>

                    <?php
                    }
                    ?>

                    <p class="text-center text-muted mb-4">

                        Registrate para reservar vuelos y gestionar tus viajes.

                    </p>

                    <p class="text-muted" style="font-size: 0.9rem;">
                        Los campos marcados con <span aria-hidden="true">*</span> son obligatorios.
                    </p>

                    <form
                        id="formRegistro"
                        action="procesarRegistro.php"
                        method="post"
                        novalidate>

<div class="row">

    <div class="col-md-6 mb-3">

        <label for="nombre" class="form-label">

            Nombre <span aria-hidden="true">*</span>

        </label>

        <input
            type="text"
            id="nombre"
            name="nombre"
            class="form-control"
            maxlength="60"
            minlength="2"
            pattern="[A-Za-zÀ-ÿ\s]+"
            required
            aria-required="true"
            aria-describedby="nombreAyuda">

        <small id="nombreAyuda" class="form-text text-muted">Solo letras y espacios, entre 2 y 60 caracteres.</small>

    </div>

    <div class="col-md-6 mb-3">

        <label for="apellido" class="form-label">

            Apellido <span aria-hidden="true">*</span>

        </label>

        <input
            type="text"
            id="apellido"
            name="apellido"
            class="form-control"
            maxlength="60"
            minlength="2"
            pattern="[A-Za-zÀ-ÿ\s]+"
            required
            aria-required="true"
            aria-describedby="apellidoAyuda">

        <small id="apellidoAyuda" class="form-text text-muted">Solo letras y espacios, entre 2 y 60 caracteres.</small>

    </div>

</div>

<div class="row">

    <div class="col-md-6 mb-3">

        <label for="dni" class="form-label">

            DNI <span aria-hidden="true">*</span>

        </label>

        <input
            type="text"
            id="dni"
            name="dni"
            class="form-control"
            inputmode="numeric"
            maxlength="8"
            minlength="7"
            pattern="\d{7,8}"
            required
            aria-required="true"
            aria-describedby="dniAyuda">

        <small id="dniAyuda" class="form-text text-muted">Solo números, sin puntos (7 u 8 dígitos).</small>

    </div>

    <div class="col-md-6 mb-3">

        <label for="telefono" class="form-label">

            Teléfono <span aria-hidden="true">*</span>

        </label>

        <input
            type="tel"
            id="telefono"
            name="telefono"
            class="form-control"
            maxlength="20"
            minlength="6"
            pattern="[0-9+\-\s()]{6,20}"
            required
            aria-required="true"
            aria-describedby="telefonoAyuda">

        <small id="telefonoAyuda" class="form-text text-muted">Solo números y los símbolos + - ( ), entre 6 y 20 caracteres.</small>

    </div>

</div>

                        <div class="mb-3">

                            <label for="email" class="form-label">

                                Correo Electrónico <span aria-hidden="true">*</span>

                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                maxlength="100"
                                required
                                aria-required="true"
                                aria-describedby="emailAyuda">

                            <small id="emailAyuda" class="form-text text-muted">Ejemplo: nombre@ejemplo.com</small>

                        </div>

                        <div class="mb-3">

                            <label for="clave" class="form-label">

                                Contraseña <span aria-hidden="true">*</span>

                            </label>

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

                            <small id="claveAyuda" class="form-text text-muted">Mínimo 8 caracteres, con al menos una letra y un número.</small>

                        </div>

                        <div class="mb-4">

                            <label for="claveConfirmar" class="form-label">

                                Confirmar Contraseña <span aria-hidden="true">*</span>

                            </label>

                            <div class="input-group">

                                <input
                                    type="password"
                                    id="claveConfirmar"
                                    name="claveConfirmar"
                                    class="form-control"
                                    minlength="8"
                                    pattern="(?=.*[A-Za-z])(?=.*\d).{8,}"
                                    required
                                    aria-required="true"
                                    aria-describedby="claveConfirmarAyuda claveConfirmarError">

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary toggle-clave"
                                    data-target="claveConfirmar"
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

                            <small id="claveConfirmarAyuda" class="form-text text-muted">Debe coincidir con la contraseña ingresada.</small>
                            <div id="claveConfirmarError" class="text-danger mt-1" role="alert" style="display:none; font-size: 0.9rem;">
                                Las contraseñas no coinciden.
                            </div>

                        </div>

                        <div class="mb-4">

                            <label for="tipoUsuario" class="form-label">

                                Tipo de Usuario <span aria-hidden="true">*</span>

                            </label>

                            <select
                                id="tipoUsuario"
                                name="tipoUsuario"
                                class="form-select"
                                required
                                aria-required="true">

                                <option value="CLIENTE">

                                    Cliente

                                </option>

                                <option value="CEO">

                                    CEO de Aerolínea

                                </option>

                            </select>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100">

                            Registrarme

                        </button>

                    </form>

                    <hr>

                    <p class="text-center">

                        ¿Ya tenés cuenta?

                        <a href="login.php">

                            Iniciar sesión

                        </a>

                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    (function () {
        var formulario = document.getElementById('formRegistro');
        var clave = document.getElementById('clave');
        var claveConfirmar = document.getElementById('claveConfirmar');
        var errorConfirmar = document.getElementById('claveConfirmarError');

        // mostrar ocultar contraseña
        document.querySelectorAll('.toggle-clave').forEach(function (boton) {
            boton.addEventListener('click', function () {
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

        // contraseñas coincidan antes de enviar
        function claveCoincide() {
            var coincide = clave.value === claveConfirmar.value;

            if (!coincide) {
                claveConfirmar.setCustomValidity('Las contraseñas no coinciden.');
                errorConfirmar.style.display = 'block';
            } else {
                claveConfirmar.setCustomValidity('');
                errorConfirmar.style.display = 'none';
            }

            return coincide;
        }

        clave.addEventListener('input', claveCoincide);
        claveConfirmar.addEventListener('input', claveCoincide);

        formulario.addEventListener('submit', function (evento) {
            var esValido = formulario.checkValidity();
            var coincide = claveCoincide();

            if (!esValido || !coincide) {
                evento.preventDefault();
                formulario.reportValidity();

                if (!coincide) {
                    claveConfirmar.focus();
                }
            }
        });
    })();
</script>

<?php

include("../includes/footer.php");

?>