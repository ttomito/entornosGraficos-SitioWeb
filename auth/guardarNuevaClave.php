<?php

include("../includes/conexion.php");
include("../includes/header.php");

$token = $_POST['token'];
$clave = $_POST['clave'];

$sql = "

UPDATE usuarios

SET
claveUsuario = '$clave',
tokenRecuperacion = NULL

WHERE tokenRecuperacion = '$token'

";

mysqli_query(
    $link,
    $sql
);

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card card-custom">

                <div class="card-body text-center">

                    <div class="alert alert-success">

                        <h4>

                            ✅ Contraseña actualizada correctamente

                        </h4>

                        <p class="mb-0">

                            Ya podés iniciar sesión con tu nueva contraseña.

                        </p>

                    </div>

                    <a
                    href="login.php"
                    class="btn btn-primary">

                        Ir al Login

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php
include("../includes/footer.php");
?>