<?php

include("../includes/header.php");
include("../includes/conexion.php");

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');

$tokenValido = false;
$usuario = null;
$errores = [];
$actualizada = false;

if ($token !== '') {

    $tokenEsc = mysqli_real_escape_string($link, $token);

    $sqlToken = "SELECT * FROM usuarios WHERE tokenRecuperacion = '$tokenEsc' AND tokenRecuperacionExpira > NOW()";

    $resultadoToken = mysqli_query($link, $sqlToken);

    if ($resultadoToken && mysqli_num_rows($resultadoToken) > 0) {
        $tokenValido = true;
        $usuario = mysqli_fetch_assoc($resultadoToken);
    }
}

if ($tokenValido && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $clave = $_POST['clave'] ?? '';
    $claveConfirmar = $_POST['claveConfirmar'] ?? '';

    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $clave)) {
        $errores[] = 'clave';
    }

    if ($clave !== $claveConfirmar) {
        $errores[] = 'claveConfirmar';
    }

    if ($clave !== '' && password_verify($clave, $usuario['claveUsuario'])) {
        $errores[] = 'claveIgual';
    }

    if (empty($errores)) {

        $claveHash = password_hash($clave, PASSWORD_DEFAULT);
        $codUsuario = (int) $usuario['codUsuario'];

        $sqlUpdate = "UPDATE usuarios SET claveUsuario = '$claveHash', tokenRecuperacion = NULL, tokenRecuperacionExpira = NULL WHERE codUsuario = $codUsuario";

        mysqli_query($link, $sqlUpdate);

        header("Location: login.php?actualizada=1");
        exit();
    }
}

?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card card-custom">

                <div class="card-body">

                    <h2 id="tituloRestablecer">

                        Restablecer Contraseña

                    </h2>

                    <hr>

                    <?php if (!$tokenValido) { ?>

                        <div class="alert alert-danger" role="alert">

                            El enlace no es válido o ya venció. Solicitá uno nuevo.

                        </div>

                        <a href="recuperar.php" class="btn btn-primary">

                            Solicitar nuevo enlace

                        </a>

                    <?php } else { ?>

                        <?php if (!empty($errores)) { ?>

                            <div class="alert alert-danger" role="alert">

                                <?php if (in_array('clave', $errores)) { ?>
                                    <p class="mb-1">La contraseña debe tener al menos 8 caracteres, con letras y números.</p>
                                <?php } ?>

                                <?php if (in_array('claveConfirmar', $errores)) { ?>
                                    <p class="mb-1">Las contraseñas no coinciden.</p>
                                <?php } ?>

                                <?php if (in_array('claveIgual', $errores)) { ?>
                                    <p class="mb-0">La nueva contraseña no puede ser igual a la actual. Elegí una diferente.</p>
                                <?php } ?>

                            </div>

                        <?php } ?>

                        <p class="text-muted" style="font-size: 0.9rem;">
                            Los campos marcados con <span aria-hidden="true">*</span> son obligatorios.
                        </p>

                        <form
                            id="formRestablecer"
                            action="restablecer.php"
                            method="post"
                            aria-labelledby="tituloRestablecer"
                            novalidate>

                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                            <div class="mb-3">

                                <label for="clave" class="form-label">

                                    Nueva Contraseña <span aria-hidden="true">*</span>

                                </label>

                                <div class="input-group">

                                    <input
                                        type="password"
                                        id="clave"
                                        name="clave"
                                        class="form-control"
                                        minlength="8"
                                        pattern="(?=.*[A-Za-z])(?=.*\d).{8,}"
                                        required
                                        aria-required="true"
                                        autocomplete="new-password"
                                        aria-describedby="claveAyuda">

                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary toggle-clave"
                                        data-target="clave"
                                        aria-pressed="false"
                                        aria-label="Mostrar contraseña">

                                        <svg class="icon-mostrar" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>

                                        <svg class="icon-ocultar" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false" style="display:none;">
                                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.6 21.6 0 0 1 5.06-6.06M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 7 11 7a21.7 21.7 0 0 1-3.22 4.36M14.12 14.12a3 3 0 1 1-4.24-4.24"></path>
                                            <line x1="1" y1="1" x2="23" y2="23"></line>
                                        </svg>

                                    </button>

                                </div>

                                <small id="claveAyuda" class="form-text text-muted">Mínimo 8 caracteres, con al menos una letra y un número.</small>

                            </div>

                            <div class="mb-4">

                                <label for="claveConfirmar" class="form-label">

                                    Confirmar Contraseña <span aria-hidden="true">*</span>

                                </label>

                                <div class="input-group">

                                    <input
                                        type="password"
                                        id="claveConfirmar"
                                        name="claveConfirmar"
                                        class="form-control"
                                        minlength="8"
                                        pattern="(?=.*[A-Za-z])(?=.*\d).{8,}"
                                        required
                                        aria-required="true"
                                        autocomplete="new-password"
                                        aria-describedby="claveConfirmarAyuda claveConfirmarError">

                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary toggle-clave"
                                        data-target="claveConfirmar"
                                        aria-pressed="false"
                                        aria-label="Mostrar contraseña">

                                        <svg class="icon-mostrar" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>

                                        <svg class="icon-ocultar" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false" style="display:none;">
                                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.6 21.6 0 0 1 5.06-6.06M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 7 11 7a21.7 21.7 0 0 1-3.22 4.36M14.12 14.12a3 3 0 1 1-4.24-4.24"></path>
                                            <line x1="1" y1="1" x2="23" y2="23"></line>
                                        </svg>

                                    </button>

                                </div>

                                <small id="claveConfirmarAyuda" class="form-text text-muted">Debe coincidir con la contraseña ingresada.</small>
                                <div id="claveConfirmarError" class="text-danger mt-1" role="alert" style="display:none; font-size: 0.9rem;">
                                    Las contraseñas no coinciden.
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary w-100">

                                Restablecer contraseña

                            </button>

                        </form>

                    <?php } ?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php if ($tokenValido) { ?>
<script>
    (function () {
        var formulario = document.getElementById('formRestablecer');
        var clave = document.getElementById('clave');
        var claveConfirmar = document.getElementById('claveConfirmar');
        var errorConfirmar = document.getElementById('claveConfirmarError');

        // mostrar / ocultar contraseña
        document.querySelectorAll('.toggle-clave').forEach(function (boton) {
            boton.addEventListener('click', function () {
                var idCampo = boton.getAttribute('data-target');
                var campo = document.getElementById(idCampo);
                var iconoMostrar = boton.querySelector('.icon-mostrar');
                var iconoOcultar = boton.querySelector('.icon-ocultar');
                var seVeAhora = campo.type === 'password';

                campo.type = seVeAhora ? 'text' : 'password';
                boton.setAttribute('aria-pressed', seVeAhora ? 'true' : 'false');
                boton.setAttribute('aria-label', seVeAhora ? 'Ocultar contraseña' : 'Mostrar contraseña');
                iconoMostrar.style.display = seVeAhora ? 'none' : 'inline-block';
                iconoOcultar.style.display = seVeAhora ? 'inline-block' : 'none';
            });
        });

        // contraseñas coincidan antes de enviar
        function claveCoincide() {
            var coincide = clave.value === claveConfirmar.value;

            if (!coincide) {
                claveConfirmar.setCustomValidity('Las contraseñas no coinciden.');
                errorConfirmar.style.display = 'block';
            } else {
                claveConfirmar.setCustomValidity('');
                errorConfirmar.style.display = 'none';
            }

            return coincide;
        }

        clave.addEventListener('input', claveCoincide);
        claveConfirmar.addEventListener('input', claveCoincide);

        formulario.addEventListener('submit', function (evento) {
            var esValido = formulario.checkValidity();
            var coincide = claveCoincide();

            if (!esValido || !coincide) {
                evento.preventDefault();
                formulario.reportValidity();

                if (!coincide) {
                    claveConfirmar.focus();
                }
            }
        });
    })();
</script>
<?php } ?>

<?php
include("../includes/footer.php");
?>