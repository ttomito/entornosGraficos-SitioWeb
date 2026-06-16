<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$id = $_GET['id'];

$sqlCEO = "

SELECT *

FROM usuarios

WHERE codUsuario = $id

";

$resultadoCEO = mysqli_query(
    $link,
    $sqlCEO
);

$ceo = mysqli_fetch_assoc(
    $resultadoCEO
);

$sqlAerolineas = "

SELECT *

FROM aerolineas

ORDER BY nombreAerolinea

";

$aerolineas = mysqli_query(
    $link,
    $sqlAerolineas
);

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2>

                        Asignar Aerolínea

                    </h2>

                    <hr>

                    <p>

                        CEO:

                        <strong>

                            <?= $ceo['nombreUsuario'] ?>

                        </strong>

                    </p>

                    <form
                    action="guardarAsignacion.php"
                    method="post">

                        <input
                        type="hidden"
                        name="idCEO"
                        value="<?= $ceo['codUsuario'] ?>">

                        <div class="mb-3">

                            <label>

                                Aerolínea

                            </label>

                            <select
                            name="codAerolinea"
                            class="form-select"
                            required>

                                <option value="">

                                    Seleccione una aerolínea

                                </option>
<option value="0">

    Sin asignar

</option>
                                <?php
                                while($a = mysqli_fetch_assoc($aerolineas))
                                {
                                ?>
                                

                                    <option
                                    value="<?= $a['codAerolinea'] ?>">

                                        <?= $a['nombreAerolinea'] ?>

                                    </option>
                                    

                                <?php
                                }
                                ?>

                            </select>

                        </div>

                        <button
                        class="btn btn-success">

                            Guardar Asignación

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>