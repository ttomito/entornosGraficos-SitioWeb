<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$id = $_GET['id'];

$idCEO = $_SESSION['id'];

$sql = "

SELECT
v.*

FROM vuelos v

INNER JOIN usuarios u
ON v.codAerolinea = u.codAerolinea

WHERE v.codVuelo = $id

AND u.codUsuario = $idCEO

";

$resultado = mysqli_query(
    $link,
    $sql
);

$vuelo = mysqli_fetch_assoc(
    $resultado
);

if(!$vuelo)
{
    die("Acceso denegado");
}

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2>

                        Editar Vuelo

                    </h2>

                    <form
                    action="actualizar.php"
                    method="post">

                        <input
                        type="hidden"
                        name="id"
                        value="<?= $vuelo['codVuelo'] ?>">

                        <div class="mb-3">

                            <label>

                                Origen

                            </label>

                            <input
                            type="text"
                            name="origen"
                            class="form-control"
                            value="<?= $vuelo['origenVuelo'] ?>"
                            required>

                        </div>

                        <div class="mb-3">

                            <label>

                                Destino

                            </label>

                            <input
                            type="text"
                            name="destino"
                            class="form-control"
                            value="<?= $vuelo['destinoVuelo'] ?>"
                            required>

                        </div>

                        <div class="mb-3">

                            <label>

                                Fecha

                            </label>

                            <input
                            type="date"
                            name="fecha"
                            class="form-control"
                            value="<?= $vuelo['fechaVuelo'] ?>"
                            required>

                        </div>

                        <div class="mb-3">

                            <label>

                                Hora

                            </label>

                            <input
                            type="time"
                            name="hora"
                            class="form-control"
                            value="<?= $vuelo['horaSalida'] ?>"
                            required>

                        </div>

                        <div class="mb-3">

                            <label>

                                Precio

                            </label>

                            <input
                            type="number"
                            step="0.01"
                            name="precio"
                            class="form-control"
                            value="<?= $vuelo['precioVuelo'] ?>"
                            required>

                        </div>

                        <div class="mb-3">

                            <label>

                                Asientos Disponibles

                            </label>

                            <input
                            type="number"
                            name="asientos"
                            class="form-control"
                            value="<?= $vuelo['asientosDisponibles'] ?>"
                            required>

                        </div>
                        <div class="mb-3">

                            <label>
                                
                                Imagen de referencia
                            
                            </label>

                            <input
                            type="text"
                            name="imagen"
                            class="form-control"
                            value="<?= $vuelo['imagenVuelo'] ?>"
                            required>

                        </div>

                        <button
                        class="btn btn-primary">

                            Actualizar

                        </button>

                        <a
                        href="listar.php"
                        class="btn btn-secondary">

                            Cancelar

                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>