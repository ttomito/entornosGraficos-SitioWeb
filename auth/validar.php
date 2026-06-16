<?php

include("../includes/conexion.php");

$token = $_GET['token'];

$sql = "
UPDATE usuarios
SET estadoCuenta = 'ACTIVA',
tokenValidacion = NULL
WHERE tokenValidacion = '$token'
";
// tokenValidacion = NULL esta fila sirve para que el link no pueda reutilizarse infinitas veces

$resultado = mysqli_query(
    $link,
    $sql
);

include("../includes/header.php");

?>

<div class="container my-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card card-custom shadow-lg">

                <div class="card-body text-center p-5">

                    <?php

                    if($resultado)
                    {
                    ?>

                        <h1 class="mb-4 text-success">

                            ✅ Cuenta Activada

                        </h1>

                        <p class="lead">

                            Tu cuenta fue validada correctamente.

                        </p>

                        <p class="text-muted">

                            Ya podés iniciar sesión y comenzar a utilizar AirTickets.

                        </p>

                        <a
                            href="login.php"
                            class="btn btn-primary btn-lg mt-3">

                            Ir al Login

                        </a>

                    <?php
                    }
                    else
                    {
                    ?>

                        <h1 class="mb-4 text-danger">

                            ❌ Error

                        </h1>

                        <p>

                            No se pudo validar la cuenta.

                        </p>

                    <?php
                    }
                    ?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

include("../includes/footer.php");

?>