<?php

include("../includes/verificarSession.php");
include("../includes/conexion.php");
include("../includes/header.php");

$id = $_SESSION['id'];

$sql = "SELECT * FROM usuarios WHERE codUsuario = $id";
$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($link));
}

$usuario = mysqli_fetch_assoc($resultado);


?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2> Mi Perfil</h2>
                    <hr>

                    <form action="actualizar.php" method="post">

                        <div class="mb-3">

                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="<?= $usuario['nombreUsuario'] ?>" required>

                        </div>

                        <div class="mb-3">

                            <label>Email</label>
                            <input type="email" class="form-control" value="<?= $usuario['emailUsuario'] ?>" disabled>

                        </div>

                        <div class="mb-3">

                            <label>Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="<?= $usuario['telefonoUsuario'] ?>">

                        </div>

                        <div class="mb-3">

                            <label>Nueva Contraseña</label>
                            <input type="password" name="clave" class="form-control">

                        </div>

                        <div class="mb-3">

                            <label>Tipo Usuario</label>
                            <input type="text" class="form-control" value="<?= $usuario['tipoUsuario'] ?>" disabled>

                        </div>

                        <button class="btn btn-primary" onclick="guardarCambios(event, this)">Guardar Cambios</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

$alertas = [
    'actualizado' => [
        'icon'  => 'success',
        'title' => '¡Modificado!',
        'text'  => 'Los cambios fueron guardados correctamente.'
    ],
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
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
            window.location.href = 'index.php';
        }
    });
</script>
<?php }; ?>

<script>

    function guardarCambios(event, elemento)
    {
        event.preventDefault();
        const formulario = elemento.closest('form');

        Swal.fire({
            title:               '¿Estás seguro?',
            text:                '¿Desea guardar los cambios',
            icon:                'warning',
            showCancelButton:    true,
            confirmButtonColor:  '#198754',
            cancelButtonColor:   '#6c757d',
            confirmButtonText:   'Sí, guardar',
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
include("../includes/footer.php");
?>