<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$id = $_GET['id'];

$idCEO = $_SESSION['id'];

$sql = "SELECT v.*
FROM vuelos v
INNER JOIN usuarios u
ON v.codAerolinea = u.codAerolinea
WHERE v.codVuelo = $id
AND u.codUsuario = $idCEO";

$resultado = mysqli_query($link, $sql);
$vuelo = mysqli_fetch_assoc($resultado);

if (!$vuelo) {
    die("Acceso denegado");
}

$fechaMinima = date('Y-m-d', strtotime('+1 day'));

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2>Editar Vuelo</h2>
                    <p class="text-muted" style="font-size: 0.9rem;">Los campos marcados con <span aria-hidden="true">*</span> son obligatorios.</p>

                    <form action="actualizar.php" method="post" enctype="multipart/form-data">

                        <input type="hidden" name="id" value="<?= $vuelo['codVuelo'] ?>">
                        <input type="hidden" name="imagenActual" value="<?= $vuelo['imagenVuelo'] ?>">

                        <div class="mb-3">
                            <label for="origen">Origen <span aria-hidden="true">*</span></label>
                            <input type="text" id="origen" name="origen" class="form-control" maxlength="50" minlength="3" pattern="[A-Za-zÀ-ÿ\s]+" value="<?= $vuelo['origenVuelo'] ?>" required aria-required="true" aria-describedby="origenAyuda">
                            <small id="origenAyuda" class="form-text text-muted">Solo letras y espacios, entre 3 y 50 caracteres.</small>
                        </div>

                        <div class="mb-3">
                            <label for="destino">Destino <span aria-hidden="true">*</span></label>
                            <input type="text" id="destino" name="destino" class="form-control" maxlength="50" minlength="3" pattern="[A-Za-zÀ-ÿ\s]+" value="<?= $vuelo['destinoVuelo'] ?>" required aria-required="true" aria-describedby="destinoAyuda">
                            <small id="destinoAyuda" class="form-text text-muted">Solo letras y espacios, entre 3 y 50 caracteres.</small>
                        </div>

                        <div class="mb-3">
                            <label for="fecha">Fecha <span aria-hidden="true">*</span></label>
                            <input type="date" id="fecha" name="fecha" class="form-control" min="<?= $fechaMinima ?>" value="<?= $vuelo['fechaVuelo'] ?>" required aria-required="true" aria-describedby="fechaAyuda">
                            <small id="fechaAyuda" class="form-text text-muted">Debe ser posterior a hoy.</small>
                        </div>

                        <div class="mb-3">
                            <label for="hora">Hora <span aria-hidden="true">*</span></label>
                            <input type="time" id="hora" name="hora" class="form-control" value="<?= $vuelo['horaSalida'] ?>" required aria-required="true">
                        </div>

                        <div class="mb-3">
                            <label for="precio">Precio <span aria-hidden="true">*</span></label>
                            <input type="number" id="precio" step="0.01" min="50" max="5000000" name="precio" class="form-control" value="<?= $vuelo['precioVuelo'] ?>" required aria-required="true" aria-describedby="precioAyuda">
                            <small id="precioAyuda" class="form-text text-muted">Valor entre 50 y 5.000.000.</small>
                        </div>

                        <div class="mb-3">
                            <label for="asientos">Asientos Disponibles <span aria-hidden="true">*</span></label>
                            <input type="number" id="asientos" name="asientos" class="form-control" min="0" max="500" step="1" value="<?= $vuelo['asientosDisponibles'] ?>" required aria-required="true" aria-describedby="asientosAyuda">
                            <small id="asientosAyuda" class="form-text text-muted">Número entero entre 0 y 500.</small>
                        </div>

                        <div class="mb-3">

                            <label for="imagen">Imagen de referencia</label>

                            <?php if (!empty($vuelo['imagenVuelo'])): ?>
                                <div class="mb-2">
                                    <img src="../../uploads/vuelos/<?= $vuelo['imagenVuelo'] ?>" alt="Imagen actual del vuelo cargada previamente" style="max-height: 150px; border-radius: 6px;">
                                    <p class="text-muted mt-1" style="font-size: 0.85rem;">Imagen actual. Seleccioná una nueva para reemplazarla.</p>
                                </div>
                            <?php endif; ?>

                            <input type="file" id="imagen" name="imagen" class="form-control" accept="image/png, image/jpeg, image/webp" aria-describedby="imagenAyuda">
                            <small id="imagenAyuda" class="form-text text-muted">Formatos permitidos: PNG, JPG, JPEG o WEBP.</small>

                        </div>

                        <button class="btn btn-primary" onclick="modificarVuelo(event)">Actualizar</button>
                        <a href="listar.php" class="btn btn-secondary">Cancelar</a>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    function modificarVuelo(event) {
        event.preventDefault();

        const formulario = event.target.closest('form');

        Swal.fire({
            title:              '¿Estás seguro?',
            text:               '¿Desea modificar este vuelo?',
            icon:               'warning',
            showCancelButton:   true,
            confirmButtonColor: '#198754',
            cancelButtonColor:  '#6c757d',
            confirmButtonText:  'Sí, modificar',
            cancelButtonText:   'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                formulario.submit();
            }
        });
    }
</script>

<?php
include("../../includes/footer.php");
?>