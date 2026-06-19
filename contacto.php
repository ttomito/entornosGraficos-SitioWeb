<?php

include("includes/header.php");

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom shadow">

                <div class="card-body p-4">

                    <h2 class="mb-4">

                        Contacto

                    </h2>

                    <p class="text-muted">

                        Si tenés consultas, sugerencias o inconvenientes,
                        completá el siguiente formulario.

                    </p>

                    <?php

                    if(isset($_GET['ok']))
                    {
                    ?>

                        <div class="alert alert-success">

                            Mensaje enviado correctamente.

                        </div>

                    <?php
                    }

                    if(isset($_GET['error']))
                    {
                    ?>

                        <div class="alert alert-danger">

                            Ocurrió un error al enviar el mensaje.

                        </div>

                    <?php
                    }
                    ?>

                    <form
                    action="enviarContacto.php"
                    method="post">

                        <div class="mb-3">

                            <label class="form-label">

                                Nombre

                            </label>

                            <input
                            type="text"
                            name="nombre"
                            class="form-control"
                            required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Email

                            </label>

                            <input
                            type="email"
                            name="email"
                            class="form-control"
                            required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Mensaje

                            </label>

                            <textarea
                            name="mensaje"
                            rows="5"
                            class="form-control"
                            required></textarea>

                        </div>

                        <button
                        type="submit"
                        class="btn btn-primary">

                            Enviar Mensaje

                        </button>

                    </form>

                        <hr>

<h4>Información de Contacto</h4>

<p>
    Email: sistemavuelos@gmail.com
</p>

<p>
    Teléfono: +54 341 1234567
</p>

<p>
    Facultad Regional Rosario - UTN
</p>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

include("includes/footer.php");

?>