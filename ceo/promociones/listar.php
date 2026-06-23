<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$idCEO = $_SESSION['id'];

if($idCEO <= 0){
    die("Acceso denegado");
}

$sqlCEO = "SELECT codAerolinea FROM usuarios WHERE codUsuario = $idCEO";
$resultadoCEO = mysqli_query($link, $sqlCEO);

if (!$resultadoCEO) {
    die(mysqli_error($link));
}

$ceo = mysqli_fetch_assoc($resultadoCEO);
$codAerolinea = $ceo['codAerolinea'];

$sql = "SELECT * FROM promociones WHERE codAerolinea = $codAerolinea ORDER BY codPromocion DESC";

$resultado = mysqli_query($link, $sql);
if (!$resultado) {
    die(mysqli_error($link));
}

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>Gestión de Promociones</h2>
        <a href="crear.php" class="btn btn-success">Nueva Promoción</a>

    </div>

    <?php if(mysqli_num_rows($resultado) > 0) { ?>

    <div class="card card-custom">

        <div class="card-body">

            <table class="table table-hover">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Descuento</th>
                        <th>Fecha limite</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)) { ?>

                    <tr>
                        <td><?= $fila['codPromocion'] ?></td>
                        <td><?= $fila['descripcionPromocion'] ?></td>
                        <td><?= $fila['descuentoPromocion'] ?>%</td>
                        <td><?= $fila['fechaLimitePromocion'] ?></td>
                        <td><?= $fila['estadoPromocion'] ?></td>

                        <td>
                            <a href="editar.php?id=<?= $fila['codPromocion'] ?>" class="btn btn-warning btn-sm">
                                Editar
                            </a>

                            <a href="eliminar.php?id=<?= $fila['codPromocion'] ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('¿Eliminar promoción?')">
                                Eliminar
                            </a>
                        </td>
                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

    <?php } else { ?>

        <div class="alert alert-info">
            No hay promociones registradas.
        </div>

    <?php } ?>

</div>

<?php

$alertas = [
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
    ],
    'creada' => [
        'icon'  => 'success',
        'title' => '¡Creado!',
        'text'  => 'Se ha creado la promoción.'
    ],
    'eliminado' => [
        'icon'  => 'success',
        'title' => '¡Oculto!',
        'text'  => 'Se ha ocultado el vuelo y sus reservas.'
    ],
    'modificada' => [
        'icon'  => 'success',
        'title' => '¡Modificado!',
        'text'  => 'Se ha actualizado la promoción.'
    ],
    'fecha_invalida' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'La fecha límite debe ser posterior a hoy.'
    ],
    'descuento_invalido' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'El descuento debe estar entre 1% y 100%.'
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
    function ocultarVuelo(event, elemento)
    {
        event.preventDefault();

        Swal.fire({
            title:               '¿Estás seguro?',
            text:                '¿Desea ocultar este vuelo? Al hacerlo también se desactivarán las reservas asociadas.',
            icon:                'warning',
            showCancelButton:    true,
            confirmButtonColor:  '#dc3545',
            cancelButtonColor:   '#6c757d',
            confirmButtonText:   'Sí, ocultar',
            cancelButtonText:    'Cancelar'
        }).then((result) => {
            if (result.isConfirmed){
                window.location.href = elemento.href;
            }
        });
    }

    function activarVuelo(event, elemento)
    {
        event.preventDefault();

        Swal.fire({
            title:               '¿Estás seguro?',
            text:                '¿Desea activar el vuelo? Al hacerlo también se activarán las reservas asociadas',
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