<?php

include("../includes/verificarSession.php");
include("../includes/conexion.php");
include("../includes/header.php");

$id = $_SESSION['id'];

$sql = "

SELECT *

FROM usuarios

WHERE codUsuario = $id

";

$resultado = mysqli_query(
    $link,
    $sql
);

$usuario = mysqli_fetch_assoc(
    $resultado
);

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2>

                        Miii Perfil

                    </h2>

                    <hr>

                    <form
                    action="actualizar.php"
                    method="post">

                        <div class="mb-3">

                            <label>

                                Nombre

                            </label>

                            <input
                            type="text"
                            name="nombre"
                            class="form-control"
                            value="<?= $usuario['nombreUsuario'] ?>"
                            required>

                        </div>

                        <div class="mb-3">

                            <label>

                                Email

                            </label>

                            <input
                            type="email"
                            class="form-control"
                            value="<?= $usuario['emailUsuario'] ?>"
                            disabled>

                        </div>

                        <div class="mb-3">

                            <label>

                                Teléfono

                            </label>

                            <input
                            type="text"
                            name="telefono"
                            class="form-control"
                            value="<?= $usuario['telefonoUsuario'] ?>">

                        </div>

                        <div class="mb-3">

                            <label>

                                Nueva Contraseña

                            </label>

                            <input
                            type="password"
                            name="clave"
                            class="form-control">

                        </div>

                        <div class="mb-3">

                            <label>

                                Tipo Usuario

                            </label>

                            <input
                            type="text"
                            class="form-control"
                            value="<?= $usuario['tipoUsuario'] ?>"
                            disabled>

                        </div>

                        <button
                        class="btn btn-primary">

                            Guardar Cambios

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