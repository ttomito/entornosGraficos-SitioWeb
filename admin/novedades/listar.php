<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$sql = "SELECT * FROM novedades ORDER BY codNovedad DESC";

$resultado = mysqli_query($link,$sql);

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>Gestión de Novedades</h2>

        <a href="crear.php" class="btn btn-success">Nueva Novedad</a>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Novedad</th>
                        <th>Publicación</th>
                        <th>Expiración</th>
                        <th>Acciones</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>

                        <td><?= $fila['codNovedad'] ?></td>
                        <td><?= $fila['textoNovedad'] ?></td>
                        <td><?= $fila['fechaPublicacion'] ?></td>
                        <td><?= $fila['fechaExpiracion'] ?></td>
                        <td>

                            <a href="editar.php?id=<?= $fila['codNovedad'] ?>" class="btn btn-warning btn-sm">
                            Editar
                            </a>

                            <a href="eliminar.php?id=<?= $fila['codNovedad'] ?>" class="btn btn-danger btn-sm"
                            onclick="eliminarNovedad(event, this, '<?= $fila['textoNovedad'] ?>')">
                            Eliminar
                            </a>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php

$alertas = [
    'eliminada' => [
        'icon'  => 'success',
        'title' => '¡Eliminada!',
        'text'  => 'La novedad fue eliminada correctamente.'
    ],
    'actualizada' => [
        'icon'  => 'success',
        'title' => '¡Actualizada!',
        'text'  => 'La novedad fue actualizada correctamente.'
    ],
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
    ],
    'creada' => [
        'icon'  => 'success',
        'title' => '¡Creada!',
        'text'  => 'La novedad fue creada correctamente.'
    ],
    'campos_vacios' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'Los campos no pueden ser vacíos.'
    ],
];

if (isset($_GET['alerta']) && array_key_exists($_GET['alerta'], $alertas)){
    $alerta = $alertas[$_GET['alerta']];
?>

<script>
    Swal.fire({
        icon:              '<?= $alerta['icon'] ?>',
        title:             '<?= $alerta['title'] ?>',
        text:              '<?= $alerta['text'] ?>',
        confirmButtonText: 'Aceptar'
    }).then((result) => {
        if (result.isConfirmed)
        {
            window.location.href = 'listar.php';
        }
    });
</script>
<?php }; ?>

<script>
    function eliminarNovedad(event, elemento, nombre)
    {
        event.preventDefault();

        Swal.fire({
            title:               '¿Estás seguro?',
            text:                `¿Desea eliminar la novedad "${nombre}"?`,
            icon:                'warning',
            showCancelButton:    true,
            confirmButtonColor:  '#dc3545',
            cancelButtonColor:   '#6c757d',
            confirmButtonText:   'Sí, eliminar',
            cancelButtonText:    'Cancelar'
        }).then((result) => {
            if (result.isConfirmed)
            {
                window.location.href = elemento.href;
            }
        });
    }
</script>

<?php
include("../../includes/footer.php");
?>