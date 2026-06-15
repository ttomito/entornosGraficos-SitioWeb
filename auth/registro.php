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

                    <p class="text-center text-muted mb-4">

                        Registrate para reservar vuelos y gestionar tus viajes.

                    </p>

                    <form
                    action="procesarRegistro.php"
                    method="post">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Nombre Completo

                                </label>

                                <input
                                type="text"
                                name="nombre"
                                class="form-control"
                                required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Teléfono

                                </label>

                                <input
                                type="text"
                                name="telefono"
                                class="form-control">

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