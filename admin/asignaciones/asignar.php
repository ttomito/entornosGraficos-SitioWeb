<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}

$sqlCEO = "SELECT * FROM usuarios WHERE codUsuario = ?";
$stmtCEO = mysqli_prepare($link, $sqlCEO);

if (!$stmtCEO) {
    error_log("Error al preparar la consulta: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmtCEO, "i", $id);
mysqli_stmt_execute($stmtCEO);

$resultadoCEO = mysqli_stmt_get_result($stmtCEO);
$ceo = $resultadoCEO ? mysqli_fetch_assoc($resultadoCEO) : null;
mysqli_stmt_close($stmtCEO);

if (!$ceo) {
    header("Location: listar.php?alerta=no_encontrada");
    exit();
}

$nombreCeoEscapado = htmlspecialchars($ceo['nombreUsuario'], ENT_QUOTES, 'UTF-8');

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2 id="titulo-asignar">

                        Asignar Aerolínea

                    </h2>

                    <hr>

                    <p>

                        CEO:

                        <strong>

                            <?= $nombreCeoEscapado ?>

                        </strong>

                    </p>

                    <form
                        action="guardarAsignacion.php"
                        method="post"
                        aria-labelledby="titulo-asignar">

                        <input
                            type="hidden"
                            name="idCEO"
                            value="<?= (int)$ceo['codUsuario'] ?>">

                        <div class="mb-3">

                            <label for="codAerolinea">

                                Aerolínea

                            </label>

                            <select
                                id="codAerolinea"
                                name="codAerolinea"
                                class="form-select"
                                required
                                aria-describedby="codAerolinea-error">

                                <option value="">

                                    Seleccione una aerolínea

                                </option>
                                <option value="0">

                                    Sin asignar

                                </option>
                                <?php
                                while ($a = mysqli_fetch_assoc($aerolineas)) {
                                ?>


                                    <option
                                        value="<?= (int)$a['codAerolinea'] ?>">

                                        <?= htmlspecialchars($a['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>

                                    </option>


                                <?php
                                }
                                ?>

                            </select>
                            <div id="codAerolinea-error" class="invalid-feedback" role="alert"></div>

                        </div>

                        <button
                            type="submit"
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
$alertasAsignar = [
    'campos_vacios' => [
        'icon'  => 'error',
        'title' => 'Seleccioná una aerolínea',
        'text'  => 'Debés seleccionar una opción antes de guardar.'
    ],
    'aerolinea_invalida' => [
        'icon'  => 'error',
        'title' => 'Aerolínea inválida',
        'text'  => 'La aerolínea seleccionada no existe. Volvé a intentarlo.'
    ]
];

if (isset($_GET['alerta']) && array_key_exists($_GET['alerta'], $alertasAsignar)) {
    $alertaAsignar = $alertasAsignar[$_GET['alerta']];
?>

    <script>
        Swal.fire({
            icon: '<?= $alertaAsignar['icon'] ?>',
            title: '<?= $alertaAsignar['title'] ?>',
            text: '<?= $alertaAsignar['text'] ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php } ?>

<?php
include("../../includes/footer.php");
?>