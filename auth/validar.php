<?php

include("../includes/conexion.php");

$token = $_GET['token'];

$consulta = "

SELECT *

FROM usuarios

WHERE tokenValidacion = '$token'

";

$resultadoUsuario = mysqli_query(
    $link,
    $consulta
);

$usuario = mysqli_fetch_assoc(
    $resultadoUsuario
);

/*
    Token inválido o ya utilizado
*/

if(!$usuario)
{
    include("../includes/header.php");

    echo "

    <div class='container mt-5'>

        <div class='row justify-content-center'>

            <div class='col-md-6'>

                <div class='card card-custom'>

                    <div class='card-body text-center p-5'>

                        <h2 class='text-danger'>

                            Link inválido

                        </h2>

                        <p>

                            Este enlace ya fue utilizado o no existe.

                        </p>

                        <a
                        href='login.php'
                        class='btn btn-primary'>

                            Ir al Login

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

    ";

    include("../includes/footer.php");

    exit();
}

/*
    CLIENTE
*/

if($usuario['tipoUsuario'] == 'CLIENTE')
{
    $sql = "

    UPDATE usuarios

    SET
    estadoCuenta = 'ACTIVA',
    tokenValidacion = NULL

    WHERE tokenValidacion = '$token'

    ";
}

/*
    CEO
*/

else
{
    $sql = "

    UPDATE usuarios

    SET
    tokenValidacion = NULL

    WHERE tokenValidacion = '$token'

    ";
}

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

                    <?php if($resultado){ ?>

                        <h1 class="text-success mb-4">

                            ✅ Cuenta Validada

                        </h1>

                        <?php
                        if($usuario['tipoUsuario'] == 'CEO')
                        {
                        ?>

                            <p class="lead">

                                Tu correo fue validado correctamente.

                            </p>

                            <p class="text-muted">

                                Ahora un administrador debe aprobar tu solicitud.

                            </p>

                        <?php
                        }
                        else
                        {
                        ?>

                            <p class="lead">

                                Tu cuenta fue activada correctamente.

                            </p>

                            <p class="text-muted">

                                Ya podés iniciar sesión.

                            </p>

                        <?php
                        }
                        ?>

                        <a
                        href="login.php"
                        class="btn btn-primary btn-lg mt-3">

                            Ir al Login

                        </a>

                    <?php } ?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

include("../includes/footer.php");

?>