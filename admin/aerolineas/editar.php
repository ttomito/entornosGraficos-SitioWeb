<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$id = $_GET['id'];

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}

$sql = "SELECT * FROM aerolineas WHERE codAerolinea = $id";

$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    error_log("Error al obtener aerolínea: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

$aerolinea = mysqli_fetch_assoc($resultado);

if (!$aerolinea) {
    header("Location: listar.php?alerta=no_encontrada");
    exit();
}

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-4">

    <div class="card card-custom">

        <div class="card-body">

            <h2 class="mb-4">Editar Aerolínea</h2>

            <form action="actualizar.php" method="post">

                <input type="hidden" name="id" value="<?= $aerolinea['codAerolinea'] ?>">

                <div class="mb-3">

                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= $aerolinea['nombreAerolinea'] ?>" required>

                </div>

                <div class="mb-3">

                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control"><?= $aerolinea['descripcionAerolinea'] ?></textarea>

                </div>

                <div class="mb-3">

                    <label>País</label>
                    <input type="text" name="pais" class="form-control" value="<?= $aerolinea['codPais'] ?>">

                </div>

                <button class="btn btn-primary" onclick="confirmarActivacion(event, '<?= $aerolinea['nombreAerolinea'] ?>')">Actualizar</button>

                <a href="listar.php" class="btn btn-secondary">Cancelar</a>

            </form>

        </div>

    </div>

</div>

<script>
    function confirmarActivacion(event, nombre)
{
    event.preventDefault();

    const formulario = event.target.closest('form');

    Swal.fire({
        title:               '¿Estás seguro?',
        text:                `¿Desea modificar la aerolínea "${nombre}"?`,
        icon:                'warning',
        showCancelButton:    true,
        confirmButtonColor:  '#198754',
        cancelButtonColor:   '#6c757d',
        confirmButtonText:   'Sí, modificar',
        cancelButtonText:    'Cancelar'
    }).then((result) => {
        if (result.isConfirmed)
        {
            formulario.submit();
        }
    });
}
</script>

<?php
include("../../includes/footer.php");
?>