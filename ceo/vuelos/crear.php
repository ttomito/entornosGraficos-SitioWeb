<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2>Nuevo Vuelo</h2>
                    <p class="text-muted" style="font-size: 0.9rem;">Los campos marcados con <span aria-hidden="true">*</span> son obligatorios.</p>

                    <form action="guardar.php" method="post" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="origen">Origen <span aria-hidden="true">*</span></label>
                            <input type="text" id="origen" name="origen" class="form-control" maxlength="50" minlength="3" pattern="[A-Za-zÀ-ÿ\s]+" required aria-required="true" aria-describedby="origenAyuda">
                            <small id="origenAyuda" class="form-text text-muted">Solo letras y espacios, entre 3 y 50 caracteres.</small>
                        </div>

                        <div class="mb-3">
                            <label for="destino">Destino <span aria-hidden="true">*</span></label>
                            <input type="text" id="destino" name="destino" class="form-control" maxlength="50" minlength="3" pattern="[A-Za-zÀ-ÿ\s]+" required aria-required="true" aria-describedby="destinoAyuda">
                            <small id="destinoAyuda" class="form-text text-muted">Solo letras y espacios, entre 3 y 50 caracteres.</small>
                        </div>

                        <div class="mb-3">
                            <label for="fecha">Fecha <span aria-hidden="true">*</span></label>
                            <input type="date" id="fecha" name="fecha" class="form-control" min="<?= date('Y-m-d') ?>" required aria-required="true" aria-describedby="fechaAyuda">
                            <small id="fechaAyuda" class="form-text text-muted">No puede ser anterior a hoy.</small>
                        </div>

                        <div class="mb-3">
                            <label for="hora">Hora <span aria-hidden="true">*</span></label>
                            <input type="time" id="hora" name="hora" class="form-control" required aria-required="true">
                        </div>

                        <div class="mb-3">
                            <label for="precio">Precio <span aria-hidden="true">*</span></label>
                            <input type="number" id="precio" step="0.01" name="precio" class="form-control" min="50" max="5000000" required aria-required="true" aria-describedby="precioAyuda">
                            <small id="precioAyuda" class="form-text text-muted">Valor entre 50 y 5.000.000.</small>
                        </div>

                        <div class="mb-3">
                            <label for="asientos">Asientos Disponibles <span aria-hidden="true">*</span></label>
                            <input type="number" id="asientos" name="asientos" class="form-control" min="0" max="500" required aria-required="true" aria-describedby="asientosAyuda">
                            <small id="asientosAyuda" class="form-text text-muted">Número entero entre 0 y 500.</small>
                        </div>

                        <div class="mb-3">
                            <label for="imagen">Imagen de referencia</label>
                            <input type="file" id="imagen" name="imagen" class="form-control" accept="image/png, image/jpeg, image/webp" aria-describedby="imagenAyuda">
                            <small id="imagenAyuda" class="form-text text-muted">Formatos permitidos: PNG, JPEG o WEBP. Campo opcional.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>