<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$sql = "SELECT * FROM aerolineas ORDER BY codAerolinea";

$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($link));
}

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>Gestión de Aerolíneas</h2>
        <a href="crear.php" class="btn btn-success">Nueva Aerolínea</a>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <?php if (mysqli_num_rows($resultado) === 0){ ?>

                <p class="text-muted">No hay aerolíneas registradas.</p>

            <?php } else { ?>

                <table class="table table-hover">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>País</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                        <tr>
                            <td><?= $fila['codAerolinea'] ?></td>
                            <td><?= $fila['nombreAerolinea'] ?></td>
                            <td><?= $fila['descripcionAerolinea'] ?></td>
                            <td><?= $fila['codPais'] ?></td>
                            <td>
                                
                                <a href="editar.php?id=<?= $fila['codAerolinea'] ?>"
                                class="btn btn-warning btn-sm">
                                    Editar
                                </a>

                                <?php if($fila['activo'] == 1) {?>
                                <a href="eliminar.php?id=<?= $fila['codAerolinea'] ?>&activo=<?= $fila['activo'] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="confirmarEliminacion(event, this, '<?= $fila['nombreAerolinea'] ?>')">
                                    Desactivar
                                </a>
                                <?php } else { ?>
                                <a href="eliminar.php?id=<?= $fila['codAerolinea'] ?>&activo=<?= $fila['activo'] ?>"
                                class="btn btn-success btn-sm"
                                onclick="confirmarActivacion(event, this, '<?= $fila['nombreAerolinea'] ?>')">
                                    Activar
                                </a>
                                <?php } ?>
                            </td>
                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            <?php } ?>

        </div>

    </div>

</div>

<?php

$alertas = [
    'eliminada' => [
        'icon'  => 'success',
        'title' => '¡Eliminada!',
        'text'  => 'La aerolínea fue eliminada correctamente.'
    ],
    'activada' => [
        'icon'  => 'success',
        'title' => '¡Activada!',
        'text'  => 'La aerolínea fue activada correctamente.'
    ],
    'vuelos_desactivados' => [
        'icon'  => 'warning',
        'title' => 'Aerolínea desactivada',
        'text'  => 'Se desactivaron los vuelos y reservas asociadas a la aerolínea.'
    ],
    'vuelos_activados' => [
        'icon'  => 'success',
        'title' => 'Aerolínea activada',
        'text'  => 'Se activaron los vuelos y reservas asociadas a la aerolínea.'
    ],
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
    ],
    'no_encontrada' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Aerolínea no encontrada.'
    ],
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Error al buscar aerolínea.'
    ],
    'campos_vacios' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'No se pueden ingresar campos vacíos.'
    ],
    'actualizada' => [
        'icon'  => 'success',
        'title' => '¡Actualizada!',
        'text'  => 'La aerolínea fue actualizada.'
    ],
    'creada' => [
        'icon'  => 'success',
        'title' => '¡Creada!',
        'text'  => 'Se ha creado la aerolínea.'
    ]
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
    function confirmarEliminacion(event, elemento, nombre)
    {
        event.preventDefault();

        Swal.fire({
            title:               '¿Estás seguro?',
            text:                `¿Desea ocultar la aerolínea "${nombre}"?`,
            icon:                'warning',
            showCancelButton:    true,
            confirmButtonColor:  '#d33',
            cancelButtonColor:   '#6c757d',
            confirmButtonText:   'Sí, ocultar',
            cancelButtonText:    'Cancelar'
        }).then((result) => {
            if (result.isConfirmed)
            {
                window.location.href = elemento.href;
            }
        });
    }

    function confirmarActivacion(event, elemento, nombre)
    {
        event.preventDefault();

        Swal.fire({
            title:               '¿Estás seguro?',
            text:                `¿Desea activar la aerolínea "${nombre}"?`,
            icon:                'warning',
            showCancelButton:    true,
            confirmButtonColor:  '#198754',
            cancelButtonColor:   '#6c757d',
            confirmButtonText:   'Sí, activar',
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