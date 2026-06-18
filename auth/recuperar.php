<?php

include("../includes/header.php");

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card card-custom">

                <div class="card-body">

                    <h2>

                        Recuperar Contraseña

                    </h2>

                    <hr>

                    <?php
                    if(isset($_GET['ok']))
                    {
                    ?>
                        <div class="alert alert-success">

                            Revisá tu correo electrónico.

                        </div>
                    <?php
                    }

                    if(isset($_GET['error']))
                    {
                    ?>
                        <div class="alert alert-danger">

                            No existe una cuenta con ese correo.

                        </div>
                    <?php
                    }
                    ?>

                    <form
                    action="enviarRecuperacion.php"
                    method="post">

                        <div class="mb-3">

                            <label>

                                Correo electrónico

                            </label>

                            <input
                            type="email"
                            name="email"
                            class="form-control"
                            required>

                        </div>

                        <button
                        class="btn btn-primary">

                            Enviar enlace de recuperacion

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php
include("../includes/footer.php");
?>