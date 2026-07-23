<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<main id="contenido-principal">

<div class="container mt-5">

    <div class="row justify-content-center">

    <div class="col-md-8">

        <div class="card card-custom">

            <div class="card-body p-5">

                <h2>Nueva Promoción</h2>

                <p class="text-body-secondary">
                    Los campos marcados con
                    <span aria-hidden="true">*</span>
                    <span class="visually-hidden">(obligatorio)</span>
                    son obligatorios.
                </p>

                <form action="guardar.php" method="post">

                    <div class="mb-3">

                        <label for="descripcion">
                            Descripción
                            <span aria-hidden="true">*</span>
                        </label>

                        <textarea
                            id="descripcion"
                            name="descripcion"
                            class="form-control"
                            maxlength="200"
                            aria-describedby="ayudaDescripcion"
                            required
                            aria-required="true"></textarea>

                        <div id="ayudaDescripcion" class="form-text">
                            200 caracteres de máximo.
                        </div>

                    </div>

                    <div class="mb-3">

                        <label for="descuento">
                            Descuento %
                            <span aria-hidden="true">*</span>
                        </label>

                        <input type="number"
                               id="descuento"
                               name="descuento"
                               class="form-control"
                               min="1"
                               max="100"
                               aria-describedby="ayudaDescuento"
                               required
                               aria-required="true">

                        <div id="ayudaDescuento" class="form-text">
                            Ingresá un valor entero entre 1 y 100.
                        </div>

                    </div>

                    <div class="mb-3">

                        <label for="fechaLimite">
                            Fecha límite
                            <span aria-hidden="true">*</span>
                        </label>

                        <input type="date"
                               id="fechaLimite"
                               name="fechaLimite"
                               class="form-control"
                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                               aria-describedby="ayudaFecha"
                               required
                               aria-required="true">

                        <div id="ayudaFecha" class="form-text">
                            Debe ser una fecha posterior a hoy.
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Guardar</button>

                </form>

            </div>

        </div>

    </div>

</div>

</div>

</main>

<?php

$alertas = [
    'campos_vacios' => [
        'icon'  => 'warning',
        'title' => 'Faltan datos',
        'text'  => 'Completá todos los campos antes de guardar.'
    ],
    'descripcion_invalida' => [
        'icon'  => 'warning',
        'title' => 'Descripción inválida',
        'text'  => 'La descripción no puede superar los 255 caracteres ni contener símbolos como < > " \' & /.'
    ],
    'descuento_invalido' => [
        'icon'  => 'warning',
        'title' => 'Descuento inválido',
        'text'  => 'El descuento debe estar entre 1% y 100%.'
    ],
    'fecha_invalida' => [
        'icon'  => 'warning',
        'title' => 'Fecha inválida',
        'text'  => 'La fecha límite debe ser posterior a hoy.'
    ],
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
    ],
];

if (isset($_GET['alerta']) && array_key_exists($_GET['alerta'], $alertas)) {
    $alerta = $alertas[$_GET['alerta']];
?>

    <script>
        Swal.fire({
            icon: '<?= $alerta['icon'] ?>',
            title: '<?= $alerta['title'] ?>',
            text: '<?= $alerta['text'] ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>

<?php } ?>

<?php
include("../../includes/footer.php");
?>