<?php

include("../includes/conexion.php");

$token = $_GET['token'];

$sql = "

SELECT *

FROM usuarios

WHERE tokenRecuperacion = '$token'

";

$resultado = mysqli_query(
    $link,
    $sql
);

if(mysqli_num_rows($resultado) == 0)
{
    die("Token inválido.");
}

include("../includes/header.php");

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card card-custom">

                <div class="card-body">

                    <h2>

                        Nueva Contraseña

                    </h2>

           <form
action="guardarNuevaClave.php"
method="post"
onsubmit="return validarClaves();">

    <input
    type="hidden"
    name="token"
    value="<?= $token ?>">

    <div class="mb-3">

        <label>

            Nueva contraseña

        </label>

        <input
        type="password"
        name="clave"
        id="clave"
        class="form-control"
        required>

    </div>

    <div class="mb-3">

        <label>

            Confirmar contraseña

        </label>

        <input
        type="password"
        name="confirmar"
        id="confirmar"
        class="form-control"
        required>

    </div>

    <div
id="errorClave"
class="alert alert-danger mt-3 d-none">

    Las contraseñas no coinciden.

</div>

    <button
    class="btn btn-success">

        Guardar Contraseña

    </button>

  

</form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

function validarClaves()
{
    let clave =
    document.getElementById("clave").value;

    let confirmar =
    document.getElementById("confirmar").value;

    let error =
    document.getElementById("errorClave");

    if(clave !== confirmar)
    {
        error.classList.remove(
            "d-none"
        );

        return false;
    }

    error.classList.add(
        "d-none"
    );

    return true;
}

</script>

<?php
include("../includes/footer.php");
?>