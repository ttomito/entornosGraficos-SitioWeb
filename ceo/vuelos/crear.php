<?php

include("../../includes/verificarSessionCEO.php");
include("../../includes/header.php");

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2>Nuevo Vuelo</h2>
                    <p class="text-muted" style="font-size: 0.9rem;">Los campos marcados con <span aria-hidden="true">*</span> son obligatorios.</p>

                    <form action="guardar.php" method="post" enctype="multipart/form-data">

                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

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
                            <label for="imagen">Imagen de referencia <span aria-hidden="true">*</span></label>
                            <input type="file" id="imagen" name="imagen" class="form-control" accept="image/png, image/jpeg, image/webp" required aria-required="true" aria-describedby="imagenAyuda">
                            <small id="imagenAyuda" class="form-text text-muted">Formatos permitidos: PNG, JPEG o WEBP.</small>

                            <div class="mt-2 d-none" id="contenedorVistaPrevia">
                                <p class="text-muted mb-1" style="font-size: 0.85rem;">Vista previa:</p>
                                <img id="vistaPreviaImagen" src="" alt="Vista previa de la imagen seleccionada" style="max-height: 150px; border-radius: 6px;">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    const inputImagen = document.getElementById('imagen');
    const contenedorVistaPrevia = document.getElementById('contenedorVistaPrevia');
    const vistaPreviaImagen = document.getElementById('vistaPreviaImagen');

    inputImagen.addEventListener('change', function() {
        const archivo = this.files && this.files[0];

        if (!archivo) {
            contenedorVistaPrevia.classList.add('d-none');
            vistaPreviaImagen.src = '';
            return;
        }

        const lector = new FileReader();

        lector.onload = function(evento) {
            vistaPreviaImagen.src = evento.target.result;
            contenedorVistaPrevia.classList.remove('d-none');
        };

        lector.readAsDataURL(archivo);
    });
</script>

<?php

$alertasCrearVuelo = [
    'campos_vacios' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'No pueden haber campos vacíos.'
    ],
    'imagen_requerida' => [
        'icon'  => 'error',
        'title' => 'Falta la imagen',
        'text'  => 'Tenés que subir una imagen para crear el vuelo.'
    ],
    'origen_invalido' => [
        'icon'  => 'error',
        'title' => 'Origen inválido',
        'text'  => 'El origen debe tener entre 3 y 50 caracteres, solo letras y espacios.'
    ],
    'destino_invalido' => [
        'icon'  => 'error',
        'title' => 'Destino inválido',
        'text'  => 'El destino debe tener entre 3 y 50 caracteres, solo letras y espacios.'
    ],
    'fecha_invalida' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'La fecha del vuelo debe ser mayor a hoy.'
    ],
    'hora_invalida' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'La hora no tiene un formato válido.'
    ],
    'precio_invalido' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'El precio debe estar entre 50 y 5.000.000.'
    ],
    'asientos_invalidos' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'Los asientos deben ser un número de 0 a 500.'
    ],
    'imagen_invalida' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'Formato de la imagen inválido.'
    ],
    'imagen_muy_grande' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'La imagen puede pesar hasta 3MB.'
    ],
    'error_imagen' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'Ocurrió un error con la imagen.'
    ],
    'sin_aerolinea' => [
        'icon'  => 'error',
        'title' => 'Cuenta sin vincular',
        'text'  => 'Tu cuenta todavía no está vinculada a una aerolínea. Contactá a un administrador.'
    ],
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
    ],
];

if (isset($_GET['alerta']) && array_key_exists($_GET['alerta'], $alertasCrearVuelo)) {
    $alertaCrearVuelo = $alertasCrearVuelo[$_GET['alerta']];
?>

    <script>
        Swal.fire({
            icon: '<?= $alertaCrearVuelo['icon'] ?>',
            title: '<?= $alertaCrearVuelo['title'] ?>',
            text: '<?= $alertaCrearVuelo['text'] ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php } ?>

<?php
include("../../includes/footer.php");
?>