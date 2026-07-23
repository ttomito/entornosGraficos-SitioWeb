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

                    <h2>Mi Perfil</h2>

                    <p class="text-body-secondary">
                        Los campos marcados con
                        <span aria-hidden="true">*</span>
                        <span class="visually-hidden">(obligatorio)</span>
                        son obligatorios.
                    </p>

                    <hr>

                    <form action="actualizar.php" method="post" novalidate>

                        <div class="mb-3">

                            <label for="nombre">
                                Nombre
                                <span aria-hidden="true">*</span>
                            </label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                class="form-control"
                                value="<?= htmlspecialchars($usuario['nombreUsuario'], ENT_QUOTES, 'UTF-8') ?>"
                                maxlength="60"
                                autocomplete="given-name"
                                required
                                aria-required="true">

                        </div>

                        <div class="mb-3">

                            <label for="apellido">
                                Apellido
                                <span aria-hidden="true">*</span>
                            </label>

                            <input
                                type="text"
                                id="apellido"
                                name="apellido"
                                class="form-control"
                                value="<?= htmlspecialchars($usuario['apellidoUsuario'], ENT_QUOTES, 'UTF-8') ?>"
                                maxlength="60"
                                autocomplete="family-name"
                                required
                                aria-required="true">

                        </div>

                        <div class="mb-3">

                            <label for="email">Email</label>

                            <input
                                type="email"
                                id="email"
                                class="form-control"
                                value="<?= htmlspecialchars($usuario['emailUsuario'], ENT_QUOTES, 'UTF-8') ?>"
                                autocomplete="email"
                                disabled
                                aria-describedby="ayudaEmail">

                            <div id="ayudaEmail" class="form-text">
                                El email no se puede modificar desde aquí.
                            </div>

                        </div>

                        <div class="mb-3">

                            <label for="dni">
                                DNI
                                <span aria-hidden="true">*</span>
                            </label>

                            <input
                                type="text"
                                id="dni"
                                name="dni"
                                class="form-control"
                                value="<?= htmlspecialchars($usuario['dniUsuario'], ENT_QUOTES, 'UTF-8') ?>"
                                maxlength="8"
                                pattern="\d{7,8}"
                                inputmode="numeric"
                                autocomplete="off"
                                required
                                aria-required="true"
                                aria-describedby="ayudaDni">

                            <div id="ayudaDni" class="form-text">
                                Solo números, sin puntos (7 u 8 dígitos).
                            </div>

                        </div>

                        <div class="mb-3">

                            <label for="telefono">
                                Teléfono
                                <span aria-hidden="true">*</span>
                            </label>

                            <input
                                type="tel"
                                id="telefono"
                                name="telefono"
                                class="form-control"
                                value="<?= htmlspecialchars($usuario['telefonoUsuario'], ENT_QUOTES, 'UTF-8') ?>"
                                maxlength="20"
                                autocomplete="tel"
                                required
                                aria-required="true">

                        </div>

                        <div class="mb-3">

                            <label for="claveNueva">
                                Nueva Contraseña
                            </label>

                            <div class="input-group">

                                <input
                                    type="password"
                                    id="claveNueva"
                                    name="clave"
                                    class="form-control"
                                    minlength="8"
                                    autocomplete="new-password"
                                    placeholder="Dejar vacío para mantener la actual"
                                    aria-describedby="ayudaClave">

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="togglePassword('claveNueva', this)"
                                    aria-label="Mostrar contraseña"
                                    aria-pressed="false">

                                    <i class="bi bi-eye" aria-hidden="true"></i>

                                </button>

                            </div>

                            <div id="ayudaClave" class="form-text">
                                Mínimo 8 caracteres. Solo puede contener letras, números y caracteres especiales.
                            </div>

                        </div>

                        <div class="mb-3">

                            <label for="claveConfirmacion">
                                Confirmar Nueva Contraseña
                            </label>

                            <div class="input-group">

                                <input
                                    type="password"
                                    id="claveConfirmacion"
                                    name="clave_confirmacion"
                                    class="form-control"
                                    minlength="8"
                                    autocomplete="new-password"
                                    placeholder="Repetir la nueva contraseña">

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="togglePassword('claveConfirmacion', this)"
                                    aria-label="Mostrar contraseña"
                                    aria-pressed="false">

                                    <i class="bi bi-eye" aria-hidden="true"></i>

                                </button>

                            </div>

                        </div>

                        <div class="mb-3">

                            <label for="tipoUsuario">Tipo Usuario</label>

                            <input
                                type="text"
                                id="tipoUsuario"
                                class="form-control"
                                value="<?= htmlspecialchars($usuario['tipoUsuario'], ENT_QUOTES, 'UTF-8') ?>"
                                disabled>

                        </div>

                        <button type="submit" class="btn btn-primary" onclick="guardarCambios(event, this)">
                            Guardar Cambios
                        </button>

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
    ],
    'clave_invalida' => [
    'icon'  => 'warning',
    'title' => 'Contraseña inválida',
    'text'  => 'La contraseña solo puede contener letras, números y caracteres especiales.'
],
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
    function togglePassword(inputId, boton) {
        const input = document.getElementById(inputId);
        const icono = boton.querySelector('i');
        const mostrando = input.type === 'text';

        input.type = mostrando ? 'password' : 'text';

        icono.classList.toggle('bi-eye', mostrando);
        icono.classList.toggle('bi-eye-slash', !mostrando);

        boton.setAttribute('aria-pressed', String(!mostrando));
        boton.setAttribute(
            'aria-label',
            mostrando ? 'Mostrar contraseña' : 'Ocultar contraseña'
        );
    }

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