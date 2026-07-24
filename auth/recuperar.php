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
                    if(isset($_GET['ok']))
                    {
                    ?>
                        <div class="alert alert-success" role="alert">

                            Revisá tu correo electrónico.

                        </div>
                    <?php
                    }

                    if(isset($_GET['error']))
                    {
                    ?>
                        <div class="alert alert-danger" role="alert">

                            No existe una cuenta con ese correo.

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

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    (function () {
        var formulario = document.getElementById('formRecuperar');

        formulario.addEventListener('submit', function (evento) {
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