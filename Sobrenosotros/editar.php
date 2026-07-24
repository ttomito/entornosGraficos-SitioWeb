<?php

include("../includes/conexion.php");
include("../includes/header.php");

$sql = "SELECT * FROM sobre_nosotros LIMIT 1";

$resultado = mysqli_query($link, $sql);

$sobre = mysqli_fetch_assoc($resultado) ?: [];

$codSobre = (int)($sobre['codSobre'] ?? 0);
$titulo = htmlspecialchars($sobre['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
$descripcion = htmlspecialchars($sobre['descripcion'] ?? '', ENT_QUOTES, 'UTF-8');
$mision = htmlspecialchars($sobre['mision'] ?? '', ENT_QUOTES, 'UTF-8');
$vision = htmlspecialchars($sobre['vision'] ?? '', ENT_QUOTES, 'UTF-8');

?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-body">

            <h2 id="titulo-form">Editar Sobre Nosotros</h2>

            <p class="text-muted">
                Todos los campos son obligatorios.
            </p>

            <form
                id="formSobreNosotros"
                action="guardar.php"
                method="post"
                aria-labelledby="titulo-form">

                <input
                    type="hidden"
                    name="codSobre"
                    value="<?= $codSobre ?>">

                <div class="mb-3">

                    <label for="titulo">Título</label>

                    <input
                        type="text"
                        id="titulo"
                        name="titulo"
                        class="form-control"
                        value="<?= $titulo ?>"
                        required
                        minlength="3"
                        maxlength="150"
                        aria-describedby="titulo-ayuda titulo-error">
                    <div id="titulo-ayuda" class="form-text">Entre 3 y 150 caracteres.</div>
                    <div id="titulo-error" class="invalid-feedback" role="alert"></div>

                </div>

                <div class="mb-3">

                    <label for="descripcion">Descripción</label>

                    <textarea
                        id="descripcion"
                        name="descripcion"
                        class="form-control"
                        rows="4"
                        required
                        minlength="10"
                        maxlength="1000"
                        aria-describedby="descripcion-ayuda descripcion-error"><?= $descripcion ?></textarea>
                    <div id="descripcion-ayuda" class="form-text">Entre 10 y 1000 caracteres.</div>
                    <div id="descripcion-error" class="invalid-feedback" role="alert"></div>

                </div>

                <div class="mb-3">

                    <label for="mision">Misión</label>

                    <textarea
                        id="mision"
                        name="mision"
                        class="form-control"
                        rows="4"
                        required
                        minlength="10"
                        maxlength="1000"
                        aria-describedby="mision-ayuda mision-error"><?= $mision ?></textarea>
                    <div id="mision-ayuda" class="form-text">Entre 10 y 1000 caracteres.</div>
                    <div id="mision-error" class="invalid-feedback" role="alert"></div>

                </div>

                <div class="mb-3">

                    <label for="vision">Visión</label>

                    <textarea
                        id="vision"
                        name="vision"
                        class="form-control"
                        rows="4"
                        required
                        minlength="10"
                        maxlength="1000"
                        aria-describedby="vision-ayuda vision-error"><?= $vision ?></textarea>
                    <div id="vision-ayuda" class="form-text">Entre 10 y 1000 caracteres.</div>
                    <div id="vision-error" class="invalid-feedback" role="alert"></div>

                </div>

                <button
                    type="submit"
                    class="btn btn-success"
                    onclick="validarYEnviar(event)">

                    Guardar cambios

                </button>

            </form>

        </div>

    </div>

</div>

<script>
    const REGLAS_VALIDACION = [{
            id: 'titulo',
            requerido: true,
            minLength: 3,
            maxLength: 150,
            mensajeVacio: 'Ingresá un título.',
            mensajeCorto: 'El título debe tener al menos 3 caracteres.',
            mensajeLargo: 'El título no puede superar los 150 caracteres.'
        },
        {
            id: 'descripcion',
            requerido: true,
            minLength: 10,
            maxLength: 1000,
            mensajeVacio: 'Ingresá una descripción.',
            mensajeCorto: 'La descripción debe tener al menos 10 caracteres.',
            mensajeLargo: 'La descripción no puede superar los 1000 caracteres.'
        },
        {
            id: 'mision',
            requerido: true,
            minLength: 10,
            maxLength: 1000,
            mensajeVacio: 'Ingresá la misión.',
            mensajeCorto: 'La misión debe tener al menos 10 caracteres.',
            mensajeLargo: 'La misión no puede superar los 1000 caracteres.'
        },
        {
            id: 'vision',
            requerido: true,
            minLength: 10,
            maxLength: 1000,
            mensajeVacio: 'Ingresá la visión.',
            mensajeCorto: 'La visión debe tener al menos 10 caracteres.',
            mensajeLargo: 'La visión no puede superar los 1000 caracteres.'
        }
    ];

    function mostrarError(campo, mensaje) {
        campo.classList.add('is-invalid');
        campo.setAttribute('aria-invalid', 'true');

        const elementoError = document.getElementById(campo.id + '-error');
        if (elementoError) {
            elementoError.textContent = mensaje;
        }
    }

    function ocultarError(campo) {
        campo.classList.remove('is-invalid');
        campo.removeAttribute('aria-invalid');

        const elementoError = document.getElementById(campo.id + '-error');
        if (elementoError) {
            elementoError.textContent = '';
        }
    }

    function validarCampo(regla) {
        const campo = document.getElementById(regla.id);
        const valor = campo.value.trim();

        if (regla.requerido && valor === '') {
            mostrarError(campo, regla.mensajeVacio);
            return false;
        }

        if (valor !== '' && regla.minLength && valor.length < regla.minLength) {
            mostrarError(campo, regla.mensajeCorto);
            return false;
        }

        if (regla.maxLength && valor.length > regla.maxLength) {
            mostrarError(campo, regla.mensajeLargo);
            return false;
        }

        ocultarError(campo);
        return true;
    }

    function validarFormulario() {
        let esValido = true;
        let primerCampoInvalido = null;

        REGLAS_VALIDACION.forEach((regla) => {
            const campoValido = validarCampo(regla);
            if (!campoValido) {
                esValido = false;
                if (!primerCampoInvalido) {
                    primerCampoInvalido = document.getElementById(regla.id);
                }
            }
        });

        if (primerCampoInvalido) {
            primerCampoInvalido.focus();
        }

        return esValido;
    }

    // Valida cada campo apenas el usuario sale de él, para dar feedback temprano
    REGLAS_VALIDACION.forEach((regla) => {
        const campo = document.getElementById(regla.id);
        campo.addEventListener('blur', () => validarCampo(regla));
    });

    function validarYEnviar(event) {
        event.preventDefault();

        const formulario = event.target.closest('form');

        if (!validarFormulario()) {
            return;
        }

        formulario.submit();
    }
</script>

<?php
$alertasEditar = [
    'titulo_invalido' => [
        'icon'  => 'error',
        'title' => 'Título inválido',
        'text'  => 'El título debe tener entre 3 y 150 caracteres.'
    ],
    'descripcion_invalida' => [
        'icon'  => 'error',
        'title' => 'Descripción inválida',
        'text'  => 'La descripción debe tener entre 10 y 1000 caracteres.'
    ],
    'mision_invalida' => [
        'icon'  => 'error',
        'title' => 'Misión inválida',
        'text'  => 'La misión debe tener entre 10 y 1000 caracteres.'
    ],
    'vision_invalida' => [
        'icon'  => 'error',
        'title' => 'Visión inválida',
        'text'  => 'La visión debe tener entre 10 y 1000 caracteres.'
    ],
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error al guardar. Intente nuevamente.'
    ]
];

if (isset($_GET['alerta']) && array_key_exists($_GET['alerta'], $alertasEditar)) {
    $alertaEditar = $alertasEditar[$_GET['alerta']];
?>

    <script>
        Swal.fire({
            icon: '<?= $alertaEditar['icon'] ?>',
            title: '<?= $alertaEditar['title'] ?>',
            text: '<?= $alertaEditar['text'] ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php } ?>

<?php include("../includes/footer.php"); ?>