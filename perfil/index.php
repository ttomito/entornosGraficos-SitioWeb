<?php

include("../includes/verificarSession.php");
include("../includes/conexion.php");
include("../includes/header.php");

$id = (int) ($_SESSION['id'] ?? 0);

if ($id <= 0) {
    header("Location: /entornosGraficos-SitioWeb/auth/login.php");
    exit();
}

$sql = "SELECT * FROM usuarios WHERE codUsuario = $id";
$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($link));
}

$usuario = mysqli_fetch_assoc($resultado);

if (!$usuario) {
    header("Location: /entornosGraficos-SitioWeb/auth/login.php");
    exit();
}

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
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombreUsuario'], ENT_QUOTES, 'UTF-8') ?>" maxlength="60" required>

                        </div>
                        <div class="mb-3">

                            <label>

                                Apellido

                            </label>

                            <input
                                type="text"
                                name="apellido"
                                class="form-control"
                                value="<?= htmlspecialchars($usuario['apellidoUsuario'], ENT_QUOTES, 'UTF-8') ?>"
                                maxlength="60"
                                required>

                        </div>

                        <div class="mb-3">

                            <label>Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($usuario['emailUsuario'], ENT_QUOTES, 'UTF-8') ?>" disabled>

                        </div>
                        <div class="mb-3">

                            <label>

                                DNI

                            </label>

                            <input
                                type="text"
                                name="dni"
                                class="form-control"
                                value="<?= htmlspecialchars($usuario['dniUsuario'], ENT_QUOTES, 'UTF-8') ?>"
                                maxlength="8"
                                pattern="\d{7,8}"
                                required>

                        </div>

                        <div class="mb-3">

                            <label>

                                Teléfono

                            </label>

                            <input
                                type="text"
                                name="telefono"
                                class="form-control"
                                value="<?= htmlspecialchars($usuario['telefonoUsuario'], ENT_QUOTES, 'UTF-8') ?>"
                                maxlength="20"
                                required>

                        </div>

                        <div class="mb-3">

                            <label>

                                Nueva Contraseña

                            </label>

                            <input
                                type="password"
                                name="clave"
                                class="form-control"
                                minlength="8"
                                placeholder="Dejar vacío para mantener la actual">

                        </div>

                        <div class="mb-3">
                            <label>Confirmar Nueva Contraseña</label>
                            <input
                                type="password"
                                name="clave_confirmacion"
                                class="form-control"
                                minlength="8"
                                placeholder="Repetir la nueva contraseña">
                        </div>

                        <div class="mb-3">

                            <label>Tipo Usuario</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['tipoUsuario'], ENT_QUOTES, 'UTF-8') ?>" disabled>

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
    ],
    'dni' => [
        'icon'  => 'warning',
        'title' => 'DNI duplicado',
        'text'  => 'Ya existe un usuario registrado con ese DNI.'
    ],
    'dni_invalido' => [
        'icon'  => 'warning',
        'title' => 'DNI inválido',
        'text'  => 'El DNI debe contener solo números (7 u 8 dígitos).'
    ],
    'telefono_invalido' => [
        'icon'  => 'warning',
        'title' => 'Teléfono inválido',
        'text'  => 'Revisá el formato del teléfono ingresado.'
    ],
    'clave_corta' => [
        'icon'  => 'warning',
        'title' => 'Contraseña muy corta',
        'text'  => 'La nueva contraseña debe tener al menos 8 caracteres.'
    ],
    'datos_invalidos' => [
        'icon'  => 'warning',
        'title' => 'Datos inválidos',
        'text'  => 'Revisá que todos los campos estén completos correctamente.'
    ],
    'clave_no_coincide' => [
    'icon'  => 'warning',
    'title' => 'Las contraseñas no coinciden',
    'text'  => 'La nueva contraseña y su confirmación deben ser iguales.'
    ]
];

if (isset($_GET['alerta']) && array_key_exists($_GET['alerta'], $alertas)) {
    $alerta = $alertas[$_GET['alerta']];
?>

    <script>
        Swal.fire({
            icon: '<?= $alerta['icon'] ?>',
            title: '<?= $alerta['title'] ?>',
            text: '<?= $alerta['text'] ?>',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php';
            }
        });
    </script>
<?php }; ?>

<script>
    function guardarCambios(event, elemento) {
        event.preventDefault();
        const formulario = elemento.closest('form');

        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Desea guardar los cambios?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                formulario.submit();
            }
        });
    }
</script>

<?php
include("../includes/footer.php");
?>