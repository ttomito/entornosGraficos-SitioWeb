<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}

$sql = "SELECT * FROM novedades WHERE codNovedad = ?";
$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    error_log("Error al preparar la consulta: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$novedad = $resultado ? mysqli_fetch_assoc($resultado) : null;
mysqli_stmt_close($stmt);

if (!$novedad) {
    header("Location: listar.php?alerta=no_encontrada");
    exit();
}

$tituloEscapado = htmlspecialchars($novedad['tituloNovedad'], ENT_QUOTES, 'UTF-8');
$textoEscapado = htmlspecialchars($novedad['textoNovedad'] ?? '', ENT_QUOTES, 'UTF-8');
$publicacionEscapada = htmlspecialchars($novedad['fechaPublicacion'] ?? '', ENT_QUOTES, 'UTF-8');
$expiracionEscapada = htmlspecialchars($novedad['fechaExpiracion'] ?? '', ENT_QUOTES, 'UTF-8');
$imagenEscapada = htmlspecialchars($novedad['imagen'] ?? '', ENT_QUOTES, 'UTF-8');

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2 id="titulo-form">Editar Novedad</h2>
                    <p class="text-muted" style="font-size: 0.9rem;">Los campos marcados con <span aria-hidden="true">*</span> son obligatorios.</p>

                    <form action="actualizar.php" method="post" enctype="multipart/form-data" aria-labelledby="titulo-form">

                        <input type="hidden" name="id" value="<?= (int)$novedad['codNovedad'] ?>">
                        <input type="hidden" name="imagenActual" value="<?= $imagenEscapada ?>">

                        <div class="mb-3">

                            <label for="tituloNovedad">Título novedad <span aria-hidden="true">*</span></label>
                            <input
                                type="text"
                                id="tituloNovedad"
                                name="tituloNovedad"
                                class="form-control"
                                value="<?= $tituloEscapado ?>"
                                required
                                minlength="3"
                                maxlength="150"
                                aria-required="true"
                                aria-describedby="tituloAyuda">
                            <small id="tituloAyuda" class="form-text text-muted">Entre 3 y 150 caracteres.</small>

                        </div>

                        <div class="mb-3">

                            <label for="texto">Novedad</label>
                            <textarea
                                id="texto"
                                name="texto"
                                class="form-control"
                                rows="4"
                                maxlength="2000"
                                aria-describedby="textoAyuda"><?= $textoEscapado ?></textarea>
                            <small id="textoAyuda" class="form-text text-muted">Hasta 2000 caracteres.</small>

                        </div>

                        <div class="mb-3">

                            <label for="publicacion">Fecha Publicación</label>
                            <input type="date" id="publicacion" name="publicacion" value="<?= $publicacionEscapada ?>" class="form-control">

                        </div>

                        <div class="mb-3">

                            <label for="expiracion">Fecha Expiración</label>
                            <input type="date" id="expiracion" name="expiracion" value="<?= $expiracionEscapada ?>" class="form-control">

                        </div>

                        <div class="mb-3">

                            <label for="imagen">Imagen</label>

                            <?php if (!empty($imagenEscapada)): ?>
                                <div class="mb-2" id="contenedorImagenActual">
                                    <p class="text-muted mb-1" style="font-size: 0.85rem;">Imagen actual:</p>
                                    <img src="../../uploads/novedades/<?= $imagenEscapada ?>" alt="Imagen actual de la novedad" style="max-height: 150px; border-radius: 6px;">
                                </div>
                            <?php endif; ?>

                            <input type="file" id="imagen" name="imagen" class="form-control" accept="image/png, image/jpeg, image/webp" aria-describedby="imagenAyuda">
                            <small id="imagenAyuda" class="form-text text-muted">Formatos permitidos: PNG, JPG, JPEG o WEBP. Seleccioná una nueva para reemplazar la actual.</small>

                            <div class="mt-2 d-none" id="contenedorVistaPrevia">
                                <p class="text-muted mb-1" style="font-size: 0.85rem;">Nueva imagen seleccionada (se guardará al actualizar):</p>
                                <img id="vistaPreviaImagen" src="" alt="Vista previa de la nueva imagen seleccionada" style="max-height: 150px; border-radius: 6px;">
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary" onclick="confirmarActualizacion(event)">Actualizar</button>
                        <a href="listar.php" class="btn btn-secondary">Cancelar</a>

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

    function confirmarActualizacion(event) {
        event.preventDefault();

        const formulario = event.target.closest('form');

        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Desea modificar esta novedad?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, modificar',
            cancelButtonText: 'Cancelar'
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