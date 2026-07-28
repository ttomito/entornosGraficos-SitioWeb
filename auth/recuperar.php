<?php

include("../includes/header.php");

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card card-custom">

                <div class="card-body">

                    <h2 id="tituloRecuperar">

                        Recuperar Contraseña

                    </h2>

                    <hr>

                    <?php
                    if (isset($_GET['ok'])) {
                    ?>
                        <div class="alert alert-success" role="alert">

                            Si existe una cuenta con ese correo, te enviamos un enlace
                            para cambiar la contraseña. Revisá tu bandeja de entrada
                            y la carpeta de correo no deseado.

                        </div>
                    <?php
                    }

                    if (isset($_GET['invalido'])) {
                    ?>
                        <div class="alert alert-warning" role="alert">

                            Ingresá una dirección de correo válida.

                        </div>
                    <?php
                    }

                    if (isset($_GET['error'])) {
                    ?>
                        <div class="alert alert-danger" role="alert">

                            No pudimos procesar tu pedido en este momento.
                            Intentá nuevamente en unos minutos.

                        </div>
                    <?php
                    }
                    ?>

                    <p class="text-muted" style="font-size: 0.9rem;">
                        Ingresá el correo con el que te registraste. Este campo es obligatorio.
                    </p>

                    <form
                        id="formRecuperar"
                        action="enviarRecuperacion.php"
                        method="post"
                        aria-labelledby="tituloRecuperar"
                        novalidate>

                        <div class="mb-3">

                            <label for="email" class="form-label">

                                Correo electrónico

                            </label>

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

                            <small id="emailAyuda" class="form-text text-muted">Ejemplo: nombre@ejemplo.com</small>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary">

                            Enviar enlace de recuperación

                        </button>

                        <a
                            href="login.php"
                            class="d-inline-block mt-3">

                            Volver al inicio de sesión

                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    (function() {
        var formulario = document.getElementById('formRecuperar');

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