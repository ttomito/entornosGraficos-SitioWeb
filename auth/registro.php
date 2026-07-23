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

                        <div class="alert alert-success text-center">

                            Registro exitoso.
                            Revisá tu correo electrónico para activar la cuenta.

                        </div>

                    <?php
                    }

                    if (isset($_GET['ceo'])) {
                    ?>

                        <div class="alert alert-info text-center">

                            Registro exitoso.

                            Revisá tu correo electrónico para validar la cuenta.

                            Luego un administrador deberá aprobar tu solicitud.

                        </div>

                    <?php
                    }

                    if (isset($_GET['existe'])) {
                    ?>

                        <div class="alert alert-danger text-center">

                            Ya existe una cuenta asociada a ese correo.

                        </div>

                    <?php
                    }
                    ?>

                    <p class="text-center text-muted mb-4">

                        Registrate para reservar vuelos y gestionar tus viajes.

                    </p>

                    <form
                        action="procesarRegistro.php"
                        method="post">
<div class="row">

    <div class="col-md-6 mb-3">

        <label class="form-label">

            Nombre

        </label>

        <input
            type="text"
            name="nombre"
            class="form-control"
            maxlength="60"
            required>

    </div>

    <div class="col-md-6 mb-3">

        <label class="form-label">

            Apellido

        </label>

        <input
            type="text"
            name="apellido"
            class="form-control"
            maxlength="60"
            required>

    </div>

</div>

<div class="row">

    <div class="col-md-6 mb-3">

        <label class="form-label">

            DNI

        </label>

        <input
            type="text"
            name="dni"
            class="form-control"
            maxlength="15"
            required>

    </div>

    <div class="col-md-6 mb-3">

        <label class="form-label">

            Teléfono

        </label>

        <input
            type="text"
            name="telefono"
            class="form-control"
            maxlength="20"
            required>

    </div>

</div>

                        <div class="mb-3">

                            <label class="form-label">

                                Correo Electrónico

                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Contraseña

                            </label>

                            <input
                                type="password"
                                name="clave"
                                class="form-control"
                                required>

                        </div>

                        <div class="mb-4">

                            <label class="form-label">

                                Tipo de Usuario

                            </label>

                            <select
                                name="tipoUsuario"
                                class="form-select">

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

<?php

include("../includes/footer.php");

?>