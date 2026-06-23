<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2>Nueva Novedad</h2>

                    <form action="guardar.php" method="post" enctype="multipart/form-data">

                        <div class="mb-3">

                            <label>Título novedad</label>
                            <input type="text" name="tituloNovedad" class="form-control" rows="4" required>

                        </div>

                        <div class="mb-3">

                            <label>Novedad</label>
                            <textarea name="texto" class="form-control" rows="4" required></textarea>

                        </div>

                        <div class="mb-3">

                            <label>Fecha Publicación</label>
                            <input type="date" name="publicacion" class="form-control" required>

                        </div>

                        <div class="mb-3">

                            <label>Fecha Expiración</label>
                            <input type="date" name="expiracion" class="form-control" required>

                        </div>

                        <div class="mb-3">

                            <label>Imagen</label>
                            <input type="file" name="imagen" class="form-control" accept="image/*">

                        </div>

                        <button class="btn btn-primary">Guardar</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>