<?php
include("../includes/header.php");
?>

<div class="container">

    <div class="row justify-content-center mt-5">

        <div class="col-md-5">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2 class="text-center mb-4">

                        Iniciar Sesión

                    </h2>
                    <?php

if(isset($_GET['error']))
{
?>

<div class="alert alert-danger text-center">

    Usuario o contraseña incorrectos.

</div>

<?php
}

if(isset($_GET['pendiente']))
{
?>

<div class="alert alert-warning text-center">

    Debés validar tu correo electrónico antes de iniciar sesión.

</div>

<?php
}
?>

<?php

if(isset($_GET['esperando']))
{
?>

<div class="alert alert-info text-center">

    Tu cuenta está pendiente de aprobación por un administrador.

</div>

<?php
}
?>

                    <form action="procesarLogin.php" method="post">

                        <div class="mb-3">

                            <label>Email</label>

                            <input
                                type="email"
                                name="email"
                                class="form-control">

                        </div>

                        <div class="mb-3">

                            <label>Contraseña</label>

                            <input
                                type="password"
                                name="clave"
                                class="form-control">

                        </div>

                        <button
                            class="btn btn-primary w-100">

                            Ingresar

                        </button>
                        <a
href="recuperar.php">

    ¿Olvidaste tu contraseña?

</a>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php
include("../includes/footer.php");
?>