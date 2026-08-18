<?php

include("../../includes/verificarSessionAdmin.php");
include("../../includes/header.php");

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2 id="titulo-form">Nueva Novedad</h2>
                    <p class="text-muted" style="font-size: 0.9rem;">Los campos marcados con <span aria-hidden="true">*</span> son obligatorios.</p>

                    <form action="guardar.php" method="post" enctype="multipart/form-data" aria-labelledby="titulo-form">

                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                        <div class="mb-3">

                            <label for="tituloNovedad">Título novedad <span aria-hidden="true">*</span></label>
                            <input
                                type="text"
                                id="tituloNovedad"
                                name="tituloNovedad"
                                class="form-control"
                                required
                                minlength="3"
                                maxlength="100"
                                aria-required="true"
                                aria-describedby="tituloAyuda">
                            <small id="tituloAyuda" class="form-text text-muted">Entre 3 y 100 caracteres.</small>

                        </div>

                        <div class="mb-3">

                            <label for="texto">Novedad <span aria-hidden="true">*</span></label>
                            <textarea
                                id="texto"
                                name="texto"
                                class="form-control"
                                rows="4"
                                required
                                maxlength="500"
                                aria-required="true"
                                aria-describedby="textoAyuda"></textarea>
                            <small id="textoAyuda" class="form-text text-muted">Hasta 500 caracteres.</small>

                        </div>

                        <div class="mb-3">

                            <label for="publicacion">Fecha Publicación <span aria-hidden="true">*</span></label>
                            <input type="date" id="publicacion" name="publicacion" class="form-control" required aria-required="true">

                        </div>

                        <div class="mb-3">

                            <label for="expiracion">Fecha Expiración <span aria-hidden="true">*</span></label>
                            <input type="date" id="expiracion" name="expiracion" class="form-control" required aria-required="true">

                        </div>

                        <div class="mb-3">

                            <label for="imagen">Imagen <span aria-hidden="true">*</span></label>
                            <input
                                type="file"
                                id="imagen"
                                name="imagen"
                                class="form-control"
                                accept="image/png, image/jpeg, image/webp"
                                required
                                aria-required="true"
                                aria-describedby="imagenAyuda">
                            <small id="imagenAyuda" class="form-text text-muted">Formatos permitidos: PNG, JPG, JPEG o WEBP.</small>

                            <div class="mt-2 d-none" id="contenedorVistaPrevia">
                                <p class="text-muted mb-1" style="font-size: 0.85rem;">Vista previa:</p>
                                <img id="vistaPreviaImagen" src="" alt="Vista previa de la imagen seleccionada" style="max-height: 150px; border-radius: 6px;">
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary" onclick="confirmarCreacion(event)">Guardar</button>

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

    function confirmarCreacion(event) {
        event.preventDefault();

        const formulario = event.target.closest('form');

        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Desea crear esta novedad?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, crear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                formulario.submit();
            }
        });
    }
</script>

<?php
$alertasCrearNovedad = [
    'campos_vacios' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'Los campos no pueden ser vacíos.'
    ],
    'titulo_corto' => [
        'icon'  => 'error',
        'title' => 'Título muy corto',
        'text'  => 'El título debe tener al menos 3 caracteres.'
    ],
    'titulo_largo' => [
        'icon'  => 'error',
        'title' => 'Título muy largo',
        'text'  => 'El título no puede superar los 100 caracteres.'
    ],
    'texto_largo' => [
        'icon'  => 'error',
        'title' => 'Texto muy largo',
        'text'  => 'El texto no puede superar los 500 caracteres.'
    ],
    'imagen_requerida' => [
        'icon'  => 'error',
        'title' => 'Falta la imagen',
        'text'  => 'Tenés que subir una imagen para crear la novedad.'
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
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
    ],
];

if (isset($_GET['alerta']) && array_key_exists($_GET['alerta'], $alertasCrearNovedad)) {
    $alertaCrearNovedad = $alertasCrearNovedad[$_GET['alerta']];
?>

    <script>
        Swal.fire({
            icon: '<?= $alertaCrearNovedad['icon'] ?>',
            title: '<?= $alertaCrearNovedad['title'] ?>',
            text: '<?= $alertaCrearNovedad['text'] ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php } ?>

<?php
include("../../includes/footer.php");
?>