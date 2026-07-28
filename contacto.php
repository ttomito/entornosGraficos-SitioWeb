<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$valores = $_SESSION['contacto_valores'] ?? [];
unset($_SESSION['contacto_valores']);

$vNombre  = htmlspecialchars($valores['nombre']  ?? '', ENT_QUOTES, 'UTF-8');
$vEmail   = htmlspecialchars($valores['email']   ?? '', ENT_QUOTES, 'UTF-8');
$vMensaje = htmlspecialchars($valores['mensaje'] ?? '', ENT_QUOTES, 'UTF-8');

include("includes/header.php");

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom shadow">

                <div class="card-body p-4">

                    <h2 class="mb-4" id="tituloContacto">

                        Contacto

                    </h2>

                    <p class="text-muted">

                        Si tenés consultas, sugerencias o inconvenientes,
                        completá el siguiente formulario.

                    </p>

                    <?php
                    if (isset($_GET['ok'])) {
                    ?>

                        <div class="alert alert-success" role="alert">

                            Mensaje enviado correctamente. Te vamos a responder a la brevedad.

                        </div>

                    <?php
                    }

                    if (isset($_GET['invalido'])) {
                    ?>

                        <div class="alert alert-warning" role="alert">

                            Revisá los datos ingresados: alguno de los campos está incompleto
                            o no cumple el formato requerido.

                        </div>

                    <?php
                    }

                    if (isset($_GET['error'])) {
                    ?>

                        <div class="alert alert-danger" role="alert">

                            Ocurrió un error al enviar el mensaje. Intentá nuevamente
                            en unos minutos.

                        </div>

                    <?php
                    }
                    ?>

                    <p class="text-muted" style="font-size: 0.9rem;">
                        Todos los campos son obligatorios.
                    </p>

                    <form
                        id="formContacto"
                        action="enviarContacto.php"
                        method="post"
                        aria-labelledby="tituloContacto"
                        novalidate>

                        <div class="mb-3">

                            <label for="nombre" class="form-label">

                                Nombre

                            </label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                class="form-control"
                                maxlength="60"
                                minlength="2"
                                required
                                aria-required="true"
                                autocomplete="name"
                                aria-describedby="nombreAyuda"
                                value="<?= $vNombre ?>">

                            <small id="nombreAyuda" class="form-text text-muted">Entre 2 y 60 caracteres.</small>

                        </div>

                        <div class="mb-3">

                            <label for="email" class="form-label">

                                Email

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
                                aria-describedby="emailAyuda"
                                value="<?= $vEmail ?>">

                            <small id="emailAyuda" class="form-text text-muted">Ejemplo: nombre@ejemplo.com</small>

                        </div>

                        <div class="mb-3">

                            <label for="mensaje" class="form-label">

                                Mensaje

                            </label>

                            <textarea
                                id="mensaje"
                                name="mensaje"
                                rows="5"
                                class="form-control"
                                maxlength="2000"
                                minlength="10"
                                required
                                aria-required="true"
                                aria-describedby="mensajeAyuda"><?= $vMensaje ?></textarea>

                            <small id="mensajeAyuda" class="form-text text-muted">Entre 10 y 2000 caracteres.</small>

                        </div>

                        <!--
                            Trampa para bots: los robots completan todos los campos
                            que encuentran en el HTML. Una persona nunca lo ve, así que
                            si llega con contenido el mensaje se descarta del lado del
                            servidor. Va oculto por CSS y fuera del orden de tabulación.
                        -->
                        <div class="d-none" aria-hidden="true">
                            <label for="sitioWeb">No completar este campo</label>
                            <input
                                type="text"
                                id="sitioWeb"
                                name="sitioWeb"
                                tabindex="-1"
                                autocomplete="off">
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary">

                            Enviar Mensaje

                        </button>

                    </form>

                    <hr>

                    <h3 class="h4">Información de Contacto</h3>

                    <p>
                        Email:
                        <a href="mailto:sistemavuelos@gmail.com">sistemavuelos@gmail.com</a>
                    </p>

                    <p>
                        Teléfono:
                        <a href="tel:+543411234567">+54 341 1234567</a>
                    </p>

                    <p>
                        Facultad Regional Rosario - UTN
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    (function() {
        var formulario = document.getElementById('formContacto');

        formulario.addEventListener('submit', function(evento) {
            if (!formulario.checkValidity()) {
                evento.preventDefault();
                formulario.reportValidity();
            }
        });
    })();
</script>

<?php

include("includes/footer.php");

?>