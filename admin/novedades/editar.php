<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$id = $_GET['id'];

$sql = "SELECT * FROM novedades WHERE codNovedad = $id";

$resultado = mysqli_query($link, $sql);
if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($link));
}

$novedad = mysqli_fetch_assoc($resultado);

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2>Editar Novedad</h2>

                    <form action="actualizar.php" method="post" enctype="multipart/form-data">

                        <input type="hidden" name="id" value="<?= $novedad['codNovedad'] ?>">
                        <input type="hidden" name="imagenActual" value="<?= $novedad['imagen'] ?>">

                        <div class="mb-3">

                            <label>Título novedad</label>
                            <input type="text" name="tituloNovedad" class="form-control" value="<?= $novedad['tituloNovedad'] ?>" required>

                        </div>

                        <div class="mb-3">

                            <label>Novedad</label>
                            <textarea name="texto" class="form-control" rows="4"><?= $novedad['textoNovedad'] ?></textarea>

                        </div>

                        <div class="mb-3">

                            <label>Fecha Publicación</label>
                            <input type="date" name="publicacion" value="<?= $novedad['fechaPublicacion'] ?>" class="form-control">

                        </div>

                        <div class="mb-3">

                            <label>Fecha Expiración</label>
                            <input type="date" name="expiracion" value="<?= $novedad['fechaExpiracion'] ?>" class="form-control">

                        </div>

                        <div class="mb-3">

                            <label>Imagen</label>

                            <?php if (!empty($novedad['imagen'])): ?>
                                <div class="mb-2">
                                    <img src="../../uploads/novedades/<?= $novedad['imagen'] ?>" alt="Imagen actual" style="max-height: 150px; border-radius: 6px;">
                                    <p class="text-muted mt-1" style="font-size: 0.85rem;">Imagen actual. Seleccioná una nueva para reemplazarla.</p>
                                </div>
                            <?php endif; ?>

                            <input type="file" name="imagen" class="form-control" accept="image/*">

                        </div>

                        <button class="btn btn-primary">Actualizar</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>